<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomUser;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenomUser;

    /**
     * @ORM\OneToMany(targetEntity=Evenement::class, mappedBy="even")
     */
    private $evenement;

    /**
     * @ORM\OneToMany(targetEntity=PostLike::class, mappedBy="nom_client")
     */
    private $postLikes;

    /**
     * @ORM\OneToMany(targetEntity=Evenement::class, mappedBy="nom_client")
     */
    private $evenements;

    /**
     * @ORM\OneToMany(targetEntity=Reservationeven::class, mappedBy="nom_client")
     */
    private $reservationevens;

    public function __construct()
    {
        $this->evenement = new ArrayCollection();
        $this->postLikes = new ArrayCollection();
        $this->evenements = new ArrayCollection();
        $this->reservationevens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $Id): self
    {
        $this->Id = $Id;

        return $this;
    }

    public function getNomUser(): ?string
    {
        return $this->nomUser;
    }

    public function setNomUser(string $nomUser): self
    {
        $this->nomUser = $nomUser;

        return $this;
    }

    public function getPrenomUser(): ?string
    {
        return $this->prenomUser;
    }

    public function setPrenomUser(string $prenomUser): self
    {
        $this->prenomUser = $prenomUser;

        return $this;
    }

    /**
     * @return Collection|Evenement[]
     */
    public function getEvenement(): Collection
    {
        return $this->evenement;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenement->contains($evenement)) {
            $this->evenement[] = $evenement;
            $evenement->setEven($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenement->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getEven() === $this) {
                $evenement->setEven(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PostLike[]
     */
    public function getPostLikes(): Collection
    {
        return $this->postLikes;
    }

    public function addPostLike(PostLike $postLike): self
    {
        if (!$this->postLikes->contains($postLike)) {
            $this->postLikes[] = $postLike;
            $postLike->setNomClient($this);
        }

        return $this;
    }

    public function removePostLike(PostLike $postLike): self
    {
        if ($this->postLikes->removeElement($postLike)) {
            // set the owning side to null (unless already changed)
            if ($postLike->getNomClient() === $this) {
                $postLike->setNomClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Evenement[]
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    /**
     * @return Collection|Reservationeven[]
     */
    public function getReservationevens(): Collection
    {
        return $this->reservationevens;
    }

    public function addReservationeven(Reservationeven $reservationeven): self
    {
        if (!$this->reservationevens->contains($reservationeven)) {
            $this->reservationevens[] = $reservationeven;
            $reservationeven->setNomClient($this);
        }

        return $this;
    }

    public function removeReservationeven(Reservationeven $reservationeven): self
    {
        if ($this->reservationevens->removeElement($reservationeven)) {
            // set the owning side to null (unless already changed)
            if ($reservationeven->getNomClient() === $this) {
                $reservationeven->setNomClient(null);
            }
        }

        return $this;
    }
}
