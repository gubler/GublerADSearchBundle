<?php

declare(strict_types=1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gubler\ADSearchBundle\Service\ActiveDirectory;

use Symfony\Component\Ldap\Ldap;

final class LdapFactory implements LdapFactoryInterface
{
    protected Ldap $ldap;

    public function __construct(
        string $host,
        int $port,
        string $bindDn,
        string $bindPassword,
        bool $secure,
        ?string $certPath
    ) {
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

        $this->ldap->bind(dn: $bindDn, password: $bindPassword);
    }

    public function getLdapConnection(): Ldap
    {
        return $this->ldap;
    }
}
