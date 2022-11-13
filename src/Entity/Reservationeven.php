<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reservationeven
 *
 * @ORM\Table(name="reservationeven", indexes={@ORM\Index(name="fk_r1", columns={"idEvenement"})})
 * @ORM\Entity(repositoryClass=App\Repository\ReservationevenRepository::class)
 */
class Reservationeven
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
     * @Assert\NotBlank(message="il faut saisir votre Nom")
     * @ORM\Column(name="nompartici", type="string", length=100, nullable=false)
     */
    private $nompartici;

    /**
     * @var string
     * @Assert\NotBlank(message="il faut saisir votre Nom")
     * @ORM\Column(name="type", type="string", length=100, nullable=false)
     */
    private $type;

    /**
     * @var string
     * @Assert\NotBlank(message="il faut saisir votre Lieu")
     * @ORM\Column(name="lieu", type="string", length=100, nullable=false)
     */
    private $lieu;

    /**
     * @var int
     * @Assert\NotBlank(message="il faut saisir votre Id participon")
     * @ORM\Column(name="idParticipon", type="integer", nullable=false)
     */
    private $idparticipon;

    /**
     * @var string
     * @Assert\NotBlank(message="il faut saisir votre evenment choisi ")
     * @ORM\Column(name="nomEvenement", type="string", length=100, nullable=false)
     */
    private $nomevenement;

    /**
     * @var \DateTime



     * @ORM\Column(name="Date", type="date", nullable=false)
     */
    private $date;

  

    /**
     * @var \Evenement

     * @ORM\ManyToOne(targetEntity="Evenement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idEvenement", referencedColumnName="id")
     * })
     */
    private $idevenement;

    /**
     * @ORM\ManyToOne(targetEntity=Evenement::class, inversedBy="reservationevens")
     * @ORM\JoinColumn(nullable=false)
     *   @Assert\NotBlank(message="il faut saisir l'idevenement")
     */
    private $evenement;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reservationevens")
     */
    private $nom_client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNompartici(): ?string
    {
        return $this->nompartici;
    }

    public function setNompartici(string $nompartici): self
    {
        $this->nompartici = $nompartici;

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

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getIdparticipon(): ?int
    {
        return $this->idparticipon;
    }

    public function setIdparticipon(int $idparticipon): self
    {
        $this->idparticipon = $idparticipon;

        return $this;
    }

    public function getNomevenement(): ?string
    {
        return $this->nomevenement;
    }

    public function setNomevenement(string $nomevenement): self
    {
        $this->nomevenement = $nomevenement;

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



    public function getIdevenement(): ?Evenement
    {
        return $this->idevenement;
    }

    public function setIdevenement(?Evenement $idevenement): self
    {
        $this->idevenement = $idevenement;

        return $this;
    }
    public function __toString()
    {
        return $this->nom;

    }

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): self
    {
        $this->evenement = $evenement;

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
    
}
