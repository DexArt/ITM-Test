<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserTrainingRepository")
 */
class UserTraining
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userTrainings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GroupTraining")
     * @ORM\JoinColumn(nullable=false)
     */
    private $groupTraining;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\NotificationType")
     * @ORM\JoinColumn(nullable=false,name="notification_type_id", referencedColumnName="id")
     */
    private $notification_type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_deleted;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param User
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getGroupTraining(): ?GroupTraining
    {
        return $this->groupTraining;
    }

    public function setGroupTraining(?GroupTraining $groupTraining): self
    {
        $this->groupTraining = $groupTraining;

        return $this;
    }

    public function getNotificationType(): ?NotificationType
    {
        return $this->notification_type;
    }

    public function setNotificationType(?NotificationType $notification_type): self
    {
        $this->notification_type = $notification_type;

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->is_deleted;
    }

    public function setIsDeleted(bool $is_deleted): self
    {
        $this->is_deleted = $is_deleted;

        return $this;
    }

}
