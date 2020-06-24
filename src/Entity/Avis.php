<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AvisRepository::class)
 */
class Avis
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nom_destinataire;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $prenom_destinataire;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $email_destinataire;

    /**
     * @ORM\ManyToOne(targetEntity=Entreprise::class, inversedBy="avis")
     * @ORM\JoinColumn(nullable=false)
     */
    private $entreprise_concernee;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_envoi_enquete;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_reponse_enquete;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $statut_avis;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $ip_destinataire;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token_security;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $note_prestation_realisee;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $note_professionnalisme_entreprise;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $note_satisfaction_globale;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $recommander_commentaire_a_entreprise;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $temoignage_video;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telephone_destinataire;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nb_relance;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_derniere_relance;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomDestinataire(): ?string
    {
        return $this->nom_destinataire;
    }

    public function setNomDestinataire(string $nom_destinataire): self
    {
        $this->nom_destinataire = $nom_destinataire;

        return $this;
    }

    public function getPrenomDestinataire(): ?string
    {
        return $this->prenom_destinataire;
    }

    public function setPrenomDestinataire(string $prenom_destinataire): self
    {
        $this->prenom_destinataire = $prenom_destinataire;

        return $this;
    }

    public function getEmailDestinataire(): ?string
    {
        return $this->email_destinataire;
    }

    public function setEmailDestinataire(string $email_destinataire): self
    {
        $this->email_destinataire = $email_destinataire;

        return $this;
    }

    public function getEntrepriseConcernee(): ?Entreprise
    {
        return $this->entreprise_concernee;
    }

    public function setEntrepriseConcernee(?Entreprise $entreprise_concernee): self
    {
        $this->entreprise_concernee = $entreprise_concernee;

        return $this;
    }

    public function getDateEnvoiEnquete(): ?\DateTimeInterface
    {
        return $this->date_envoi_enquete;
    }

    public function setDateEnvoiEnquete(?\DateTimeInterface $date_envoi_enquete): self
    {
        $this->date_envoi_enquete = $date_envoi_enquete;

        return $this;
    }

    public function getDateReponseEnquete(): ?\DateTimeInterface
    {
        return $this->date_reponse_enquete;
    }

    public function setDateReponseEnquete(?\DateTimeInterface $date_reponse_enquete): self
    {
        $this->date_reponse_enquete = $date_reponse_enquete;

        return $this;
    }

    public function getStatutAvis(): ?string
    {
        return $this->statut_avis;
    }

    public function setStatutAvis(?string $statut_avis): self
    {
        $this->statut_avis = $statut_avis;

        return $this;
    }

    public function getIpDestinataire(): ?string
    {
        return $this->ip_destinataire;
    }

    public function setIpDestinataire(?string $ip_destinataire): self
    {
        $this->ip_destinataire = $ip_destinataire;

        return $this;
    }

    public function getTokenSecurity(): ?string
    {
        return $this->token_security;
    }

    public function setTokenSecurity(?string $token_security): self
    {
        $this->token_security = $token_security;

        return $this;
    }

    public function getNotePrestationRealisee(): ?int
    {
        return $this->note_prestation_realisee;
    }

    public function setNotePrestationRealisee(?int $note_prestation_realisee): self
    {
        $this->note_prestation_realisee = $note_prestation_realisee;

        return $this;
    }

    public function getNoteProfessionnalismeEntreprise(): ?int
    {
        return $this->note_professionnalisme_entreprise;
    }

    public function setNoteProfessionnalismeEntreprise(?int $note_professionnalisme_entreprise): self
    {
        $this->note_professionnalisme_entreprise = $note_professionnalisme_entreprise;

        return $this;
    }

    public function getNoteSatisfactionGlobale(): ?int
    {
        return $this->note_satisfaction_globale;
    }

    public function setNoteSatisfactionGlobale(?int $note_satisfaction_globale): self
    {
        $this->note_satisfaction_globale = $note_satisfaction_globale;

        return $this;
    }

    public function getRecommanderCommentaireAEntreprise(): ?string
    {
        return $this->recommander_commentaire_a_entreprise;
    }

    public function setRecommanderCommentaireAEntreprise(?string $recommander_commentaire_a_entreprise): self
    {
        $this->recommander_commentaire_a_entreprise = $recommander_commentaire_a_entreprise;

        return $this;
    }

    public function getTemoignageVideo(): ?string
    {
        return $this->temoignage_video;
    }

    public function setTemoignageVideo(?string $temoignage_video): self
    {
        $this->temoignage_video = $temoignage_video;

        return $this;
    }

    public function getTelephoneDestinataire(): ?string
    {
        return $this->telephone_destinataire;
    }

    public function setTelephoneDestinataire(?string $telephone_destinataire): self
    {
        $this->telephone_destinataire = $telephone_destinataire;

        return $this;
    }

    public function getNbRelance(): ?int
    {
        return $this->nb_relance;
    }

    public function setNbRelance(?int $nb_relance): self
    {
        $this->nb_relance = $nb_relance;

        return $this;
    }

    public function getDateDerniereRelance(): ?\DateTimeInterface
    {
        return $this->date_derniere_relance;
    }

    public function setDateDerniereRelance(?\DateTimeInterface $date_derniere_relance): self
    {
        $this->date_derniere_relance = $date_derniere_relance;

        return $this;
    }
}
