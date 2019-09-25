<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"users_by_customer","show_user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"users_by_customer", "show_user", "create_user"})
     * @Assert\NotBlank
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"users_by_customer", "show_user","create_user"})
     * @Assert\NotBlank(groups={"create_user"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"list_users", "get_user"})
     * @Assert\NotBlank(groups={"create_user"})
     */
    private $birthDay;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"users_by_customer", "show_user","create_user"})
     * @Assert\NotBlank
     */
    private $address;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"users_by_customer", "show_user","create_user"})
     * @Assert\NotBlank
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"users_by_customer", "show_user", "create_user"})
     * @Assert\NotBlank
     * @Assert\Email(message="The email '{{value}}' is not a valid email",
     * checkMX = true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"users_by_customer", "show_user"})
     */
    private $mobileNumber;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="users", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @ORM\JoinColumn(name="id",                referencedColumnName="id")
     * @Groups({"users_by_customer","list_users", "show_user"})
     * @Assert\NotBlank
     */
    private $customer;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBirthDay(): ?\DateTimeInterface
    {
        return $this->birthDay;
    }

    public function setBirthDay(?\DateTimeInterface $birthDay): self
    {
        $this->birthDay = $birthDay;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMobileNumber(): ?string
    {
        return $this->mobileNumber;
    }

    public function setMobileNumber(?string $mobileNumber): self
    {
        $this->mobileNumber = $mobileNumber;

        return $this;
    }
}