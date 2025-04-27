<?php
namespace Mage\Grid\Model\DataProcessor;

use Mage\Grid\Api\DataProcessorInterface;

class ChainProcessor implements DataProcessorInterface
{
    /**
     * @var DataProcessorInterface[]
     */
    protected $processors = [];

    public function __construct(array $processors = [])
    {
        foreach ($processors as $processor) {
            $this->addProcessor($processor);
        }
    }

    /**
     * Add a processor to the chain
     *
     * @param DataProcessorInterface $processor
     * @return $this
     */
    public function addProcessor(DataProcessorInterface $processor)
    {
        $this->processors[] = $processor;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function process(string $field, $value, array $row = []): string
    {
        if (empty($this->processors)) {
            return $value;
        }
        usort($this->processors, function ($a, $b) {
            return $a->getOrder() <=> $b->getOrder();
        });

        $result = $value;
        foreach ($this->processors as $processor) {
            $result = $processor->process($field, $result, $row);
        }
        return $result;
    }

    public function getOrder(): int
    {
        return 0;
    }
} 