<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * clé primaire
     * @ORM\Id()
     * auto-increment
     * @ORM\GeneratedValue()
     * un integer en bdd
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * varchar(50) en bdd
     * @ORM\Column(type="string", length=50)
     */
    private $lastname;

    /**
     * varchar(20) en bdd
     * @ORM\Column(type="string", length=20)
     */
    private $firstname;

    /**
     * varchar(255) unique en bdd
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * date nullable en bdd
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthdate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

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

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }
}
