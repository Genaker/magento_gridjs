<?php
namespace Mage\Grid\Controller\Adminhtml\Grid;

use Mage\Grid\Block\GenericGrid;
use Mage\Grid\ViewModel\GenericViewModelGrid;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\LayoutFactory;

class Data extends Action
{
    /**
     * @var GenericViewModelGrid
     */
    private $gridViewModel;

    /**
     * @var GenericGrid
     */
    private $gridBlock;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param GenericViewModelGrid $gridViewModel
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        Action\Context $context,
        GenericViewModelGrid $gridViewModel,
        LayoutFactory $layoutFactory,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->gridViewModel = $gridViewModel;
        $this->layoutFactory = $layoutFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Check admin permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::sales');
    }

    /**
     * Get grid data via API
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        try {
            // Get the layout and block
            $layout = $this->resultPageFactory->create()->getLayout();
            $request = $this->getRequest();
            $request->setParams(['data' => 'false']);

            $layout->getUpdate()->load();  // collect all handles (default, route, etc.)
            $layout->generateXml();  // merge the XML
            $layout->generateElements();  // turn <block/> tags into PHP objects

            $this->gridBlock = $layout->getBlock('grid_generic_grid');

            if (!$this->gridBlock) {
                throw new LocalizedException(__('Grid block not found'));
            }

            $fields = array_keys($this->gridBlock->getFieldsNames());  // array: ['id', 'order_number', ...]
            $fieldsFull = $this->gridBlock->getFields();  // associative array: ['id' => 'ID', ...]
            $jsonGridData = $this->gridBlock->getGridJsonData();  // JSON-encoded grid data
            // dd($jsonGridData);

            // Return JSON response
            $this->getResponse()->representJson(
                $jsonGridData
            );
        } catch (LocalizedException $e) {
            throw new \Exception('Error: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while fetching grid data:' . $e->getMessage(), 500);
        }
    }
}
