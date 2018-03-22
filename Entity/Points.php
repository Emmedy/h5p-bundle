<?php

namespace Emmedy\H5PBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Emmedy\UserBundle\Entity\User;

/**
 * @ORM\Entity()
 * @ORM\Table(name="h5p_points")
 */
class Points
{
    /**
     * @var User
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Emmedy\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var Content
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="\Emmedy\H5PBundle\Entity\Content")
     * @ORM\JoinColumn(name="content_main_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $content;

    /**
     * @var integer
     *
     * @ORM\Column(name="started", type="integer")
     */
    private $started;

    /**
     * @var integer
     *
     * @ORM\Column(name="finished", type="integer")
     */
    private $finished = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="points", type="integer", nullable=true)
     */
    private $points;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_points", type="integer", nullable=true)
     */
    private $maxPoints;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param Content $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getStarted()
    {
        return $this->started;
    }

    /**
     * @param int $started
     */
    public function setStarted($started)
    {
        $this->started = $started;
    }

    /**
     * @return int
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * @param int $finished
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;
    }

    /**
     * @return int
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @param int $points
     */
    public function setPoints($points)
    {
        $this->points = $points;
    }

    /**
     * @return int
     */
    public function getMaxPoints()
    {
        return $this->maxPoints;
    }

    /**
     * @param int $maxPoints
     */
    public function setMaxPoints($maxPoints)
    {
        $this->maxPoints = $maxPoints;
    }

}