<?php

namespace App\Entity;

use App\Repository\BuildComponentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuildComponentRepository::class)]
class BuildComponent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'buildComponents')]
    private ?Category $category = null;
    
    #[ORM\ManyToOne(inversedBy: 'buildComponents')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Product $component = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'buildComponents')]
    private ?Build $relatedBuild = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getComponent(): ?Product
    {
        return $this->component;
    }

    public function setComponent(?Product $component): static
    {
        $this->component = $component;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getRelatedBuild(): ?Build
    {
        return $this->relatedBuild;
    }

    public function setRelatedBuild(?Build $relatedBuild): static
    {
        $this->relatedBuild = $relatedBuild;

        return $this;
    }
}
