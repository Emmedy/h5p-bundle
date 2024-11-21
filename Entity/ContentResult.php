<?php


namespace Emmedy\H5PBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: "h5p_content_result")]
class ContentResult
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy:"AUTO")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Content::class)]
    #[ORM\JoinColumn(onDelete:"CASCADE")]
    private ?Content $content = null;

    #[ORM\Column(type: "string", nullable: false)]
    private string|int|null $userId;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $score = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $maxScore = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $opened = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $finished = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $time = null;

    public function __construct(string|int|null $userId)
    {
        $this->userId = $userId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?Content
    {
        return $this->content;
    }

    public function setContent(?Content $content)
    {
        $this->content = $content;
    }

    public function getUserId(): string|int|null
    {
        return $this->userId;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score)
    {
        $this->score = $score;
    }

    public function getMaxScore(): ?int
    {
        return $this->maxScore;
    }

    public function setMaxScore(?int $maxScore)
    {
        $this->maxScore = $maxScore;
    }

    public function getOpened(): ?int
    {
        return $this->opened;
    }

    public function setOpened(?int $opened)
    {
        $this->opened = $opened;
    }

    public function getFinished(): ?int
    {
        return $this->finished;
    }

    public function setFinished(?int $finished)
    {
        $this->finished = $finished;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(?int $time)
    {
        $this->time = $time;
    }
}
