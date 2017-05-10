<?php

namespace Gubler\ADSearchBundle\Domain\Search;

/**
 * Interface LdapAdapterInterface
 */
interface LdapAdapterInterface
{
    /**
     * @param string $ldapUsername Username for initial binding to LDAP
     * @param string $ldapPassword Password for initial binding to LDAP
     * @param string $ldapHost     LDAP server hostname
     * @param string $ldapPort     LDAP server port
     * @param string $ldapBaseDn   Base DN for LDAP tree searches
     */
    public function __construct(
        string $ldapUsername,
        string $ldapPassword,
        string $ldapHost,
        string $ldapPort,
        string $ldapBaseDn
    );

    /**
     * combines ldap_control_paged_results, ldap_search and ldap_get_entries
     *
     * @param string $filter
     * @param array  $attributes
     * @param int    $attrsonly
     * @param int    $sizelimit
     * @param int    $timelimit
     * @param int    $deref
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
        int $timelimit = 300,
        int $deref = LDAP_DEREF_NEVER
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
