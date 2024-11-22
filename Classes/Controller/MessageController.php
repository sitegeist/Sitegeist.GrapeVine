<?php

declare(strict_types=1);

namespace Sitegeist\GrapeVine\Controller;

use Neos\Flow\Mvc\Routing\UriBuilder;
use Neos\Flow\Persistence\Doctrine\QueryResult;
use Neos\Flow\Security\Context;
use Neos\Flow\Security\Policy\Role;
use Neos\Fusion\View\FusionView;
use Neos\Neos\Controller\Backend\MenuHelper;
use Neos\Neos\Controller\Module\AbstractModuleController;
use Neos\Neos\Service\UserService;
use Sitegeist\GrapeVine\Domain\Model\Message;
use Sitegeist\GrapeVine\Domain\Model\Notification;
use Sitegeist\GrapeVine\Domain\Repository\MessageRepository;
use Sitegeist\GrapeVine\Domain\Repository\NotificationRepository;

class MessageController extends AbstractModuleController
{
    /**
     * @var string
     */
    protected $defaultViewObjectName = FusionView::class;

    public function __construct(
        private readonly MessageRepository $messageRepository,
        private readonly NotificationRepository $notificationRepository,
        private readonly Context $securityContext,
    ) {
    }

    public function indexAction(): void
    {
        $this->securityContext->getRoles();
        $this->view->assignMultiple([
            'messages' => $this->messageRepository->findByRoleIdentifiers(
                array_values(
                    array_map(
                        fn(Role $role) => $role->getIdentifier(),
                        $this->securityContext->getRoles()
                    )
                )
            ),
            'notifications' => $this->notificationRepository->findByAccount(
                $this->securityContext->getAccount(),
            )
        ]);
    }

    public function showAction(Message $message): void
    {
        $notification = null;
        foreach ($message->getNotifications() as $messageNotification) {
            if ($messageNotification->getAccount() === $this->securityContext->getAccount()) {
                $notification = $messageNotification;
            }
        }
        $this->view->assign('message', $message);
        $this->view->assign('notification', $notification);
    }

    public function confirmNotificationAction(Notification $notification): void
    {
        if ($this->securityContext->getAccount() !== $notification->getAccount()) {
            throw new \Exception('forbidden access to foreign notifications');
        }

        $this->notificationRepository->remove($notification);
        $this->persistenceManager->persistAll();
        $this->forward('index');
    }
}
