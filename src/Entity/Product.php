<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Vous n'avez pas les droits pour cette action."
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Vous n'avez pas les droits pour cette action."
        ),
        new Put(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Vous n'avez pas les droits pour cette action."
        )
    ],
    normalizationContext: ['groups' => ['product:read']],
    denormalizationContext: ['groups' => ['product:write']],
)]

class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Veuillez renseigner le nom de l\'oeuvre.')]
    #[Groups(['product:read', 'product:write', 'order:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Veuillez renseigner une description.')]
    #[Assert\Length(
        min: 2,
        max: 500,
        minMessage: 'Veuillez renseigner une description d\'au moins {{ limit }} caratères.',
        maxMessage: 'Veuillez renseigner une description avec moins de {{ limit }} caratères'
    )]
    #[Groups(['product:read', 'product:write'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: 'Veuillez indiquer la quantité en stock.')]
    #[Assert\PositiveOrZero(message: 'La quantité doit être supérieur ou égale à zéro.')]
    #[Groups(['product:read', 'product:write', 'order:read'])]
    private ?int $quantity = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Veuillez renseigner le prix de l\'oeuvre.')]
    #[Groups(['product:read', 'product:write', 'order:read'])]
    private ?float $price = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['product:read'])]
    private ?string $slug = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'products')]
    #[Groups(['product:read', 'product:write'])]
    private Collection $categories;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Comment::class)]
    #[Groups(['product:read'])]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Image::class)]
    #[Groups(['product:read', 'product:write', 'order:read'])]
    private Collection $images;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['product:read', 'product:write', 'order:read'])]
    private ?string $discount = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Order $orderProduct = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }


    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setProduct($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getProduct() === $this) {
                $comment->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }

    public function getDiscount(): ?string
    {
        return $this->discount;
    }

    public function setDiscount(?string $discount): static
    {
        $this->discount = $discount;

        return $this;
    }

    public function getOrderProduct(): ?Order
    {
        return $this->orderProduct;
    }

    public function setOrderProduct(?Order $orderProduct): static
    {
        $this->orderProduct = $orderProduct;

        return $this;
    }
}
