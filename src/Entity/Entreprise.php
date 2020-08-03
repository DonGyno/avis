<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EntrepriseRepository::class)
 */
class Entreprise
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
    private $nom;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $raison_sociale;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adresse_rue;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $adresse_ville;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $adresse_code_postal;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telephone_fixe;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telephone_portable;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $email_contact;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $horaires;

    /**
     * @ORM\OneToMany(targetEntity=Avis::class, mappedBy="entreprise_concernee")
     */
    private $avis;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $ape;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $siren_siret;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('#[^\\pL\d]+#u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        if (function_exists('iconv'))
        {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('#[^-\w]+#', '', $text);

        if (empty($text))
        {
            return 'n-a';
        }

        return $text;
    }

    public function __construct()
    {
        $this->avis = new ArrayCollection();
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

        $this->setSlug($this->nom);

        return $this;
    }

    public function getRaisonSociale(): ?string
    {
        return $this->raison_sociale;
    }

    public function setRaisonSociale(?string $raison_sociale): self
    {
        $this->raison_sociale = $raison_sociale;

        return $this;
    }

    public function getAdresseRue(): ?string
    {
        return $this->adresse_rue;
    }

    public function setAdresseRue(?string $adresse_rue): self
    {
        $this->adresse_rue = $adresse_rue;

        return $this;
    }

    public function getAdresseVille(): ?string
    {
        return $this->adresse_ville;
    }

    public function setAdresseVille(?string $adresse_ville): self
    {
        $this->adresse_ville = $adresse_ville;

        return $this;
    }

    public function getAdresseCodePostal(): ?string
    {
        return $this->adresse_code_postal;
    }

    public function setAdresseCodePostal(?string $adresse_code_postal): self
    {
        $this->adresse_code_postal = $adresse_code_postal;

        return $this;
    }

    public function getTelephoneFixe(): ?string
    {
        return $this->telephone_fixe;
    }

    public function setTelephoneFixe(?string $telephone_fixe): self
    {
        $this->telephone_fixe = $telephone_fixe;

        return $this;
    }

    public function getTelephonePortable(): ?string
    {
        return $this->telephone_portable;
    }

    public function setTelephonePortable(?string $telephone_portable): self
    {
        $this->telephone_portable = $telephone_portable;

        return $this;
    }

    public function getEmailContact(): ?string
    {
        return $this->email_contact;
    }

    public function setEmailContact(?string $email_contact): self
    {
        $this->email_contact = $email_contact;

        return $this;
    }

    public function getHoraires(): ?string
    {
        return $this->horaires;
    }

    public function setHoraires(?string $horaires): self
    {
        $this->horaires = $horaires;

        return $this;
    }

    /**
     * @return Collection|Avis[]
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): self
    {
        if (!$this->avis->contains($avi)) {
            $this->avis[] = $avi;
            $avi->setEntrepriseConcernee($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): self
    {
        if ($this->avis->contains($avi)) {
            $this->avis->removeElement($avi);
            // set the owning side to null (unless already changed)
            if ($avi->getEntrepriseConcernee() === $this) {
                $avi->setEntrepriseConcernee(null);
            }
        }

        return $this;
    }

    public function getApe(): ?string
    {
        return $this->ape;
    }

    public function setApe(?string $ape): self
    {
        $this->ape = $ape;

        return $this;
    }

    public function getSirenSiret(): ?string
    {
        return $this->siren_siret;
    }

    public function setSirenSiret(?string $siren_siret): self
    {
        $this->siren_siret = $siren_siret;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $this->slugify($slug);

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

}
