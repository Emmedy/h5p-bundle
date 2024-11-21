<?php


namespace Emmedy\H5PBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LibraryRepository::class)]
#[ORM\Table(name: 'h5p_library')]
class Library
{
    #[ORM\Id]
    #[ORM\Column(type:"integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id;

    #[ORM\Column(name: "machine_name", type: "string", length: 127)]
    private string $machineName;

    #[ORM\Column(name: "title", type: "string", length: 255)]
    private string $title;

    #[ORM\Column(name: "major_version", type: "integer")]
    private int $majorVersion;

    #[ORM\Column(name: "minor_version", type: "integer")]
    private int $minorVersion;

    #[ORM\Column(name: "patch_version", type: "integer")]
    private int $patchVersion;

    #[ORM\Column(name: "patch_version_in_folder_name", type: "boolean", options: [ "default" => 0])]
    private bool $patchVersionInFolderName = false;

    #[ORM\Column(name: "runnable", type: "boolean", options: [ "default" => 1])]
    private bool $runnable = true;

    #[ORM\Column(name: "fullscreen", type: "boolean", options: [ "default" => 0])]
    private bool $fullscreen = false;

    #[ORM\Column(name: "embed_types", type: "string", length: 255)]
    private ?string $embedTypes;

    #[ORM\Column(name: "preloaded_js", type: "text", nullable: true)]
    private ?string $preloadedJs;

    #[ORM\Column(name: "preloaded_css", type: "text", nullable: true)]
    private ?string $preloadedCss;

    #[ORM\Column(name: "drop_library_css", type: "text", nullable: true)]
    private ?string $dropLibraryCss;

    #[ORM\Column(name: "semantics", type: "text")]
    private ?string $semantics;

    #[ORM\Column(name: "restricted", type: "boolean", options: ['default' => 0])]
    private bool $restricted = false;

    #[ORM\Column(name: "tutorial_url", type: "string", length: 1000, nullable: true)]
    private ?string $tutorialUrl;

    #[ORM\Column(name: "has_icon", type: "boolean", options: ['default' => 0])]
    private bool $hasIcon = false;

    #[ORM\OneToMany(targetEntity: ContentLibraries::class, mappedBy: "library")]
    private Collection $contentLibraries;

    #[ORM\Column(name: "metadata_settings", type: "text", nullable: true)]
    private ?string $metadataSettings;

    #[ORM\Column(name: "add_to", type: "text", nullable: true)]
    private ?string $addTo;

    public function __get($name)
    {
        if ($name === "name") {
            return $this->machineName;
        }
        $name = $this->getLocalName($name);
        return $this->$name;
    }

    public function __isset($name): bool
    {
        $name = $this->getLocalName($name);
        return isset($this->$name);
    }

    public function __set($name, $value): void
    {
        $name = $this->getLocalName($name);
        $this->$name = $value;
    }

    private function getLocalName($name): string
    {
        $name = \H5PCore::snakeToCamel([$name => 1]);
        return array_keys($name)[0];
    }

    public function __construct()
    {
        $this->contentLibraries = new ArrayCollection();
    }

    public function __toString(): string
    {
        return "{$this->machineName} {$this->majorVersion}.{$this->minorVersion}";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMachineName(): ?string
    {
        return $this->machineName;
    }

    public function setMachineName(string $machineName): self
    {
        $this->machineName = $machineName;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle($title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getMajorVersion(): int
    {
        return $this->majorVersion;
    }

    public function setMajorVersion(int $majorVersion): self
    {
        $this->majorVersion = $majorVersion;
        return $this;
    }

    public function getMinorVersion(): int
    {
        return $this->minorVersion;
    }

    public function setMinorVersion(int $minorVersion): self
    {
        $this->minorVersion = $minorVersion;
        return $this;
    }

    public function getPatchVersion(): int
    {
        return $this->patchVersion;
    }

    public function setPatchVersion(int $patchVersion): self
    {
        $this->patchVersion = $patchVersion;
        return $this;
    }

    public function isRunnable(): bool
    {
        return $this->runnable;
    }

    public function setRunnable($runnable): self
    {
        $this->runnable = $runnable;
        return $this;
    }

    public function isFullscreen(): bool
    {
        return $this->fullscreen;
    }

    public function setFullscreen($fullscreen): self
    {
        $this->fullscreen = $fullscreen;
        return $this;
    }

    public function getEmbedTypes(): ?string
    {
        return $this->embedTypes;
    }

    public function setEmbedTypes(?string $embedTypes): self
    {
        $this->embedTypes = $embedTypes;
        return $this;
    }

    public function getPreloadedJs(): ?string
    {
        return $this->preloadedJs;
    }

    public function setPreloadedJs(?string $preloadedJs): self
    {
        $this->preloadedJs = $preloadedJs;
        return $this;
    }

    public function getPreloadedCss(): ?string
    {
        return $this->preloadedCss;
    }

    public function setPreloadedCss(?string $preloadedCss): self
    {
        $this->preloadedCss = $preloadedCss;
        return $this;
    }

    public function getDropLibraryCss(): ?string
    {
        return $this->dropLibraryCss;
    }

    public function setDropLibraryCss(?string $dropLibraryCss): self
    {
        $this->dropLibraryCss = $dropLibraryCss;
        return $this;
    }

    public function getSemantics(): ?string
    {
        return $this->semantics;
    }

    public function setSemantics(?string $semantics): self
    {
        $this->semantics = $semantics;
        return $this;
    }

    public function isRestricted(): ?bool
    {
        return $this->restricted;
    }

    public function setRestricted(?bool $restricted): self
    {
        $this->restricted = $restricted;
        return $this;
    }

    public function getTutorialUrl(): ?string
    {
        return $this->tutorialUrl;
    }

    public function setTutorialUrl(?string $tutorialUrl): self
    {
        $this->tutorialUrl = $tutorialUrl;
        return $this;
    }

    public function isHasIcon(): bool
    {
        return $this->hasIcon;
    }

    public function setHasIcon(bool $hasIcon): self
    {
        $this->hasIcon = $hasIcon;
        return $this;
    }

    public function isFrame(): bool
    {
        return (str_contains($this->embedTypes, 'iframe'));
    }

    public function getMetadataSettings(): ?string
    {
        return $this->metadataSettings;
    }

    public function setMetadataSettings(?string $metadataSettings): self
    {
        $this->metadataSettings = $metadataSettings;
        return $this;
    }

    public function getAddTo(): ?string
    {
        return $this->addTo;
    }

    public function setAddTo(?string $addTo): self
    {
        $this->addTo = $addTo;
        return $this;
    }

    public function isPatchVersionInFolderName(): bool
    {
        return $this->patchVersionInFolderName;
    }

    public function setPatchVersionInFolderName(bool $patchVersionInFolderName): self
    {
        $this->patchVersionInFolderName = $patchVersionInFolderName;
        return $this;
    }
}
