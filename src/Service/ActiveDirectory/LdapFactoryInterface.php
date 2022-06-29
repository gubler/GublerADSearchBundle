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

interface LdapFactoryInterface
{
    public function __construct(string $host, int $port, string $bindDn, string $bindPassword);

    public function getLdapConnection(): Ldap;
}
