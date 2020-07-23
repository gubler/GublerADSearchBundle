<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gubler\ADSearchBundle\Model\Search;

use Ramsey\Uuid\Guid\Guid;
use Symfony\Component\Ldap\Entry;

interface ADSearchAdapterInterface
{
    /**
     * @param string $term
     * @param array  $fields
     *
     * @return array
     */
    public function search(string $term, array $fields): array;

    /**
     * @param string $byField
     * @param string $term
     *
     * @return null|Entry
     */
    public function findOne(string $byField, string $term): ?Entry;

    /**
     * @param Guid $guid
     *
     * @return null|Entry
     */
    public function find(Guid $guid): ?Entry;
}
