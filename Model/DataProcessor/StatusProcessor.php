<?php

namespace Mage\Grid\Model\DataProcessor;

use Mage\Grid\Api\DataProcessorInterface;

class StatusProcessor extends DefaultProcessor
{
    protected int $order = 10;

    /**
     * @inheritDoc
     */
    public function process(string $field, $value, array $row = []): string
    {
        $status = strtolower($value);
        $color = 'gray';
        $icon = '';

        switch ($status) {
            case 'complete':
                $color = 'green';
                $icon = '✓';
                break;
            case 'processing':
                $color = 'blue';
                $icon = '⟳';
                break;
            case 'pending':
                $color = 'orange';
                $icon = '⏳';
                break;
            case 'canceled':
                $color = 'red';
                $icon = '✕';
                break;
        }

        $rowJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
        return sprintf(
            '<span class="status-badge" style="color: %s; background: %s20; padding: 4px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid %s; cursor:pointer;" data-row="%s">
            <span class="status-icon">%s</span>
            <span class="status-text">%s</span>
        </span>',
            $color,
            $color,
            $color,
            $rowJson,
            $icon,
            htmlspecialchars($value, ENT_QUOTES, 'UTF-8')
        );
    }
}
