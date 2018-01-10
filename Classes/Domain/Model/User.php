<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 10.01.18
 * Time: 21:26
 */

namespace AlexGunkel\FeUseradd\Domain\Model;


use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;

final class User extends AbstractDomainObject
{
    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $company;

    /**
     * @var string
     */
    protected $position;

    /**
     * @var string
     */
    protected $password;

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
     */
    final public function setFirstName($firstName)
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
     */
    final public function setLastName($lastName)
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
    final public function setEmail($email)
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
    final public function setCompany($company)
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
    final public function setPosition($position)
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
    final public function setPassword($password)
    {
        $this->password = $password;
    }
}