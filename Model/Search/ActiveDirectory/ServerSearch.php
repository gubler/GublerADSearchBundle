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
use Gubler\ADSearchBundle\Lib\GuidTools;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Ldap;

class ServerSearch implements ADSearchAdapterInterface
{
    /**
     * @var Ldap
     */
    protected $ldap;

    /**
     * @param LdapFactoryInterface $ldapFactory
     */
    public function __construct(LdapFactoryInterface $ldapFactory)
    {
        $this->ldap = $ldapFactory->getLdapConnection();
    }

    /**
     * @param string $term
     * @param array  $fields
     *
     * @return array
     */
    public function search(string $term, array $fields): array
    {
        $escapedTerm = $this->escape($term);

        return $this->ldap
            ->query(
                '',
                $this->buildSearchFilter(
                    $escapedTerm,
                    $fields,
                    false
                )
            )
            ->execute()
            ->toArray()
            ;
    }

    /**
     * @param string $byField
     * @param string $term
     *
     * @return null|Entry
     *
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

        if (empty($results)) {
            return null;
        }

        if (count($results) > 1) {
            throw new NonUniqueADResultException();
        }

        return $results[0];
    }

    /**
     * @param UuidInterface $adGuid
     *
     * @return null|Entry
     *
     * @throws NonUniqueADResultException
     */
    public function find(UuidInterface $adGuid): ?Entry
    {
        $results = $this->ldap->query(
            '',
            $this->buildSearchFilter(
                GuidTools::guidToADHex($adGuid),
                ['objectGUID'],
                true
            )
        )->execute();

        if (empty($results)) {
            return null;
        }

        if (count($results) > 1) {
            throw new NonUniqueADResultException();
        }

        return $results[0];
    }

    /**
     * @param string $name
     * @param array  $fields
     *
     * @return string
     */
    protected function buildSearchFilter(string $name, array $fields, bool $strict): string
    {
        $filter = '';

        $searchName = $strict ? $name : $name.'*';

        foreach ($fields as $field) {
            $filter .= '(' . $field . '=' . $searchName . ')';
        }

        $filter = '(|' . $filter . ')';

        return '(&(objectclass=user)'.$filter.')';
    }

    /**
     * @param string $value
     *
     * @return  string
     */
    protected function escape(string $value): string
    {
        $metaChars = array("\\00", '\\', '(', ')', '*');
        $quotedMetaChars = array();
        foreach ($metaChars as $key => $val) {
            $quotedMetaChars[$key] = '\\'.\dechex(\ord($val));
        }

        return str_replace(
            $metaChars,
            $quotedMetaChars,
            $value
        );
    }
}
