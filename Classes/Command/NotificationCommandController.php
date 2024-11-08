<?php

declare(strict_types=1);

namespace Sitegeist\GrapeVine\Command;

use GPBMetadata\Google\Monitoring\V3\NotificationService;
use Neos\Flow\Cli\CommandController;
use Neos\Flow\Persistence\Doctrine\PersistenceManager;
use Sitegeist\GrapeVine\Domain\Model\Notification;
use Sitegeist\GrapeVine\Domain\Repository\NotificationRepository;

class NotificationCommandController extends CommandController
{
    protected function __construct(
        private readonly NotificationRepository $notificationRepository,
    ) {
        parent::__construct();
    }

    public function listCommand(): void
    {
        $this->notificationRepository->findAll();
        $this->output->outputTable(
            array_map(
                fn(Notification $notification) => [$notification->getAccount()->getAccountIdentifier(), $notification->getMessage()->getTitle()],
                $this->notificationRepository->findAll()->toArray()
            ),
            ['Account', 'Message']
        );
    }

    /**
     * @param bool $yes If set no confirmation is required
     */
    public function pruneCommand(bool $yes): void
    {
        if ($yes) {
            $confirm = $yes;
        } else {
            $confirm = $this->output->askConfirmation('Are you sure?');
        }
        if ($confirm) {
            $count = $this->notificationRepository->countAll();
            $this->notificationRepository->removeAll();
            $this->outputLine('Removed %d notifications', [$count]);
        }
    }
}
