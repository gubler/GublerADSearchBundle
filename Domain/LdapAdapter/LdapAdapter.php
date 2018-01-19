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
 * Class LdapAdapter
 */
class LdapAdapter implements LdapAdapterInterface
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

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param string          $ldapUsername Username for initial binding to LDAP
     * @param string          $ldapPassword Password for initial binding to LDAP
     * @param string          $ldapHost     LDAP server hostname
     * @param string          $ldapPort     LDAP server port
     * @param string          $ldapBaseDn   Base DN for LDAP tree searches
     * @param LoggerInterface $logger
     */
    public function __construct(string $ldapUsername, string $ldapPassword, string $ldapHost, string $ldapPort, string $ldapBaseDn, LoggerInterface $logger)
    {
        $this->ldapUsername = $ldapUsername;
        $this->ldapPassword = $ldapPassword;
        $this->ldapHost = $ldapHost;
        $this->ldapPort = $ldapPort;
        $this->ldapBaseDn = $ldapBaseDn;
        $this->logger = $logger;

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
        \ldap_unbind($this->ldapConnection);
    }

    /**
     * {@inheritdoc}

     * @param string $filter
     * @param array  $attributes
     * @param int    $attrsonly
     * @param int    $sizelimit
     * @param int    $timelimit
     *
     * @return array|bool
     */
    public function search(string $filter, array $attributes = [], int $attrsonly = 0, int $sizelimit = 1000, int $timelimit = 300)
    {
        \ldap_control_paged_result($this->ldapConnection, $sizelimit);

        $this->logger->debug('LDAP Search with filter: `'.$filter.'`');

        $ldapSearch = \ldap_search(
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
            $attrsonly,
            $sizelimit,
            $timelimit
        );

        $results = \ldap_get_entries($this->ldapConnection, $ldapSearch);

        $this->logger->debug('LDAP Search results: `'.json_encode($results).'`');

        return $results;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $value
     *
     * @return string
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
        );

        return $cleaned;
    }

    /**
     * Connects and binds LDAP as well as setting OPT_PROTOCOL to 3
     *
     * @return void
     */
    protected function ldapConnect()
    {
        $this->ldapConnection = \ldap_connect($this->ldapHost, $this->ldapPort);
        \ldap_set_option($this->ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
        \ldap_set_option($this->ldapConnection, LDAP_OPT_REFERRALS, 0);
        $bindStatus = \ldap_bind($this->ldapConnection, $this->ldapUsername, $this->ldapPassword);
        if (false === $bindStatus) {
            $this->logger->alert('Unable to bind to LDAP server');
        }
    }
}
