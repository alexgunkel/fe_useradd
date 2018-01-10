<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 10.01.18
 * Time: 21:40
 */

namespace AlexGunkel\FeUseradd\Controller;

use AlexGunkel\FeUseradd\Domain\Model\User;
use AlexGunkel\FeUseradd\Domain\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class UserController extends ActionController
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \AlexGunkel\FeUseradd\Domain\Repository\UserRepository
     *
     * @inject
     */
    private $userRepository;

    /**
     * @param User $feUser
     */
    public function addUserAction(User $feUser = null)
    {
        $this->getLogger()->debug("Called controller action " . __METHOD__);
    }

    public function submitUserAction(User $feUser)
    {
        $this->getLogger()->debug("Called controller action " . __METHOD__);

        $password = "very_secure";
        $feUser->setPassword($password);

        $this->userRepository->add($feUser);
        $this->getLogger()->info("Added user $feUser with password $password to database.");
    }

    /**
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    private function getLogger() : LoggerInterface
    {
        if ($this->logger instanceof LoggerInterface) {
            return $this->logger;
        }

        /** @var LogManager $logManager */
        $logManager = GeneralUtility::makeInstance(LogManager::class);
        return $this->logger = $logManager->getLogger(__CLASS__);
    }
}