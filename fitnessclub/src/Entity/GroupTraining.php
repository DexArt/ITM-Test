<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupTrainingRepository")
 */
class GroupTraining
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Required")
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Assert\NotBlank(message="Required")
     * @ORM\Column(type="string", length=255)
     */
    private $master_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_expired;

    public function __construct()
    {
        $this->userTraining = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMasterName(): ?string
    {
        return $this->master_name;
    }

    public function setMasterName(string $master_name): self
    {
        $this->master_name = $master_name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIsExpired(): ?bool
    {
        return $this->is_expired;
    }

    public function setIsExpired(bool $is_expired): self
    {
        $this->is_expired = $is_expired;

        return $this;
    }
}
