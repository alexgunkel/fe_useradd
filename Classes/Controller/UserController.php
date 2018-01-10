<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 10.01.18
 * Time: 21:40
 */

namespace AlexGunkel\FeUseradd\Controller;

use AlexGunkel\FeUseradd\Domain\Model\User;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class UserController extends ActionController
{
    /**
     * @param User $feUser
     */
    public function addUserAction(User $feUser)
    {
        $this->getLogger()->error("Called controller action " . __METHOD__);
    }

    /**
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    private function getLogger() : LoggerInterface
    {
        /** @var LogManager $logManager */
        $logManager = GeneralUtility::makeInstance(LogManager::class);
        return $logManager->getLogger(__CLASS__);
    }
}