<?php
namespace Mage\Grid\Model\DataProcessor;

use Mage\Grid\Api\DataProcessorInterface;
use Magento\Backend\Model\UrlInterface;


class IdProcessor extends DefaultProcessor
{
    protected int $order = 10;

    public function __construct(
        protected UrlInterface $urlBuilder
    ) {
        parent::__construct($this->order);
    }

    /**
     * @inheritDoc
     */
    public function process(string $field, $value, array $row = []): string
    {
        if (!is_numeric($value)) {
            return $value;
        }

        // Get the order URL using the increment ID
        $orderUrl = $this->urlBuilder->getUrl(
            'sales/order/view',
            ['order_id' => $value]
        );

        return sprintf(
            '<span class="id" style="font-weight: bold; color:rgb(0, 35, 110);"> <a href="%s">%s</a></span>',
            $orderUrl,
            $value
        );
    }
} 