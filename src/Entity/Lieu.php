<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: [
        'get' => ['path' => '/lieux'],
        'post' => ['path' => '/lieux']
    ],
    itemOperations: [
        'get' => [
            'path' => '/lieux/{id}',
            'requirements' => ['id' => '\d+']
            ]
    ],
    denormalizationContext: ['groups' => ['postLieu']],
    normalizationContext: ['groups' => ['getLieu']]
)]
#[ORM\Entity(repositoryClass: LieuRepository::class)]
class Lieu
{
    #[Groups(['getLieu', 'getVille'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['getLieu', 'getVille', 'postLieu'])]
    #[ORM\Column(type: 'string', length: 100)]
    private $nom;

    #[Groups(['getLieu', 'getVille', 'postLieu'])]
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $rue;

    #[Groups(['getLieu', 'getVille', 'postLieu'])]
    #[ORM\Column(type: 'float', nullable: true)]
    private $latitude;

    #[Groups(['getLieu', 'getVille', 'postLieu'])]
    #[ORM\Column(type: 'float', nullable: true)]
    private $longitude;

    #[Groups(['getLieu', 'postLieu'])]
    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'lieux', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private $ville;

    #[Groups('getLieu')]
    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Sortie::class, orphanRemoval: true)]
    private $sorties;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorties(Sortie $sortie): self
    {
        if (!$this->sorties->contains($sortie)) {
            $this->sorties[] = $sortie;
            $sortie->setLieu($this);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): self
    {
        if ($this->sorties->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getLieu() === $this) {
                $sortie->setLieu(null);
            }
        }

        return $this;
    }
}
