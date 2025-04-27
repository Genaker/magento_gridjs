<?php
namespace Mage\Grid\Model\DataProcessor;

use Mage\Grid\Api\DataProcessorInterface;

class PriceProcessor extends DefaultProcessor
{
    protected int $order = 10;

    /**
     * @inheritDoc
     */
    public function process(string $field, $value, array $row = []): string
    {
        if (!is_numeric($value)) {
            return $value;
        }

        return sprintf(
            '<span class="price" style="font-weight: bold; color: #006400;">$%s</span>',
            number_format((float)$value, 2)
        );
    }
} 