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
    /** @var LdapAdapterInterface */
    protected $ldapAdapter;

    /**
     * @param LdapAdapterInterface $ldapAdapter
     */
    public function __construct(LdapAdapterInterface $ldapAdapter)
    {
        $this->ldapAdapter = $ldapAdapter;
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
        $searchName = $this->ldapAdapter->escape($name);

        $filter = $this->buildSearchFilter($searchName, $fields, $includeGroups);

        $info = $this->ldapAdapter->search(
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
        $searchName = $this->ldapAdapter->escape($name);

        $info = $this->ldapAdapter->search(
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
