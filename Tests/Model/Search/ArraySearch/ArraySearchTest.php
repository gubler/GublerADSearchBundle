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

/**
 * Class ArraySearchTest
 */
class ArraySearchTest extends TestCase
{
    /** @var ArraySearch */
    protected $search;

    /**
     * Set Up Tests
     */
    public function setUp()
    {
        $this->search = new ArraySearch(__DIR__.'/test_users.json');
    }

    /**
     * @test
     */
    public function canSearchForUsers()
    {
        $expected = [
            'Particle, Proton',
            'Particle, Neutron',
            'Particle, Electron',
        ];

        $result = [];
        $found = $this->search->search('particle', ['displayName']);
        $this->assertCount(3, $found);
        foreach ($found as $entry) {
            $this->assertInstanceOf(Entry::class, $entry);
            /** @var Entry $entry */
            $displayName = $entry->getAttribute('displayName')[0];
            $result[] = $displayName;
            $this->assertTrue(\in_array($displayName, $expected, true));
        }

        $this->assertCount(3, $result);
    }

    /**
     * @test
     */
    public function canFindUserByGuid()
    {
        $expected = 'Particle, Proton';

        $guid = Uuid::fromString('192D7590-6036-4358-9239-BEA350285CA1');

        $entry = $this->search->find($guid);

        $this->assertInstanceOf(Entry::class, $entry);
        $this->assertEquals($expected, $entry->getAttribute('displayName')[0]);
    }

    /**
     * @test
     */
    public function findCanReturnNull()
    {
        $guid = Uuid::fromString('192D7590-6036-4358-9239-BEA350285CA2');
        $entry = $this->search->find($guid);
        $this->assertNull($entry);
    }

    /**
     * @test
     */
    public function canFindUserByField()
    {
        $expected = 'Particle, Proton';

        $entry = $this->search->findOne('samaccountname', 'atomproton');
        $this->assertInstanceOf(Entry::class, $entry);
        $this->assertEquals($expected, $entry->getAttribute('displayName')[0]);

        $entry = $this->search->findOne('samaccountname', 'atompro');
        $this->assertNull($entry);
    }
}
