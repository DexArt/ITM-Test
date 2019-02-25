<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotNull(message="Firstname is required")
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $firstname;

    /**
     * @Assert\NotBlank(message="Lastname is required")
     * @ORM\Column(type="string", length=60)
     */
    private $lastname;

    /**
     * @Assert\Email(message="Wrong email")
     * @Assert\NotBlank(message="Email is required")
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     *
     * @Assert\Regex(pattern= "/[a-zA-Z0-9]/", message="Wrong characters")
     * @Assert\Length(
     *      min = 8,
     *      max = 100,
     *      minMessage = "Password short, min {{ limit }}",
     *      maxMessage = "Password long, max {{ limit }}"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $gender;

    /**
     * @Assert\DateTime
     * @Assert\NotNull(message="Birthday is required")
     * @ORM\Column(type="datetime")
     */
    private $birthday;

    /**
     * @Assert\Regex(pattern= "/^[+]?\d+$/", message="Incorrect phone number")
     * @Assert\NotBlank(message="Phone is required")
     * @Assert\Length(
     *      min = 4,
     *      max = 20,
     *      minMessage = "Too short, min {{ limit }}",
     *      maxMessage = "Too long, max {{ limit }}"
     * )
     * @ORM\Column(type="string")
     */
    private $phone;

    /**
     * @ORM\Column(type="json")
     */
    private $roles;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $auth_token;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_authorised;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_deleted;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserTraining", mappedBy="user")
     */
    private $userTrainings;

    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $is_active;

    /**
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    public function __construct()
    {
        $this->userTrainings = new ArrayCollection();
    }

    /**
     * @param mixed $is_deleted
     */
    public function setIsDeleted(bool $is_deleted): self
    {
        $this->is_deleted = $is_deleted;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getisDeleted()
    {
        return $this->is_deleted;
    }

    /**
     * @param mixed $is_authorised
     */
    public function setIsAuthorised(bool $is_authorised): self
    {
        $this->is_authorised = $is_authorised;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsAuthorised(): ?bool
    {
        return $this->is_authorised;
    }

    /**
     * @return mixed
     */
    public function getAuthToken()
    {
        return $this->auth_token;
    }

    /**
     * @param mixed $auth_token
     */
    public function setAuthToken($auth_token): void
    {
        $this->auth_token = $auth_token;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname($firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname($lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday($birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    /**
     * @param mixed $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function unserialize($serialized)
    {
        list($this->id,
            $this->firstname,
            $this->lastname,
            $this->email,
            $this->password,
            $this->phone,
            $this->gender,
            $this->birthday,
            $this->roles,
            $this->is_authorised,
            $this->is_deleted
            ) = unserialize($serialized,['allowed_classes' => false]);
    }
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->firstname,
            $this->lastname,
            $this->email,
            $this->password,
            $this->phone,
            $this->gender,
            $this->birthday,
            $this->roles,
            $this->is_authorised,
            $this->is_deleted
        ]);
    }

    /**
     * @return Collection|UserTraining[]
     */
    public function getUserTrainings(): Collection
    {
        $array = new ArrayCollection();

        foreach ($this->userTrainings as $training){
            if(!$training->getIsDeleted()){
                $array->add($training);
            }
        }
        return $array;
    }

    public function addUserTraining(UserTraining $userTraining): self
    {
        if (!$this->userTrainings->contains($userTraining)) {
            $this->userTrainings[] = $userTraining;
            $userTraining->setUser($this);
        }

        return $this;
    }

    public function removeUserTraining(UserTraining $userTraining): self
    {
        if ($this->userTrainings->contains($userTraining)) {
            $this->userTrainings->removeElement($userTraining);
            // set the owning side to null (unless already changed)
            if ($userTraining->getUser() === $this) {
                $userTraining->setIsDeleted(true);
            }
        }

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function __toString(){
        // to show the name of the Category in the select
        return $this->getFirstname().' '.$this->getLastname();
        // to show the id of the Category in the select
        // return $this->id;
    }

}
