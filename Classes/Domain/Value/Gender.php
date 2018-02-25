<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.02.18
 * Time: 20:26
 */

namespace AlexGunkel\FeUseradd\Domain\Value;


use AlexGunkel\FeUseradd\Exception\LogicException;

class Gender
{
    public const MALE = 'm';
    public const FEMALE = 'f';
    private const OPTIONS = [self::MALE, self::FEMALE];

    private $gender;

    public function __construct(string $gender)
    {
        if (!in_array($gender, self::OPTIONS)) {
            throw new LogicException(
                "$gender must be one of the allowed values: "
                . implode(', ', self::OPTIONS),
                1518636724
            );
        }

        $this->gender = $gender;
    }

    public static function getOptions(): array
    {
        return array_combine(self::OPTIONS, self::OPTIONS);
    }

    public function __toString(): string
    {
        return $this->gender;
    }
}