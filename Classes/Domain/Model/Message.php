<?php

declare(strict_types=1);

namespace Sitegeist\GrapeVine\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Neos\Flow\Security\Account;
use Neos\Neos\Domain\Model\User;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Entity
 */
class Message
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var \DateTimeImmutable
     */
    protected $date;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     * @var array<string>
     */
    protected $recipientRoles;

    /**
     * @var Account|null
     * @ORM\Column(nullable=true)
     * @ORM\ManyToOne
     */
    protected $author;

    /**
     * @phpstan-var ArrayCollection<int, Notification>
     * @var ArrayCollection<Notification>
     * @ORM\OneToMany(mappedBy="message", cascade={"all"}, orphanRemoval=true)
     */
    protected $notifications;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string[]
     */
    public function getRecipientRoles(): array
    {
        return $this->recipientRoles;
    }

    /**
     * @param string[] $recipientRoles
     */
    public function setRecipientRoles(array $recipientRoles): void
    {
        $this->recipientRoles = $recipientRoles;
    }

    public function getAuthor(): ?Account
    {
        return $this->author;
    }

    public function setAuthor(?Account $author): void
    {
        $this->author = $author;
    }
}
