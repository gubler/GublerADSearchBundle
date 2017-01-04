<?php

namespace Gubler\ADSearchBundle\Domain\Search;

use Gubler\ADSearchBundle\Entity\ADUser;

/**
 * Search array for AD information.
 *
 * Used for local development and testing when no AD server may be available.
 *
 * @version 1.0.0
 * @package Gubler\ADSearchBundle\Domain\Search
 */
class ArraySearch implements ActiveDirectorySearch
{
    /**
     * test users
     *
     * @var array
     **/
    protected $users;

    /**
     * string to filter
     *
     * @var string
     **/
    protected $searchName;

    /**
     * Constructor with arguments supplied by service
     *
     * @param string $ldapUsername
     * @param string $ldapPassword
     * @param string $ldapHost
     * @param string $ldapPort
     * @param string $ldapBaseDn
     */
    public function __construct($ldapUsername, $ldapPassword, $ldapHost, $ldapPort, $ldapBaseDn)
    {
        $this->searchName = '';
        $this->users = $this->testUsers();
    }

    /**
     * {@inheritdoc}
     *
     * @param string $name
     * @param int    $count
     * @return array
     */
    public function search($name, $count = 30)
    {
        $this->searchName = $this->__ldapEscape($name);

        // array_filter $users array against $name
        $filteredUsers = array_filter($this->users, array($this, 'filterUsersArray'));

        $info = array();

        foreach ($filteredUsers as $f) {
            $user = (new ADUser($f['samaccountname']))
                ->setDomain('TEST')
                ->setName($f['cn'])
                ->setOffice($f['physicaldeliveryofficename'])
                ->setTitle($f['title'])
                ->setEmail($f['mail'])
                ->setPhone($f['telephonenumber']);
            $info[] = $user;
        }

        return $info;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $name
     * @return ADUser
     */
    public function getUser($name)
    {
        $this->searchName = $this->__ldapEscape($name);

        // array_filter $users array against $name
        $filteredUsers = array_filter($this->users, array($this, 'filterUsersArrayForSamaccountnameExactMatch'));

        $user = null;

        if (count($filteredUsers) == 1) {
            $keys = array_keys($filteredUsers);
            $key = $keys[0];
            $user = (new ADUser($filteredUsers[$key]['samaccountname']))
                ->setDomain('TEST')
                ->setName($filteredUsers[$key]['cn'])
                ->setOffice($filteredUsers[$key]['physicaldeliveryofficename'])
                ->setTitle($filteredUsers[$key]['title'])
                ->setEmail($filteredUsers[$key]['mail'])
                ->setPhone($filteredUsers[$key]['telephonenumber']);
        }

        // return formatted results
        return $user;
    }

    /**
     * Escape the string used in LDAP search in order to avoid
     * "LDAP-injections"
     *
     * @param   string   $str
     * @return  string
     */
    protected function __ldapEscape($str)
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
        );

        return ($str);
    }

    /**
     * test samaccountname and cn if they contain search term
     *
     * @param  array $search array to search
     * @return bool
     * @SuppressWarnings("unused")
     **/
    protected function filterUsersArray($search)
    {
        if (stripos($search['samaccountname'], $this->searchName) !== false ||
            stripos($search['cn'], $this->searchName) !== false ||
            stripos($search['mail'], $this->searchName) !== false
        ) {
            return true;
        }

        return false;
    }

    /**
     * test samaccountname for exact match
     *
     * @param  array $search array to search
     * @return bool
     * @SuppressWarnings("unused")
     **/
    protected function filterUsersArrayForSamaccountnameExactMatch($search)
    {
        if ($search['samaccountname'] == $this->searchName) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    protected function testUsers()
    {
        return [
            # System Admin
            ['cn' => 'System Admin', 'samaccountname'=>  'admin', 'mail' => 'admin@example.com', 'title' => 'System Admin', 'telephonenumber' => '1234', 'physicaldeliveryofficename' => 'VISN'],
            # Atom Group
            ['cn' => 'Proton', 'samaccountname'=>  'ATOMproton', 'mail' => 'proton@example.com', 'title' => 'Proton', 'physicaldeliveryofficename' => 'The Atom', 'telephonenumber' => '1234'],
            ['cn' => 'Neutron', 'samaccountname'=> 'ATOMneutron', 'mail' => 'neutron@example.com', 'title' => 'Neutron', 'physicaldeliveryofficename' => 'The Atom', 'telephonenumber' => '1234'],
            ['cn' => 'Electron', 'samaccountname'=>  'ATOMelectron', 'mail' => 'electron@example.com', 'title' => 'Electron', 'physicaldeliveryofficename' => 'The Atom', 'telephonenumber' => '1234'],
            ['cn' => 'Quark', 'samaccountname'=>  'ATOMquark', 'mail' => 'quark@example.com', 'title' => 'Quark', 'physicaldeliveryofficename' => 'The Atom', 'telephonenumber' => '1234'],
            # α Group
            ['cn' => 'Cat, Alpha', 'samaccountname'=>  'ALPHAcat', 'mail' => 'alpha.cat@example.com', 'title' => 'Alpha AI Coordinator', 'physicaldeliveryofficename' => 'ALPHA', 'telephonenumber' => '1234'],
            ['cn' => 'Siamese, Alpha', 'samaccountname'=>  'ALPHAsiamese', 'mail' => 'alpha.siamese@example.com', 'title' => 'Alpha Siamese', 'physicaldeliveryofficename' => 'ALPHA', 'telephonenumber' => '1234'],
            ['cn' => 'Sphinx, Alpha', 'samaccountname'=>  'ALPHAsphinx', 'mail' => 'alpha.sphinx@example.com', 'title' => 'Alpha Sphinx', 'physicaldeliveryofficename' => 'ALPHA', 'telephonenumber' => '1234'],
            ['cn' => 'Bengal, Alpha', 'samaccountname'=>  'ALPHAbengal', 'mail' => 'alpha.bengal@example.com', 'title' => 'Alpha Bengal', 'physicaldeliveryofficename' => 'ALPHA', 'telephonenumber' => '1234'],
            ['cn' => 'Persian, Alpha', 'samaccountname'=>  'ALPHApersian', 'mail' => 'alpha.persian@example.com', 'title' => 'Alpha Persion', 'physicaldeliveryofficename' => 'ALPHA', 'telephonenumber' => '1234'],
            # β Group
            ['cn' => 'Dog, Beta', 'samaccountname'=>  'BETAdog', 'mail' => 'beta.dog@example.com', 'title' => 'Beta Dog', 'physicaldeliveryofficename' => 'BETA', 'telephonenumber' => '1234'],
            ['cn' => 'Corgi, Beta', 'samaccountname'=>  'BETAcorgi', 'mail' => 'beta.corgi@example.com', 'title' => 'Beta Corgi', 'physicaldeliveryofficename' => 'BETA', 'telephonenumber' => '1234'],
            ['cn' => 'Mastiff, Beta', 'samaccountname'=>  'BETAmastiff', 'mail' => 'beta.mastiff@example.com', 'title' => 'Beta Mastiff', 'physicaldeliveryofficename' => 'BETA', 'telephonenumber' => '1234'],
            ['cn' => 'Poodle, Beta', 'samaccountname'=>  'BETApoodle', 'mail' => 'beta.poodle@example.com', 'title' => 'Beta Poodle', 'physicaldeliveryofficename' => 'BETA', 'telephonenumber' => '1234'],
            ['cn' => 'Boxer, Beta', 'samaccountname'=>  'BETAboxer', 'mail' => 'beta.boxer@example.com', 'title' => 'Beta Boxer', 'physicaldeliveryofficename' => 'BETA', 'telephonenumber' => '1234'],
        ];
    }
}
