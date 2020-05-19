<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Program", mappedBy="category")
     */
    //mappedBy="category" fait référence à l'attribut $category de la classe Program.
    private $programs; // Ajoute un attribut $programs (au pluriel) et son annotation

    /* Ajout du constructeur avec, dedans, l'initialisation de l'attribut $programs à un ArrayCollection.
    L'attribut $programs contient toutes les instances de la classe Program représentant chaque tuple de la base de données,
    qui sont stockées dans ArrayCollection */
    public function __construct()
    {
        $this->programs = new ArrayCollection();
    }

    /**
     * @return Collection|Program[]
     */
    //Ajout du getter de cet attribut
    public function getPrograms(): Collection
    {
        return $this->programs;
    }

    /**
     * param Program $program
     * @return Category
     */
    //Ajout d’une nouvelle méthode pour associer une nouvelle série à une catégorie
    public function addProgram(Program $program): self
    {
        if (!$this->programs->contains($program)) {
            $this->programs[] = $program;
            $program->setCategory($this);
        }

        return $this;
    }

    /**
     * @param Program $program
     * @return Category
     */
    //Ajout d’une nouvelle méthode pour supprimer l’association d’une série
    public function removeProgram(Program $program): self
    {
        if ($this->programs->contains($program)) {
            $this->programs->removeElement($program);
            // set the owning side to null (unless already changed)
            if ($program->getCategory() === $this) {
                $program->setCategory(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
