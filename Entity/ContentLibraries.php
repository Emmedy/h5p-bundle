<?php


namespace Emmedy\H5PBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: "h5p_content_libraries")]
class ContentLibraries
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Content::class)]
    #[ORM\JoinColumn(name: "content_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private Content $content;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Library::class, inversedBy: "contentLibraries")]
    #[ORM\JoinColumn(name: "library_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private Library $library;

    #[ORM\Id()]
    #[ORM\Column(name: "dependency_type", type: "string", length: 31)]
    private string $dependencyType;

    #[ORM\Column(name: "drop_css", type: "boolean", length: 1)]
    private bool $dropCss;

    #[ORM\Column(name: "weight", type: "integer")]
    private int $weight;

    public function getContent(): Content
    {
        return $this->content;
    }

    public function setContent(Content $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getLibrary(): Library
    {
        return $this->library;
    }

    public function setLibrary(Library $library): self
    {
        $this->library = $library;
        return $this;
    }

    public function getDependencyType(): string
    {
        return $this->dependencyType;
    }

    public function setDependencyType(string $dependencyType): self
    {
        $this->dependencyType = $dependencyType;
        return $this;
    }

    public function isDropCss(): bool
    {
        return $this->dropCss;
    }

    public function setDropCss(bool $dropCss): self
    {
        $this->dropCss = $dropCss;
        return $this;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;
        return $this;
    }
}
