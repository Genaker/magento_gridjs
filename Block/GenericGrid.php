<?php
namespace Mage\Grid\Block;

use Magento\Backend\Block\Template;
use Magento\Framework\View\Element\Template\Context as FrontendContext; // <-- use this if we need framework grid
use Magento\Backend\Block\Template\Context as BackendContext; // <-- use this if we need backend grid
use Mage\Grid\ViewModel\GenericViewModelGrid;
use Magento\Framework\Message\ManagerInterface;

/**
 * Mage Grid GenericGrid Block
 *
 * This block is the main entry point for rendering a Mage Grid in Magento 2. It is highly configurable via layout XML and designed for AI-first extensibility.
 *
 * Key features:
 * - Configurable data sources (collection or table)
 * - Dynamic field and template configuration
 * - Support for additional HTML/JS templates (e.g., popups, custom scripts)
 * - Robust error handling and developer-friendly structure
 */
class GenericGrid extends Template
{
    /**
     * @var GenericViewModelGrid The ViewModel for grid data and logic
     */
    protected $viewModel;

    /**
     * @var array Associative array of field keys => labels
     */
    protected $fields;

    /**
     * @var string|null The database table name (if using SQL-based grid)
     */
    protected $tableName;

    /**
     * @var string|object|null The collection class (if using collection-based grid)
     */
    protected $collectionClass;

    /**
     * @var ManagerInterface For displaying admin error messages
     */
    protected $messageManager;

    /**
     * @var string The main grid template file
     */
    protected $_template = 'Mage_Grid::grid/grid-component.phtml';

    /**
     * @var array List of additional HTML/JS templates to render after the grid
     */
    protected $additionalHtmlTemplates = ['Mage_Grid::grid/additional-html.phtml'];

    /**
     * Constructor
     *
     * @param BackendContext $backendContext
     * @param GenericViewModelGrid $defaultViewModel
     * @param ManagerInterface $messageManager
     * @param array $data
     *
     * Initializes the grid block, sets up fields, data sources, and error handling.
     */
    public function __construct(
        BackendContext $backendContext,
        GenericViewModelGrid $defaultViewModel,
        ManagerInterface $messageManager,
        array $data = []
    ) {
        parent::__construct($backendContext, $data);
        $this->messageManager = $messageManager;
        // Use provided ViewModel or fallback to default
        $this->viewModel = $this->getData('viewModel') ?: $defaultViewModel;
        $this->collectionClass = $this->getData('collectionClass');

        $this->tableName = $this->getData('tableName');
        // If a collection class is set, ignore table name
        if ($this->collectionClass !== 'none') {
            $this->tableName = null;
        }
        // Set collection class in ViewModel if provided
        if ($this->collectionClass !== 'none') {
            $this->viewModel->setCollectionClass($this->collectionClass);
        }
        // Require at least one data source
        if ($this->tableName === 'none' && $this->collectionClass === 'none') {
            $this->messageManager->addErrorMessage('Mage Grid: Collection class or table name is required');
            throw new \Exception('Mage Grid: Collection class or table name is required');
        }
        // Set up fields and field names
        $this->fields = $this->getData('fields') ?: ['id' => 'ID'];
        $this->viewModel->setFields(array_keys($this->fields));
        $this->viewModel->setFieldsNames(array_values($this->fields));
        $this->viewModel->setTableName($this->tableName);
    }

    /**
     * Get the ViewModel instance
     * @return GenericViewModelGrid
     */
    public function getViewModel()
    {
        return $this->viewModel;
    }

    /**
     * Get the configured fields (associative array)
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get the field keys (for use in data arrays)
     * @return array
     */
    public function getFieldNames()
    {
        return array_keys($this->fields);
    }

    /**
     * Get the configured table name (if any)
     * @return string|null
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Get the grid data as JSON (for Grid.js)
     * @return string JSON-encoded data
     */
    public function getGridJsonData()
    {
        return $this->viewModel->getJsonGridData($this->getFieldNames());
    }

    /**
     * Render all configured additional HTML/JS templates after the grid
     *
     * @return string Rendered HTML/JS (with error messages for missing templates)
     *
     * Usage: Place <?= $block->getAditionalHTML() ?> in your grid template.
     * Configure templates via layout XML with the 'additional_html_templates' argument.
     */
    public function getAditionalHTML()
    {
        $templates = $this->getData('additional_html_templates') ?: $this->additionalHtmlTemplates;
        if (!is_array($templates)) {
            $templates = [$templates];
        }
        $output = '';
        foreach ($templates as $template) {
            $templateFile = $this->getTemplateFile($template);
            if (!is_file($templateFile)) {
                $output .= '<div class="message message-error">Error: Additional HTML template not found: ' . htmlspecialchars($template) . '</div>';
                continue;
            }
            $output .= "<!-- aditional html -->" . $this->fetchView($templateFile);
        }
        return $output;
    }
}