<?php

declare(strict_types=1);

namespace Sitegeist\GrapeVine\Domain\Model;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Security\Account;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Notification
{
    /**
     * @var Account
     * @ORM\ManyToOne
     */
    protected $account;

    /**
     * @var Message
     * @ORM\ManyToOne
     */
    protected $message;

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function setMessage(Message $message): void
    {
        $this->message = $message;
    }
}
