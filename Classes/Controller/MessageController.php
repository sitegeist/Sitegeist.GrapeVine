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
        private readonly MessageRepository      $messageRepository,
        private readonly NotificationRepository $notificationRepository,
        private readonly Context                $securityContext,
        private readonly MenuHelper             $menuHelper
    ) {
    }

    public function indexAction(): void
    {
        $account = $this->securityContext->getAccount();
        /**
         * @var QueryResult $notifications
         */
        $notifications = $this->notificationRepository->findByAccount($account);
        if ($notifications->count() === 0) {

            $uriBuilder = new UriBuilder();
            $uriBuilder->setRequest($this->request->getMainRequest());
            $uri = $uriBuilder
                ->reset()
                ->setCreateAbsoluteUri(true)
                ->uriFor('index', ['module' => 'content'], 'Backend\Module', 'Neos.Neos');
            $this->redirectToUri($uri);
        }
        $this->view->assign('notifications', $notifications);
    }

    public function listAction(): void
    {
        $this->securityContext->getRoles();
        $this->view->assign('messages',
            $this->messageRepository->findByRoleIdentifiers(
                array_values(
                    array_map(
                        fn(Role $role) => $role->getIdentifier(),
                        $this->securityContext->getRoles()
                    )
                )
            )
        );
    }

    public function showAction(Message $message): void
    {
        $this->view->assign('message', $message);
    }
}
