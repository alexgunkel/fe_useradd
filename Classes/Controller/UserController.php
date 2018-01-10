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
use TYPO3\CMS\Saltedpasswords\Salt\SaltFactory;

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
        $feUser->setPassword($saltedPw = $this->getSaltedPassword($password));

        $this->userRepository->add($feUser);
        $this->getLogger()->info("Added user $feUser with password $password ($saltedPw) to database.");
    }

    public function allowUser()
    {
        $arguments = $this->request->getArguments();
        if (!array_key_exists('email', $arguments) || !array_key_exists('saltedPassword', $arguments)) {
            throw new \Exception("Arguments 'email' and 'saltedPassword' are required.");
        }

        $feUser = $this->userRepository->findByEmail($arguments['email']);
        $this->view->assign('feUser', $feUser);
    }

    /**
     * @param string $password
     * @return string
     */
    private function getSaltedPassword(string $password) : string
    {
        $salting = SaltFactory::getSaltingInstance(null, 'FE');
        return $salting->getHashedPassword($password);
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