<?php
/**
 * Active Directory / LDAP Search
 */

namespace Gubler\ADSearchBundle\Domain\Search;

use Gubler\ADSearchBundle\Entity\ADUser;

/**
 * Active Directory / LDAP Search
 *
 * @version 1.0.0
 * @package Gubler\ADSearchBundle\Domain\Search
 */
abstract class AbstractServerSearch implements ActiveDirectorySearch
{

    /**
     * LDAP Bind Username
     *
     * @var string
     */
    protected $ldapUsername = null;

    /**
     * LDAP Bind Password
     *
     * @var string
     */
    protected $ldapPassword = null;

    /**
     * LDAP Connection Host
     *
     * @var string
     */
    protected $ldapHost = null;

    /**
     * LDAP Connection Port
     *
     * @var string
     */
    protected $ldapPort = null;

    /**
     * LDAP Base DN for tree searches
     *
     * @var string
     */
    protected $ldapBaseDn = null;

    /**
     * LDAP Connection Resource
     *
     * @var resource
     */
    protected $ldapConnection = null;

    /**
     * Initializes class properties, connects to LDAP server and does initial bind to LDAP server.
     *
     * @param string $ldapUsername Username for initial binding to LDAP
     * @param string $ldapPassword Password for initial binding to LDAP
     * @param string $ldapHost     LDAP server hostname
     * @param string $ldapPort     LDAP server port
     * @param string $ldapBaseDn   Base DN for LDAP tree searches
     *
     * @return AbstractServerSearch
     */
    public function __construct($ldapUsername, $ldapPassword, $ldapHost, $ldapPort, $ldapBaseDn)
    {
        $this->ldapUsername = $ldapUsername;
        $this->ldapPassword = $ldapPassword;
        $this->ldapHost = $ldapHost;
        $this->ldapPort = $ldapPort;
        $this->ldapBaseDn = $ldapBaseDn;

        // connect and bind LDAP
        $this->ldapConnect();
    }

    /**
     * Closes connection to LDAP server
     *
     * @return void
     */
    public function __destruct()
    {
        ldap_unbind($this->ldapConnection);
    }

    /**
     * Search LDAP for name in the list of fields and maps results to an array of ADUsers.
     *
     * By default, this searches the 'cn', `samaccountname`, `displayname`, `surname`, and `email` fields.
     *
     * @param string $name          name to search for
     * @param array  $fields        fields to be searched
     * @param int    $count         number of results to return
     * @param bool   $includeGroups include groups in search results
     *
     * @return array Array of search results
     */
    public function search(
        $name,
        $fields = ['cn', 'samaccountname', 'displayname', 'surname', 'email'],
        $count = 30,
        $includeGroups = false
    ) {
        // Sanitize
        $searchName = $this->ldapEscape($name);

        $filter = $this->buildSearchFilter($searchName, $fields, $includeGroups);

        \ldap_control_paged_result($this->ldapConnection, $count);

        $ldapSearch = ldap_search(
            $this->ldapConnection,
            $this->ldapBaseDn,
            $filter,
            array(
                    'cn',
                    'dn',
                    'samaccountname',
                    'displayname',
                    'title',
                    'mail',
                    'telephonenumber',
                    'physicaldeliveryofficename',
            ),
            0,
            $count,
            30
        );

        $info = ldap_get_entries($this->ldapConnection, $ldapSearch);

        $foundUsers = array();

        foreach ($info as $user) {
            $domain = $this->dnToDomain($user['dn']);

            // skip if not an array (count of results) or no samaccountname (not a user) or Domain was not parsed
            if (!is_array($user) ||
                !isset($user['samaccountname']) ||
                $domain === false
            ) {
                continue;
            }

            $adUser = new ADUser($user['samaccountname'][0]);
            $adUser->setDomain($domain)
                ->setName($this->chooseNameForAccount($info));
            $adUser->setTitle($this->valueOrNull($user, 'title'));
            $adUser->setPhone($this->valueOrNull($user, 'telephonenumber'));
            $adUser->setOffice($this->valueOrNull($user, 'physicaldeliveryofficename'));
            $adUser->setEmail($this->valueOrNull($user, 'mail'));
            $foundUsers[] = $adUser;
        }

        return $foundUsers;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $name
     * @return ADUser
     */
    public function getUser($name)
    {
        // Sanitize
        $searchName = $this->ldapEscape($name);

        $ldapSearch = ldap_search(
            $this->ldapConnection,
            $this->ldapBaseDn,
            '(samaccountname='.$searchName.')',
            array(
                'cn',
                'dn',
                'samaccountname',
                'displayName',
                'title',
                'mail',
                'telephonenumber',
                'physicaldeliveryofficename',
            ),
            0,
            1,
            30
        );

        $info = ldap_get_entries($this->ldapConnection, $ldapSearch);

        $user = null;

        // skip if either no results or more than one result
        if ($info['count'] === 1) {
            $adUser = new ADUser($info[0]['samaccountname'][0]);
            $adUser->setDomain($this->dnToDomain($info[0]['dn']))
                ->setName($this->chooseNameForAccount($info));
            $adUser->setTitle($this->valueOrNull($info[0], 'title'));
            $adUser->setPhone($this->valueOrNull($info[0], 'telephonenumber'));
            $adUser->setOffice($this->valueOrNull($info[0], 'physicaldeliveryofficename'));
            $adUser->setEmail($this->valueOrNull($info[0], 'mail'));
            $user = $adUser;
        }

        return $user;
    }

    /**
     * Get the display name for the account
     *
     * @param array $adFields
     * @return string
     */
    abstract protected function chooseNameForAccount($adFields);

    /**
     * @param string $adDn
     *
     * @return string
     */
    abstract protected function dnToDomain($adDn);

    /**
     * Return value or null
     *
     * @param array $users Users array
     * @param string $key Users array key
     * @return string|null
     **/
    protected function valueOrNull($users, $key)
    {
        if (isset($users[$key][0])) {
            return $users[$key][0];
        }

        return null;
    }

    /**
     * Escape the string used in LDAP search in order to avoid
     * "LDAP-injections"
     *
     * @param   mixed   $str
     * @return  mixed
     */
    protected function ldapEscape($str)
    {

        $metaChars = array ("\\00", '\\', '(', ')', '*');
        $quotedMetaChars = array ();
        foreach ($metaChars as $key => $value) {
            $quotedMetaChars[$key] = '\\'.\dechex(\ord($value));
        }
        $str = str_replace(
            $metaChars,
            $quotedMetaChars,
            $str
        ); //replace them

        return $str;
    }

    /**
     * Connects and binds LDAP as well as setting OPT_PROTOCOL to 3
     *
     * @return void
     */
    protected function ldapConnect()
    {
        $this->ldapConnection = ldap_connect($this->ldapHost, $this->ldapPort);
        ldap_set_option($this->ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->ldapConnection, LDAP_OPT_REFERRALS, 0);
        ldap_bind($this->ldapConnection, $this->ldapUsername, $this->ldapPassword);
    }

    /**
     * @param string $name
     * @param array  $fields
     * @param bool   $includeGroups
     *
     * @return string
     */
    protected function buildSearchFilter($name, $fields, $includeGroups)
    {
        $filter = collect($fields)->reduce(function($current, $field) use ($name) {
            $filterAddition = '('.$field.'='.$name.'.*)';
            return $current.$filterAddition;
        });

        $filter = '(|'.$filter.')';

        if ($includeGroups) {
            return $filter;
        }

        return '(&(objectclass=user)'.$filter.')';
    }
}
