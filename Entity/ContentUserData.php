<?php


namespace Emmedy\H5PBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

 #[ORM\Entity()]
 #[ORM\Table(name: "h5p_content_user_data")]
class ContentUserData
{
    #[ORM\Id]
    #[ORM\Column(name: "user_id", type: "integer")]
    private ?int $user = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Content::class)]
    #[ORM\JoinColumn(name: "content_main_id", referencedColumnName:"id", onDelete: 'CASCADE')]
    private ?Content $mainContent = null;

    #[ORM\Id]
    #[ORM\Column(name: "sub_content_id", type: "integer", length: 10)]
    private int $subContentId;

    #[ORM\Id]
    #[ORM\Column(name: "data_id", type: "string", length: 127)]
    private null|int|string $dataId;

    #[ORM\Column(name: "timestamp", type: "integer", length: 10)]
    private int $timestamp;

    #[ORM\Column(name: "data", type: "text")]
    private string $data;

    #[ORM\Column(name: "preloaded", type: "boolean", nullable: true)]
    private ?bool $preloaded = null;

    #[ORM\Column(name: "delete_on_content_change", type: "boolean", nullable: true)]
    private ?bool $deleteOnContentChange = null;

    public function getUser(): ?int
    {
        return $this->user;
    }

    public function setUser(?int $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getMainContent(): ?Content
    {
        return $this->mainContent;
    }

    public function setMainContent(?Content $mainContent): self
    {
        $this->mainContent = $mainContent;
        return $this;
    }

    public function getSubContentId(): ?int
    {
        return $this->subContentId;
    }

    public function setSubContentId($subContentId): self
    {
        $this->subContentId = $subContentId;
        return $this;
    }

    public function getDataId(): null|int|string
    {
        return $this->dataId;
    }

    public function setDataId(null|int|string $dataId): self
    {
        $this->dataId = $dataId;
        return $this;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function isPreloaded(): ?bool
    {
        return $this->preloaded;
    }

    public function setPreloaded(?bool $preloaded): self
    {
        $this->preloaded = $preloaded;
        return $this;
    }

    public function isDeleteOnContentChange(): ?bool
    {
        return $this->deleteOnContentChange;
    }

    public function setDeleteOnContentChange(?bool $deleteOnContentChange): self
    {
        $this->deleteOnContentChange = $deleteOnContentChange;
        return $this;
    }
}
