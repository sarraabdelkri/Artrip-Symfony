<?php

namespace App\Entity;

use App\Repository\PostLikeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostLikeRepository::class)
 */
class PostLike
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="postLikes")
     */
    private $nom_client;

    /**
     * @ORM\ManyToOne(targetEntity=Evenement::class, inversedBy="postLikes")
     */
    private $Evenement;

    /**
     * @ORM\ManyToOne(targetEntity=Evenement::class, inversedBy="likes")
     */
    private $evenement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $Id): self
    {
        $this->Id = $Id;

        return $this;
    }

    public function getNomClient(): ?User
    {
        return $this->nom_client;
    }

    public function setNomClient(?User $nom_client): self
    {
        $this->nom_client = $nom_client;

        return $this;
    }

    public function getEvenement(): ?Evenement
    {
        return $this->Evenement;
    }

    public function setEvenement(?Evenement $Evenement): self
    {
        $this->Evenement = $Evenement;

        return $this;
    }
}
