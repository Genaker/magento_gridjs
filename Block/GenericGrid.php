<?php

namespace Mage\Grid\Block;

use GuzzleHttp\Promise\Is;
use Mage\Grid\ViewModel\GenericViewModelGrid;
use Magento\Backend\Block\Template\Context as BackendContext;  // <-- use this if we need backend grid
use Magento\Backend\Block\Template;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Element\Template\Context as FrontendContext;  // <-- use this if we need framework grid

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
        $dataProcessors = $this->getData('dataProcessors') ?: [];
        foreach ($dataProcessors as $field => $processor) {
            $this->viewModel->addDataProcessor($field, $processor);
        }

        // Set up fields and field names
        $this->fields = $this->getData('fields') ?: ['id' => 'ID'];
        $this->viewModel->setFields($this->fields);
        $this->viewModel->setFieldsNames($this->fields);
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
    public function getFieldsNames()
    {
        return $this->viewModel->getFieldsNames();
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
     * Get the configured fields config
     * Must be called after getGridJsonData because it depends on the table name
     * @return array
     */
    public function getFieldsConfig()
    {
        $fieldsConfig = $this->viewModel->getFieldsConfig();
        $table = $this->viewModel->getCollection()->getMainTable();

        foreach ($fieldsConfig as $key => $value) {
            foreach ($value as $key2 => $value2) {
                if ($key2 === 'source_model' && is_object($value2)) {
                    $value2->setTableName($table);
                    $fieldsConfig[$key]['data'] = $value2->getValues($key);
                }
            }
        }
        return $fieldsConfig;
    }

    public function getFieldsHtml()
    {
        return $this->viewModel->getFieldsHtml();
    }

    /**
     * Get the processed filter fields
     * @param array $fields
     * @param array $fieldsConfig
     * @param array $filters
     * @return array
     */
    public function getProcessedFields($fields, $fieldsConfig, $filters)
    {
        foreach ($fields as $field) {
            $processedFields[$field] = [
                'config' => isset($fieldsConfig[$field]) ? $fieldsConfig[$field] : [],
                'hidden' => isset($fieldsConfig[$field]['hidden'])
                    ? $fieldsConfig[$field]['hidden']
                    : false,
                'label' => isset($fieldsConfig[$field]['label'])
                    ? $fieldsConfig[$field]['label']
                    : ucwords(str_replace('_', ' ', $field)),
                'element' => isset($fieldsConfig[$field]['element'])
                    ? $fieldsConfig[$field]['element']
                    : 'text',
                'data' => isset($fieldsConfig[$field]['data'])
                    ? $fieldsConfig[$field]['data']
                    : [],
                'filter_value' => isset($filters[$field])
                    ? (is_array($filters[$field]) ? $filters[$field] : (string) $filters[$field])
                    : (isset($fieldsConfig[$field]['element']) &&
                        in_array($fieldsConfig[$field]['element'], ['select', 'multiselect']) ? [] : '')
            ];
        }
        return $processedFields;
    }

    /**
     * Lazy load the collection class
     */
    function lazyLoadCollectionClass()
    {
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
    }

    /**
     * Get the grid data as JSON (for Grid.js)
     * @return string JSON-encoded data
     */
    public function getGridJsonData()
    {
        $this->lazyLoadCollectionClass();

        return $this->viewModel->getJsonGridData(array_keys($this->getFieldsNames()));
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
            $output .= '<!-- aditional html -->' . $this->fetchView($templateFile);
        }
        return $output;
    }

    /**
     * Get filter value for a specific field
     *
     * @param string $field
     * @param array $filters
     * @return string|array
     */
    protected function getFilterValue($field, $filters)
    {
        $fieldsConfig = $this->getFieldsConfig();

        if (isset($fieldsConfig[$field]['element']) &&
                in_array($fieldsConfig[$field]['element'], ['select', 'multiselect'])) {
            // For select/multiselect, keep as array
            $filterValue = isset($filters[$field]) ? $filters[$field] : [];
            if (!is_array($filterValue)) {
                $filterValue = $filterValue ? [$filterValue] : [];
            }
        } else {
            // For text inputs, ensure string
            $filterValue = isset($filters[$field]) ? (string) $filters[$field] : '';
        }

        return $filterValue;
    }

    /**
     * Get current filter values
     *
     * @return array
     */
    public function getCurrentFilters()
    {
        return $this->getRequest()->getParam('filter', []);
    }

    /**
     * Get rendered filters HTML
     *
     * @return string
     */
    public function getFiltersHtml($filterData): string
    {
        return $this
            ->getLayout()
            ->createBlock(\Mage\Grid\Block\Grid\Filters::class)
            ->setFilterData($filterData)
            ->toHtml();
    }
}
