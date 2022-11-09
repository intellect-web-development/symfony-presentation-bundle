<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Dto\Input;

//todo: сделать это интерфейсом как-нибудь, чтобы была возможность кастомизировать валидацию в конструкторе
class Pagination
{
    private int $pageNumber;
    private int $pageSize;
    private int $offset;

    public function __construct(int $pageNumber = 1, int $pageSize = 1, int $maxSize = 500)
    {
        //todo: если $pageNumber меньше 1 - то присвоить единицу
        $this->pageNumber = $pageNumber;
        $this->pageSize = ($pageSize > $maxSize) ? $maxSize : $pageSize;
        $this->offset = ($this->pageNumber - 1) * $this->pageSize;
    }

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }
}
