<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 10.01.18
 * Time: 21:26
 */

namespace AlexGunkel\FeUseradd\Domain\Model;

use AlexGunkel\FeUseradd\Domain\Value\Gender;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class User
 * @package AlexGunkel\FeUseradd\Domain\Model
 */
final class User extends AbstractEntity
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
     * @validate NotEmpty
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
    protected $gender;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup>
     */
    protected $feGroups;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->setFeGroups(new ObjectStorage());
    }

    /**
     *
     */
    final public function __clone()
    {
        parent::__clone();
        $this->password = '';
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

    /**
     * @param string $registrationState
     */
    final public function setRegistrationState(string $registrationState)
    {
        $this->registrationState = $registrationState;
    }

    /**
     * @return string
     */
    final public function getRegistrationState(): string
    {
        return $this->registrationState;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup>
     */
    public function getFeGroups(): ObjectStorage
    {
        return $this->feGroups ? clone $this->feGroups : new ObjectStorage();
    }

    /**
     * @param FrontendUserGroup $feGroup
     */
    public function addFeGroup(FrontendUserGroup $feGroup)
    {
        $this->feGroups->attach($feGroup);
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup> $feGroups
     */
    public function setFeGroups(ObjectStorage $feGroups)
    {
        $this->feGroups = $feGroups;
    }

    /**
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender(string $gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return FrontendUser
     */
    final public function toFrontendUser(): FrontendUser
    {
        $clone = new \In2code\Femanager\Domain\Model\User();
        $clone->setUsername($this->getEmail());
        $clone->setFirstName($this->getFirstName());
        $clone->setLastName($this->getLastName());
        $clone->setEmail($this->getEmail());
        $clone->setCompany($this->getCompany());
        $clone->setTitle($this->getTitle());
        DebuggerUtility::var_dump($this);
        $clone->setGender(($this->getGender() === Gender::FEMALE) ? 1 : 0);
        foreach ($this->getFeGroups() as $feGroup) {
            $clone->addUsergroup($feGroup);
        }

        return $clone;
    }
}