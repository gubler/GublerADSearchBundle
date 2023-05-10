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

namespace Gubler\ADSearchBundle\Service;

use Symfony\Component\Ldap\Entry;
use Symfony\Component\Uid\Uuid;

interface ADSearchAdapterInterface
{
    /**
     * @param string[] $fields
     *
     * @return Entry[]
     */
    public function search(string $term, array $fields, string $dn = '', int $maxResults = 50): array;

    public function findOne(string $byField, string $term, string $dn = ''): ?Entry;

    public function find(Uuid $adGuid, string $dn = ''): ?Entry;
}
