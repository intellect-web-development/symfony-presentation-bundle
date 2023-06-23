<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Console;

use IWD\Symfony\PresentationBundle\Exception\ValidatorException;
use IWD\Symfony\PresentationBundle\Interfaces\InputContractInterface;
use IWD\Symfony\PresentationBundle\Service\CliContractResolver;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

abstract class CliCommand extends Command
{
    public function __construct(
        private readonly CliContractResolver $cliContractResolver,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        if ($this->autoconfigure()) {
            $inputContractClass = static::getInputContractClass();
            $inputContract = new $inputContractClass();
            // Получаем объект ReflectionClass для класса InputContract
            $reflectionClass = new ReflectionClass($inputContractClass);

            // Получаем свойства класса
            $properties = $reflectionClass->getProperties();

            // Массив для хранения информации о свойствах
            $propertiesInfo = [];

            // Обходим каждое свойство
            foreach ($properties as $property) {
                // Получаем имя свойства
                $propertyName = $property->getName();

                // Получаем комментарий свойства
                $propertyCommentRaw = $property->getDocComment();
                if (false === $propertyCommentRaw) {
                    $propertyCommentRaw = '';
                }
                $propertyComment = trim(str_replace(['/**', '*/', '/*'], '', $propertyCommentRaw));

                // Проверяем, является ли свойство nullable
                $isNullable = $property->getType()?->allowsNull();

                // Получаем дефолтное значение свойства
                $propertyDefaultValue = $property->isInitialized($inputContract) ? $property->getValue($inputContract) : null;

                // Добавляем информацию о свойстве в массив
                $propertiesInfo[$propertyName] = [
                    'name' => $propertyName,
                    'description' => $propertyComment,
                    'nullable' => $isNullable,
                    'default' => $propertyDefaultValue,
                ];
            }

            foreach ($propertiesInfo as $propertyInfo) {
                $this->addOption(
                    name: $propertyInfo['name'],
                    mode: $propertyInfo['nullable'] ? InputOption::VALUE_OPTIONAL : InputOption::VALUE_REQUIRED,
                    description: $propertyInfo['description'],
                    default: $propertyInfo['default'],
                );
            }
        }
    }

    /**
     * @return class-string<InputContractInterface>
     */
    abstract protected static function getInputContractClass(): string;

    abstract protected function handle(SymfonyStyle $io, InputContractInterface $inputContract): int;

    protected function autoconfigure(): bool
    {
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            /** @var InputContractInterface $inputContract */
            $inputContract = $this->cliContractResolver->resolve($input, static::getInputContractClass());
        } catch (ValidatorException $exception) {
            $violations = json_decode($exception->getMessage(), true, 512, JSON_THROW_ON_ERROR);
            $message = 'Command options has violations:' . PHP_EOL;

            $i = 0;
            foreach ($violations as $property => $violation) {
                ++$i;
                $message .= sprintf(
                        '%s. %s: %s',
                        $i, $property, $violation
                    ) . PHP_EOL;
            }
            $io->error($message);

            return self::FAILURE;
        }

        return $this->handle(
            io: $io,
            inputContract: $inputContract,
        );
    }
}
