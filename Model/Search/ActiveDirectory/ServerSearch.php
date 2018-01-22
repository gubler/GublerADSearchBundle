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
use Symfony\Component\Ldap\Ldap;

class ServerSearch implements ADSearchAdapterInterface
{
    protected $ldap;

    public function __construct(string $host, int $port, string $baseDn, string $bindDn, string $bindPassword)
    {
        $this->ldap = Ldap::create(
            'ext_ldap',
            array('connection_string' => 'ldap://'.$host.':'.$port)
        );

        $this->ldap->bind($bindDn, $bindPassword);
    }

    /**
     * @param string $term
     * @param array  $fields
     *
     * @return array
     */
    public function search(string $term, array $fields): array
    {
        return $this->ldap->query('', $this->buildSearchFilter($term, $fields))->execute();
    }

    /**
     * @param string $byField
     * @param string $term
     *
     * @return null|Entry
     * @throws NonUniqueADResultException
     */
    public function findOne(string $byField, string $term): ?Entry
    {

        $results = $this->ldap->query(
            '',
            $this->buildSearchFilter($term, [$byField])
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
    protected function buildSearchFilter(string $name, array $fields): string
    {
        $filter = '';

        foreach ($fields as $field) {
            $filter = $filter.'('.$field.'='.$name.'*)';
        }

        $filter = '(|'.$filter.')';

        return '(&(objectclass=user)'.$filter.')';
    }
}