<?php

namespace App\Entity;

use App\Entity\Note;
use App\Entity\Categorie;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RecetteRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: RecetteRepository::class)]
class Recette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 255)]
    private ?string $fiche = null;

    #[ORM\OneToMany(mappedBy: 'recette', targetEntity: Note::class, orphanRemoval: true)]
    private Collection $notes;

    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'recettes')]
    private Collection $categories;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description3 = null;

    #[ORM\Column(length: 255)]
    private ?string $avatar2 = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;



    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getFiche(): ?string
    {
        return $this->fiche;
    }

    public function setFiche(string $fiche): static
    {
        $this->fiche = $fiche;
        return $this;
    }

    /** @return Collection<int, Note> */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setRecette($this);
        }
        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            if ($note->getRecette() === $this) {
                $note->setRecette(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, Categorie> */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }
        return $this;
    }

    public function removeCategory(Categorie $category): static
    {
        $this->categories->removeElement($category);
        return $this;
    }

    public function getDescription3(): ?string
    {
        return $this->description3;
    }

    public function setDescription3(string $description3): static
    {
        $this->description3 = $description3;

        return $this;
    }

    public function getAvatar2(): ?string
    {
        return $this->avatar2;
    }

    public function setAvatar2(string $avatar2): static
    {
        $this->avatar2 = $avatar2;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

}
