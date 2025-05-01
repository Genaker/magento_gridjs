<?php
declare(strict_types=1);

namespace Mage\Grid\Plugin;

use Magento\Framework\Webapi\Authorization;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Framework\Webapi\Request;

class WebapiAuthorizationPlugin
{
    /**
     * @var AdminSession
     */
    private $adminSession;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param AdminSession $adminSession
     * @param Request $request
     */
    public function __construct(
        AdminSession $adminSession,
        Request $request
    ) {
        $this->adminSession = $adminSession;
        $this->request = $request;
    }

    /**
     * Allow access if user has admin session only for grid API
     *
     * @param Authorization $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsAllowed(Authorization $subject, bool $result): bool
    {
        // If already authorized, return true
        if ($result) {
            return true;
        }

        // Check if this is our grid API route
        $path = $this->request->getPathInfo();
        if (strpos($path, '/V1/grid/') !== 0) {
            return $result;
        }

        // For grid API, check if user has admin session
        return $this->adminSession->isLoggedIn();
    }
} 