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

/**
 * Interface ADUserInterface
 */
class ADUser implements ADUserInterface
{
    /** @var UuidInterface */
    protected $ADGuid;
    /** @var string */
    protected $ADDn;
    /** @var array */
    protected $ADAttributes;

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
        $this->ADGuid = $entry->getAttribute('objectguid');
        $this->ADAttributes = $entry->getAttributes();

        return $this;
    }
}
