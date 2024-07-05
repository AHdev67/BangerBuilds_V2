<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $label = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column]
    private ?bool $inStock = null;

    #[ORM\Column(nullable: true)]
    private ?int $restockDelay = null;

    #[ORM\Column]
    private array $specs = [];

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Category $category = null;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'reviewedProduct', orphanRemoval: true)]
    private Collection $reviews;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'product')]
    private Collection $orderItems;

    /**
     * @var Collection<int, BuildComponent>
     */
    #[ORM\OneToMany(targetEntity: BuildComponent::class, mappedBy: 'component', orphanRemoval: true)]
    private Collection $buildComponents;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description;

    public function __construct()
    {
        // $this->relatedOrders = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
        $this->buildComponents = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->label;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function isInStock(): ?bool
    {
        return $this->inStock;
    }

    public function setInStock(bool $inStock): static
    {
        $this->inStock = $inStock;

        return $this;
    }

    public function getRestockDelay(): ?int
    {
        return $this->restockDelay;
    }

    public function setRestockDelay(?int $restockDelay): static
    {
        $this->restockDelay = $restockDelay;

        return $this;
    }

    public function getSpecs(): array
    {
        return $this->specs;
    }

    public function setSpecs(array $specs): static
    {
        $this->specs = $specs;

        return $this;
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

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setReviewedProduct($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getReviewedProduct() === $this) {
                $review->setReviewedProduct(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setProduct($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getProduct() === $this) {
                $orderItem->setProduct(null);
            }
        }

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
            $buildComponent->setComponent($this);
        }

        return $this;
    }

    public function removeBuildComponent(BuildComponent $buildComponent): static
    {
        if ($this->buildComponents->removeElement($buildComponent)) {
            // set the owning side to null (unless already changed)
            if ($buildComponent->getComponent() === $this) {
                $buildComponent->setComponent(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getGlobalRating(): ?float
    {
        $totalRating = 0;
        $count = 0;

        foreach ($this->reviews as $review) {
            $totalRating += $review->getRating();
            $count++;
        }

        if ($count === 0) {
            return null;
        }

        return $totalRating / $count;
    }
}
