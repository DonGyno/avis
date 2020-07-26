<?php

namespace App\Entity;

use App\Repository\FicheProspectionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FicheProspectionRepository::class)
 */
class FicheProspection
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $nom_entreprise;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $raison_sociale_entreprise;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $code_ape;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rue_entreprise;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $ville_entreprise;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $code_postal_entreprise;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $telephone_fixe_entreprise;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $telephone_portable_entreprise;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email_entreprise;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_derniere_modification;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="fiches_prospection")
     */
    private $responsable_fiche_prospection;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $siret_siren;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_creation;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $statut;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEntreprise(): ?string
    {
        return $this->nom_entreprise;
    }

    public function setNomEntreprise(string $nom_entreprise): self
    {
        $this->nom_entreprise = $nom_entreprise;

        return $this;
    }

    public function getRaisonSocialeEntreprise(): ?string
    {
        return $this->raison_sociale_entreprise;
    }

    public function setRaisonSocialeEntreprise(string $raison_sociale_entreprise): self
    {
        $this->raison_sociale_entreprise = $raison_sociale_entreprise;

        return $this;
    }

    public function getCodeApe(): ?string
    {
        return $this->code_ape;
    }

    public function setCodeApe(?string $code_ape): self
    {
        $this->code_ape = $code_ape;

        return $this;
    }

    public function getRueEntreprise(): ?string
    {
        return $this->rue_entreprise;
    }

    public function setRueEntreprise(?string $rue_entreprise): self
    {
        $this->rue_entreprise = $rue_entreprise;

        return $this;
    }

    public function getVilleEntreprise(): ?string
    {
        return $this->ville_entreprise;
    }

    public function setVilleEntreprise(?string $ville_entreprise): self
    {
        $this->ville_entreprise = $ville_entreprise;

        return $this;
    }

    public function getCodePostalEntreprise(): ?string
    {
        return $this->code_postal_entreprise;
    }

    public function setCodePostalEntreprise(?string $code_postal_entreprise): self
    {
        $this->code_postal_entreprise = $code_postal_entreprise;

        return $this;
    }

    public function getTelephoneFixeEntreprise(): ?string
    {
        return $this->telephone_fixe_entreprise;
    }

    public function setTelephoneFixeEntreprise(?string $telephone_fixe_entreprise): self
    {
        $this->telephone_fixe_entreprise = $telephone_fixe_entreprise;

        return $this;
    }

    public function getTelephonePortableEntreprise(): ?string
    {
        return $this->telephone_portable_entreprise;
    }

    public function setTelephonePortableEntreprise(?string $telephone_portable_entreprise): self
    {
        $this->telephone_portable_entreprise = $telephone_portable_entreprise;

        return $this;
    }

    public function getEmailEntreprise(): ?string
    {
        return $this->email_entreprise;
    }

    public function setEmailEntreprise(?string $email_entreprise): self
    {
        $this->email_entreprise = $email_entreprise;

        return $this;
    }

    public function getDateDerniereModification(): ?\DateTimeInterface
    {
        return $this->date_derniere_modification;
    }

    public function setDateDerniereModification(?\DateTimeInterface $date_derniere_modification): self
    {
        $this->date_derniere_modification = $date_derniere_modification;

        return $this;
    }

    public function getResponsableFicheProspection(): ?User
    {
        return $this->responsable_fiche_prospection;
    }

    public function setResponsableFicheProspection(?User $responsable_fiche_prospection): self
    {
        $this->responsable_fiche_prospection = $responsable_fiche_prospection;

        return $this;
    }

    public function getSiretSiren(): ?string
    {
        return $this->siret_siren;
    }

    public function setSiretSiren(?string $siret_siren): self
    {
        $this->siret_siren = $siret_siren;

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

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }
}
