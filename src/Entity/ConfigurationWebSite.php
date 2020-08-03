<?php

namespace App\Entity;

use App\Repository\ConfigurationWebSiteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConfigurationWebSiteRepository::class)
 */
class ConfigurationWebSite
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomParametre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $valueParametre;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomParametre(): ?string
    {
        return $this->nomParametre;
    }

    public function setNomParametre(?string $nomParametre): self
    {
        $this->nomParametre = $nomParametre;

        return $this;
    }

    public function getValueParametre(): ?string
    {
        return $this->valueParametre;
    }

    public function setValueParametre(?string $valueParametre): self
    {
        $this->valueParametre = $valueParametre;

        return $this;
    }
}
