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

use Gubler\ADSearchBundle\Lib\EntryAttributeHelper;
use Gubler\ADSearchBundle\Service\ArraySearch\ArraySearch;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Uid\Uuid;

class ArraySearchTest extends TestCase
{
    protected ArraySearch $search;

    protected function setUp(): void
    {
        $this->search = new ArraySearch(pathToTestUsersJson: __DIR__ . '/test_users.json');
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
        $found = $this->search->search(term: 'particle', fields: ['displayName']);
        self::assertCount(expectedCount: 3, haystack: $found);
        foreach ($found as $entry) {
            self::assertInstanceOf(expected: Entry::class, actual: $entry);
            $displayName = EntryAttributeHelper::getAttribute(entry: $entry, attribute: 'displayName');
            $result[] = $displayName;
            self::assertContains(needle: $displayName, haystack: $expected);
        }

        self::assertCount(expectedCount: 3, haystack: $result);
    }

    /**
     * @covers \Gubler\ADSearchBundle\Service\ArraySearch\ArraySearch
     */
    public function testCanFindUserByGuidString(): void
    {
        $expected = 'Particle, Proton';

        $guid = Uuid::fromString(uuid: '192D7590-6036-4358-9239-BEA350285CA1');

        $entry = $this->search->find(adGuid: $guid);

        self::assertInstanceOf(expected: Entry::class, actual: $entry);
        $displayName = EntryAttributeHelper::getAttribute(entry: $entry, attribute: 'displayName');
        self::assertEquals(expected: $expected, actual: $displayName);
    }

    /**
     * @covers \Gubler\ADSearchBundle\Service\ArraySearch\ArraySearch
     */
    public function testCanFindUserByGuidBytes(): void
    {
        $expected = 'Particle, Proton';

        $guid = Uuid::fromString(uuid: '192D7590-6036-4358-9239-BEA350285CA1');
        $bytes = $guid->toBinary();
        $guid = Uuid::fromBinary(uid: $bytes);
        $entry = $this->search->find(adGuid: $guid);

        self::assertInstanceOf(expected: Entry::class, actual: $entry);
        $displayName = EntryAttributeHelper::getAttribute(entry: $entry, attribute: 'displayName');
        self::assertEquals(expected: $expected, actual: $displayName);
    }

    /**
     * @covers \Gubler\ADSearchBundle\Service\ArraySearch\ArraySearch
     */
    public function testFindCanReturnNull(): void
    {
        $guid = Uuid::fromString(uuid: '192D7591-6036-4358-9239-BEA350285CA1');
        $entry = $this->search->find(adGuid: $guid);
        self::assertNull(actual: $entry);
    }

    /**
     * @covers \Gubler\ADSearchBundle\Service\ArraySearch\ArraySearch
     */
    public function testCanFindUserByField(): void
    {
        $expected = 'Particle, Proton';

        $entry = $this->search->findOne(byField: 'samaccountname', term: 'atomproton');
        self::assertInstanceOf(expected: Entry::class, actual: $entry);
        $displayName = EntryAttributeHelper::getAttribute(entry: $entry, attribute: 'displayName');
        self::assertEquals(expected: $expected, actual: $displayName);

        $entry = $this->search->findOne(byField: 'samaccountname', term: 'atompro');
        self::assertNull(actual: $entry);
    }
}
