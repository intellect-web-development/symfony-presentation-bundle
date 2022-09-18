# Symfony Presentation bundle
## Описание:
Данный пакет предназначен для удобной работы с уровнем представления symfony-приложений.

## Назначение:
- Валидация входных аргументов к методам API
- Генерирование Swagger-документации на основе OpenApi-совместимых DTO
- Сортировка, фильтрация и пагинация по сущностям доктрины
- Получение ресурса/агрегата на основе сущностей доктрины

## Пример использования фильтрации:
### Пример строки запроса:
```http request
GET /clients?filter[emails.email][like]=26d@&sort=-createdAt,updatedAt&page[number]=1&page[size]=20&filter[userId][eq]=ccf92b7a-8e05-4f4b-9f0a-e4360dbacb23&filter[name.translations.last][eq]=Tesla&lang=ru
```

### Сортировка:
#### Описание:
По умолчанию сортировка задается параметром "sort".
Направление сортировки задается опциональным знаком '-' перед названием свойства, по которому предполагается сортировка.
Если знак '-' присутствует, то сортировка по этому полю ведется с модификатором DESC, иначе - ASC.
Допускается сортировка по нескольким полям агрегата. Для этого необходимо написать несколько полей, разделив их символом
','. Чем раньше было указано поле, тем больший "вес" оно имеет при выборке.
#### Пример:
```
sort='-createdAt,updatedAt'
```

### Пагинация:
По умолчанию пагинация задается параметром "page".
Параметр имеет два поля - number и size.
- "number" указывает на номер страницы, которую запрашивает клиент. По умолчанию: 1
- "size" указывает размер страницы(сколько агрегатов должно быть отображено). По умолчанию: 20
#### Описание:
```
page[number]='1'
page[size]='20'
```

### Фильтрация:
#### Описание:
Операторы поиска:

| Название          | Допустимые значения              | Пример                                                     | Описание                                           |
|-------------------|----------------------------------|------------------------------------------------------------|----------------------------------------------------|
| NOT_IN            | 'not-in'                         | filter[status][not-in][]='blocked'                         | Свойство не содержит ни одно из указанных значений |
| IN                | 'in'                             | filter[status][in][]='active'                              | Свойство содержит одно из указанных значений       |
| RANGE             | 'range'                          | filter[rating][range]='17,42'                              | Свойство находится в выбранном указанном диапазоне |
| IS_NULL           | 'is-null'                        | filter[gender][is-null]                                    | Свойство равно null                                |
| NOT_NULL          | 'not-null'                       | filter[name][not-null]                                     | Свойство не равно null                             |
| LESS_THAN         | 'less-than', '<', 'lt'           | filter[rating][<]='94'                                     | Свойство меньше указанного значения                |
| GREATER_THAN      | 'greater-than', '>', 'gt'        | filter[rating][>]='42'                                     | Свойство больше указанного значения                |
| LESS_OR_EQUALS    | 'less-or-equals', '<=', 'lte'    | filter[rating][<=]='15'                                    | Свойство меньше или равно указанному значению      |
| GREATER_OR_EQUALS | 'greater-or-equals', '>=', 'gte' | filter[rating][>=]='97'                                    | Свойство больше или равно указанному значению      |
| LIKE              | 'like'                           | filter[email][like]='26d@'                                 | Свойство содержит часть указанного значения        |
| NOT_LIKE          | 'not-like'                       | filter[email][not-like]='27d@'                             | Свойство не содержит часть указанного значения     |
| EQUALS            | 'equals', '=', 'eq'              | filter[userId][eq]='ccf92b7a-8e05-4f4b-9f0a-e4360dbacb23'  | Свойство эквивалентно указанному значению          |
| NOT_EQUALS        | 'not-equals', '!=', '<>', 'neq'  | filter[userId][neq]='aaf92b7a-8e05-4f4b-9f0a-e4360dbacb23' | Свойство не эквивалентно указанному значению       |

#### Пример:
```
filter[userId][eq]='ccf92b7a-8e05-4f4b-9f0a-e4360dbacb23'
filter[name.translations.last][eq]='Tesla'
filter[emails.email][like]='26d@'
filter[userId][eq]='ccf92b7a-8e05-4f4b-9f0a-e4360dbacb23'
filter[name.translations.last][eq]='Tesla'
filter[emails.email][in][]='0791d11b6a952a3804e7cb8a220d0a9b@mail.ru'
filter[emails.email][in][]='0891d11b6a952a3804e7cb8a220d0a9b@mail.ru'
```

## Примеры кода:
### Query:
#### Определение
Query - запрос на получение текущего состояния сущности(ресурса/агрегата), без изменения его состояния.

#### Aggregate:
Запрос на получение данных агрегата.

#### Пример Read-action:
```php
<?php

declare(strict_types=1);

namespace App\Http\User\Read;

use App\Entity\User;
use App\Http\User\CommonOutputContract;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\PresentationBundle\Dto\Input\OutputFormat;
use Symfony\PresentationBundle\Dto\Output\ApiFormatter;
use Symfony\PresentationBundle\Service\Presenter;
use Symfony\PresentationBundle\Service\QueryBus\Aggregate\Bus;
use Symfony\PresentationBundle\Service\QueryBus\Aggregate\Query;

class Action
{
    /**
     * @OA\Tag(name="User")
     * @OA\Response(
     *     response=200,
     *     description="Read User",
     *     @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref=@Model(type=ApiFormatter::class)),
     *              @OA\Schema(type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      ref=@Model(type=CommonOutputContract::class)
     *                  ),
     *                  @OA\Property(
     *                      property="status",
     *                      example="200"
     *                 )
     *             )
     *         }
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad Request"
     * ),
     * @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     * ),
     * @OA\Response(
     *     response=403,
     *     description="Forbidden"
     * ),
     * @OA\Response(
     *     response=404,
     *     description="Resource Not Found"
     * )
     * @Security(name="Bearer")
     */
    #[Route(
        data: '/users/{id}.{_format}',
        name: 'users.read',
        defaults: ['_format' => 'json'],
        methods: ['GET']
    )]
    public function read(string $id, Bus $bus, OutputFormat $outputFormat, Presenter $presenter): Response
    {
        $query = new Query(
            aggregateId: $id,
            targetEntityClass: User::class
        );

        /** @var User $user */
        $user = $bus->query($query);

        return $presenter->present(
            data: ApiFormatter::prepare(
                CommonOutputContract::create($user)
            ),
            outputFormat: $outputFormat
        );
    }
}
```

#### Пример Search-action:
```php
<?php

declare(strict_types=1);

namespace App\Http\User\Search;

use App\Entity\User;
use App\Http\User\CommonOutputContract;
use Symfony\PresentationBundle\Service\Presenter;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\PresentationBundle\Dto\Input\OutputFormat;
use Symfony\PresentationBundle\Dto\Input\SearchQuery;
use Symfony\PresentationBundle\Dto\Output\ApiFormatter;
use Symfony\PresentationBundle\Dto\Output\OutputPagination;
use Symfony\PresentationBundle\Service\QueryBus\Search\Bus;
use Symfony\PresentationBundle\Service\QueryBus\Search\Query;

class Action
{
    /**
     * @OA\Tag(name="User")
     * @OA\Get(
     *     @OA\Parameter(
     *          name="searchQuery",
     *          in="query",
     *          required=false,
     *          @OA\Schema(
     *              ref=@Model(type=QueryParams::class)
     *          ),
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Search by Users",
     *     @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref=@Model(type=ApiFormatter::class)),
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      type="object",
     *                      @OA\Property(
     *                          property="data",
     *                          ref=@Model(type=CommonOutputContract::class),
     *                          type="object"
     *                      ),
     *                      @OA\Property(
     *                          property="pagination",
     *                          ref=@Model(type=OutputPagination::class),
     *                          type="object"
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="status",
     *                      example="200"
     *                 )
     *             )
     *         }
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad Request"
     * ),
     * @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     * ),
     * @OA\Response(
     *     response=403,
     *     description="Forbidden"
     * ),
     * @OA\Response(
     *     response=404,
     *     description="Resource Not Found"
     * )
     * @Security(name="Bearer")
     */
    #[Route(
        data: '/users.{_format}',
        name: 'users.search',
        defaults: ['_format' => 'json'],
        methods: ['GET']
    )]
    public function search(
        Bus          $bus,
        SearchQuery  $searchQuery,
        OutputFormat $outputFormat,
        Presenter    $presenter
    ): Response {
        $query = new Query(
            targetEntityClass: User::class,
            pagination: $searchQuery->pagination,
            filters: $searchQuery->filters,
            sorts: $searchQuery->sorts
        );

        $searchResult = $bus->query($query);
        return $presenter->present(
            data: ApiFormatter::prepare([
                'data' => array_map(static function (User $user) {
                    return CommonOutputContract::create($user);
                }, $searchResult->entities),
                'pagination' => $searchResult->pagination
            ]),
            outputFormat: $outputFormat
        );
    }
}
```
#### Пример SearchQueryParams:
```php
<?php

declare(strict_types=1);

namespace App\Http\User\Search;

use OpenApi\Annotations as OA;
use Symfony\PresentationBundle\Dto\Input\Filters;
use Symfony\PresentationBundle\Dto\Input\SearchQuery;

class QueryParams extends SearchQuery
{
    /**
     * @OA\Property(
     *     property="filter",
     *     type="object",
     *     example={
     *         "id": {"eq": "ab4ac777-e054-45ec-b997-b69062917d10"},
     *         "createdAt": {"range": "2022-02-22 12:00:00,2022-02-22 14:00:00"},
     *         "updatedAt": {"range": "2022-02-22 12:00:00,2022-02-22 14:00:00"},
     *         "email": {"eq": "user@dev.ru"},
     *         "status": {"eq": "active"}
     *     }
     * )
     */
    public Filters $filters;
}
```

#### Пример Command-action:
```php
<?php

declare(strict_types=1);

namespace App\Http\User\Create;

use App\Http\User\CommonOutputContract;
use App\Entity\User\UseCase\Create\Handler;
use Symfony\PresentationBundle\Service\Presenter;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\PresentationBundle\Dto\Input\OutputFormat;
use Symfony\PresentationBundle\Dto\Output\ApiFormatter;

class Action
{
    /**
     * @OA\Tag(name="Auth.User")
     * @OA\Post(
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 ref=@Model(type=InputContract::class)
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Create User",
     *     @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref=@Model(type=ApiFormatter::class)),
     *              @OA\Schema(type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      ref=@Model(type=CommonOutputContract::class)
     *                  ),
     *                  @OA\Property(
     *                      property="status",
     *                      example="200"
     *                 )
     *             )
     *         }
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad Request"
     * ),
     * @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     * ),
     * @OA\Response(
     *     response=403,
     *     description="Forbidden"
     * ),
     * @OA\Response(
     *     response=404,
     *     description="Resource Not Found"
     * )
     * @Security(name="Bearer")
     */
    #[Route(
        data: '/users/create.{_format}',
        name: 'users.create',
        defaults: ['_format' => 'json'],
        methods: ['POST']
    )]
    public function create(
        OutputFormat $outputFormat,
        InputContract $contract,
        Handler $handler,
        Presenter $presenter
    ): Response {
        $user = $handler->handle(
            $contract->createCommand()
        );

        return $presenter->present(
            data: ApiFormatter::prepare(
                data: CommonOutputContract::create($user),
                messages: ['User created']
            ),
            outputFormat: $outputFormat
        );
    }
}
```

#### Пример InputContract:
```php
<?php

declare(strict_types=1);

namespace App\Http\User\Create;

use App\Entity\User\ValueObject\Email as UserEmail;
use App\Entity\User\UseCase\Create\Command;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\PresentationBundle\Interfaces\InputContractInterface;

class InputContract implements InputContractInterface
{
    #[NotNull]
    #[Length(min: 3, max: 255)]
    public string $password;

    #[NotNull]
    #[Email]
    #[Length(max: 255)]
    public string $email;

    public function createCommand(): Command
    {
        return new Command(
            password: $this->password,
            email: new UserEmail($this->email)
        );
    }
}
```

#### Пример OutputContract:
```php
<?php

declare(strict_types=1);

namespace App\Http\Contract\User;

use App\Entity\User;
use DateTimeInterface;

class CommonOutputContract
{
    public string $id;
    public string $createdAt;
    public string $updatedAt;
    public string $email;
    public string $status;
    public string $role;

    public static function create(User $user): self
    {
        $contract = new self();
        $contract->id = $user->getId()->getValue();
        $contract->createdAt = $user->getCreatedAt()->format(DateTimeInterface::ATOM);
        $contract->updatedAt = $user->getUpdatedAt()->format(DateTimeInterface::ATOM);
        $contract->email = $user->getEmail()->getValue();
        $contract->status = $user->getStatus();
        $contract->role = $user->getRole()->getName();

        return $contract;
    }
}
```
