<?php

namespace Emmedy\H5PBundle\Entity;

use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionRepository::class)]
#[ORM\Table('h5p_option')]
class Option
{
    private const INTEGER = "integer";
    private const BOOLEAN = "boolean";
    private const STRING = "string";

    #[ORM\Id]
    #[ORM\Column(name: "name", type: "string", length: 255)]
    private ?string $name;

    #[ORM\Column(name: "value", type: "text")]
    private string $value;

    #[ORM\Column(name: "type", type: "string", length: 255)]
    private string $type;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string|int|bool
    {
        if ($this->type === self::INTEGER) {
            return (int)$this->value;
        }

        if ($this->type === self::BOOLEAN) {
            return (bool)$this->value;
        }

        return $this->value;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setValue(string|int|bool $value): void
    {
        if (is_int($value)) {
            $this->type = self::INTEGER;
        } elseif (is_bool($value)) {
            $this->type = self::BOOLEAN;
        } elseif (is_string($value)) {
            $this->type = self::STRING;
        } else {
            throw new InvalidArgumentException("The value type is not supported.");
        }
        $this->value = (string)$value;
    }
}
