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
    private $inputOne;
    private $inputTwo;

    /**
     * @param mixed $inputOne
     */
    public function setInputOne(string $inputOne)
    {
        $this->inputOne = $inputOne;
    }

    /**
     * @param mixed $inputTwo
     */
    public function setInputTwo(string $inputTwo)
    {
        $this->inputTwo = $inputTwo;
    }

    final public function __toString()
    {
        $this->check();
        return $this->inputOne;
    }

    private function check()
    {
        if ($this->inputOne !== $this->inputTwo) {
            throw new \Exception("Strings are different.");
        }
    }
}