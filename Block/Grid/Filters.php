<?php
namespace Mage\Grid\Block\Grid;

use Magento\Framework\View\Element\Template;

class Filters extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Mage_Grid::grid/filters.phtml';

    /**
     * @var array
     */
    protected $filterData = [];

    /**
     * Set filter data
     *
     * @param array $data
     * @return $this
     */
    public function setFilterData(array $data)
    {
        $this->filterData = $data;
        return $this;
    }

    /**
     * Get filter data
     *
     * @return array
     */
    public function getFilterData(): array
    {
        return $this->filterData;
    }

    /**
     * Get fields data
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->filterData['fields'] ?? [];
    }

    /**
     * Get filters data
     *
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filterData['filters'] ?? [];
    }
} 