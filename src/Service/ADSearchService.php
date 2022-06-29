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
    public function search(string $term, array $fields = self::DEFAULT_SEARCH_FIELDS): array
    {
        return $this->adapter->search($term, $fields);
    }

    public function find(Uuid $guid): ?Entry
    {
        return $this->adapter->find($guid);
    }

    public function findBySamAccountName(string $sAMAccountName): ?Entry
    {
        return $this->adapter->findOne('samaccountname', $sAMAccountName);
    }

    public function findByEmail(string $email): ?Entry
    {
        return $this->adapter->findOne('mail', $email);
    }
}
