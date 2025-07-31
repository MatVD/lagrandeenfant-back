<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_USER') and (object.getCustomer() == user or is_granted('ROLE_ADMIN'))",
            securityMessage: "Vous ne pouvez voir que vos propres commandes."
        ),
        new GetCollection(
            security: "is_granted('ROLE_USER')",
            securityMessage: "Vous devez être connecté pour voir les commandes."
        ),
        new Post(
            security: "is_granted('ROLE_USER')",
            securityMessage: "Vous devez être connecté pour passer commande."
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les administrateurs peuvent modifier les commandes."
        )
    ],
    normalizationContext: ['groups' => ['order:read']],
    denormalizationContext: ['groups' => ['order:write']]
)]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['order:read', 'product:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La date de création ne peut être vide.')]
    #[Groups(['order:read', 'product:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]

    private ?\DateTimeImmutable $shippingDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Choice(
        choices: ['En cours de traitement', 'Envoyée', 'Reçue'],
        message: 'Le statut de la Ordere doit faire partie des trois états suivants : {{ choices }}. {{ value }} n\'en fait pas partie'
    )]
    #[Groups(['order:read', 'product:read'])]
    private ?string $OrderStatus = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Choice(
        choices: ['Non payée', 'Payée'],
        message: 'Le statut de la Ordere doit faire partie des deux états suivants : {{ choices }}. {{ value }} n\'en fait pas partie'
    )]
    #[Groups(['order:read', 'product:read'])]
    private ?string $paymentStatus = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['order:read', 'product:read'])]
    private ?string $shippingNumber = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['order:read', 'product:read'])]
    private ?float $discount = null;

    #[ORM\Column]
    #[Groups(['order:read', 'product:read'])]
    private ?int $quantityByProduct = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La quantité totale ne peut être vide.')]
    #[Groups(['order:read', 'product:read'])]
    private ?int $totalQuantity = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le prix total de la Ordere ne peut être vide.')]
    #[Groups(['order:read', 'product:read'])]
    private ?float $totalPrice = null;

    #[ORM\ManyToOne(inversedBy: 'Orders')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Veuillez renseigner le client qui à fait l\'achat.')]
    #[Groups(['order:read', 'product:read'])]
    private ?User $customer = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(mappedBy: 'orderProduct', targetEntity: Product::class)]
    #[Groups(['order:read'])]
    private Collection $products;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->OrderStatus = "En cours de traitement";
        $this->paymentStatus = "Non payée";
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getShippingDate(): ?\DateTimeImmutable
    {
        return $this->shippingDate;
    }

    public function setShippingDate(?\DateTimeImmutable $shippingDate): static
    {
        $this->shippingDate = $shippingDate;

        return $this;
    }

    public function getOrderStatus(): ?string
    {
        return $this->OrderStatus;
    }

    public function setOrderStatus(?string $OrderStatus): static
    {
        $this->OrderStatus = $OrderStatus;

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(?string $paymentStatus): static
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    public function getShippingNumber(): ?string
    {
        return $this->shippingNumber;
    }

    public function setShippingNumber(?string $shippingNumber): static
    {
        $this->shippingNumber = $shippingNumber;

        return $this;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setDiscount(?float $discount): static
    {
        $this->discount = $discount;

        return $this;
    }

    public function getQuantityByProduct(): ?int
    {
        return $this->quantityByProduct;
    }

    public function setQuantityByProduct(int $quantityByProduct): static
    {
        $this->quantityByProduct = $quantityByProduct;

        return $this;
    }

    public function getTotalQuantity(): ?int
    {
        return $this->totalQuantity;
    }

    public function setTotalQuantity(int $totalQuantity): static
    {
        $this->totalQuantity = $totalQuantity;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setOrderProduct($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getOrderProduct() === $this) {
                $product->setOrderProduct(null);
            }
        }

        return $this;
    }
}
