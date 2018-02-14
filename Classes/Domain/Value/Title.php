<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.02.18
 * Time: 20:33
 */

namespace AlexGunkel\FeUseradd\Domain\Value;


use AlexGunkel\FeUseradd\Exception\LogicException;

class Title
{
    public const DOCTOR = 'Dr.';
    public const PROFESSOR = 'Prof. Dr.';
    public const OPTIONS = [self::DOCTOR, self::PROFESSOR];
    private $title;

    public function __construct(string $title)
    {
        if (!in_array($this, self::OPTIONS)) {
            throw new LogicException(
                "$title must be one of the allowed values: "
                . implode(', ', self::OPTIONS),
                1518636946
            );
        }

        $this->title = $title;
    }

    public function __toString(): string
    {
        return $this->title;
    }
}