<?php
declare(strict_types=1);

namespace Mage\Grid\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Mage\Grid\Model\Grid;

class GridViewModel implements ArgumentInterface
{
    /**
     * @var Grid
     */
    private $grid;

    /**
     * @param Grid $grid
     */
    public function __construct(
        Grid $grid
    ) {
        $this->grid = $grid;
    }

    /**
     * Get grid instance
     *
     * @return Grid
     */
    public function getGrid(): Grid
    {
        return $this->grid;
    }
} 