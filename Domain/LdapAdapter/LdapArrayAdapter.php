<?php
/**
 * LDAP Adapter to abstract calls to native LDAP functions
 */

namespace Gubler\ADSearchBundle\Domain\LdapAdapter;

/**
 * Class LdapArrayAdapter
 */
class LdapArrayAdapter implements LdapAdapterInterface
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
    ) {
    }

    /**
     * Closes connection to LDAP server
     *
     * @return void
     */
    public function __destruct()
    {
    }

    /**
     * {@inheritdoc}

     * @param string $filter
     * @param array  $attributes
     * @param int    $attrsonly
     * @param int    $sizelimit
     * @param int    $timelimit
     * @param int    $deref
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
    ) {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $value
     * @return  string
     */
    public function escape(string $value): string
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
        ); //replace them

        return $cleaned;
    }
}
