<?php

declare(strict_types=1);

namespace Sitegeist\GrapeVine\Aspect;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Aop\JoinPointInterface;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Http\HttpRequestHandlerInterface;
use Neos\Flow\Mvc\ActionRequestFactory;
use Neos\Flow\Mvc\Routing\UriBuilder;
use Neos\Flow\Persistence\Doctrine\QueryResult;
use Neos\Flow\Security\Context;
use Neos\Neos\Service\UserService;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Sitegeist\GrapeVine\Domain\Model\Notification;
use Sitegeist\GrapeVine\Domain\Repository\MessageRepository;
use Sitegeist\GrapeVine\Domain\Repository\NotificationRepository;

#[Flow\Scope("singleton")]
#[Flow\Aspect]
class BackendRedirectionAspect
{
    public function __construct(
        private readonly NotificationRepository $notificationRepository,
        private readonly Context $securityContext,
        private readonly Bootstrap $bootstrap,
        private readonly ActionRequestFactory $actionRequestFactory,
        private readonly ServerRequestFactoryInterface $serverRequestFactory,
    ) {
    }

    /**
     * @Flow\Inject
     * @var UserService
     */
    protected UserService $userService;

    #[Flow\Around(pointcutExpression: "method(Neos\Neos\Service\BackendRedirectionService->getAfterLoginRedirectionUri())")]
    public function redirectToNewMessagesOnLogin(JoinPointInterface $joinPoint): ?string
    {
        $account = $this->securityContext->getAccount();
        /**
         * @var QueryResult $notifications
         */
        $notifications = $this->notificationRepository->findByAccount($account);
        $latestNotification = $notifications->getFirst();

        if ($latestNotification instanceof Notification) {
            $handler = $this->bootstrap->getActiveRequestHandler();
            if ($handler instanceof HttpRequestHandlerInterface) {
                $request = $handler->getHttpRequest();
            } else {
                $request = $this->serverRequestFactory->createServerRequest("get", "/");
            }
            $actionRequest = $this->actionRequestFactory->createActionRequest($request);
            $uriBuilder = new UriBuilder();
            $uriBuilder->setRequest($actionRequest);

            $uri = $uriBuilder
                ->reset()
                ->setCreateAbsoluteUri(true)
                ->uriFor(
                    'index',
                    [
                        'module' => 'management/messages',
                        'moduleArguments' => [
                            'package' => "Sitegeist.Grapevine",
                            'controller' => "message",
                            'action' => 'show',
                            'format' => 'html',
                            'message' => $latestNotification->getMessage()
                        ]
                    ],
                    'Backend\Module',
                    'Neos.Neos'
                );
            return $uri;
        }
        return $joinPoint->getAdviceChain()->proceed($joinPoint);
    }
}
