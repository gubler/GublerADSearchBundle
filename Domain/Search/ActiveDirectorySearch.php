<?php
/**
 * AD Search Interface
 **/

namespace Gubler\ADSearchBundle\Domain\Search;

use Gubler\ADSearchBundle\Domain\LdapAdapter\LdapAdapterInterface;
use Gubler\ADSearchBundle\Entity\ADUser;

/**
 * AD Search Interface
 *
 * @version 1.0.0
 **/
interface ActiveDirectorySearch
{
    /**
     * Constructor with arguments supplied via service
     *
     * @param LdapAdapterInterface $ldapAdapter
     */
    public function __construct(LdapAdapterInterface $ldapAdapter);

    /**
     * Search Active Directory for name and return a list of matches.
     *
     * @param string $name  Name of user to search for
     * @param array  $fields fields to search
     * @param int    $count Max number of results to return
     * @param bool   $includeGroups
     *
     * @return ADUser[]
     */
    public function search(
        string $name,
        array $fields = ['cn', 'samaccountname', 'displayname', 'surname', 'mail'],
        int $count = 30,
        bool $includeGroups = false
    ): array;

    /**
     * Retrieve a single user from Active Directory or null if no user found.
     *
     * Fields will be searched in array order of the `$fields` parameter.
     *
     * @param string $name   `samaccountname` of user to get information for
     * @param array  $fields array of fields to search by, in order of preference
     *
     * @return ADUser|null
     */
    public function getUser(string $name, array $fields = ['samaccountname', 'mail']);
}
