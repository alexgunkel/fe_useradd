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
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
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

    final public function moveToFeUser(User $user)
    {
        $frontendUser = new FrontendUser($user->getEmail(), $user->getPassword());
        $frontendUser->setFirstName($user->getFirstName());
        $frontendUser->setLastName($user->getLastName());
        $frontendUser->setEmail($user->getEmail());

        $this->feUserRepository->add($frontendUser);
        $this->remove($user);
    }
}