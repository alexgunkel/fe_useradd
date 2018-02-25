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
    private $subject;
    public const STATUS_VALIDATE = 1;
    public const STATUS_INFORM = 2;
    private const SUBJECT = [
        self::STATUS_VALIDATE => 'HYPOS-Mitgliederbereich - eine neue Anmeldung liegt vor',
        self::STATUS_INFORM => 'HYPOS-Mitgliederbereich - Ihre Anmeldung',
    ];

    public function __construct(string $validationLink, User $user, int $status = 1)
    {
        $this->validationLink = $validationLink;
        $this->feUser = $user;
        $this->status = $status;
        $this->subject = self::SUBJECT[$status];
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject)
    {
        $this->subject = $subject;
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