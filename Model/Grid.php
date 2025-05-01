<?php
declare(strict_types=1);

namespace Mage\Grid\Model;

use Magento\Framework\DataObject;
use Mage\Grid\Api\Data\GridInterface;

class Grid extends DataObject implements GridInterface
{
    /**
     * @inheritDoc
     */
    public function getGridData(): array
    {
        return $this->getData('data') ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setGridData(array $data): self
    {
        return $this->setData('data', $data);
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        return $this->getGridData();
    }

    /**
     * @inheritDoc
     */
    public function getTotalCount(): int
    {
        return (int)($this->getData('total_count') ?? 0);
    }

    /**
     * @inheritDoc
     */
    public function setTotalCount(int $totalCount): self
    {
        return $this->setData('total_count', $totalCount);
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return $this->getData('config') ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $config): self
    {
        return $this->setData('config', $config);
    }

    /**
     * @inheritDoc
     */
    public function getFields(): array
    {
        return $this->getData('fields') ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setFields(array $fields): self
    {
        return $this->setData('fields', $fields);
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return $this->getData('filters') ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setFilters(array $filters): self
    {
        return $this->setData('filters', $filters);
    }

    /**
     * @inheritDoc
     */
    public function getPerformanceMetrics(): array
    {
        return $this->getData('performance_metrics') ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setPerformanceMetrics(array $metrics): self
    {
        return $this->setData('performance_metrics', $metrics);
    }

    /**
     * Get grid data
     *
     * @param string $gridId
     * @param array $filters
     * @param int $page
     * @param int $pageSize
     * @return array
     */
    public function getGrid(
        string $gridId,
        array $filters = [],
        int $page = 1,
        int $pageSize = 20
    ): array {
        // Example data structure
        return [
            'data' => $this->getData('data') ?? [],
            'total' => $this->getData('total') ?? 0,
            'page' => $page,
            'pageSize' => $pageSize
        ];
    }
} 