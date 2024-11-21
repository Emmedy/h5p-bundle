<?php


namespace Emmedy\H5PBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LibrariesLanguagesRepository::class)]
#[ORM\Table(name: "h5p_libraries_languages")]
class LibrariesLanguages
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Library::class)]
    #[ORM\JoinColumn(name: "library_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private Library $library;

    #[ORM\Id]
    #[ORM\Column(name: "language_code", type: "string", length: 31)]
    private string $languageCode;

    #[ORM\Column(name: "language_json", type: "text")]
    private string $languageJson;

    public function getLibrary(): Library
    {
        return $this->library;
    }

    public function setLibrary(Library $library)
    {
        $this->library = $library;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    public function setLanguageCode(string $languageCode)
    {
        $this->languageCode = $languageCode;
    }

    public function getLanguageJson(): string
    {
        return $this->languageJson;
    }

    public function setLanguageJson(string $languageJson)
    {
        $this->languageJson = $languageJson;
    }
}
