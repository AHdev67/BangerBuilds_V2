<?php

namespace App\Entity;

use App\Repository\BuildComponentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuildComponentRepository::class)]
class BuildComponent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'buildComponents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $component = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'buildComponents')]
    private ?Build $relatedBuild = null;

    public function getId(): ?int
    {
        return $this->id;
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
