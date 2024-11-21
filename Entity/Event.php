<?php


namespace Emmedy\H5PBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Table('h5p_event')]
class Event
{
    #[ORM\Id]
    #[ORM\Column(type:"integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    #[ORM\Column(name: 'user_id', type: "integer")]
    private int $user;

    #[ORM\Column(name: 'created_at', type: "integer")]
    private int $createdAt;

    #[ORM\Column(name: "type", type: "string", length: 63)]
    private string $type;

    #[ORM\Column(name: "sub_type", type: "string", length: 63)]
    private string $subType;

    #[ORM\ManyToOne(targetEntity: Content::class)]
    #[ORM\JoinColumn(name: "content_id", referencedColumnName: "id", onDelete: 'CASCADE')]
    private ?Content $content = null;

    #[ORM\Column(name: "content_title", type: "string", length: 255)]
    private string $contentTitle;

    #[ORM\Column(name: "library_name", type: "string", length: 127)]
    private string $libraryName;

    #[ORM\Column(name: "library_version", type: "string", length: 31)]
    private string $libraryVersion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): int
    {
        return $this->user;
    }

    public function setUser(int $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getSubType(): string
    {
        return $this->subType;
    }

    public function setSubType(string $subType): self
    {
        $this->subType = $subType;
        return $this;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function setContent(Content $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContentTitle(): string
    {
        return $this->contentTitle;
    }

    public function setContentTitle(string $contentTitle): self
    {
        $this->contentTitle = $contentTitle;
        return $this;
    }

    public function getLibraryName():?string
    {
        return $this->libraryName;
    }

    public function setLibraryName(string $libraryName): self
    {
        $this->libraryName = $libraryName;
        return $this;
    }

    public function getLibraryVersion(): string
    {
        return $this->libraryVersion;
    }

    public function setLibraryVersion(string $libraryVersion): self
    {
        $this->libraryVersion = $libraryVersion;
        return $this;
    }
}
