<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Dto\Output;

use OpenApi\Annotations as OA;

class OutputPagination
{
    /**
     * @OA\Property(example="425")
     */
    public int $count;

    /**
     * @OA\Property(example="22")
     */
    public int $totalPages;

    /**
     * @OA\Property(example="1")
     */
    public int $page;

    /**
     * @OA\Property(example="20")
     */
    public int $size;

    public function __construct(
        int $count,
        int $totalPages,
        int $page,
        int $size
    ) {
        $this->count = $count;
        $this->totalPages = $totalPages;
        $this->page = $page;
        $this->size = $size;
    }
}
