<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Gubler\ADSearchBundle\Test\Model\Search\ActiveDirectory;

use Gubler\ADSearchBundle\Exception\NonUniqueADResultException;
use Gubler\ADSearchBundle\Model\Search\ActiveDirectory\LdapFactory;
use Gubler\ADSearchBundle\Model\Search\ActiveDirectory\ServerSearch;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Ldap\Adapter\AdapterInterface;
use Symfony\Component\Ldap\Adapter\ExtLdap\Collection;
use Symfony\Component\Ldap\Adapter\QueryInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Ldap;

/**
 * Class ServerSearchTest
 */
class ServerSearchTest extends TestCase
{
    /** @var ServerSearch */
    protected $search;
    /** @var QueryInterface */
    protected $query;
    /** @var AdapterInterface */
    protected $adapter;
    /** @var Ldap */
    protected $ldap;

    protected function setUp(): void
    {
        $this->query = $this->getMockBuilder(QueryInterface::class)->getMock();
        $this->adapter = $this->getMockBuilder(AdapterInterface::class)->getMock();
        $this->adapter
            ->expects($this->once())
            ->method('createQuery')
            ->willReturn($this->query)
        ;
        $this->ldap = new Ldap($this->adapter);
        $ldapFactory = $this->createMock(LdapFactory::class);
        $ldapFactory->method('getLdapConnection')
            ->willReturn($this->ldap);
        $this->search = new ServerSearch($ldapFactory);
    }

    /**
     * @test
     */
    public function canSearchForUsers(): void
    {
        $this->query
            ->expects($this->once())
            ->method('execute')
            ->willReturn($this->ldapReturnEntries(true))
        ;

        $expected = [
            'Particle, Proton',
            'Particle, Neutron',
        ];

        $result = [];
        $found = $this->search->search('particle', ['displayName']);
        foreach ($found as $entry) {
            $this->assertInstanceOf(Entry::class, $entry);
            /** @var Entry $entry */
            $displayName = $entry->getAttribute('displayName')[0];
            $result[] = $displayName;
            $this->assertContains($displayName, $expected);
        }

        $this->assertCount(2, $result);
    }

    /**
     * @test
     */
    public function canFindUserByGuid(): void
    {
        $this->query
            ->expects($this->once())
            ->method('execute')
            // will return an array with the first test Entry
            ->willReturn([$this->ldapReturnEntries()[0]])
        ;

        $expected = 'Particle, Proton';
        $guid = Uuid::fromString('192D7590-6036-4358-9239-BEA350285CA1');
        $entry = $this->search->find($guid);
        $this->assertInstanceOf(Entry::class, $entry);
        $this->assertEquals($expected, $entry->getAttribute('displayName')[0]);
    }

    /**
     * @test
     */
    public function findCanReturnNull(): void
    {
        $this->query
            ->expects($this->once())
            ->method('execute')
            // will return an array with the first test Entry
            ->willReturn([])
        ;

        $guid = Uuid::fromString('192D7590-6036-4358-9239-BEA350285CA1');
        $entry = $this->search->find($guid);
        $this->assertNull($entry);
    }

    /**
     * @test
     */
    public function throwsNonUniqueErrorIfFindMethodFindsMultipleUsers(): void
    {
        $this->expectException(NonUniqueADResultException::class);

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->willReturn($this->ldapReturnEntries())
        ;

        $guid = Uuid::fromString('192D7590-6036-4358-9239-BEA350285CA1');
        $this->search->find($guid);
    }

    /**
     * @test
     */
    public function canFindUserByField(): void
    {
        $this->query
            ->expects($this->once())
            ->method('execute')
            // will return an array with the first test Entry
            ->willReturn([$this->ldapReturnEntries()[0]])
        ;

        $expected = 'Particle, Proton';

        $entry = $this->search->findOne('sAMAccountName', 'atomproton');
        $this->assertInstanceOf(Entry::class, $entry);
        $this->assertEquals($expected, $entry->getAttribute('displayName')[0]);
    }

    /**
     * @test
     */
    public function findByCanReturnNull(): void
    {
        $this->query
            ->expects($this->once())
            ->method('execute')
            ->willReturn([])
        ;

        $entry = $this->search->findOne('sAMAccountName', 'atomproton');
        $this->assertNull($entry);
    }

    /**
     * @test
     */
    public function throwsNonUniqueErrorIfFindByFindsMultipleUsers(): void
    {
        $this->expectException(NonUniqueADResultException::class);

        $this->query
            ->expects($this->once())
            ->method('execute')
            ->willReturn($this->ldapReturnEntries())
        ;

        $this->search->findOne('sAMAccountName', 'atomproton');
    }


    /**
     * @param bool $asObject
     *
     * @return mixed
     */
    protected function ldapReturnEntries(bool $asObject = false)
    {
        $users = [
            new Entry(
                'CN=Particle, Proton,OU=atom,DC=acme,DC=com',
                [
                    'objectClass' => ['top', 'person', 'organizationalPerson', 'user'],
                    'cn' => ['Particle, Proton'],
                    'sn' => ['Proton'],
                    'l' => ['Nucleus'],
                    'st' => ['Atom'],
                    'title' => ['Nuclear Particle'],
                    'description' => ['Positively Charged'],
                    'postalCode' => ['00001'],
                    'physicalDeliveryOfficeName' => ['The Atom'],
                    'telephoneNumber' => ['123123'],
                    'givenName' => ['Proton'],
                    'initials' => ['PP'],
                    'distinguishedName' => ['CN=Particle, Proton,OU=atom,DC=acme,DC=com'],
                    'instanceType' => ['4'],
                    'whenCreated' => ['20180101100000.0Z'],
                    'whenChanged' => ['20180102160000.0Z'],
                    'displayName' => ['Particle, Proton'],
                    'uSNCreated' => ['1001'],
                    'uSNChanged' => ['1004'],
                    'co' => ['Cosmos'],
                    'department' => ['Atoms'],
                    'company' => ['Acme, Inc.'],
                    'proxyAddresses' => ['SMTP =>proton.particle@acme.com'],
                    'streetAddress' => ['123 The Atom'],
                    'name' => ['Particle, Proton'],
                    'objectGUID' => ['192D7590-6036-4358-9239-BEA350285CA1'],
                    'userAccountControl' => ['111222'],
                    'pwdLastSet' => ['131145256860505663'],
                    'primaryGroupID' => ['5521'],
                    'objectSid' => ['S-1-5-21-3165297888-301567370-590-1103'],
                    'sAMAccountName' => ['ATOMproton'],
                    'sAMAccountType' => ['805306368'],
                    'userPrincipalName' => ['proton.particle@acme.com'],
                    'objectCategory' => ['CN=Person,CN=Schema,CN=Configuration,DC=acme,DC=com'],
                    'lastLogonTimestamp' => ['131610992411099685'],
                    'mail' => ['proton.particle@acme.com'],
                ]
            ),
            new Entry(
                'CN=Particle, Proton,OU=atom,DC=acme,DC=com',
                [
                    'objectClass' => ['top', 'person', 'organizationalPerson', 'user'],
                    'cn' => ['Particle, Neutron'],
                    'sn' => ['Neutron'],
                    'l' => ['Nucleus'],
                    'st' => ['Atom'],
                    'title' => ['Nuclear Particle'],
                    'description' => ['No Charge'],
                    'postalCode' => ['00001'],
                    'physicalDeliveryOfficeName' => ['The Atom'],
                    'telephoneNumber' => ['234234'],
                    'givenName' => ['Neutron'],
                    'initials' => ['NP'],
                    'distinguishedName' => ['CN=Particle, Neutron,OU=atom,DC=acme,DC=com'],
                    'instanceType' => ['4'],
                    'whenCreated' => ['20180101100000.0Z'],
                    'whenChanged' => ['20180102160000.0Z'],
                    'displayName' => ['Particle, Neutron'],
                    'uSNCreated' => ['1001'],
                    'uSNChanged' => ['1004'],
                    'co' => ['Cosmos'],
                    'department' => ['Atoms'],
                    'company' => ['Acme, Inc.'],
                    'proxyAddresses' => ['SMTP =>neutron.particle@acme.com'],
                    'streetAddress' => ['123 The Atom'],
                    'name' => ['Particle, Neutron'],
                    'objectGUID' => ['5199FB62-A76F-45B1-B01B-8FB7B7C9248C'],
                    'userAccountControl' => ['111222'],
                    'pwdLastSet' => ['131145256860505663'],
                    'primaryGroupID' => ['5521'],
                    'objectSid' => ['S-1-5-21-3165297888-301567370-904-1103'],
                    'sAMAccountName' => ['ATOMneutron'],
                    'sAMAccountType' => ['805306368'],
                    'userPrincipalName' => ['neutron.particle@acme.com'],
                    'objectCategory' => ['CN=Person,CN=Schema,CN=Configuration,DC=acme,DC=com'],
                    'lastLogonTimestamp' => ['131610992411099685'],
                    'mail' => ['neutron.particle@acme.com'],
                ]
            ),
        ];

        if ($asObject) {
            $collection = $this->getMockBuilder(Collection::class)
                ->disableOriginalConstructor()
                ->getMock();
            $collection->method('toArray')
                ->willReturn($users);

            return $collection;
        }

        return $users;
    }
}
