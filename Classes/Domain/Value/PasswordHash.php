<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 15.01.18
 * Time: 22:34
 */

namespace AlexGunkel\FeUseradd\Domain\Value;

final class PasswordHash
{
    private $passwordHash;
    public function __construct(string $passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }

    public function __toString(): string
    {
        return $this->passwordHash;
    }

    /**
     * @return string
     */
    private function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @param string $passwordHash
     */
    private function setPasswordHash(string $passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }
}