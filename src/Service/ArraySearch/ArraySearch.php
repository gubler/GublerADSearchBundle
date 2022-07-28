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

namespace Gubler\ADSearchBundle\Service\ArraySearch;

use Gubler\ADSearchBundle\Exception\NonUniqueADResultException;
use Gubler\ADSearchBundle\Service\ADSearchAdapterInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Uid\Uuid;

class ArraySearch implements ADSearchAdapterInterface
{
    protected array $testUsers;

    public function __construct(string $pathToTestUsersJson)
    {
        $this->testUsers = json_decode(
            file_get_contents($pathToTestUsersJson) ?: '',
            true,
            512,
            \JSON_THROW_ON_ERROR
        );

        // decode utf8 GUIDs to binary to be the same as AD
        foreach ($this->testUsers as $key => $user) {
            $this->testUsers[$key]['objectGUID'][0] = utf8_decode($user['objectGUID'][0]);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param string[] $fields
     *
     * @return Entry[]
     */
    public function search(string $term, array $fields, int $maxResults = 50): array
    {
        $filteredUsers = array_filter($this->testUsers, function (array $row) use ($term, $fields) {
            $fields = array_map('strtolower', $fields);

            // iterate through each field in the row
            $row = array_change_key_case($row);
            foreach ($row as $field => $value) {
                // check if field is in list of fields to search
                if (false === \in_array($field, $fields, true)) {
                    continue;
                }
                /** @var array $value */
                foreach ($value as $test) {
                    if ($this->testTermInValue($term, $test)) {
                        return true;
                    }
                }
            }

            return false;
        });

        return array_map(function (array $row) {
            return new Entry($row['distinguishedName'][0], $row);
        }, $filteredUsers);
    }

    public function findOne(string $byField, string $term): ?Entry
    {
        $users = array_filter(
            $this->testUsers,
            function (array $row) use ($byField, $term) {
                $row = array_change_key_case($row, \CASE_LOWER);
                $byField = strtolower($byField);

                return 0 === strcasecmp($row[$byField][0], $term);
            }
        );

        $users = array_values($users);

        if (1 !== \count($users)) {
            return null;
        }

        $user = $users[0];

        return new Entry($user['distinguishedName'][0], $user);
    }

    public function find(Uuid $adGuid): ?Entry
    {
        $users = array_filter($this->testUsers, function (array $entry) use ($adGuid) {
            return Uuid::fromBinary($entry['objectGUID'][0])->equals($adGuid);
        });

        if (\count($users) > 1) {
            throw new NonUniqueADResultException();
        }

        if (0 === \count($users)) {
            return null;
        }

        $user = current($users);

        return new Entry($user['distinguishedName'][0], $user);
    }

    protected function testTermInValue(string $term, string $value): bool
    {
        return 0 === stripos($value, $term);
    }
}
