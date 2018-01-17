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
use AlexGunkel\FeUseradd\Exception\ValidationException;
use TYPO3\CMS\Extbase\Mvc\Request;

class UserService
{
    /**
     * @var \AlexGunkel\FeUseradd\Service\PasswordService
     * @inject
     */
    private $passwordService;

    public function getLoginDataFromRequest(Request $request): LoginData
    {
        $arguments = $request->getArguments();
        if (!array_key_exists('email', $arguments) || !array_key_exists('password', $arguments)) {
            throw new \Exception("Arguments 'email' and 'password' are required.");
        }

        return new LoginData($arguments['email'], new Password($arguments['password']));
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
}