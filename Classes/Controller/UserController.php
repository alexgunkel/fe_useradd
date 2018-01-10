<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 10.01.18
 * Time: 21:40
 */

namespace AlexGunkel\FeUseradd\Controller;

use AlexGunkel\FeUseradd\Domain\Model\PasswordInput;
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

        $password = $this->generateRandomPassword();
        $feUser->setPassword($saltedPw = $this->getSaltedPassword($password));

        $this->userRepository->add($feUser);
        $this->getLogger()->info("Added user $feUser with password $password ($saltedPw) to database.");

        $link = $this->uriBuilder->uriFor(
            'allowUser',
            [
                'email' => $feUser->getEmail(),
                'password' => $password,
            ]
        );

        $this->getLogger()->debug("Generated link: $link");
    }

    public function allowUserAction()
    {
        $feUser = $this->getFeUserFromRequest();
        $this->view->assign('feUser', clone $feUser);
        $password = $this->generateRandomPassword();
        $feUser->setPassword($saltedPw = $this->getSaltedPassword($password));

        $this->userRepository->update($feUser);

        $link = $this->uriBuilder->uriFor(
            'activateUser',
            [
                'email' => $feUser->getEmail(),
                'password' => $password,
            ]
        );

        $this->getLogger()->debug("Generated link: $link");
    }

    public function activateUserAction()
    {
        $feUser = $this->getFeUserFromRequest();
        $this->view->assign('feUser', clone $feUser);
        $this->view->assign('password', $this->request->getArgument('password'));
        $this->view->assign('email', $this->request->getArgument('email'));
    }

    /**
     * @param PasswordInput $passwordInput
     * @param string $email
     */
    public function setPasswordAction(PasswordInput $passwordInput)
    {
        $passwordInput->check();
        $feUser = $this->userRepository->findByEmail($passwordInput->getEmail());
        $this->getLogger()->debug("set Password for $feUser: $passwordInput");
        $feUser->setPassword($passwordInput);
    }

    private function generateRandomPassword() : string
    {
        return 'very_secure';
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

    /**
     * @return User
     * @throws \Exception
     */
    private function getFeUserFromRequest(): User
    {
        $arguments = $this->request->getArguments();
        if (!array_key_exists('email', $arguments) || !array_key_exists('password', $arguments)) {
            throw new \Exception("Arguments 'email' and 'password' are required.");
        }

        $feUser = $this->userRepository->findByEmail($arguments['email']);
        return $feUser;
    }
}