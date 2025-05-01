<?php
declare(strict_types=1);

namespace Mage\Grid\Api\Data;

/**
 * Interface for grid data response
 */
interface GridDataInterface
{
    /**
     * Get grid data
     *
     * @param string $gridId
     * @param array $filters
     * @param int $page
     * @param int $pageSize
     * @return array
     */
    public function getGridData(
        string $gridId,
        array $filters = [],
        int $page = 1,
        int $pageSize = 20
    ): array;
} 