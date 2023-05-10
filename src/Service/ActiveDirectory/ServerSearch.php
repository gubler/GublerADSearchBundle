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

final class ServerSearch implements ADSearchAdapterInterface
{
    private Ldap $ldap;

    public function __construct(LdapFactoryInterface $ldapFactory)
    {
        $this->ldap = $ldapFactory->getLdapConnection();
    }

    /**
     * @param string[] $fields
     *
     * @return Entry[]
     */
    public function search(string $term, array $fields, string $dn = '', int $maxResults = 50): array
    {
        $escapedTerm = $this->escape(value: $term);

        return $this->ldap
            ->query(
                dn: $dn,
                query: $this->buildSearchFilter(
                    name: $escapedTerm,
                    fields: $fields,
                    strict: false
                ),
                options: [
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
    public function findOne(string $byField, string $term, string $dn = ''): ?Entry
    {
        $escapedTerm = $this->escape(value: $term);

        $results = $this->ldap->query(
            dn: $dn,
            query: $this->buildSearchFilter(
                name: $escapedTerm,
                fields: [$byField],
                strict: true
            )
        )->execute();

        if (0 === $results->count()) {
            return null;
        }

        if (\count(value: $results) > 1) {
            throw new NonUniqueADResultException();
        }

        return $results[0];
    }

    /**
     * @throws NonUniqueADResultException
     */
    public function find(Uuid $adGuid, string $dn = ''): ?Entry
    {
        $results = $this->ldap->query(
            dn: $dn,
            query: $this->buildSearchFilter(
                name: ldap_escape(value: $adGuid->toBinary()),
                fields: ['objectGUID'],
                strict: true
            )
        )->execute();

        if (0 === $results->count()) {
            return null;
        }

        if (\count(value: $results) > 1) {
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
            $quotedMetaChars[$key] = '\\' . dechex(num: \ord(character: $val));
        }

        return str_replace(
            search: $metaChars,
            replace: $quotedMetaChars,
            subject: $value
        );
    }
}
