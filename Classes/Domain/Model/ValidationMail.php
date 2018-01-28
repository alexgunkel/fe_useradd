<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 15.01.18
 * Time: 23:52
 */

namespace AlexGunkel\FeUseradd\Domain\Model;


class ValidationMail
{
    private $validationLink;
    private $feUser;
    private $status;
    public const STATUS_VALIDATE = 1;
    public const STATUS_INFORM = 2;

    public function __construct(string $validationLink, User $user, int $status = 1)
    {
        $this->validationLink = $validationLink;
        $this->feUser = $user;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getValidationLink(): string
    {
        return $this->validationLink;
    }

    /**
     * @return User
     */
    public function getFeUser(): User
    {
        return $this->feUser;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    public function __toString(): string
    {
        return "For validation of user $this->feUser open this link:\n
        $this->validationLink";
    }
}