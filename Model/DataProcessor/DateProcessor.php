<?php
namespace Mage\Grid\Model\DataProcessor;

use Mage\Grid\Api\DataProcessorInterface;

class DateProcessor extends DefaultProcessor
{
    protected int $order = 10;

    public function __construct(?int $order = null)
    {
        $this->order = $order ?? 10;
    }
    /**
     * @inheritDoc
     */
    public function process(string $field, $value, array $row = []): string
    {
        if (empty($value)) {
            return '';
        }

        try {
            $date = new \DateTime($value);
            return $date->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return $value;
        }
    }
} 