<?php
/**
 * AD Search Interface
 **/

namespace Gubler\ADSearchBundle\Domain\Search;

/**
 * AD Search Interface
 *
 * @package Gubler\ADSearchBundle\Domain\Search
 * @version 1.0.0
 **/
interface ActiveDirectorySearch
{
    /**
     * Constructor with arguments supplied via service
     *
     * @param string $ldapUsername
     * @param string $ldapPassword
     * @param string $ldapHost
     * @param string $ldapPort
     * @param string $ldapBaseDn
     */
    public function __construct($ldapUsername, $ldapPassword, $ldapHost, $ldapPort, $ldapBaseDn);

    /**
     * Search Active Directory for name and return a list of matches.
     *
     * @param string $name  Name of user to search for
     * @param int    $count Max number of results to return
     * @return array
     **/
    public function search($name, $count);


    /**
     * Retrieve a single user from Active Directory by `samaccountname` or null if no user found.
     *
     * @param string $name `samaccount` name of user to get information for
     * @return mixed
     */
    public function getUser($name);
}
