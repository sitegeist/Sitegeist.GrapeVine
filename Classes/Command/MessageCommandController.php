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
     * @param array $recipient
     */
    public function addCommand(string $title, string $text, array $recipient = [], bool $notify = false): void
    {

        $roles = array_map(
            fn(string $roleId) => $this->policyService->getRole($roleId),
            $recipient
        );

        $message = new Message();
        $message->setTitle($title);
        $message->setText($text);
        $message->setRecipientRoles($recipient);
        $message->setDate(new \DateTimeImmutable());

        $this->messageRepository->add($message);

        if ($notify) {
            /**
             * @var Account[] $accountsToNotify
             */
            $accountsToNotify = [];
            foreach ($roles as $role) {
                /**
                 * @var Account[] $accounts
                 * @phpstan-ignore method.notFound
                 */
                $accounts = $this->accountRepository->findByRoleIdentifiers($role->getIdentifier());
                foreach ($accounts as $account) {
                    $id = $this->persistenceManager->getIdentifierByObject($account);
                    $accountsToNotify[$id] = $account;
                }
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
                    implode(', ', $message->getRecipientRoles()),
                    $message->getDate()->format('Y-m-d H:i:s'),
                ],
                iterator_to_array($messages)
            ),
            ['Title', 'Text', 'Recipients', 'Date']
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
