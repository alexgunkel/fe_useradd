<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 15.01.18
 * Time: 23:05
 */

namespace AlexGunkel\FeUseradd\Service;


use AlexGunkel\FeUseradd\Domain\Model\LoginData;
use AlexGunkel\FeUseradd\Domain\Model\User;
use AlexGunkel\FeUseradd\Domain\Repository\UserRepository;
use AlexGunkel\FeUseradd\Domain\Value\Password;
use AlexGunkel\FeUseradd\Domain\Value\RegistrationState;
use AlexGunkel\FeUseradd\Exception\FeUseraddException;
use AlexGunkel\FeUseradd\Exception\ValidationException;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;

class UserService
{
    private const LOGGER_NAME = 'fe-user-add: UserService';

    /**
     * @var \AlexGunkel\FeUseradd\Service\PasswordService
     * @inject
     */
    private $passwordService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function getLoginDataFromRequest(Request $request): LoginData
    {
        $arguments = $request->getArguments();
        if (!array_key_exists('email', $arguments) || !array_key_exists('password', $arguments)) {
            throw new \Exception("Arguments 'email' and 'password' are required.");
        }

        return new LoginData($arguments['email'], new Password($arguments['password']));
    }

    public function prepareNewUser(UserRepository $userRepository, User $feUser): Password
    {
        if ($userRepository->checkExistence($feUser)) {
            throw new FeUseraddException("User $feUser already exists");
        }

        $feUser->setRegistrationState(new RegistrationState(RegistrationState::NEW));
        $password = $this->setNewRandomPassword($feUser);
        $userRepository->add($feUser);
        $this->getLogger()->info("Added user $feUser with password $password to database.");

        return $password;
    }

    /**
     * @return User
     * @throws \Exception
     */
    public function getValidatedFeUser(UserRepository $repository, LoginData $loginData): User
    {
        $feUser = $repository->findByEmail($loginData->getEmail());
        if ($this->passwordService->validateUser($feUser, $loginData->getPassword())) {
            return $feUser;
        }

        throw new ValidationException("Password not valid");
    }

    public function setPassword(User $user, Password $password)
    {
        $saltedPw = $this->passwordService->getSaltedPassword($password);
        $user->setPassword($saltedPw);
    }

    public function setNewRandomPassword(User $user): Password
    {
        $password = $this->passwordService->generateRandomPassword();
        $saltedPw = $this->passwordService->getSaltedPassword($password, $user->getRegistrationState());
        $user->setPassword($saltedPw);
        return $password;
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
}