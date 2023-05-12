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

interface LdapFactoryInterface
{
    /**
     * @param string $host
     * @param int    $port
     * @param string $bindDn
     * @param string $bindPassword
     */
    public function __construct(string $host, int $port, string $bindDn, string $bindPassword, bool $secure = false, ?string $certPath = null);

    /**
     * @return Ldap
     */
    public function getLdapConnection(): Ldap;
}
