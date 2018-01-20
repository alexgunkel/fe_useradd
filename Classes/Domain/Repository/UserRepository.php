<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 10.01.18
 * Time: 21:30
 */

namespace AlexGunkel\FeUseradd\Domain\Repository;

use AlexGunkel\FeUseradd\Domain\Model\User;
use AlexGunkel\FeUseradd\Exception\ValidationException;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class UserRepository extends Repository
{
    /**
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     *
     * @inject
     */
    private $feUserRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * UserRepository constructor.
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     */
    public function __construct(\TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager)
    {
        parent::__construct($objectManager);
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
    }

    /**
     * @param string $email
     *
     * @return User
     * @throws ValidationException
     */
    final public function findByEmail(string $email) : User
    {
        $query = $this->createQuery();
        $query->matching($query->equals('email', $email, false));
        /** @var QueryResultInterface $result */
        $result = $query->execute(false);

        if ($result->count() === 0) {
            throw new ValidationException("Given email-address is unknown.");
        } elseif ($result->count() > 1) {
            throw new ValidationException('Given email-address is not unique.');
        }

        return $result->getFirst();
    }

    /**
     * @param User $user
     */
    final public function moveToFeUser(User $user)
    {
        $this->logger->debug("Add $user to fe_users-table");
        $typo3CoreFeUser = $user->toFrontendUser();
        $typo3CoreFeUser->addUsergroup($user->getFeGroups());
        $this->feUserRepository->add($typo3CoreFeUser);


        $this->logger->debug("Remove $user from registration-table");
        $this->remove($user);
    }
}