<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Evenement
 *
 * @ORM\Table(name="evenement")
 * @ORM\Entity(repositoryClass=App\Repository\EvenementRepository::class)

 */
class Evenement
{
    const TYPE=[
        0=>'randonnée',
        1=>'Paddle',
        2=>'camping',
        3=>'kayak'
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @Assert\Length(
     *      min = 2,
     *      max = 100)
     * @ORM\Column(name="Titre", type="string", length=100, nullable=false)
     * @Assert\NotBlank(message="il faut saisir votre adresse")
     */
    private $titre;

    /**
      * @Assert\Length(
     *      min = 2,
     *      max = 100)
     * @var string
     * @Assert\NotBlank(message="il faut saisir votre Lieu")
     * @ORM\Column(name="Lieu", type="string", length=100, nullable=false)
     */
    private $lieu;

    /**

     * @var string
     * @Assert\NotBlank(message="il faut saisir votre Description")
     * @ORM\Column(name="Description", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      min = 2,
     *      minMessage = "Your event's description must be at least {{ limit }} characters long"
     * )
     */
    private $description;

    /**
     * @var string

     * @ORM\Column(name="Image", type="string", length=100, nullable=false)
     */
    private $image;

    /**
     * @Assert\Length(
     *      max = 2)
     *
     * @var string
     * @Assert\NotBlank(message="il faut saisir votre Type")
     * @ORM\Column(name="Type", type="string", length=100, nullable=false)
     */
    private $type;

    /**

     * @Assert\GreaterThanOrEqual(value = "today")
     * @ORM\Column(name="Date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var int
     * @Assert\NotBlank(message="il faut saisir votre Prix")
     *\Length(max=3)

     * @ORM\Column(name="Prix", type="integer", nullable=false)
     */
    private $prix;

    /**
     * @var int
     *
     * @ORM\Column(name="Etat", type="integer", nullable=false)
     */
    private $etat = '0';

    /**
     * @ORM\OneToMany(targetEntity=Reservationeven::class, mappedBy="evenement")
     */
    private $reservationeven;

    /**
     * @ORM\OneToMany(targetEntity=Reservationeven::class, mappedBy="evenement")
     */
    private $reservationevens;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbrGoing=0;

    /**
     * @ORM\Column(type="integer")
      * @Assert\NotEqualTo(value = 0)
     */
    private $nbrmaxpart;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="evenement")
     */
    private $even;

    /**
     * @ORM\OneToMany(targetEntity=PostLike::class, mappedBy="evenement")
     */
    private $likes;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="evenements")
     */
    private $nom_client;

    /**


     * @ORM\Column(type="date", nullable=true)
     */
    private $dateend;



    public function __construct()
    {
        $this->reservationeven = new ArrayCollection();
        $this->reservationevens = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }



    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|Reservationeven[]
     */
    public function getReservationeven(): Collection
    {
        return $this->reservationeven;
    }

    public function addReservationeven(Reservationeven $reservationeven): self
    {
        if (!$this->reservationeven->contains($reservationeven)) {
            $this->reservationeven[] = $reservationeven;
            $reservationeven->setEvenement($this);
        }

        return $this;
    }

    public function removeReservationeven(Reservationeven $reservationeven): self
    {
        if ($this->reservationeven->removeElement($reservationeven)) {
            // set the owning side to null (unless already changed)
            if ($reservationeven->getEvenement() === $this) {
                $reservationeven->setEvenement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Reservationeven[]
     */
    public function getReservationevens(): Collection
    {
        return $this->reservationevens;
    }


    public function __toString()
    {
        return $this->titre;

    }

    public function getNbrGoing(): ?int
    {
        return $this->nbrGoing;
    }

    public function setNbrGoing(?int $nbrGoing): self
    {
        $this->nbrGoing = $nbrGoing;

        return $this;
    }

    public function getNbrmaxpart(): ?int
    {
        return $this->nbrmaxpart;
    }

    public function setNbrmaxpart(int $nbrmaxpart): self
    {
        $this->nbrmaxpart = $nbrmaxpart;

        return $this;
    }

    public function getEven(): ?User
    {
        return $this->even;
    }

    public function setEven(?User $even): self
    {
        $this->even = $even;

        return $this;
    }

    /**
     * Permet de savoir si cet article est like par user
     * @param User $user
     * @return boolean
     */
    public function isLikedByUser(User $user) : bool
    {
        foreach ($this->likes as $like) {
            if ($like->getNomClient() === $user)
                return true;
        }
        return false;
    }

    /**
     * @return Collection|PostLike[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(PostLike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setEvenement($this);
        }

        return $this;
    }

    public function removeLike(PostLike $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getEvenement() === $this) {
                $like->setEvenement(null);
            }
        }

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

    public function getDateend(): ?\DateTimeInterface
    {
        return $this->dateend;
    }

    public function setDateend(?\DateTimeInterface $dateend): self
    {
        $this->dateend = $dateend;

        return $this;
    }


}
