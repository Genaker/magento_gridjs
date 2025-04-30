<?php
namespace Mage\Grid\Model\DataProcessor;

use Mage\Grid\Api\DataProcessorInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class DefaultProcessor implements DataProcessorInterface
{
    protected int $order = 0;

    public function __construct(?int $order = 10)
    {
        $this->order = $order ?? $this->order;
    }

    /**
     * @inheritDoc
     */
    public function process(string $field, $value, array $row = []): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }

    public function getOrder(): int
    {
        return $this->order;
    }
} 