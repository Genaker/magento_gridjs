<?php
namespace Mage\Grid\Api;

use Magento\Framework\View\Element\Block\ArgumentInterface;

interface DataProcessorInterface extends ArgumentInterface
{
    /**
     * Process the data for a specific field
     *
     * @param string $field
     * @param mixed $value
     * @param array $row
     * @return string
     */
    public function process(string $field, $value, array $row = []): string;

    /**
     * Return the execution order (lower runs first)
     */
    public function getOrder(): int;
} 