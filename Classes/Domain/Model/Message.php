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
     * @var string
     */
    protected $recipientRoleIdentifier;

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

    public function getRecipientRoleIdentifier(): string
    {
        return $this->recipientRoleIdentifier;
    }

    public function setRecipientRoleIdentifier(string $recipientRoleIdentifier): void
    {
        $this->recipientRoleIdentifier = $recipientRoleIdentifier;
    }

    public function getAuthor(): ?Account
    {
        return $this->author;
    }

    public function setAuthor(?Account $author): void
    {
        $this->author = $author;
    }

    /**
     * @return ArrayCollection<int, Notification>
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * @param ArrayCollection<int, Notification> $notifications
     */
    public function setNotifications($notifications): void
    {
        $this->notifications = $notifications;
    }
}
