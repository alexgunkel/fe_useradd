<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 10.01.18
 * Time: 21:26
 */

namespace AlexGunkel\FeUseradd\Domain\Model;


use AlexGunkel\FeUseradd\Domain\Value\RegistrationState;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;

final class User extends AbstractDomainObject
{
    /**
     * @var string
     * @validate StringLength(minimum=3, maximum=30)
     */
    protected $firstName;

    /**
     * @var string
     * @validate StringLength(minimum=3, maximum=30)
     */
    protected $lastName;

    /**
     * @var string
     * @validate NotEmpty
     * @validate EmailAddress
     */
    protected $email;

    /**
     * @var string
     * @validate StringLength(minimum=3, maximum=30)
     */
    protected $company;

    /**
     * @var string
     * @validate StringLength(minimum=3, maximum=30)
     */
    protected $position;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $registrationState;

    /**
     * @var string
     */
    protected $userGroup;

    final public function __clone()
    {
        parent::__clone();
        $this->password = '';
        $this->userGroup = '';
    }

    /**
     * @return string
     */
    final public function __toString() : string
    {
        return "$this->firstName $this->lastName ($this->email, $this->position bei $this->company)";
    }

    /**
     * @return string
     */
    final public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @validate StringLength(minimum=3, maximum=30)
     */
    final public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    final public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @validate StringLength(minimum=3, maximum=30)
     */
    final public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    final public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    final public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    final public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    final public function setCompany(string $company)
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    final public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    final public function setPosition(string $position)
    {
        $this->position = $position;
    }

    /**
     * @return string
     */
    final public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    final public function setPassword(string $password)
    {
        $this->password = $password;
    }

    final public function setRegistrationState(string $registrationState)
    {
        $this->registrationState = $registrationState;
    }

    final public function getRegistrationState(): string
    {
        return $this->registrationState;
    }

    /**
     * @return string
     */
    public function getUserGroup(): string
    {
        return $this->userGroup;
    }

    /**
     * @param string $userGroup
     */
    public function setUserGroup(string $userGroup)
    {
        $this->userGroup = $userGroup;
    }

    final public function toFrontendUser(): FrontendUser
    {
        $clone = new FrontendUser($this->getEmail(), $this->getPassword());
        $clone->setFirstName($this->getFirstName());
        $clone->setLastName($this->getLastName());
        $clone->setEmail($this->getEmail());

        return $clone;
    }
}