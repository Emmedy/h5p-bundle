<?php


namespace Emmedy\H5PBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LibraryLibrariesRepository::class)]
#[ORM\Table(name: "h5p_library_libraries")]
class LibraryLibraries
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Library::class)]
    #[ORM\JoinColumn(name: "library_id", referencedColumnName: "id", onDelete: 'CASCADE')]
    private Library $library;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Library::class)]
    #[ORM\JoinColumn(name: "required_library_id", referencedColumnName: "id", onDelete: 'CASCADE')]
    private Library $requiredLibrary;

    #[ORM\Column(name: "dependency_type", type: "string", length: 31)]
    private string $dependencyType;

    public function getDependencyType(): string
    {
        return $this->dependencyType;
    }

    public function setDependencyType(string $dependencyType): void
    {
        $this->dependencyType = $dependencyType;
    }

    public function getRequiredLibrary(): Library
    {
        return $this->requiredLibrary;
    }

    public function setRequiredLibrary(Library $requiredLibrary): void
    {
        $this->requiredLibrary = $requiredLibrary;
    }

    public function getLibrary(): Library
    {
        return $this->library;
    }

    public function setLibrary(Library $library): void
    {
        $this->library = $library;
    }
}
