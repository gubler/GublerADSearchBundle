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

namespace Gubler\ADSearchBundle\Test\Service\ActiveDirectory;

use Gubler\ADSearchBundle\Exception\NonUniqueADResultException;
use Gubler\ADSearchBundle\Lib\EntryAttributeHelper;
use Gubler\ADSearchBundle\Service\ActiveDirectory\LdapFactoryInterface;
use Gubler\ADSearchBundle\Service\ActiveDirectory\ServerSearch;
use Gubler\ADSearchBundle\Test\BasicCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Ldap\Adapter\AdapterInterface;
use Symfony\Component\Ldap\Adapter\CollectionInterface;
use Symfony\Component\Ldap\Adapter\QueryInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Uid\Uuid;

class ServerSearchTest extends TestCase
{
    private ServerSearch $search;
    /**
     * @phpstan-var QueryInterface&MockObject
     */
    private QueryInterface $query;

    /**
     * @var Entry[]
     */
    private array $users;

    protected function setUp(): void
    {
        $this->setUsers();
        $this->query = $this->getMockBuilder(className: QueryInterface::class)->getMock();
        $adapter = $this->getMockBuilder(className: AdapterInterface::class)->getMock();
        $adapter
            ->expects(self::once())
            ->method('createQuery')
            ->willReturn(value: $this->query)
        ;
        $ldap = new Ldap(adapter: $adapter);
        $ldapFactory = $this->createMock(originalClassName: LdapFactoryInterface::class);
        $ldapFactory->method('getLdapConnection')
            ->willReturn(value: $ldap);
        $this->search = new ServerSearch(ldapFactory: $ldapFactory);
    }

    /**
     * @covers \Gubler\ADSearchBundle\Service\ActiveDirectory\ServerSearch
     */
    public function testCanSearchForUsers(): void
    {
        $this->query
            ->expects(self::once())
            ->method('execute')
            ->willReturn(value: $this->ldapReturnEntries(users: $this->users))
        ;

        $expected = [
            'Particle, Proton',
            'Particle, Neutron',
        ];

        $result = [];
        $found = $this->search->search(term: 'particle', fields: ['displayName']);
        foreach ($found as $entry) {
            self::assertInstanceOf(expected: Entry::class, actual: $entry);
            $displayName = EntryAttributeHelper::getAttribute(entry: $entry, attribute: 'displayName');
            $result[] = $displayName;
            self::assertContains(needle: $displayName, haystack: $expected);
        }

        self::assertCount(expectedCount: 2, haystack: $result);
    }

    /**
     * @covers \Gubler\ADSearchBundle\Service\ActiveDirectory\ServerSearch
     */
    public function testCanFindUserByGuid(): void
    {
        $this->query
            ->expects(self::once())
            ->method('execute')
            ->willReturn(value: $this->ldapReturnEntries(users: [$this->users[0]]))
        ;

        $expected = 'Particle, Proton';
        $guid = Uuid::fromString(uuid: '192D7590-6036-4358-9239-BEA350285CA1');
        $entry = $this->search->find(adGuid: $guid);
        self::assertInstanceOf(expected: Entry::class, actual: $entry);
        self::assertEquals(
            expected: $expected,
            actual: EntryAttributeHelper::getAttribute(
                entry: $entry,
                attribute: 'displayName'));
    }

    /**
     * @covers \Gubler\ADSearchBundle\Service\ActiveDirectory\ServerSearch
     */
    public function testFindCanReturnNull(): void
    {
        $this->query
            ->expects(self::once())
            ->method('execute')
            ->willReturn(value: $this->ldapReturnEntries(users: []))
        ;

        $guid = Uuid::fromString(uuid: '192D7590-6036-4358-9239-BEA350285CA1');
        $entry = $this->search->find(adGuid: $guid);
        self::assertNull(actual: $entry);
    }

    /**
     * @covers \Gubler\ADSearchBundle\Service\ActiveDirectory\ServerSearch
     */
    public function testThrowsNonUniqueErrorIfFindMethodFindsMultipleUsers(): void
    {
        $this->expectException(exception: NonUniqueADResultException::class);

        $this->query
            ->expects(self::once())
            ->method('execute')
            ->willReturn(value: $this->ldapReturnEntries(users: $this->users))
        ;

        $guid = Uuid::fromString(uuid: '192D7590-6036-4358-9239-BEA350285CA1');
        $this->search->find(adGuid: $guid);
    }

    /**
     * @covers \Gubler\ADSearchBundle\Service\ActiveDirectory\ServerSearch
     */
    public function testCanFindUserByField(): void
    {
        $this->query
            ->expects(self::once())
            ->method('execute')
            // will return an array with the first test Entry
            ->willReturn(value: $this->ldapReturnEntries(users: [$this->users[0]]))
        ;

        $expected = 'Particle, Proton';

        $entry = $this->search->findOne(byField: 'sAMAccountName', term: 'atomproton');
        self::assertInstanceOf(expected: Entry::class, actual: $entry);
        $displayName = EntryAttributeHelper::getAttribute(entry: $entry, attribute: 'displayName');
        self::assertEquals(expected: $expected, actual: $displayName);
    }

    /**
     * @covers \Gubler\ADSearchBundle\Service\ActiveDirectory\ServerSearch
     */
    public function testFindByCanReturnNull(): void
    {
        $this->query
            ->expects(self::once())
            ->method('execute')
            ->willReturn(value: $this->ldapReturnEntries(users: []))
        ;

        $entry = $this->search->findOne(byField: 'sAMAccountName', term: 'atomproton');
        self::assertNull(actual: $entry);
    }

    /**
     * @covers \Gubler\ADSearchBundle\Service\ActiveDirectory\ServerSearch
     */
    public function testThrowsNonUniqueErrorIfFindByFindsMultipleUsers(): void
    {
        $this->expectException(exception: NonUniqueADResultException::class);

        $this->query
            ->expects(self::once())
            ->method('execute')
            ->willReturn(value: $this->ldapReturnEntries(users: $this->users))
        ;

        $this->search->findOne(byField: 'sAMAccountName', term: 'atomproton');
    }

    private function setUsers(): void
    {
        $this->users = [
            new Entry(
                dn: 'CN=Particle, Proton,OU=atom,DC=acme,DC=com',
                attributes: [
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
                    'objectGUID' => [Uuid::fromString(uuid: '192D7590-6036-4358-9239-BEA350285CA1')->toBinary()],
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
                dn: 'CN=Particle, Proton,OU=atom,DC=acme,DC=com',
                attributes: [
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
                    'objectGUID' => [Uuid::fromString(uuid: '5199FB62-A76F-45B1-B01B-8FB7B7C9248C')->toBinary()],
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
    }

    /**
     * @param Entry[] $users
     *
     * @return BasicCollection<int, Entry>
     */
    private function ldapReturnEntries(array $users = []): CollectionInterface
    {
        return new BasicCollection(entries: $users);
    }
}
