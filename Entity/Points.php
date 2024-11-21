<?php


namespace Emmedy\H5PBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: "h5p_points")]
class Points
{
    #[ORM\Column(name: "user_id", type: "integer")]
    private ?int $user = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Content::class)]
    #[ORM\JoinColumn(name: "content_main_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private Content $content;

    #[ORM\Column(name: "started", type: "integer")]
    private int $started;

    #[ORM\Column(name: "finished", type: "integer")]
    private int $finished = 0;

    #[ORM\Column(name: "points", type: "integer", nullable: true)]
    private ?int $points;

    #[ORM\Column(name: "max_points", type: "integer", nullable: true)]
    private ?int $maxPoints;

    public function getUser(): ?int
    {
        return $this->user;
    }

    public function setUser(?int $user): void
    {
        $this->user = $user;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function setContent(Content $content): void
    {
        $this->content = $content;
    }

    public function getStarted(): int
    {
        return $this->started;
    }

    public function setStarted(int $started): void
    {
        $this->started = $started;
    }

    public function getFinished(): int
    {
        return $this->finished;
    }

    public function setFinished(int $finished): void
    {
        $this->finished = $finished;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): void
    {
        $this->points = $points;
    }

    public function getMaxPoints(): int
    {
        return $this->maxPoints;
    }

    public function setMaxPoints(int $maxPoints): void
    {
        $this->maxPoints = $maxPoints;
    }
}
