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

namespace Gubler\ADSearchBundle\Service\ActiveDirectory;

use Gubler\ADSearchBundle\Exception\NonUniqueADResultException;
use Gubler\ADSearchBundle\Service\ADSearchAdapterInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Uid\Uuid;

class ServerSearch implements ADSearchAdapterInterface
{
    protected Ldap $ldap;

    public function __construct(LdapFactoryInterface $ldapFactory)
    {
        $this->ldap = $ldapFactory->getLdapConnection();
    }

    /**
     * @param string[] $fields
     *
     * @return Entry[]
     */
    public function search(string $term, array $fields, int $maxResults = 50): array
    {
        $escapedTerm = $this->escape($term);

        return $this->ldap
            ->query(
                '',
                $this->buildSearchFilter(
                    $escapedTerm,
                    $fields,
                    false
                ),
                [
                    'sizeLimit' => $maxResults,
                ],
            )
            ->execute()
            ->toArray()
        ;
    }

    /**
     * @throws NonUniqueADResultException
     */
    public function findOne(string $byField, string $term): ?Entry
    {
        $escapedTerm = $this->escape($term);

        $results = $this->ldap->query(
            '',
            $this->buildSearchFilter(
                $escapedTerm,
                [$byField],
                true
            )
        )->execute();

        if (0 === $results->count()) {
            return null;
        }

        if (\count($results) > 1) {
            throw new NonUniqueADResultException();
        }

        return $results[0];
    }

    /**
     * @throws NonUniqueADResultException
     */
    public function find(Uuid $adGuid): ?Entry
    {
        $results = $this->ldap->query(
            '',
            $this->buildSearchFilter(
                $adGuid->toBinary(),
                ['objectGUID'],
                true
            )
        )->execute();

        if (0 === $results->count()) {
            return null;
        }

        if (\count($results) > 1) {
            throw new NonUniqueADResultException();
        }

        return $results[0];
    }

    /**
     * @param string[] $fields
     */
    protected function buildSearchFilter(string $name, array $fields, bool $strict): string
    {
        $filter = '';

        $searchName = $strict ? $name : $name . '*';

        foreach ($fields as $field) {
            $filter .= '(' . $field . '=' . $searchName . ')';
        }

        $filter = '(|' . $filter . ')';

        return '(&(objectclass=user)' . $filter . ')';
    }

    protected function escape(string $value): string
    {
        $metaChars = ['\\00', '\\', '(', ')', '*'];
        $quotedMetaChars = [];
        foreach ($metaChars as $key => $val) {
            $quotedMetaChars[$key] = '\\' . dechex(\ord($val));
        }

        return str_replace(
            $metaChars,
            $quotedMetaChars,
            $value
        );
    }
}
