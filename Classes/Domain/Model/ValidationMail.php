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

    public function __construct(string $validationLink, User $user)
    {
        $this->validationLink = $validationLink;
        $this->feUser = $user;
    }

    public function __toString(): string
    {
        return "For validation of user $this->feUser open this link:\n
        $this->validationLink";
    }
}