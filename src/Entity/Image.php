<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
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
        )
    ]
)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Veuillez renseigner le nom de l\'image.')]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?BlogPost $blogPost = null;

    #[ORM\Column(length: 255)]
    #[Groups(['products:read'])]
    private ?string $imgUrl = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getBlogPost(): ?BlogPost
    {
        return $this->blogPost;
    }

    public function setBlogPost(?BlogPost $blogPost): static
    {
        $this->blogPost = $blogPost;

        return $this;
    }

    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    public function setImgUrl(string $imgUrl): static
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }
}
