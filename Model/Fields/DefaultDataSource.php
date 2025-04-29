<?php
namespace Mage\Grid\Model\Fields;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\App\ResourceConnection;
use Mage\Grid\Model\Fields\DataSourceInterface;
use Magento\Framework\App\CacheInterface;

class DefaultDataSource implements DataSourceInterface
{
    /**
     * Cache lifetime in seconds (24 hours)
     */
    const CACHE_LIFETIME = 86400;

    /**
     * Cache tag
     */
    const CACHE_TAG = 'grid_field_filter_values';

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var CacheInterface
     */
    protected $cache;

    protected $fields = [];

    /**
     * @var string
     */
    protected $tableName;

    public function __construct(
        ResourceConnection $resource,
        CacheInterface $cache
    ) {
        $this->resource = $resource;
        $this->cache = $cache;
    }

    public function setFields(array|string $fields)
    {
        if (is_string($fields)) {
            $this->fields[] = $fields;
        } else {
            $this->fields = $fields;
        }
    }

    /**
     * Set the table name
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Get cache key for field values
     *
     * @param string $field
     * @return string
     */
    protected function getCacheKey($field)
    {
        return 'grid_field_values_' . $this->tableName . '_' . $field;
    }

    /**
     * Get all distinct values for a given field
     *
     * @param string $field
     * @return array
     */
    public function getValues($field)
    {
        $cacheKey = $this->getCacheKey($field);
        $cachedData = $this->cache->load($cacheKey);

        if ($cachedData !== false) {
            return json_decode($cachedData, true);
        }

        $connection = $this->resource->getConnection();
        $select = $connection->select()
            ->from($this->tableName, [$field])
            ->distinct();
        $results = $connection->fetchCol($select);

        // Cache the results
        $this->cache->save(
            json_encode($results),
            $cacheKey,
            [self::CACHE_TAG],
            self::CACHE_LIFETIME
        );

        return $results;
    }

    /**
     * Get all distinct values for multiple fields
     *
     * @param array $fields
     * @return array
     */
    public function getAllValues(array $fields)
    {
        $fields = array_merge($this->fields, $fields);
        $result = [];
        
        foreach ($fields as $field) {
            $result[$field] = $this->getValues($field);
        }
        
        return $result;
    }
}
