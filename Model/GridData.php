<?php
declare(strict_types=1);

namespace Mage\Grid\Model;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Mage\Grid\ViewModel\GenericViewModelGrid;
use Mage\Grid\Api\Data\GridDataInterface;

class GridData extends DataObject implements \Mage\Grid\Api\GridDataInterface
{
    /**
     * @var GenericViewModelGrid
     */
    private $viewModel;

    /**
     * @param GenericViewModelGrid $viewModel
     */
    public function __construct(
        GenericViewModelGrid $viewModel
    ) {
        $this->viewModel = $viewModel;
    }

    /**
     * @inheritDoc
     */
    public function getGridData(
        string $gridId,
        array $filters = [],
        int $page = 1,
        int $pageSize = 20
    ): array {
        try {
            // Start timing
            $startTime = microtime(true);

            // Get grid data
            $data = $this->viewModel->getGridData($filters, $page, $pageSize);
            $totalCount = $this->viewModel->getTotalCount();

            // Calculate performance metrics
            $endTime = microtime(true);
            $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

            // Return response data
            return [
                'data' => $data,
                'total' => $totalCount,
                'page' => $page,
                'pageSize' => $pageSize,
                'performance' => [
                    'execution_time' => $executionTime,
                    'sql_time' => $this->viewModel->getSqlTime(),
                    'count_time' => $this->viewModel->getCountTime()
                ]
            ];
        } catch (\Exception $e) {
            throw new LocalizedException(__('Error retrieving grid data: %1', $e->getMessage()));
        }
    }
}