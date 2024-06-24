<?php

namespace App\Entity;

use App\Repository\BuildRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuildRepository::class)]
class Build
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'builds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column]
    private ?float $total = null;

    /**
     * @var Collection<int, BuildComponent>
     */
    #[ORM\OneToMany(targetEntity: BuildComponent::class, mappedBy: 'relatedBuild')]
    private Collection $buildComponents;

    public function __construct()
    {
        $this->buildComponents = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection<int, BuildComponent>
     */
    public function getBuildComponents(): Collection
    {
        return $this->buildComponents;
    }

    public function addBuildComponent(BuildComponent $buildComponent): static
    {
        if (!$this->buildComponents->contains($buildComponent)) {
            $this->buildComponents->add($buildComponent);
            $buildComponent->setRelatedBuild($this);
        }

        return $this;
    }

    public function removeBuildComponent(BuildComponent $buildComponent): static
    {
        if ($this->buildComponents->removeElement($buildComponent)) {
            // set the owning side to null (unless already changed)
            if ($buildComponent->getRelatedBuild() === $this) {
                $buildComponent->setRelatedBuild(null);
            }
        }

        return $this;
    }
}
