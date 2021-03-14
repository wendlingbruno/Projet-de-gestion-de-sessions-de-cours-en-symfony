<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CategoriesRepository::class)
 * @UniqueEntity("nom")
 */
class Categories
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=Modules::class, mappedBy="categories")
     */
    private $Modules;

    public function __construct()
    {
        $this->Modules = new ArrayCollection();
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

    /**
     * @return Collection|Modules[]
     */
    public function getModules(): Collection
    {
        return $this->Modules;
    }

    public function addModule(Modules $module): self
    {
        if (!$this->Modules->contains($module)) {
            $this->Modules[] = $module;
            $module->setCategories($this);
        }

        return $this;
    }

    public function removeModule(Modules $module): self
    {
        if ($this->Modules->contains($module)) {
            $this->Modules->removeElement($module);
            // set the owning side to null (unless already changed)
            if ($module->getCategories() === $this) {
                $module->setCategories(null);
            }
        }

        return $this;
    }
}
