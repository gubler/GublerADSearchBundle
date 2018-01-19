<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gubler\ADSearchBundle\Domain\LdapAdapter;

use Psr\Log\LoggerInterface;

/**
 * Interface LdapAdapterInterface
 */
interface LdapAdapterInterface
{
    /**
     * @param string          $ldapUsername Username for initial binding to LDAP
     * @param string          $ldapPassword Password for initial binding to LDAP
     * @param string          $ldapHost     LDAP server hostname
     * @param string          $ldapPort     LDAP server port
     * @param string          $ldapBaseDn   Base DN for LDAP tree searches
     * @param LoggerInterface $logger
     */
    public function __construct(string $ldapUsername, string $ldapPassword, string $ldapHost, string $ldapPort, string $ldapBaseDn, LoggerInterface $logger);

    /**
     * combines ldap_control_paged_results, ldap_search and ldap_get_entries
     *
     * @param string $filter
     * @param array  $attributes
     * @param int    $attrsonly
     * @param int    $sizelimit
     * @param int    $timelimit
     *
     * @return array|bool
     */
    public function search(string $filter, array $attributes = [], int $attrsonly = 0, int $sizelimit = 1000, int $timelimit = 300);

    /**
     * Escape the string used in LDAP search in order to avoid
     * "LDAP-injections"
     *
     * @param string $value
     *
     * @return  string
     */
    public function escape(string $value): string;
}
