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
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class ServerSearch
 */
class ServerSearch implements ADSearchAdapterInterface
{
    /**
     * @var Ldap
     */
    protected $ldap;

    /**
     * @param string $host
     * @param int    $port
     * @param string $bindDn
     * @param string $bindPassword
     */
    public function __construct(string $host, int $port, string $bindDn, string $bindPassword)
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
        $escapedTerm = $this->escape($term);

        return $this->ldap
            ->query(
                '',
                $this->buildSearchFilter($escapedTerm, $fields)
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
            $this->buildSearchFilter($escapedTerm, [$byField])
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
     * @param UuidInterface $guid
     *
     * @return null|Entry
     *
     * @throws NonUniqueADResultException
     */
    public function find(UuidInterface $guid): ?Entry
    {
        $results = $this->ldap->query(
            '',
            $this->buildSearchFilter($this->uuidToGuidHex($guid), ['objectGUID'])
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

    /**
     * {@inheritdoc}
     *
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
        $cleaned = str_replace(
            $metaChars,
            $quotedMetaChars,
            $value
        );

        return $cleaned;
    }

    /**
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
            $guidHex .= '\\'.str_pad(dechex(\ord($guid[$i])), 2, '0', STR_PAD_LEFT);
        }

        return $guidHex;
    }
}
