<?php

namespace App\Entity;

use App\Repository\MessagePersonnaliseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessagePersonnaliseRepository::class)
 */
class MessagePersonnalise
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $titre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $contenu;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="messages")
     */
    private $messages_user;

    public function __construct()
    {
        $this->messages_user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(?string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getMessagesUser(): Collection
    {
        return $this->messages_user;
    }

    public function addMessagesUser(User $messagesUser): self
    {
        if (!$this->messages_user->contains($messagesUser)) {
            $this->messages_user[] = $messagesUser;
            $messagesUser->addMessage($this);
        }

        return $this;
    }

    public function removeMessagesUser(User $messagesUser): self
    {
        if ($this->messages_user->contains($messagesUser)) {
            $this->messages_user->removeElement($messagesUser);
            $messagesUser->removeMessage($this);
        }

        return $this;
    }
}
