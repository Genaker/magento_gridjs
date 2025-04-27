<?php
namespace Mage\Grid\Model\DataProcessor;

use Mage\Grid\Api\DataProcessorInterface;

class ObfuscateProcessor extends DefaultProcessor
{
    public function process(string $field, $value, array $row = []): string
    {
        if (empty($value)) {
            return '';
        }
        return str_repeat('*', strlen($value));
    }
} 