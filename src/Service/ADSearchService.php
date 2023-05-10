<?php

declare(strict_types=1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gubler\ADSearchBundle\Service;

use Symfony\Component\Ldap\Entry;
use Symfony\Component\Uid\Uuid;

class ADSearchService
{
    public const DEFAULT_SEARCH_FIELDS = [
        'cn',
        'samaccountname',
        'displayname',
        'name',
        'surname',
        'mail',
    ];

    public function __construct(protected ADSearchAdapterInterface $adapter)
    {
    }

    /**
     * @param string[] $fields
     *
     * @return Entry[]
     */
    public function search(
        string $term,
        array $fields = self::DEFAULT_SEARCH_FIELDS,
        string $dn = '',
        int $maxResults = 50
    ): array {
        return $this->adapter->search(term: $term, fields: $fields, dn: $dn, maxResults: $maxResults);
    }

    public function find(Uuid $guid, string $dn = ''): ?Entry
    {
        return $this->adapter->find(adGuid: $guid, dn: $dn);
    }

    public function findBySamAccountName(string $sAMAccountName, string $dn = ''): ?Entry
    {
        return $this->adapter->findOne(byField: 'samaccountname', term: $sAMAccountName, dn: $dn);
    }

    public function findByEmail(string $email, string $dn = ''): ?Entry
    {
        return $this->adapter->findOne(byField: 'mail', term: $email, dn: $dn);
    }
}
