<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Il y a déjà un compte avec cet email.')]
#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_USER') and (object == user or is_granted('ROLE_ADMIN'))",
            securityMessage: "Vous ne pouvez voir que votre profil."
        ),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les administrateurs peuvent lister les utilisateurs."
        ),
        new Post(
            security: "is_granted('PUBLIC_ACCESS')",
        ),
        new Patch(
            security: "is_granted('ROLE_USER') and (object == user or is_granted('ROLE_ADMIN'))",
            securityMessage: "Vous ne pouvez modifier que votre profil."
        )
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Veuillez renseigner l\'email.')]
    #[Groups(['user:read', 'user:write', 'order:read'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['user:read'])] // Seulement en lecture, pas d'écriture directe
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: 'Un mot de passe est obligatoire.')]
    #[Assert\Length(min: 10, minMessage: 'Le mot de passe doit faire au moins {{ limit }} caractères.')]
    #[Assert\Regex(
        '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@+$^%!§:\'<\->¨()%#"=*;çé€?$\/&])[A-Za-z\d@+$^%!§:\'<\->¨()%#"=*;çé€?$\/&]{12,}$/',
        message: 'Le mot de passe doit contenir des minuscules, majuscules, chiffres et caractères spéciaux (@-/&#?)'
    )]
    #[Groups(['user:write'])] // Mot de passe jamais exposé en lecture
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Veuillez renseigner votre prénom.')]
    #[Groups(['user:read', 'user:write'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Veuillez renseigner votre nom.')]
    #[Groups(['user:read', 'user:write'])]
    private ?string $lastname = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['user:read'])]
    private ?\DateTimeInterface $registrationDate = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Order::class)]
    private Collection $Orders;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $shippingInfos = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    public function __construct()
    {
        $this->registrationDate = new \DateTime;
        $this->comments = new ArrayCollection();
        $this->Orders = new ArrayCollection();
        $this->roles = ['ROLE_USER'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): static
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function __toString(): string
    {
        return $this->firstname . ' ' . $this->lastname;
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
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->Orders;
    }

    public function addOrder(Order $Order): static
    {
        if (!$this->Orders->contains($Order)) {
            $this->Orders->add($Order);
            $Order->setCustomer($this);
        }

        return $this;
    }

    public function removeOrder(Order $Order): static
    {
        if ($this->Orders->removeElement($Order)) {
            // set the owning side to null (unless already changed)
            if ($Order->getCustomer() === $this) {
                $Order->setCustomer(null);
            }
        }

        return $this;
    }

    public function getShippingInfos(): ?string
    {
        return $this->shippingInfos;
    }

    public function setShippingInfos(?string $shippingInfos): static
    {
        $this->shippingInfos = $shippingInfos;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    #[Groups(['product:read'])]
    function getFullName(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }
}
