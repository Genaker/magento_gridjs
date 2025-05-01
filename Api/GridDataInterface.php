<?php
declare(strict_types=1);

namespace Mage\Grid\Api;

/**
 * Interface for retrieving grid data
 */
interface GridDataInterface
{


    /**
     * Get grid data as an array with optional filters and pagination
     *
     * @param string $gridId
     * @param array $filters
     * @param int $page
     * @param int $pageSize
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGridData(
        string $gridId,
        array $filters = [],
        int $page = 1,
        int $pageSize = 20
    ): array;
}