<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 15.01.18
 * Time: 21:38
 */

namespace AlexGunkel\FeUseradd\Domain\Model;


use AlexGunkel\FeUseradd\Domain\Value\Password;

class LoginData
{
    private $email;
    private $password;

    public function __construct(string $email, Password $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    final private function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): Password
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    final private function setPassword(Password $password)
    {
        $this->password = $password;
    }
}