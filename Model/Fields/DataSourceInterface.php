<?php
namespace Mage\Grid\Model\Fields;

interface DataSourceInterface
{
    /**
     * Set the fields to retrieve values for
     * @param array|string $fields
     * @return void
     */
    public function setFields(array|string $fields);

    /**
     * Set the table name
     * @param string $tableName
     * @return void
     */
    public function setTableName($tableName);

    /**
     * Get all distinct values for a given field
     * @param string $field
     * @return array
     */
    public function getValues($field);

    /**
     * Get all distinct values for multiple fields
     * @param array $fields
     * @return array
     */
    public function getAllValues(array $fields);
} 