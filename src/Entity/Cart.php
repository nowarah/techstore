<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Service\NumberFormatter;

#[ORM\Entity(repositoryClass: CartRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $sessionId = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: CartItem::class, cascade: ['persist', 'remove'])]
    private Collection $items;

    #[ORM\ManyToOne]
    private ?User $user = null;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }
    public function setSessionId(string $sessionId): static
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function addItem(CartItem $item): static
    {
        foreach ($this->items as $existingItem) {
            if ($existingItem->getProduct() === $item->getProduct()) {
                $existingItem->setQuantity($existingItem->getQuantity() + $item->getQuantity());
                return $this;
            }
        }
        $item->setCart($this);
        $this->items->add($item);
        return $this;
    }

    public function getTotal(): int
    {
        return array_sum(
            $this->items->map(fn($item) => $item->getProduct()->getPrice() * $item->getQuantity())->toArray()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'items' => array_values($this->items->map(fn($item) => [
            'id' => $item->getId(),
            'product' => $item->getProduct()->toArray(),
            'quantity' => $item->getQuantity(),
            'subtotal' => $item->getProduct()->getPrice() * $item->getQuantity(),
            'subtotalFormatted' => NumberFormatter::format($item->getProduct()->getPrice() * $item->getQuantity()),
            ])->toArray()),
            'total' => $this->getTotal(),
            'totalFormatted' => NumberFormatter::format($this->getTotal()),
        ];
    }

}
