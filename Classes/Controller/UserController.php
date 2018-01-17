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
use AlexGunkel\FeUseradd\Domain\Value\RegistrationState;
use AlexGunkel\FeUseradd\Exception\FeUseraddException;
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
     * @var \AlexGunkel\FeUseradd\Service\PasswordService
     *
     * @inject
     */
    private $passwordService;

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
     * @param User $feUser
     */
    public function addUserAction(User $feUser = null)
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
        $feUser->setRegistrationState(new RegistrationState(RegistrationState::NEW));

        $password = $this->passwordService->generateRandomPassword();
        $saltedPw = $this->passwordService->getSaltedPassword($password, $feUser->getRegistrationState());
        $feUser->setPassword($saltedPw);

        $this->userRepository->add($feUser);
        $this->getLogger()->info("Added user $feUser with password $password ($saltedPw) to database.");

        $link = $this->uriBuilder->setCreateAbsoluteUri(true)
            ->uriFor(
            'allowUser',
            [
                'email' => $feUser->getEmail(),
                'password' => $password,
            ]
        );

        $sendTo = $this->settings['receiver'];
        $mail = new ValidationMail($link, $feUser);
        $this->mailService->sendMailTo($mail, $sendTo);

        $this->getLogger()->debug("Generated link: $link and send it to $sendTo");
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
            $password = $this->passwordService->generateRandomPassword();
            $saltedPw = $this->passwordService->getSaltedPassword(
                $password,
                $feUser->getRegistrationState()
            );
            $feUser->setPassword($saltedPw);

            $this->getLogger()->info("Added user $feUser with password $password ($saltedPw) to database.");

            $link = $this->uriBuilder->setCreateAbsoluteUri(true)
                ->uriFor(
                'activateUser',
                [
                    'email' => $feUser->getEmail(),
                    'password' => $password,
                ]
            );

            $this->getLogger()->debug("Generated link: $link and send it to " . $feUser->getEmail());
            $mail = new ValidationMail($link, $feUser);
            $this->mailService->sendMailTo($mail, $feUser->getEmail());


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
            $passwordInput->check();
            $feUser = $this->userService->getValidatedFeUser(
                $this->userRepository,
                $passwordInput->getLoginData()
            );
            $saltedPw = $this->passwordService->getSaltedPassword($passwordInput);
            $feUser->setPassword($saltedPw);
            $this->getLogger()->debug("set Password for $feUser: $passwordInput ($saltedPw)");

            $this->userRepository->moveToFeUser($feUser);
        } catch (FeUseraddException $exception) {
            $this->getLogger()->error("Error: " . $exception->getMessage() . ". Trace: " . $exception->getTraceAsString());
        }
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
        return $this->logger = $logManager->getLogger(__CLASS__);
    }
}