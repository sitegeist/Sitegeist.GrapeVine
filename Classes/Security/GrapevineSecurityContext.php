<?php

declare(strict_types=1);

namespace Sitegeist\GrapeVine\Security;

use Neos\Cache\CacheAwareInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Security\Context;
use Neos\Flow\Security\Policy\Role;
use Psr\Log\LoggerInterface;

/**
 * @Flow\Scope("singleton")
 */
class GrapevineSecurityContext # implements CacheAwareInterface
{
    /**
     * @Flow\Inject
     * @var Context
     */
    protected $securityContext;

    /**
     * @return string[]
     */
    public function getRoleIdentifiers(): array
    {
        $currentRoles = array_values(array_map(
            fn(Role $role) => $role->getIdentifier(),
            $this->securityContext->getRoles()
        ));
        return $currentRoles;
    }

//    /**
//     * @return string
//     */
//    public function getCacheEntryIdentifier(): string
//    {
//        return $this->securityContext->getContextHash();
//    }
}
