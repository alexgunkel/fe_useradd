<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 10.01.18
 * Time: 21:40
 */

namespace AlexGunkel\FeUseradd\Controller;

use AlexGunkel\FeUseradd\Domain\Model\PasswordInput;
use AlexGunkel\FeUseradd\Domain\Model\ValidationMail;
use AlexGunkel\FeUseradd\Domain\Value\Gender;
use AlexGunkel\FeUseradd\Domain\Value\Password;
use AlexGunkel\FeUseradd\Domain\Value\RegistrationState;
use AlexGunkel\FeUseradd\Domain\Value\Title;
use AlexGunkel\FeUseradd\Exception\FeUseraddException;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;

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
     * This action only displays an empty form. Therefore we don't have
     * to do anything here
     *
     * @param \AlexGunkel\FeUseradd\Domain\Model\User $feUser
     * @param string                                  $errorMessage
     *
     * @return void
     */
    public function addUserAction(\AlexGunkel\FeUseradd\Domain\Model\User $feUser = null, string $errorMessage = null)
    {
        $this->view->assignMultiple(
            [
                'genderOptions' => Gender::getOptions(),
                'titleOptions'  => Title::getOptions(),
                'feUser'        => $feUser,
                'errorMessage'  => $errorMessage
            ]
        );
    }

    public function existingUserErrorAction()
    {

    }

    /**
     * Basic action to submit values for a new user. The user will be created and
     * stored to the database after we checked the uniqueness of the e-mail-address.
     *
     * @todo Check the existence of the e-mail-address in the fe_users-table
     *
     * @param \AlexGunkel\FeUseradd\Domain\Model\User $feUser the fe user
     *
     * @return void
     */
    public function submitUserAction(\AlexGunkel\FeUseradd\Domain\Model\User $feUser)
    {
        $feUser->addFeGroup($this->getStandardFeUSerGroup());
        $this->getLogger()->debug("Add standard fe group " . $this->getStandardFeUSerGroup());

        try {
            $password = $this->userService->prepareNewUser($this->userRepository, $feUser);
        } catch (FeUseraddException $exception) {
            $this->getLogger()->error("Error while adding user: " . $exception->getMessage());
            $feUser->setEmail('');
            $this->forward(
                'addUser',
                null,
                null,
                [
                    'errorMessage' => $exception->getMessage(),
                    'feUser' => $feUser,
                ]
                );
        }

        $link = $this->uriBuilder->setCreateAbsoluteUri(true)
            ->uriFor(
            'allowUser',
            [
                'email' => $feUser->getEmail(),
                'password' => $password,
            ]
        );

        $this->mailService->sendMailTo(
            new ValidationMail($link, $feUser, ValidationMail::STATUS_VALIDATE),
            $this->getAdminEmail()
        );

        $this->getLogger()->debug("Generated link: $link and send it to " . $this->settings['receiver']);
    }

    /**
     * This action is for the first validation step. It is called when
     * the web-site-admin follows the link in the e-mail he should have
     * received from submitUserAction.
     *
     * @return void
     */
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
                new ValidationMail($link, $feUser, ValidationMail::STATUS_INFORM),
                $feUser->getEmail()
            );
            $this->getLogger()->debug("Generated link: $link and send it to " . $feUser->getEmail());

            $this->userRepository->update($feUser);
        } catch (FeUseraddException $exception) {
            $this->getLogger()->error("Error: " . $exception->getMessage() . ". Trace: " . $exception->getTraceAsString());
        }
    }

    /**
     * Here we check whether the user has a valid link and display a form to
     * set a new password.
     *
     * @return void
     */
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
     * After filling the password form we check the password,
     * generate its hash and move the user to the fe_users-table.
     *
     * @param PasswordInput $passwordInput
     *
     * @validate $passwordInput \AlexGunkel\FeUseradd\Validator\PasswordValidator
     *
     * @return void
     */
    public function setPasswordAction(PasswordInput $passwordInput)
    {
        try {
            $password = new Password((string) $passwordInput);
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

    /**
     * Find the configured fe_groups-entry for this plugin
     *
     * @return FrontendUserGroup
     */
    private function getStandardFeUSerGroup(): FrontendUserGroup
    {
        if (isset($this->settings['fe_user_group'])) {
            return $this->feUserGroupRepository->findByIdentifier($this->settings['fe_user_group']);
        }

        /** @var ConfigurationUtility $backendConfiguration */
        $backendConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            ConfigurationUtility::class);
        $config = $backendConfiguration->getCurrentConfiguration('fe_useradd');

        return $this->feUserGroupRepository->findByIdentifier((int) $config['defaultUserGroup']['value']);
    }

    /**
     * Find the logger, create one if it can't be found
     *
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

    /**
     * Get the configured admin email address
     *
     * @return string
     */
    private function getAdminEmail()
    {
        if (isset($this->settings['receiver']) && !empty($this->settings['receiver'])) {
            return $this->settings['receiver'];
        }

        /** @var ConfigurationUtility $backendConfiguration */
        $backendConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            ConfigurationUtility::class);
        $config = $backendConfiguration->getCurrentConfiguration('fe_useradd');

        return $config['defaultAdminEmail']['value'];
    }
}
