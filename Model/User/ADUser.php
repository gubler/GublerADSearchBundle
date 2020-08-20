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

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Ldap\Entry;

class ADUser implements ADUserInterface
{
    /** @var UuidInterface */
    protected $ADGuid;
    /** @var string */
    protected $ADDn;
    /** @var string */
    protected $ADSamAccountName;
    /** @var array */
    protected $ADAttributes;

    /**
     * @param Entry $entry
     */
    public function __construct(Entry $entry)
    {
        $this->setADInfo($entry);
    }

    /**
     * @return UuidInterface
     */
    public function getADGuid(): UuidInterface
    {
        return $this->ADGuid;
    }

    /**
     * @return string
     */
    public function getADDn(): string
    {
        return $this->ADDn;
    }

    /**
     * @return string
     */
    public function getADSamAccountName(): string
    {
        return $this->ADSamAccountName;
    }

    /**
     * @return Entry
     */
    public function getADEntry(): Entry
    {
        return new Entry($this->ADDn, $this->ADAttributes);
    }

    /**
     * @param Entry $entry
     *
     * @return ADUserInterface
     */
    public function setADInfo(Entry $entry): ADUserInterface
    {
        $this->ADDn = $entry->getDn();
        $this->ADGuid = Uuid::fromBytes($entry->getAttribute('objectGUID')[0]);
        $this->ADSamAccountName = $entry->getAttribute('sAMAccountName')[0];
        $this->ADAttributes = $entry->getAttributes();

        return $this;
    }
}
