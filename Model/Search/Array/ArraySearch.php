<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gubler\ADSearchBundle\Model\Search\ActiveDirectory;

use Gubler\ADSearchBundle\Exception\NonUniqueADResultException;
use Gubler\ADSearchBundle\Model\Search\ADSearchAdapterInterface;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Ldap\Entry;

/**
 * Class ArraySearch
 */
class ArraySearch implements ADSearchAdapterInterface
{
    /** @var array */
    protected $testUsers;

    /**
     * @param string $pathToTestUsersJson
     */
    public function __construct(string $pathToTestUsersJson)
    {
        $this->testUsers = json_decode(file_get_contents($pathToTestUsersJson), true);
    }

    /**
     * @param string $term
     * @param array  $fields
     *
     * @return array
     */
    public function search(string $term, array $fields): array
    {
        // TODO: Implement search() method.
    }

    /**
     * @param string $byField
     * @param string $term
     *
     * @return null|Entry
     */
    public function findOne(string $byField, string $term): ?Entry
    {
        // TODO: Implement findOne() method.
    }

    /**
     * @param UuidInterface $guid
     *
     * @return null|Entry
     */
    public function find(UuidInterface $guid): ?Entry
    {
        // TODO: Implement find() method.
    }
}
