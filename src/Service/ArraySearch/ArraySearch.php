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

final class ArraySearch implements ADSearchAdapterInterface
{
    private array $testUsers;

    public function __construct(string $pathToTestUsersJson)
    {
        $this->testUsers = json_decode(
            json: file_get_contents(filename: $pathToTestUsersJson) ?: '',
            associative: true,
            depth: 512,
            flags: \JSON_THROW_ON_ERROR
        );

        // decode utf8 GUIDs to binary to be the same as AD
        foreach ($this->testUsers as $key => $user) {
            $guid = $user['objectGUID'][0];
            $this->testUsers[$key]['objectGUID'][0] = mb_convert_encoding(
                string: $guid,
                to_encoding: 'ISO-8859-1',
                from_encoding: 'UTF-8'
            );
        }
    }

    /**
     * @param string[] $fields
     *
     * @return Entry[]
     */
    public function search(string $term, array $fields, string $dn = '', int $maxResults = 50): array
    {
        $filteredUsers = array_filter(array: $this->testUsers, callback: function (array $row) use ($term, $fields) {
            $fields = array_map(callback: 'strtolower', array: $fields);

            // iterate through each field in the row
            $row = array_change_key_case(array: $row);
            foreach ($row as $field => $value) {
                // check if field is in list of fields to search
                if (false === \in_array(needle: $field, haystack: $fields, strict: true)) {
                    continue;
                }
                /** @var array $value */
                foreach ($value as $test) {
                    if ($this->testTermInValue(term: $term, value: $test)) {
                        return true;
                    }
                }
            }

            return false;
        });

        return array_map(callback: function (array $row) {
            return new Entry(dn: $row['distinguishedName'][0], attributes: $row);
        }, array: $filteredUsers);
    }

    public function findOne(string $byField, string $term, string $dn = ''): ?Entry
    {
        $users = array_filter(
            array: $this->testUsers,
            callback: function (array $row) use ($byField, $term) {
                $row = array_change_key_case(array: $row, case: \CASE_LOWER);
                $byField = strtolower(string: $byField);

                return 0 === strcasecmp(string1: $row[$byField][0], string2: $term);
            }
        );

        $users = array_values(array: $users);

        if (1 !== \count(value: $users)) {
            return null;
        }

        $user = $users[0];

        return new Entry(dn: $user['distinguishedName'][0], attributes: $user);
    }

    public function find(Uuid $adGuid, string $dn = ''): ?Entry
    {
        $users = array_filter(array: $this->testUsers, callback: function (array $entry) use ($adGuid) {
            return Uuid::fromBinary(uid: $entry['objectGUID'][0])->equals(other: $adGuid);
        });

        if (\count(value: $users) > 1) {
            throw new NonUniqueADResultException();
        }

        if (0 === \count(value: $users)) {
            return null;
        }

        $user = current(array: $users);

        return new Entry(dn: $user['distinguishedName'][0], attributes: $user);
    }

    protected function testTermInValue(string $term, string $value): bool
    {
        return 0 === stripos(haystack: $value, needle: $term);
    }
}
