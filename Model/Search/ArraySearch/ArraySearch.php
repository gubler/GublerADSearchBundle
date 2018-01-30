<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gubler\ADSearchBundle\Model\Search\ArraySearch;

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
     * @return Entry[]
     */
    public function search(string $term, array $fields): array
    {
        return collect($this->testUsers)
            ->filter(function (array $row) use ($term, $fields) {
                $fields = array_map('strtolower', $fields);

                // iterate through each field in the row
                $row = array_change_key_case($row, CASE_LOWER);
                foreach ($row as $field => $value) {
                    // check if field is in list of fields to search
                    if (\in_array($field, $fields, true) === false) {
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
            })
            ->map(function (array $row) {
                return new Entry($row['distinguishedName'][0], $row);
            })
            ->toArray()
        ;
    }

    /**
     * @param string $byField
     * @param string $term
     *
     * @return null|Entry
     */
    public function findOne(string $byField, string $term): ?Entry
    {
        $user = collect($this->testUsers)
            ->first(function (array $row) use ($byField, $term) {
                $row = array_change_key_case($row, CASE_LOWER);
                $byField = strtolower($byField);

                return strcasecmp($row[$byField][0], $term) === 0;
            })
        ;

        if (null === $user) {
            return null;
        }

        return new Entry($user['distinguishedName'][0], $user);
    }

    /**
     * @param UuidInterface $guid
     *
     * @return null|Entry
     */
    public function find(UuidInterface $guid): ?Entry
    {
        $guidString = $guid->toString();

        $user = collect($this->testUsers)
            ->first(function (array $row) use ($guidString) {
                return strcasecmp($row['objectGUID'][0], $guidString) === 0;
            })
        ;

        if (null === $user) {
            return null;
        }

        return new Entry($user['distinguishedName'][0], $user);
    }

    /**
     * @param string $term
     * @param string $value
     *
     * @return bool
     */
    protected function testTermInValue(string $term, string $value): bool
    {
        return 0 === stripos($value, $term);
    }
}
