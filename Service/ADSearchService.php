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

/**
 * Class ADSearchService
 */
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
    public function findByGuid(UuidInterface $guid): ?Entry
    {
        return $this->adapter->findOne('objectGUID', $this->uuidToGuidHex($guid));
    }

    /**
     * @param string $samAccountName
     *
     * @return null|Entry
     */
    public function findBySamAccountName(string $samAccountName): ?Entry
    {
        return $this->adapter->findOne('samaccountname', $samAccountName);
    }

    /**
     * @param UuidInterface $uuid
     *
     * @return string
     */
    protected function uuidToGuidHex(UuidInterface $uuid): string
    {
        $guid = $uuid->getBytes();
        $guidHex = '';
        for ($i = 0; $i < strlen($guid); $i++) {
            $guidHex .= '\\'.str_pad(dechex(ord($guid[$i])), 2, '0', STR_PAD_LEFT);
        }

        return $guidHex;
    }
}
