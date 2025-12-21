<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Recette;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\NoteRepository;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description1 = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private ?Recette $recette = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    private ?int $note = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription1(): ?string
    {
        return $this->description1;
    }

    public function setDescription1(?string $description1): static
    {
        $this->description1 = $description1;

        return $this;
    }

    public function getRecette(): ?Recette
    {
        return $this->recette;
    }

    public function setRecette(?Recette $recette): static
    {
        $this->recette = $recette;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): static
    {
        $this->note = $note;

        return $this;
    }
}
