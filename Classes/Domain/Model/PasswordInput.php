<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 10.01.18
 * Time: 23:23
 */

namespace AlexGunkel\FeUseradd\Domain\Model;


use AlexGunkel\FeUseradd\Domain\Value\Password;
use AlexGunkel\FeUseradd\Exception\FeUseraddException;

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
     * @validate StringLength(minimum=5, maximum=20)
     * @validate NotEmpty
     */
    protected $inputOne = '';

    /**
     * @var string
     * @validate StringLength(minimum=5, maximum=20)
     * @validate NotEmpty
     */
    protected $inputTwo = '';

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
            throw new FeUseraddException("Strings are different.");
        }

        return $this;
    }

    public function getLoginData(): LoginData
    {
        return new LoginData(
            $this->email,
            new Password($this->oldPassword)
        );
    }

    /**
     * @param string $oldPassword
     */
    public function setOldPassword(string $oldPassword)
    {
        $this->oldPassword = $oldPassword;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }
}