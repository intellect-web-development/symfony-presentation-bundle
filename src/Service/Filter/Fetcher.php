<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Service\Filter;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Symfony\PresentationBundle\Exception\DomainException;

class Fetcher
{
    public const AGGREGATE_ALIAS = 'entity';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createContext(string $entityClass): FetcherContext
    {
        $entityRepository = $this->entityManager->getRepository($entityClass);
        $queryBuilder = $entityRepository->createQueryBuilder(self::AGGREGATE_ALIAS);
        $filterSqlBuilder = new FilterSqlBuilder($queryBuilder);

        return new FetcherContext(
            $this->entityManager,
            $queryBuilder,
            $entityClass,
            self::AGGREGATE_ALIAS,
            $this->entityManager->getClassMetadata($entityClass),
            $filterSqlBuilder
        );
    }

    public function count(FetcherContext $context): int
    {
        $idPropertyName = current($context->entityClassMetadata->identifier);
        $aggregateAlias = self::AGGREGATE_ALIAS;
        return (clone $context->queryBuilder)
            ->select("count(distinct {$aggregateAlias}.{$idPropertyName})")
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getById(
        FetcherContext $context,
        string $id,
        bool $eager = true,
        array $hints = []
    ): object {
        $aggregateAlias = self::AGGREGATE_ALIAS;
        $idPropertyName = $context->entityClassMetadata->identifier[0];

        $context->queryBuilder
            ->andWhere("$aggregateAlias.{$idPropertyName} = :id")
            ->setParameter('id', $id);

        if ($eager) {
            $this->addEagerQueryToRelations($context, $hints, $context->queryBuilder);
        }
        $result = $context->queryBuilder->getQuery()->getResult();
        if (empty($result)) {
            throw new DomainException("Entity with {$idPropertyName} '{$id}' not exist", 400);
        }
        return current($result);
    }

    public function getByIds(FetcherContext $context, array $ids, bool $eager = true, array $hints = []): array
    {
        $aggregateAlias = self::AGGREGATE_ALIAS;
        $idPropertyName = current($context->entityClassMetadata->identifier);

        $idsPrepared = array_map(static function (string $id) {
            return "'$id'";
        }, $ids);
        if (empty($idsPrepared)) {
            return [];
        }

        $context->queryBuilder
            ->where("$aggregateAlias.{$idPropertyName} IN (" . implode(',', $idsPrepared) . ')');

        if ($eager) {
            $this->addEagerQueryToRelations($context, $hints, $context->queryBuilder);
        }

        return $context->queryBuilder->getQuery()->getResult();
    }

    protected function fetchNestedAssocRelation(
        array &$assocRelations,
        string $metaPath,
        string $targetEntity,
        int $maxNestedLevel,
        int $currentNestedLevel,
    ): void {
        if ($maxNestedLevel < $currentNestedLevel) {
            return;
        }
        $em = $this->entityManager;
        $meta = $em->getClassMetadata($targetEntity);

        $assocRelations[$targetEntity] = [
            'entity' => $targetEntity,
            'pathPrefix' => $metaPath,
            'paths' => (static function() use ($em, $targetEntity) {
                $meta = $em->getClassMetadata($targetEntity);
                $fields = [];
                foreach ($meta->associationMappings as $mapping) {
                    $fields[] = $mapping['fieldName'];
                }

                return $fields;
            })()
        ];

        foreach ($meta->associationMappings as $mapping) {
            if (array_key_exists($mapping['targetEntity'], $assocRelations)) {
                continue;
            }
            $this->fetchNestedAssocRelation(
                assocRelations: $assocRelations,
                metaPath: "$metaPath.{$mapping['fieldName']}",
                targetEntity: $mapping['targetEntity'],
                maxNestedLevel: $maxNestedLevel,
                currentNestedLevel: ++$currentNestedLevel
            );
        }
    }

    public function createAssocRelationMap(FetcherContext $context, array $hints): array
    {
        $maxNestedLevel = $hints['maxNestedLevel'] ?? 1;
        if ($maxNestedLevel < 1) {
            return [];
        }
        $em = $this->entityManager;

        $assocRelations = [
            $context->entityClass => [
                'entity' => $context->entityClass,
                'pathPrefix' => null,
                'paths' => (static function() use ($em, $context) {
                    $meta = $em->getClassMetadata($context->entityClass);
                    $fields = [];
                    foreach ($meta->associationMappings as $mapping) {
                        $fields[] = $mapping['fieldName'];
                    }

                    return $fields;
                })()
            ]
        ];
        if ($maxNestedLevel === 1) {
            return $assocRelations;
        }

        foreach ($em->getClassMetadata($context->entityClass)->associationMappings as $mapping) {
            $this->fetchNestedAssocRelation(
                assocRelations: $assocRelations,
                metaPath: $mapping['fieldName'],
                targetEntity: $mapping['targetEntity'],
                maxNestedLevel: $maxNestedLevel,
                currentNestedLevel: 1
            );
        }

        foreach ($hints['pathPrefixMap'] ?? [] as $entityClass => $pathPrefix) {
            $assocRelations[$entityClass]['entity'] = $entityClass;
            $assocRelations[$entityClass]['pathPrefix'] = $pathPrefix;
            $assocRelations[$entityClass]['paths'] = $assocRelations[$entityClass]['paths'] ?? [];
        }

        return $assocRelations;
    }

    protected function addEagerQueryToRelations(FetcherContext $context, array $hints, QueryBuilder $qb): void
    {
        $aggregateAlias = self::AGGREGATE_ALIAS;

        $assocRelations = $this->createAssocRelationMap($context, $hints);

        $joins = [];
        foreach ($assocRelations as $relation) {
            foreach ($relation['paths'] as $path) {
                $qb->getQuery()->setFetchMode($relation['entity'], $path, ClassMetadata::FETCH_EAGER);

                $propertyPath = implode('.', array_filter([
                    $relation['pathPrefix'],
                    $path
                ], static function (?string $path) {
                    return $path !== null;
                }));

                $explodePropertyPath = explode('.', $propertyPath);
                for ($level = 1, $levelMax = count($explodePropertyPath); $level <= $levelMax; $level++) {
                    $relationPath = Helper::makeRelationPath($explodePropertyPath, $level);
                    $absolutePath = Helper::makeAliasPathFromPropertyPath("$aggregateAlias.$relationPath");

                    $alias = Helper::pathToAlias($absolutePath);
                    if (in_array($alias, $joins, true)) {
                        continue;
                    }

                    $qb->leftJoin($absolutePath, $alias)->addSelect($alias);
                    $joins[] = $alias;
                }
            }
        }
    }

    /**
     * @param FetcherContext $context
     * @return array<string>
     */
    public function searchEntityIds(FetcherContext $context): array {
        $idPropertyName = current($context->entityClassMetadata->identifier);

        return array_map(static function (array $entity) use ($idPropertyName) {
            return $entity[$idPropertyName];
        }, $context->queryBuilder
            ->select("{$context->aggregateAlias}.{$idPropertyName}")
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY)
        );
    }
}
