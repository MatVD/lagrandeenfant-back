<?php

namespace App\Entity;

use App\Repository\CommandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommandRepository::class)]
#[ApiResource]
class Command
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La date de création ne peut être vide.')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $shippingDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Choice(
        choices: ['En cours de traitement', 'Envoyée', 'Reçue'],
        message: 'Le statut de la commande doit faire partie des trois états suivants : {{ choices }}. {{ value }} n\'en fait pas partie'
    )]
    private ?string $commandStatus = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Choice(
        choices: ['Non payée', 'Payée'],
        message: 'Le statut de la commande doit faire partie des deux états suivants : {{ choices }}. {{ value }} n\'en fait pas partie'
    )]
    private ?string $paymentStatus = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shippingNumber = null;

    #[ORM\Column(nullable: true)]
    private ?float $discount = null;

    #[ORM\Column]
    private ?int $quantityByProduct = null;

    #[ORM\Column]
    private ?int $totalQuantity = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le prix total de la commande ne peut être vide.')]
    private ?float $totalPrice = null;

    #[ORM\ManyToOne(inversedBy: 'commands')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Veuillez renseigner le client qui à fait l\'achat.')]
    private ?User $customer = null;

    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'commands')]
    #[Assert\NotBlank(message: 'Veuillez renseigner l\'oeuvre qui a été acheté.')]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->commandStatus = "Non payée";
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

    public function getCommandStatus(): ?string
    {
        return $this->commandStatus;
    }

    public function setCommandStatus(?string $commandStatus): static
    {
        $this->commandStatus = $commandStatus;

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
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        $this->products->removeElement($product);

        return $this;
    }
}
