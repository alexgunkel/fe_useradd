<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 17.01.18
 * Time: 21:59
 */

namespace AlexGunkel\FeUseradd\Domain\Value;


use AlexGunkel\FeUseradd\Exception\LogicException;

class RegistrationState
{
    private $state;
    public const NEW = 'NEW';
    public const ALLOWED = 'ALLOWED';
    private const POSSIBLE = [self::NEW, self::ALLOWED];

    /**
     * RegistrationState constructor.
     * @param string $state
     * @throws LogicException
     */
    public function __construct(string $state)
    {
        if (!in_array($state, self::POSSIBLE)) {
            throw new LogicException(
                "State must be one of the allowed registration states "
                . implode(', ', self::POSSIBLE)
                . ", $state given."
            );
        }

        $this->state = $state;
    }

    public function __toString(): string
    {
        return $this->state;
    }
}