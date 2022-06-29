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

namespace Gubler\ADSearchBundle\Test\Service\ArraySearch;

use Gubler\ADSearchBundle\Service\ArraySearch\ArraySearch;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Uid\Uuid;

class ArraySearchTest extends TestCase
{
    protected ArraySearch $search;

    protected function setUp(): void
    {
        $this->search = new ArraySearch(__DIR__ . '/test_users.json');
    }

    /**
     * @covers \Gubler\ADSearchBundle\Service\ArraySearch\ArraySearch
     */
    public function testCanSearchForUsers(): void
    {
        $expected = [
            'Particle, Proton',
            'Particle, Neutron',
            'Particle, Electron',
        ];

        $result = [];
        $found = $this->search->search('particle', ['displayName']);
        self::assertCount(3, $found);
        foreach ($found as $entry) {
            self::assertInstanceOf(Entry::class, $entry);
            $displayName = $entry->getAttribute('displayName')[0];
            $result[] = $displayName;
            self::assertContains($displayName, $expected);
        }

        self::assertCount(3, $result);
    }

    /**
     * @covers \Gubler\ADSearchBundle\Service\ArraySearch\ArraySearch
     */
    public function testCanFindUserByGuidString(): void
    {
        $expected = 'Particle, Proton';

        $guid = Uuid::fromString('192D7590-6036-4358-9239-BEA350285CA1');

        $entry = $this->search->find($guid);

        self::assertInstanceOf(Entry::class, $entry);
        self::assertEquals($expected, $entry->getAttribute('displayName')[0]);
    }

    /**
     * @covers \Gubler\ADSearchBundle\Service\ArraySearch\ArraySearch
     */
    public function testCanFindUserByGuidBytes(): void
    {
        $expected = 'Particle, Proton';

        $guid = Uuid::fromString('192D7590-6036-4358-9239-BEA350285CA1');
        $bytes = $guid->toBinary();
        $guid = Uuid::fromBinary($bytes);
        $entry = $this->search->find($guid);

        self::assertInstanceOf(Entry::class, $entry);
        self::assertEquals($expected, $entry->getAttribute('displayName')[0]);
    }

    /**
     * @covers \Gubler\ADSearchBundle\Service\ArraySearch\ArraySearch
     */
    public function testFindCanReturnNull(): void
    {
        $guid = Uuid::fromString('192D7591-6036-4358-9239-BEA350285CA1');
        $entry = $this->search->find($guid);
        self::assertNull($entry);
    }

    /**
     * @covers \Gubler\ADSearchBundle\Service\ArraySearch\ArraySearch
     */
    public function testCanFindUserByField(): void
    {
        $expected = 'Particle, Proton';

        $entry = $this->search->findOne('samaccountname', 'atomproton');
        self::assertInstanceOf(Entry::class, $entry);
        self::assertEquals($expected, $entry->getAttribute('displayName')[0]);

        $entry = $this->search->findOne('samaccountname', 'atompro');
        self::assertNull($entry);
    }
}
