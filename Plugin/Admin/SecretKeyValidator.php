<?php
namespace Mage\Grid\Plugin\Admin;

use Magento\Backend\Model\Url;
use Magento\Framework\App\RequestInterface;

class SecretKeyValidator
{
    /**
     * @var RequestInterface
     */
    private $request;

    const EXEMPT_FULL_ACTION = 'adminhtml_grid_data';

    /**
     * Constructor
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * Modify useSecretKey to return false for our specific action
     */
    public function aroundUseSecretKey(
        Url $subject,
        \Closure $proceed
    ): bool {
        if (strpos($this->request->getUri()->getPath(), 'grid/data') ||
                strpos($this->request->getUri()->getPath(), 'grid/filter')) {
            return false;  // disable secret key for this action
        }

        // For all other actions, use the default behavior
        return $proceed();
    }
}
