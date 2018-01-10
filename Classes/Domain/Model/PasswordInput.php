<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 10.01.18
 * Time: 23:23
 */

namespace AlexGunkel\FeUseradd\Domain\Model;


class PasswordInput
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $oldPassword = '';

    /**
     * @var string
     */
    private $inputOne = '';

    /**
     * @var string
     */
    private $inputTwo = '';

    /**
     * @param string $inputOne
     */
    public function setInputOne(string $inputOne)
    {
        $this->inputOne = $inputOne;
    }

    /**
     * @param string $inputTwo
     */
    public function setInputTwo(string $inputTwo)
    {
        $this->inputTwo = $inputTwo;
    }

    final public function __toString()
    {
        try {
            $this->check();
        } catch (\Exception $exception) {
            return '';
        }
        return $this->inputOne;
    }

    final public function check() : self
    {
        if ($this->inputOne !== $this->inputTwo) {
            throw new \Exception("Strings are different.");
        }

        if (empty($this->inputOne)) {
            throw new \Exception("Password is not allowed to be empty.");
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getOldPassword() : string
    {
        return $this->oldPassword;
    }

    /**
     * @param string $oldPassword
     */
    public function setOldPassword(string $oldPassword)
    {
        $this->oldPassword = $oldPassword;
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
    public function setEmail(string $email)
    {
        $this->email = $email;
    }
}