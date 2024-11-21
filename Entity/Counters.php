<?php


namespace Emmedy\H5PBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: "h5p_counters")]
class Counters
{
    #[ORM\Id]
    #[ORM\Column(name: "type", type: "string", length: 63)]
    private string $type;

    #[ORM\Id]
    #[ORM\Column(name: "library_name", type: "string", length: 127)]
    private string $libraryName;

    #[ORM\Id]
    #[ORM\Column(name: "library_version", type: "string", length: 31)]
    private string $libraryVersion;

    #[ORM\Column(name: "num", type: "integer")]
    private int $num;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function getLibraryName(): string
    {
        return $this->libraryName;
    }

    public function setLibraryName(string $libraryName)
    {
        $this->libraryName = $libraryName;
    }

    public function getLibraryVersion(): string
    {
        return $this->libraryVersion;
    }

    public function setLibraryVersion(string $libraryVersion)
    {
        $this->libraryVersion = $libraryVersion;
    }

    public function getNum(): int
    {
        return $this->num;
    }

    public function setNum(int $num): self
    {
        $this->num = $num;
        return $this;
    }
}
