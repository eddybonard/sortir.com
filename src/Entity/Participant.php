<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * @ORM\Entity(repositoryClass=ParticipantRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Participant implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Champ requis")
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Assert\Length(min=2, minMessage="Nom trop court", max=255, maxMessage="Nom trop long")
     * @Assert\NotBlank(message="Champ requis")
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @Assert\Length(min=2, minMessage="Prenom trop court", max=255, maxMessage="Prenom trop long")
     * @Assert\NotBlank(message="Champ requis")
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @Assert\NotBlank(message="Champ requis")
     * @ORM\Column(type="string", length=255)
     */
    private $telephone;

    /**
     * @ORM\Column(type="boolean")
     */
    private $administrateur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
    * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="participants")
     * @Assert\NotBlank(message="Champ requis")
    * @ORM\JoinColumn(nullable=false)
    */
    private $campus;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="organisateur")
     */
    private $organisateurSorties;

    /**
     * @ORM\ManyToMany(targetEntity=Sortie::class, inversedBy="participants")
     */
    private $participantSorties;

    /**
     * @Assert\Length(min=2, minMessage="Pseudo trop court", max=255, maxMessage="Pseudo trop long")
     * @Assert\NotBlank(message="Champ requis")
     * @ORM\Column(type="string", length=255)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $photo;

    /**
     * @ORM\OneToMany(targetEntity=Tchat::class, mappedBy="participant")
     */
    private $questionsTchat;

    /**
     *
     */
    public function __construct()
    {
        $this->organisateurSorties = new ArrayCollection();
        $this->participantSorties = new ArrayCollection();
        $this->questionsTchat = new ArrayCollection();
    }


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
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

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return string|null
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     * @return $this
     */
    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    /**
     * @param string $prenom
     * @return $this
     */
    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * @param string $telephone
     * @return $this
     */
    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    /**
     * @param bool $administrateur
     * @return $this
     */
    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getActif(): ?bool
    {
        return $this->actif;
    }

    /**
     * @param bool $actif
     * @return $this
     */
    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * @return Campus|null
     */
    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    /**
     * @param Campus|null $campus
     * @return $this
     */
    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|Sortie[]
     */
    public function getOrganisateurSorties(): \Doctrine\Common\Collections\Collection
    {
        return $this->organisateurSorties;
    }

    /**
     * @param Sortie $organisateurSorty
     * @return $this
     */
    public function addOrganisateurSorty(Sortie $organisateurSorty): self
    {
        if (!$this->organisateurSorties->contains($organisateurSorty)) {
            $this->organisateurSorties[] = $organisateurSorty;
            $organisateurSorty->setOrganisateur($this);
        }

        return $this;
    }

    /**
     * @param Sortie $organisateurSorty
     * @return $this
     */
    public function removeOrganisateurSorty(Sortie $organisateurSorty): self
    {
        if ($this->organisateurSorties->removeElement($organisateurSorty)) {
            // set the owning side to null (unless already changed)
            if ($organisateurSorty->getOrganisateur() === $this) {
                $organisateurSorty->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|Sortie[]
     */
    public function getParticipantSorties(): \Doctrine\Common\Collections\Collection
    {
        return $this->participantSorties;
    }

    /**
     * @param Sortie $participantSorty
     * @return $this
     */
    public function addParticipantSorty(Sortie $participantSorty): self
    {
        if (!$this->participantSorties->contains($participantSorty)) {
            $this->participantSorties[] = $participantSorty;
        }

        return $this;
    }

    /**
     * @param Sortie $participantSorty
     * @return $this
     */
    public function removeParticipantSorty(Sortie $participantSorty): self
    {
        $this->participantSorties->removeElement($participantSorty);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    /**
     * @param string $pseudo
     * @return $this
     */
    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * @param string|null $photo
     * @return $this
     */
    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|Tchat[]
     */
    public function getQuestionsTchat(): \Doctrine\Common\Collections\Collection
    {
        return $this->questionsTchat;
    }

    /**
     * @param Tchat $questionsTchat
     * @return $this
     */
    public function addQuestionsTchat(Tchat $questionsTchat): self
    {
        if (!$this->questionsTchat->contains($questionsTchat)) {
            $this->questionsTchat[] = $questionsTchat;
            $questionsTchat->setParticipant($this);
        }

        return $this;
    }

    /**
     * @param Tchat $questionsTchat
     * @return $this
     */
    public function removeQuestionsTchat(Tchat $questionsTchat): self
    {
        if ($this->questionsTchat->removeElement($questionsTchat)) {
            // set the owning side to null (unless already changed)
            if ($questionsTchat->getParticipant() === $this) {
                $questionsTchat->setParticipant(null);
            }
        }

        return $this;
    }




}
