<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gubler\ADSearchBundle\Model\User;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Ldap\Entry;

interface ADUserInterface
{
    /**
     * @return UuidInterface
     */
    public function getADGuid(): UuidInterface;

    /**
     * @return string
     */
    public function getADDn(): string;

    /**
     * @return string
     */
    public function getADSamAccountName(): string;

    /**
     * @return Entry
     */
    public function getADEntry(): Entry;

    /**
     * @param Entry $entry
     *
     * @return ADUserInterface
     */
    public function setADInfo(Entry $entry): ADUserInterface;
}
