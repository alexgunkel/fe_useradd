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
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class UserRepository extends Repository
{
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
}