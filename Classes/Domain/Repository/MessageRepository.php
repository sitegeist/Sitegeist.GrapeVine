<?php

declare(strict_types=1);

namespace Sitegeist\GrapeVine\Domain\Repository;

use Neos\Flow\Persistence\Repository;
use Neos\Flow\Annotations as Flow;

#[Flow\Scope('singleton')]
class MessageRepository extends Repository
{
    /**
     * @var array<string, string>
     */
    protected $defaultOrderings = ['date' => 'desc'];
}
