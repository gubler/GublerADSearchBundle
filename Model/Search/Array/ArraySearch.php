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
use Symfony\Component\Ldap\Entry;

class ArraySearch implements ADSearchAdapterInterface
{
    protected $testUsers;

    public function __construct(string $pathToTestUsersJson)
    {
        $this->testUsers = json_decode(file_get_contents($pathToTestUsersJson), true);
    }

    public function search(string $term, array $fields): array
    {
        // TODO: Implement search() method.
    }

    public function findOne(string $byField, string $term): ?Entry
    {
        // TODO: Implement findOne() method.
    }

}
