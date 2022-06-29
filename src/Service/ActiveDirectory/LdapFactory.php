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

class LdapFactory implements LdapFactoryInterface
{
    protected Ldap $ldap;

    public function __construct(string $host, int $port, string $bindDn, string $bindPassword)
    {
        $this->ldap = Ldap::create(
            'ext_ldap',
            ['connection_string' => 'ldap://' . $host . ':' . $port]
        );

        $this->ldap->bind($bindDn, $bindPassword);
    }

    public function getLdapConnection(): Ldap
    {
        return $this->ldap;
    }
}
