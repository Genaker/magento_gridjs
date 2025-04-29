<?php

namespace Mage\Grid\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Psr\Log\LoggerInterface;
use Mage\Grid\Api\DataProcessorInterface;
use Mage\Grid\Model\DataProcessor\DefaultProcessor;
use Mage\Grid\Model\DataProcessor\ChainProcessor;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\CacheInterface;

/**
 * GenericViewModelGrid
 *
 * This ViewModel provides the data and logic for Mage Grid blocks. It is designed for maximum flexibility and AI-driven extensibility.
 *
 * Key features:
 * - Supports both collection-based and SQL-based data sources
 * - Handles filtering, sorting, and pagination
 * - Integrates with a modular data processor system for custom field formatting
 * - Provides JSON data for Grid.js and other frontend grids
 * - Designed for easy extension and configuration via DI and layout XML
 *
 * AI-first notes:
 * - All methods and properties are documented for discoverability
 * - Data processors and fields are dynamically configurable
 * - Error handling and logging are included for robust development
 */
class GenericViewModelGrid implements ArgumentInterface
{
    /**
     * Cache lifetime in seconds (1 hour for total count)
     */
    const CACHE_LIFETIME = 3600;

    /**
     * Cache tag for total count
     */
    const CACHE_TAG = 'grid_total_count';

    /**
     * @var AbstractCollection|null The loaded collection instance
     */
    protected $collection;

    /**
     * @var RequestInterface The current request object
     */
    protected $request;

    /**
     * @var LoggerInterface Logger for error reporting
     */
    protected $logger;

    /**
     * @var ObjectManagerInterface For dynamic class instantiation
     */
    protected $objectManager;

    /**
     * @var array Field => DataProcessorInterface mapping
     */
    protected $dataProcessors = [];

    /**
     * @var DefaultProcessor The default processor for fields
     */
    protected $defaultProcessor;

    /**
     * @var ChainProcessor The chain processor for layered processing
     */
    protected $chainProcessor;

    /**
     * @var string|null The SQL table name (if using SQL mode)
     */
    public $tableName;

    /**
     * @var string|null The raw SQL query (if using SQL mode)
     */
    public $sqlQuery = null;

    /**
     * @var string|null The SQL query for total count (if using SQL mode)
     */
    public $totalSqlQuery;

    /**
     * @var array List of field keys for the grid
     */
    public $fields = [];


    /**
     * @var array List of field config for the grid
     * 
     * example:  [
     *  "customer_group_id" => [
     *      "label" => "Customer Group ID",
     *      "element" => "select",
     *      "source_model" => "Mage\Grid\Model\Fields\DefaultDataSource"
     *  ]
     * ]
     */
    public $fieldsConfig = [];

    /**
     * @var array List of field labels for the grid
     */
    public $fieldsNames = [];

    /**
     * @var array Current filter values
     */
    public $filters = [];

    /**
     * @var string|object|null The collection class (if using collection mode)
     */
    public string|object|null $collectionClass = null;

    /**
     * @var int The maximum number of rows to return (for SQL mode)
     */
    public $LIMIT = 1000;

    /**
     * @var int The default page size for pagination
     */
    public $DEFAULT_LIMIT = 40;

    /**
     * @var string|null The raw SQL (if set directly)
     */
    protected $sql;

    /**
     * @var LayoutInterface For rendering templates (if needed)
     */
    protected $layout;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var bool Whether to use AJAX for data loading
     */
    public $isAjax = true;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * Constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     * @param LayoutInterface $layout
     * @param DefaultProcessor $defaultProcessor
     * @param ChainProcessor $chainProcessor
     * @param ResourceConnection $resource
     * @param CacheInterface $cache
     * @param array $dataProcessors
     *
     * All dependencies are injected for maximum flexibility and testability.
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        RequestInterface $request,
        LoggerInterface $logger,
        LayoutInterface $layout,
        DefaultProcessor $defaultProcessor,
        ChainProcessor $chainProcessor,
        ResourceConnection $resource,
        CacheInterface $cache,
        array $dataProcessors = []
    ) {
        $this->objectManager = $objectManager;
        $this->request = $request;
        $this->logger = $logger;
        $this->layout = $layout;
        $this->defaultProcessor = $defaultProcessor;
        $this->chainProcessor = $chainProcessor;
        $this->resource = $resource;
        $this->cache = $cache;
        $this->dataProcessors = $dataProcessors;
    }

    /**
     * Set the collection class (for collection-based grids)
     * @param string|object $collectionClass
     * @return $this
     */
    public function setCollectionClass($collectionClass)
    {
        $this->collectionClass = $collectionClass;
        return $this;
    }

    /**
     * Build and return the SQL query for SQL-based grids
     * @param array $fields
     * @param array $filters
     * @return string|false
     */
    public function getSqlQuery($fields, $filters)
    {
        if ($this->getTableName() && $this->sqlQuery === null && $this->collectionClass === null) {
            $sql = "SELECT " . implode(', ', $fields) . " FROM " . $this->getTableName() . " WHERE 1=1 ";
            foreach ($filters as $field => $value) {
                $sql .= " AND " . $field . " LIKE :" . $field;
            }
            $this->sqlQuery = $sql;
            return $this->sqlQuery;
        } else {
            return false;
        }
    }

    /**
     * Get the data for the grid (from collection or SQL)
     * Handles filtering, pagination, and sorting.
     *
     * @param array $fields
     * @param array $filters
     * @return array
     */
    public function getData(array $fields = [], array $filters = [])
    {
        try {
            // If using SQL mode, execute the SQL query
            if ($this->getSqlQuery($fields, $filters)) {
                return $this->executeSqlQuery();
            }

            /* @var $collection AbstractCollection */
            $collection = $this->getCollection();

            $fields = array_merge($fields, $this->getFields());
            $this->setFields($fields);

            // Select fields dynamically
            $collection->addFieldToSelect($fields);

            // Handle filters
            $filters = $this->getFilters();
            //dd($filters);

            foreach ($filters as $field => $value) {
                //dd($field, $value);
                if (in_array($field, $fields)) {
                    if (is_array($value)) {
                        $collection->addFieldToFilter($field, ['in' => $value]);
                    } else {
                        $collection->addFieldToFilter($field, ['like' => $value . '%']);
                    }
                }
            }

            // Handle pagination
            $offset = (int)$this->request->getParam('offset', 0);
            $limit = (int)$this->request->getParam('limit', $this->DEFAULT_LIMIT);
            $page = $offset / $limit + 1;
            $collection->setPageSize($limit);
            $collection->setCurPage($page);

            // Handle sorting
            $sortField = $this->request->getParam('sortField', null);
            $sortOrder = $this->request->getParam('sortOrder', null);
            if ($sortField && $sortOrder) {
                $collection->setOrder($sortField, $sortOrder);
            }

            // Log the SQL query (uncomment for debugging)
            //echo 'SQL Query: ' . $collection->getSelect()->__toString();

            return $collection->getData();
        } catch (\Exception $e) {
            $this->logger->error('Error fetching data: ' . $e->getMessage());
            return [
                'error' => true,
                'message' => __('Error fetching data: %1', $e->getMessage())
            ];
        }
    }

    /**
     * Get the grid data as JSON (for AJAX/Grid.js)
     *
     * @param array $fields
     * @param array $filters
     * @return string JSON-encoded data
     */
    public function getJsonGridData(array $fields = [], array $filters = [], bool $return = false)
    {
        $result = [];
        // If AJAX request, output JSON and exit
        if (strtolower($this->request->getParam('data', 'false')) === 'true') {
            //dd($fields, $filters);
            try {
                $result = $this->getGridData($fields, $filters);

                header('Content-Type: application/json');
                echo json_encode($result);
                exit;
            } catch (\Throwable $e) {
                header('Content-Type: application/json');
                echo json_encode(['error' => true, 'message' =>  $e->getMessage()]);
                exit;
            }
        }
        if ($return) {
            $result = $this->getGridData($fields, $filters);
        }

        return json_encode($result);
    }

    /**
     * Get the grid data as an array (with processors applied)
     *
     * @param array $fields
     * @param array $filters
     * @return array
     */
    public function getGridData(array $fields = [], array $filters = [])
    {
        $data = $this->getData($fields);
        if (isset($data['error'])) {
            return json_encode(['error' => true, 'message' =>  $data['message']]);
        }
        // $item is the row data
        $jsonData = array_map(function ($item) use ($fields) {
            return array_map(function ($field) use ($item) {
                $value = $item[$field] ?? '';
                $processor = $this->getDataProcessor($field);
                if ($processor) {
                    $value = $processor->process($field, $value, $item);
                }
                // Always run the chain processor last
                $value = $this->chainProcessor->process($field, $value, $item);
                return $value;
            }, $fields);
        }, $data);

        $totalCount = $this->getTotalCount();
        $result = ['data' => $jsonData, 'total' => $totalCount];
        return $result;
    }

    /**
     * Set the fields for the grid coming from the block .xml configuration
     * @param array $fields
     * @return $this example [
     *  "order_id" => "order_id",
     *  "customer_group_id" => "customer_group_id",
     *  "customer_email" => "customer_email",
     *  "created_at" => "created_at",
     *  "grand_total" => "grand_total",
     *  "status" => "status",
     *  "payment_method" => "payment_method"
     * ]
     */
    function setFields(array $fields)
    {
        foreach ($fields as $key => $field) {
            //dump($field);
            if (is_string($field)) {
                $this->fields[$key] = $key;
            } else if (is_array($field)) {
                foreach ($field as $key2 => $value) {
                    if ($key2 === 'source_model') {
                        $this->fields[$key] = $key;
                        try {
                            $this->fieldsConfig[$key][$key2] = $this->objectManager->create($value);
                        } catch (\Exception $e) {
                            $this->fieldsConfig[$key][$key2] = $e->getMessage();
                            $this->logger->error('Error creating data source model: ' . $e->getMessage());
                        }
                    } else {
                        $this->fields[$key] = $key;
                        $this->fieldsConfig[$key][$key2] = $value;
                    }
                }
            }
        }
        //dd($this->fieldsConfig);
        return $this;
    }

    /**
     * Set the raw SQL query (for SQL mode)
     * @param string|null $sql
     * @return $this
     */
    function setSqlQuery($sql = null)
    {
        $this->sqlQuery = $sql;
        return $this;
    }

    /**
     * Execute the raw SQL query (for SQL mode)
     * @return array|false
     */
    function executeSqlQuery()
    {
        if (!$this->sqlQuery) {
            return false;
        }
        $connection = $this->getCollection()->getConnection();
        return $connection->fetchAll($this->sqlQuery);
    }

    /**
     * Set the table name (for SQL mode)
     * @param string|null $tableName
     * @return $this
     */
    function setTableName(null|string $tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * Get the table name (for SQL mode)
     * @return string|null
     */
    function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Get the configured fields
     * @return array
     */
    function getFields()
    {
        return $this->fields;
    }

    function getFieldsConfig()
    {
        return $this->fieldsConfig;
    }

    /**
     * Set the field labels (for display)
     * @param array $fields
     * @return $this
     */
    function setFieldsNames(array $fields)
    {
        foreach ($fields as $key => $field) {
            if (is_string($field)) {
                $this->fieldsNames[$key] = $field;
            } else if (is_array($field)) {
                $this->fieldsNames[$key] = $field['label'];
            }
        }
        return $this;
    }

    /**
     * Set the filters for the grid
     * @param array $filters
     * @return $this
     */
    function setFilters(array $filters)
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * Get the current filters (merged from request and internal state)
     * @return array
     */
    function getFilters()
    {
        // Handle filters from request and internal state
        $filters = $this->request->getParam('filter', []);
        $filters = array_merge($filters, $this->filters);
        $this->setFilters($filters);
        return $this->filters;
    }

    /**
     * Get the collection instance (collection mode)
     * @return AbstractCollection|null
     */
    public function getCollection()
    {
        if (!$this->collection) {
            try {
                if ($this->collectionClass !== null) {
                    if (is_string($this->collectionClass)) {
                        $this->collection = $this->objectManager->create($this->collectionClass);
                    } else if (is_object($this->collectionClass)) {
                        $this->collection = $this->collectionClass;
                    } else {
                        throw new \Exception('Grid Collection class issue: collectionClass parameter is not a string or object');
                    }
                } else {
                    throw new \Exception('Grid Collection class not set: collectionClass parameter is required');
                }
                $this->setTableName($this->collection->getMainTable());
            } catch (\Exception $e) {
                $this->logger->error('Error creating collection: ' . $e->getMessage());
            }
        }
        return $this->collection;
    }

    /**
     * Get cache key for total count
     *
     * @param string $tableName
     * @param array $filters
     * @return string
     */
    protected function getTotalCountCacheKey($tableName, $filters = [])
    {
        $filterKey = empty($filters) ? 'no_filters' : md5(json_encode($filters));
        return 'grid_total_count_' . $tableName . '_' . $filterKey;
    }

    /**
     * Get the total count of records (for pagination)
     *
     * @param string|null $tableName
     * @return int
     */
    public function getTotalCount($tableName = null)
    {
        if ($this->sqlQuery) {
            return $this->executeSqlQuery();
        }

        // Get the database connection
        $connection = $this->resource->getConnection();
        $tableName = $tableName ?: $this->tableName;

        // Build the query
        $select = $connection->select()->from($tableName, 'COUNT(*)');

        // Apply filters if they exist
        if (!empty($this->filters)) {
            foreach ($this->filters as $field => $value) {
                if (is_array($value)) {
                    $select->where($field . ' IN (?)', $value);
                } else {
                    $select->where($field . ' = ?', $value);
                }
            }
        }

        // Check cache first
        $cacheKey = $this->getTotalCountCacheKey($tableName, $this->filters);
        $cachedCount = $this->cache->load($cacheKey);

        if ($cachedCount !== false) {
            $count = (int)$cachedCount;
            // If cached count is > 100, use it
            if ($count > 100) {
                return $count;
            }
        }

        // Execute the query
        $count = (int)$connection->fetchOne($select);

        // Cache only if count is greater than 100 to improve performance
        if ($count > 100) {
            $this->cache->save(
                (string)$count,
                $cacheKey,
                [self::CACHE_TAG],
                self::CACHE_LIFETIME
            );
        }

        return $count;
    }

    /**
     * Get a request parameter (helper)
     * @param string $param
     * @param string $default
     * @return string
     */
    public function getRequestParam($param, $default = '')
    {
        return $this->request->getParam($param, $default);
    }

    /**
     * Render the grid as HTML (using the main template)
     * @param string $blockName
     * @return string
     */
    public function getGridHtml()
    {
        $fields = $this->getFields();
        $jsonGridData = $this->getJsonGridData($fields);

        if (isset($jsonGridData['error']) && $jsonGridData['error']) {
            return '<div class="message message-error">' . $jsonGridData['message'] . '</div>';
        }

        $gridHtml = $this->layout->createBlock('Magento\Framework\View\Element\Template')
            ->setTemplate('Mage_Grid::grid/grid-component.phtml')
            ->setData('jsonGridData', $jsonGridData)
            ->setData('fields', $fields)
            ->toHtml();

        return $gridHtml;
    }

    /**
     * Add a data processor for a specific field (for custom formatting)
     * @param string $field
     * @param DataProcessorInterface $processor
     * @return $this
     */
    public function addDataProcessor(string $field, DataProcessorInterface $processor)
    {
        $this->dataProcessors[$field] = $processor;
        return $this;
    }

    /**
     * Get the data processor for a specific field (if any)
     * @param string $field
     * @return DataProcessorInterface|null
     */
    protected function getDataProcessor(string $field): DataProcessorInterface | null
    {
        // Get the processor for the field, or default
        $processor = $this->dataProcessors[$field] ?? null;

        return $processor;
    }
}
