<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Gubler\ADSearchBundle\Test\Model\Search\ArraySearch;

use Gubler\ADSearchBundle\Model\Search\ArraySearch\ArraySearch;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Ldap\Entry;

class ArraySearchTest extends TestCase
{
    /** @var ArraySearch */
    protected $search;

    public function setUp(): void
    {
        $this->search = new ArraySearch(__DIR__.'/test_users.json');
    }

    /**
     * @test
     */
    public function canSearchForUsers(): void
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
     * @test
     */
    public function canFindUserByGuidString(): void
    {
        $expected = 'Particle, Proton';

        $guid = Uuid::fromString('192D7590-6036-4358-9239-BEA350285CA1');

        $entry = $this->search->find($guid);

        self::assertInstanceOf(Entry::class, $entry);
        self::assertEquals($expected, $entry->getAttribute('displayName')[0]);
    }

    /**
     * @test
     */
    public function canFindUserByGuidBytes(): void
    {
        $expected = 'Particle, Proton';

        $guid = Uuid::fromString('192D7590-6036-4358-9239-BEA350285CA1');
        $bytes = $guid->getBytes();
        $guid = Uuid::fromBytes($bytes);
        $entry = $this->search->find($guid);

        self::assertInstanceOf(Entry::class, $entry);
        self::assertEquals($expected, $entry->getAttribute('displayName')[0]);
    }

    /**
     * @test
     */
    public function findCanReturnNull(): void
    {
        $guid = Uuid::fromString('192D7591-6036-4358-9239-BEA350285CA1');
        $entry = $this->search->find($guid);
        self::assertNull($entry);
    }

    /**
     * @test
     */
    public function canFindUserByField(): void
    {
        $expected = 'Particle, Proton';

        $entry = $this->search->findOne('samaccountname', 'atomproton');
        self::assertInstanceOf(Entry::class, $entry);
        self::assertEquals($expected, $entry->getAttribute('displayName')[0]);

        $entry = $this->search->findOne('samaccountname', 'atompro');
        self::assertNull($entry);
    }
}
