<?php

namespace App\Entity;

use App\Repository\GalleryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GalleryRepository::class)]
class Gallery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $PokeApiId = null;

    #[ORM\Column(length: 180)]
    private ?string $name = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $spriteUrl = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $types = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPokeApiId(): ?int
    {
        return $this->PokeApiId;
    }

    public function setPokeApiId(int $PokeApiId): static
    {
        $this->PokeApiId = $PokeApiId;

        return $this;
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

    public function getSpriteUrl(): ?string
    {
        return $this->spriteUrl;
    }

    public function setSpriteUrl(?string $spriteUrl): static
    {
        $this->spriteUrl = $spriteUrl;

        return $this;
    }

    public function getTypes(): ?string
    {
        return $this->types;
    }

    public function setTypes(?string $types): static
    {
        $this->types = $types;

        return $this;
    }
}
