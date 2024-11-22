<?php

declare(strict_types=1);

namespace Sitegeist\GrapeVine\Domain\Repository;

use Neos\Flow\Persistence\QueryResultInterface;
use Neos\Flow\Persistence\Repository;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Security\Account;
use Sitegeist\GrapeVine\Domain\Model\Message;

#[Flow\Scope('singleton')]
class NotificationRepository extends Repository
{
    public function findByAccount(Account $account): QueryResultInterface
    {
        $query = $this->createQuery();
        $query = $query->matching(
            $query->equals('account', $account)
        );
        return $query->execute();
    }
}
