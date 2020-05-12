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

use Gubler\ADSearchBundle\Exception\NonUniqueADResultException;
use Gubler\ADSearchBundle\Model\Search\ADSearchAdapterInterface;
use Gubler\Collection\Collection;
use Ramsey\Uuid\Codec\GuidStringCodec;
use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\VarDumper\VarDumper;

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
        // decode utf8 GUIDs to binary to be the same as AD
        foreach ($this->testUsers as $key => $user) {
            $this->testUsers[$key]['objectGUID'][0] = utf8_decode($user['objectGUID'][0]);
        }
    }

    /**
     * @param string $term
     * @param array  $fields
     *
     * @return Entry[]
     */
    public function search(string $term, array $fields): array
    {
        return Collection::from($this->testUsers)
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
        $user = Collection::from($this->testUsers)
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
        $users = array_filter($this->testUsers, function(array $entry) use ($guid) {
            $arrayGuid = Uuid::fromBytes($entry['objectGUID'][0]);

            return $arrayGuid->equals($guid);
        });

        if (count($users) > 1) {
            throw new NonUniqueADResultException();
        }

        if (count($users) === 0) {
            return null;
        }

        $user = current($users);

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

    /**
     * Differs from uuidtoGuidHex in ServerSearch because each character is not prefaced with `\\`.
     *
     * @param UuidInterface $uuid
     *
     * @return string
     */
    protected function uuidToGuidHex(UuidInterface $uuid): string
    {
        $guid = $uuid->getBytes();
        $guidHex = '';
        $length = \strlen($guid);
        for ($i = 0; $i < $length; ++$i) {
            $guidHex .= str_pad(dechex(\ord($guid[$i])), 2, '0', STR_PAD_LEFT);
        }

        return $guidHex;
    }
}
