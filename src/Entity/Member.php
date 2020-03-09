<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cette classe correspond à une table en bdd :
 *
 * @ORM\Entity(repositoryClass="App\Repository\MemberRepository")
 *
 * Pour ajouter un index de performance
 * @ORM\Table(indexes={@ORM\Index(name="pseudo_idx", columns={"pseudo"})})
 */
class Member
{
    /**
     * c'est une clé primaire
     * @ORM\Id()
     *
     * qui s'auto-incrémente
     * @ORM\GeneratedValue()
     *
     * de type integer
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * c'est un varchar(20) en bdd
     * @ORM\Column(type="string", length=20)
     */
    private $pseudo;

    /**
     * c'est un varchar(255) en bdd et unique
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * c'est une date qui peut être null (préciser null si oui si non null pas la peine de préciser)
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthdate;

    /*
     * Pas de setter pour l'id car auto-incrémente
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

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
