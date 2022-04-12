<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[UniqueEntity(fields: ['pseudo'], message: 'Ce pseudo est déjà utilisé.')]
#[UniqueEntity(fields: ['mail'], message: 'Cette adresse email est déjà utilisée.')]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Regex(pattern: "/^[a-zA-Z]+$/")]
    private $nom;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Regex(pattern: "/^[a-zA-Z]+$/")]
    private $prenom;

    #[ORM\Column(type: 'string', length: 10)]
    private $telephone;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\Email(message: "Addresse email non valide.")]
    private $mail;

    #[ORM\Column(type: 'string', length: 30, unique: true)]
    #[Assert\Regex(pattern: "/^[a-zA-Z0-9]+$/")]
    private $pseudo;

    #[ORM\Column(type: 'string', length: 255)]
    private $motPasse;

    #[ORM\Column(type: 'boolean')]
    private $administrateur;

    #[ORM\Column(type: 'boolean')]
    private $actif;

    #[ORM\ManyToOne(targetEntity: Campus::class, inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false)]
    private $campus;

    #[ORM\ManyToMany(targetEntity: Sortie::class, mappedBy: 'inscrits')]
    private $sortiesInscrites;

    #[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: Sortie::class, orphanRemoval: true)]
    private $sortiesOrganisees;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $image;

    public function __construct()
    {
        $this->sortiesInscrites = new ArrayCollection();
        $this->sortiesOrganisees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->mail;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->getMotPasse();
    }

    public function setPassword(string $password): self
    {
        $this->setMotPasse($password);
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMotPasse(): ?string
    {
        return $this->motPasse;
    }

    public function setMotPasse(string $motPasse): self
    {
        $this->motPasse = $motPasse;

        return $this;
    }

    public function getAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;
        if ($administrateur)
        {
            $this->roles[] = 'ROLE_ADMIN';
        }

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesInscrites(): Collection
    {
        return $this->sortiesInscrites;
    }

    public function addSortieInscrite(Sortie $sortie): self
    {
        if (!$this->sortiesInscrites->contains($sortie)) {
            $this->sortiesInscrites[] = $sortie;
            $sortie->addInscrit($this);
        }

        return $this;
    }

    public function removeSortieInscrite(Sortie $sortie): self
    {
        if ($this->sortiesInscrites->removeElement($sortie)) {
            $sortie->removeInscrit($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sortiesOrganisees;
    }

    public function addSortieOrganisee(Sortie $sortie): self
    {
        if (!$this->sortiesOrganisees->contains($sortie)) {
            $this->sortiesOrganisees[] = $sortie;
            $sortie->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortieOrganisee(Sortie $sortie): self
    {
        if ($this->sortiesOrganisees->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getOrganisateur() === $this) {
                $sortie->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }
}
