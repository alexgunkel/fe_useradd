<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 15.01.18
 * Time: 22:23
 */

namespace AlexGunkel\FeUseradd\Service;

use AlexGunkel\FeUseradd\Domain\Value\Password;
use AlexGunkel\FeUseradd\Domain\Value\PasswordHash;
use TYPO3\CMS\Saltedpasswords\Salt\SaltFactory;

class PasswordService
{
    public function generateRandomPassword(): Password
    {
        return new Password(bin2hex(openssl_random_pseudo_bytes(20)));
    }

    /**
     * @param string $password
     * @return PasswordHash
     */
    public function getSaltedPassword(string $password) : PasswordHash
    {
        $salting = SaltFactory::getSaltingInstance(null, 'FE');
        return new PasswordHash($salting->getHashedPassword($password));
    }
}