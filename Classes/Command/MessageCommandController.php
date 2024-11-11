<?php

declare(strict_types=1);

namespace Sitegeist\GrapeVine\Command;

use Neos\Flow\Cli\CommandController;
use Neos\Flow\Persistence\Doctrine\PersistenceManager;
use Neos\Flow\Security\Account;
use Neos\Flow\Security\AccountRepository;
use Neos\Flow\Security\Policy\PolicyService;
use Neos\Neos\Domain\Model\User;
use Neos\Party\Domain\Model\AbstractParty;
use Neos\Party\Domain\Repository\PartyRepository;
use Sitegeist\GrapeVine\Domain\Model\Message;
use Sitegeist\GrapeVine\Domain\Model\Notification;
use Sitegeist\GrapeVine\Domain\Repository\MessageRepository;
use Sitegeist\GrapeVine\Domain\Repository\NotificationRepository;

class MessageCommandController extends CommandController
{
    protected function __construct(
        private readonly MessageRepository $messageRepository,
        private readonly NotificationRepository $notificationRepository,
        private readonly PolicyService $policyService,
        private readonly AccountRepository $accountRepository,
        private readonly PersistenceManager $persistenceManager
    ) {
        parent::__construct();
    }

    /**
     * @phpstan-param string[] $recipient
     *
     * @param string $title
     * @param string $text
     * @param string $role
     */
    public function addCommand(string $title, string $text, string $role = 'Neos.Neos:Editor', bool $notify = true): void
    {
        $roleObject = $this->policyService->getRole($role);

        $message = new Message();
        $message->setTitle($title);
        $message->setText($text);
        $message->setRecipientRoleIdentifier($roleObject->getIdentifier());
        $message->setDate(new \DateTimeImmutable());

        $this->messageRepository->add($message);

        if ($notify) {
            /**
             * @var Account[] $accountsToNotify
             */
            $accountsToNotify = [];

            /**
             * @var Account[] $accounts
             * @phpstan-ignore method.notFound
             */
            $accounts = $this->accountRepository->findByRoleIdentifiers($roleObject->getIdentifier());
            foreach ($accounts as $account) {
                $id = $this->persistenceManager->getIdentifierByObject($account);
                $accountsToNotify[$id] = $account;
            }

            foreach ($accountsToNotify as $account) {
                $notification = new Notification();
                $notification->setAccount($account);
                $notification->setMessage($message);
                $this->notificationRepository->add($notification);
            }
        }
    }

    public function listCommand(): void
    {
        $messages = $this->messageRepository->findAll();
        $this->output->outputTable(
            array_map(
                fn(Message $message) => [
                    $message->getTitle(),
                    (strlen($message->getText()) > 20)
                        ? substr($message->getText(), 0, 20) . ' ...'
                        : $message->getText(),
                    $message->getRecipientRoleIdentifier(),
                    $message->getDate()->format('Y-m-d H:i:s'),
                ],
                iterator_to_array($messages)
            ),
            ['Title', 'Text', 'Recipient role', 'Date']
        );
    }

    /**
     * @param bool $yes If set no confirmation is required
     */
    public function pruneCommand(bool $yes = false): void
    {
        if ($yes) {
            $confirm = $yes;
        } else {
            $confirm = $this->output->askConfirmation('Are you sure?');
        }
        if ($confirm) {
            $count = $this->messageRepository->countAll();
            $this->messageRepository->removeAll();
            $this->outputLine('Removed %d messages', [$count]);
        }
    }
}
