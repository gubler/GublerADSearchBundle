<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gubler\ADSearchBundle\Domain\Search;

use Gubler\ADSearchBundle\Entity\ADUser;
use Gubler\ADSearchBundle\Domain\LdapAdapter\LdapAdapterInterface;

/**
 * Active Directory / LDAP Search
 *
 * @version 1.0.0
 */
abstract class AbstractServerSearchInterface implements ActiveDirectorySearchInterface
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
     * By default, this searches the 'cn', `samaccountname`, `displayname`, `surname`, and `mail` fields.
     *
     * @param string $name          name to search for
     * @param array  $fields        fields to be searched
     * @param int    $count         number of results to return
     * @param bool   $includeGroups include groups in search results
     *
     * @return ADUser[]
     */
    public function search(string $name, array $fields = ['cn', 'samaccountname', 'displayname', 'surname', 'mail'], int $count = 30, bool $includeGroups = false): array
    {
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
                false === $domain ||
                !isset($user['samaccountname'])
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
     * @param array  $fields
     *
     * @return ADUser|null
     */
    public function getUser(string $name, array $fields = ['samaccountname', 'mail'])
    {
        // Sanitize
        $searchName = $this->ldapAdapter->escape($name);
        $filter = $this->buildSearchFilter($searchName, $fields, false);

        $info = $this->ldapAdapter->search(
            $filter,
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
     *
     * @return string
     */
    abstract protected function chooseNameForAccount(array $adFields): string;

    /**
     * @param string $adDn
     *
     * @return string
     */
    abstract protected function dnToDomain(string $adDn): string;

    /**
     * Return value or null
     *
     * @param array  $users Users array
     * @param string $key   Users array key
     *
     * @return string|null
     **/
    protected function valueOrNull(array $users, string $key)
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
    protected function buildSearchFilter(string $name, array $fields, bool $includeGroups): string
    {
        $filter = '';

        foreach ($fields as $field) {
            $filter = $filter.'('.$field.'='.$name.'*)';
        }

        $filter = '(|'.$filter.')';

        if ($includeGroups) {
            return $filter;
        }

        return '(&(objectclass=user)'.$filter.')';
    }
}
