<?php


namespace Emmedy\H5PBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table('h5p_libraries_hub_cache')]
class LibrariesHubCache
{
    #[ORM\Id]
    #[ORM\Column(name: "id", type: 'integer')]
    #[ORM\GeneratedValue(strategy:"AUTO")]
    private ?int $id = null;

    #[ORM\Column(name: "machine_name", type: "string", length: 127)]
    private string $machineName;

    #[ORM\Column(name: "major_version", type: "integer")]
    private int $majorVersion;

    #[ORM\Column(name: "minor_version", type: "integer")]
    private int $minorVersion;

    #[ORM\Column(name: "patch_version", type: "integer")]
    private int $patchVersion;

    #[ORM\Column(name: "h5p_major_version", type: "integer", nullable: true)]
    private ?int $h5pMajorVersion = null;

    #[ORM\Column(name: "h5p_minor_version", type: "integer", nullable: true)]
    private ?int $h5pMinorVersion = null;

    #[ORM\Column(name: "title", type: "string", length: 255)]
    private string $title;

    #[ORM\Column(name: "summary", type: "text")]
    private string $summary;

    #[ORM\Column(name: "description", type: "text")]
    private string $description;

    #[ORM\Column(name: "icon", type: "text")]
    private string $icon;

    #[ORM\Column(name: "created_at", type: "integer")]
    private int $createdAt;

    #[ORM\Column(name: "updated_at", type: "integer")]
    private int $updatedAt;

    #[ORM\Column(name: "is_recommended", type: "boolean", options: ["default" => 1])]
    private bool $isRecommended = true;

    #[ORM\Column(name: "popularity", type: "integer")]
    private int $popularity = 0;

    #[ORM\Column(name: "screenshots", type: "text", nullable: true)]
    private ?string $screenshots = null;

    #[ORM\Column(name: "license", type: "text", nullable: true)]
    private ?string $license = null;

    #[ORM\Column(name: "example", type: "text")]
    private string $example;

    #[ORM\Column(name: "tutorial", type: "text", nullable: true)]
    private ?string $tutorial = null;

    #[ORM\Column(name: "keywords", type: "text", nullable: true)]
    private ?string $keywords = null;

    #[ORM\Column(name: "categories", type: "text", nullable: true)]
    private ?string $categories = null;

    #[ORM\Column(name: "owner", type: "text", nullable: true)]
    private ?string $owner = null;

    public function __get($name)
    {
        $name = \H5PCore::snakeToCamel([$name => 1]);
        $name = array_keys($name)[0];
        return $this->$name;
    }
    public function __isset($name)
    {
        $name = \H5PCore::snakeToCamel([$name => 1]);
        $name = array_keys($name)[0];
        return isset($this->$name);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMachineName(): string
    {
        return $this->machineName;
    }

    public function setMachineName(string $machineName): void
    {
        $this->machineName = $machineName;
    }

    public function getMajorVersion(): int
    {
        return $this->majorVersion;
    }

    public function setMajorVersion(int $majorVersion): void
    {
        $this->majorVersion = $majorVersion;
    }

    public function getMinorVersion(): int
    {
        return $this->minorVersion;
    }

    public function setMinorVersion(int $minorVersion): void
    {
        $this->minorVersion = $minorVersion;
    }

    public function getPatchVersion(): int
    {
        return $this->patchVersion;
    }

    public function setPatchVersion(int $patchVersion): void
    {
        $this->patchVersion = $patchVersion;
    }

    public function getH5pMajorVersion(): ?int
    {
        return $this->h5pMajorVersion;
    }

    public function setH5pMajorVersion(?int $h5pMajorVersion): void
    {
        $this->h5pMajorVersion = $h5pMajorVersion;
    }

    public function getH5pMinorVersion(): ?int
    {
        return $this->h5pMinorVersion;
    }

    public function setH5pMinorVersion(?int $h5pMinorVersion): void
    {
        $this->h5pMinorVersion = $h5pMinorVersion;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): void
    {
        $this->summary = $summary;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): int
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(int $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function isRecommended(): bool
    {
        return $this->isRecommended;
    }

    public function setIsRecommended(bool $isRecommended): void
    {
        $this->isRecommended = $isRecommended;
    }

    public function getPopularity(): int
    {
        return $this->popularity;
    }

    public function setPopularity(int $popularity): void
    {
        $this->popularity = $popularity;
    }

    public function getScreenshots(): ?string
    {
        return $this->screenshots;
    }

    public function setScreenshots(?string $screenshots): void
    {
        $this->screenshots = $screenshots;
    }

    public function getLicense(): ?string
    {
        return $this->license;
    }

    public function setLicense(?string $license): void
    {
        $this->license = $license;
    }

    public function getExample(): string
    {
        return $this->example;
    }

    public function setExample(string $example): void
    {
        $this->example = $example;
    }

    public function getTutorial(): ?string
    {
        return $this->tutorial;
    }

    public function setTutorial(?string $tutorial): void
    {
        $this->tutorial = $tutorial;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): void
    {
        $this->keywords = $keywords;
    }

    public function getCategories(): ?string
    {
        return $this->categories;
    }

    public function setCategories(?string $categories): void
    {
        $this->categories = $categories;
    }

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(?string $owner): void
    {
        $this->owner = $owner;
    }
}
