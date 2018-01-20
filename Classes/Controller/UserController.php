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
use AlexGunkel\FeUseradd\Domain\Model\ValidationMail;
use AlexGunkel\FeUseradd\Domain\Value\Password;
use AlexGunkel\FeUseradd\Domain\Value\RegistrationState;
use AlexGunkel\FeUseradd\Exception\FeUseraddException;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class UserController extends ActionController
{
    /**
     * @var string
     */
    private const LOGGER_NAME = __CLASS__;

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
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository
     *
     * @inject
     *
     */
    private $feUserGroupRepository;

    /**
     * @var \AlexGunkel\FeUseradd\Service\UserService
     *
     * @inject
     */
    private $userService;

    /**
     * @var \AlexGunkel\FeUseradd\Service\MailService
     *
     * @inject
     */
    private $mailService;

    /**
     */
    public function addUserAction()
    {
        $this->getLogger()->debug("Called controller action " . __METHOD__);
    }

    /**
     * @param \AlexGunkel\FeUseradd\Domain\Model\User $feUser the fe user
     *
     * @return void
     */
    public function submitUserAction(\AlexGunkel\FeUseradd\Domain\Model\User $feUser)
    {
        $feUser->addFeGroup($this->getStandardFeUSerGroup());
        $this->getLogger()->debug("Add standard fe group " . $this->getStandardFeUSerGroup());
        $feUser->setRegistrationState(new RegistrationState(RegistrationState::NEW));
        $password = $this->userService->setNewRandomPassword($feUser);
        $this->userRepository->add($feUser);
        $this->getLogger()->info("Added user $feUser with password $password to database.");

        $link = $this->uriBuilder->setCreateAbsoluteUri(true)
            ->uriFor(
            'allowUser',
            [
                'email' => $feUser->getEmail(),
                'password' => $password,
            ]
        );

        $this->mailService->sendMailTo(
            new ValidationMail($link, $feUser),
            $this->settings['receiver']
        );

        $this->getLogger()->debug("Generated link: $link and send it to " . $this->settings['receiver']);
    }

    public function allowUserAction()
    {
        try {
            $feUser = $this->userService->getValidatedFeUser(
                $this->userRepository,
                $this->userService->getLoginDataFromRequest($this->request)
            );

            $feUser->setRegistrationState(new RegistrationState(RegistrationState::ALLOWED));
            $this->view->assign('feUser', clone $feUser);

            $password = $this->userService->setNewRandomPassword($feUser);
            $this->getLogger()->info("Added user $feUser with password $password to database.");

            $link = $this->uriBuilder->setCreateAbsoluteUri(true)
                ->uriFor(
                'activateUser',
                [
                    'email' => $feUser->getEmail(),
                    'password' => $password,
                ]
            );


            $this->mailService->sendMailTo(
                new ValidationMail($link, $feUser),
                $feUser->getEmail()
            );
            $this->getLogger()->debug("Generated link: $link and send it to " . $feUser->getEmail());

            $this->userRepository->update($feUser);
        } catch (FeUseraddException $exception) {
            $this->getLogger()->error("Error: " . $exception->getMessage() . ". Trace: " . $exception->getTraceAsString());
        }
    }

    public function activateUserAction()
    {
        try {
            $feUser = $this->userService->getValidatedFeUser(
                $this->userRepository,
                $this->userService->getLoginDataFromRequest($this->request)
            );
            $this->view->assign('feUser', clone $feUser);
            $this->view->assign('password', $this->request->getArgument('password'));
            $this->view->assign('email', $this->request->getArgument('email'));
        } catch (FeUseraddException $exception) {
            $this->getLogger()->error("Error: " . $exception->getMessage() . ". Trace: " . $exception->getTraceAsString());
        }
    }

    /**
     * @param PasswordInput $passwordInput
     *
     * @return void
     */
    public function setPasswordAction(PasswordInput $passwordInput)
    {
        try {
            $password = new Password((string) $passwordInput->check());
            $feUser = $this->userService->getValidatedFeUser(
                $this->userRepository,
                $passwordInput->getLoginData()
            );
            $this->userService->setPassword($feUser, $password);
            $this->getLogger()->debug("set Password for $feUser: $password");

            $this->getLogger()->debug("Assign user-group " . $this->settings['fe_user_group']);

            $this->userRepository->moveToFeUser($feUser);
        } catch (FeUseraddException $exception) {
            $this->getLogger()->error("Error: " . $exception->getMessage() . ". Trace: " . $exception->getTraceAsString());
        }
    }

    private function getStandardFeUSerGroup(): FrontendUserGroup
    {
        return $this->feUserGroupRepository->findByIdentifier($this->settings['fe_user_group']);
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger() : LoggerInterface
    {
        if ($this->logger instanceof LoggerInterface) {
            return $this->logger;
        }

        /** @var LogManager $logManager */
        $logManager = GeneralUtility::makeInstance(LogManager::class);
        return $this->logger = $logManager->getLogger(self::LOGGER_NAME);
    }
}