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
    private const NONE = '';
    private const DOCTOR = 'Dr.';
    private const PROFESSOR = 'Prof. Dr.';
    private const OPTIONS = [
        self::NONE,
        self::DOCTOR,
        self::PROFESSOR,
    ];

    /**
     * @var string
     */
    private $title;

    public function __construct(string $title)
    {
        if (!in_array($this, array_flip(self::OPTIONS))) {
            throw new LogicException(
                "$title must be one of the allowed values: "
                . implode(', ', self::OPTIONS),
                1518636946
            );
        }

        $this->title = $title;
    }

    public static function getOptions(): array
    {
        return array_combine(self::OPTIONS, self::OPTIONS);
    }

    public function __toString(): string
    {
        return $this->title;
    }
}