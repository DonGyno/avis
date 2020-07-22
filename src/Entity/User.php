<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=90, nullable=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=90, nullable=true)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_creation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $derniere_connexion;

    /**
     * @ORM\OneToMany(targetEntity=FicheProspection::class, mappedBy="responsable_fiche_prospection")
     */
    private $fiches_prospection;

    public function __construct()
    {
        $this->fiches_prospection = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(?\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDerniereConnexion(): ?\DateTimeInterface
    {
        return $this->derniere_connexion;
    }

    public function setDerniereConnexion(?\DateTimeInterface $derniere_connexion): self
    {
        $this->derniere_connexion = $derniere_connexion;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(string $password):self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        return array_unique(array_merge(['ROLE_USER'],$this->roles));
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function resetRoles()
    {
        $this->roles = [];
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * @return Collection|FicheProspection[]
     */
    public function getFichesProspection(): Collection
    {
        return $this->fiches_prospection;
    }

    public function addFichesProspection(FicheProspection $fichesProspection): self
    {
        if (!$this->fiches_prospection->contains($fichesProspection)) {
            $this->fiches_prospection[] = $fichesProspection;
            $fichesProspection->setResponsableFicheProspection($this);
        }

        return $this;
    }

    public function removeFichesProspection(FicheProspection $fichesProspection): self
    {
        if ($this->fiches_prospection->contains($fichesProspection)) {
            $this->fiches_prospection->removeElement($fichesProspection);
            // set the owning side to null (unless already changed)
            if ($fichesProspection->getResponsableFicheProspection() === $this) {
                $fichesProspection->setResponsableFicheProspection(null);
            }
        }

        return $this;
    }
}
