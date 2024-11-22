<?php

declare(strict_types=1);

namespace Sitegeist\GrapeVine\Domain\Repository;

use Neos\Flow\Persistence\Doctrine\QueryResult;
use Neos\Flow\Persistence\QueryResultInterface;
use Neos\Flow\Persistence\Repository;
use Neos\Flow\Annotations as Flow;

#[Flow\Scope('singleton')]
class MessageRepository extends Repository
{
    /**
     * @var array<string, string>
     */
    protected $defaultOrderings = ['date' => 'desc'];

    /**
     * @param string[] $roleIdentifiers
     * @return QueryResultInterface
     */
    public function findByRoleIdentifiers(array $roleIdentifiers): QueryResultInterface
    {
        $query = $this->createQuery();
        $query = $query->matching(
            $query->logicalOr(
                ... array_map(
                    fn(string $roleIdentifier) => $query->equals('recipientRoleIdentifier', $roleIdentifier),
                    $roleIdentifiers
                )
            )
        );

        return $query->execute();
    }
}
