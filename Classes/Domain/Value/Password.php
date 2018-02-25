<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 15.01.18
 * Time: 22:37
 */

namespace AlexGunkel\FeUseradd\Domain\Value;


/**
 * Class Password
 * @package AlexGunkel\FeUseradd\Domain\Value
 */
final class Password
{
    /**
     * @var string
     */
    private $password;

    /**
     * Password constructor.
     * @param string $password
     */
    public function __construct(string $password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    private function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    private function setPassword(string $password)
    {
        $this->password = $password;
    }
}