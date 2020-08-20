<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gubler\ADSearchBundle\Service;

use Gubler\ADSearchBundle\Model\Search\ADSearchAdapterInterface;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Ldap\Entry;

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

    /** @var ADSearchAdapterInterface */
    protected $adapter;

    /**
     * @param ADSearchAdapterInterface $adapter
     */
    public function __construct(ADSearchAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param string $term
     * @param array  $fields
     *
     * @return array
     */
    public function search(string $term, array $fields = self::DEFAULT_SEARCH_FIELDS): array
    {
        return $this->adapter->search($term, $fields);
    }

    /**
     * @param UuidInterface $guid
     *
     * @return null|Entry
     */
    public function find(UuidInterface $guid): ?Entry
    {
        return $this->adapter->find($guid);
    }

    /**
     * @param string $sAMAccountName
     *
     * @return null|Entry
     */
    public function findBySamAccountName(string $sAMAccountName): ?Entry
    {
        return $this->adapter->findOne('samaccountname', $sAMAccountName);
    }

    /**
     * @param string $email
     *
     * @return null|Entry
     */
    public function findByEmail(string $email): ?Entry
    {
        return $this->adapter->findOne('mail', $email);
    }
}
