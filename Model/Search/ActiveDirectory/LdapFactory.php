<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gubler\ADSearchBundle\Model\Search\ActiveDirectory;

use Symfony\Component\Ldap\Ldap;

class LdapFactory implements LdapFactoryInterface
{
    /** @var Ldap */
    protected $ldap;

    /**
     * @param string $host
     * @param int    $port
     * @param string $bindDn
     * @param string $bindPassword
     */
    public function __construct(string $host, int $port, string $bindDn, string $bindPassword, bool $secure = false, ?string $certPath = null)
    {
        $config = [
            'host' => $host,
            'port' => $port,
        ];

        if ($secure) {
            $config['encryption'] = 'tls';

            if (null !== $certPath) {
                $config['options']['x_tls_cacertfile'] = $certPath;
                $config['options']['x_tls_require_cert'] = true;
            }
        }

        $this->ldap = Ldap::create(
            adapter: 'ext_ldap',
            config: $config,
        );

        $this->ldap->bind($bindDn, $bindPassword);
    }

    /**
     * @return Ldap
     */
    public function getLdapConnection(): Ldap
    {
        return $this->ldap;
    }
}
