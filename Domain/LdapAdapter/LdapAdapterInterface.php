<?php

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
    public function __construct(
        string $ldapUsername,
        string $ldapPassword,
        string $ldapHost,
        string $ldapPort,
        string $ldapBaseDn,
        LoggerInterface $logger
    );

    /**
     * combines ldap_control_paged_results, ldap_search and ldap_get_entries
     *
     * @param string $filter
     * @param array  $attributes
     * @param int    $attrsonly
     * @param int    $sizelimit
     * @param int    $timelimit
     *
     * @see \ldap_search()
     * @see \ldap_control_paged_result()
     * @see \ldap_get_entries()
     *
     * @return array|bool
     */
    public function search(
        string $filter,
        array $attributes = [],
        int $attrsonly = 0,
        int $sizelimit = 1000,
        int $timelimit = 300
    );

    /**
     * Escape the string used in LDAP search in order to avoid
     * "LDAP-injections"
     *
     * @param string $value
     * @return  string
     */
    public function escape(string $value): string;
}
