<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Gubler\ADSearchBundle\Tests\Unit\Model\User;

use Gubler\ADSearchBundle\Model\User\ADUser;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Ldap\Entry;

/**
 * Class ADUserTest
 */
class ADUserTest extends TestCase
{
    /** @var Entry */
    protected $ldapEntry;
    /** @var Uuid */
    protected $uuid;

    /**
     * Create LDAP Entry for testing
     */
    public function setUp()
    {
        $this->uuid = Uuid::fromString('ea2e7d5e-beea-472b-8105-11e762733a34');
        $this->ldapEntry = new Entry('CN=Appleseed\, Jenny A.,OU=Sowers,DC=appleseedorchards', [
            "objectClass" => [
                0 => "top",
                1 => "person",
                2 => "organizationalPerson",
                3 => "user",
            ],
            "cn" => [
                0 => "Appleseed, Jenny A.",
            ],
            "sn" => [
                0 => "Jenny",
            ],
            "l" => [
                0 => "Leominster",
            ],
            "st" => [
                0 => "MA",
            ],
            "title" => [
                0 => "Sower of Apples",
            ],
            "description" => [
                0 => "Apple Bringer",
            ],
            "postalCode" => [
                0 => "01453",
            ],
            "physicalDeliveryOfficeName" => [
                0 => "Appleseed Orchard",
            ],
            "telephoneNumber" => [
                0 => "508-555-1212 x2222",
            ],
            "givenName" => [
                0 => "Jenny",
            ],
            "initials" => [
                0 => "A",
            ],
            "distinguishedName" => [
                0 => "CN=Appleseed\, Jenny A.,OU=Sowers,DC=appleseedorchards",
            ],
            "instanceType" => [
                0 => "4",
            ],
            "whenCreated" => [
                0 => "20180101100000.0Z",
            ],
            "whenChanged" => [
                0 => "20180102160000.0Z",
            ],
            "displayName" => [
                0 => "Appleseed, Jenny A.",
            ],
            "uSNCreated" => [
                0 => "1001",
            ],
            "memberOf" => [
                0 => "CN=Sowers,OU=Teams,DC=appleseedorchards",
                1 => "CN=Public Relations,OU=Teams,DC=appleseedorchards",
            ],
            "uSNChanged" => [
                0 => "1004",
            ],
            "co" => [
                0 => "USA",
            ],
            "department" => [
                0 => "Sowers",
            ],
            "company" => [
                0 => "Appleseed Orchards",
            ],
            "proxyAddresses" => [
                0 => "SMTP:Jenny.Appleseed@AppleseedOrchards.com",
            ],
            "streetAddress" => [
                0 => "123 Orchards Lane",
            ],
            "mailNickname" => [
                0 => "appleseedj1",
            ],
            "name" => [
                0 => "Appleseed, Jenny A.",
            ],
            "objectGUID" => [
                0 => $this->uuid->getBytes(),
            ],
            "userAccountControl" => [
                    0 => "111222",
                ],
            "pwdLastSet" => [
                    0 => "131145256860505663",
                ],
            "primaryGroupID" => [
                    0 => "5521",
                ],
            "objectSid" => [
                    0 => 'S-1-5-21-1004336348-1177238915-682003330-512',
                ],
            "sAMAccountName" => [
                    0 => "AppleseedJ1",
                ],
            "sAMAccountType" => [
                    0 => "805306368",
                ],
            "userPrincipalName" => [
                    0 => "Jenny.Appleseed@AppleseedOrchards.com",
                ],
            "objectCategory" => [
                    0 => "CN=Person,CN=Schema,CN=Configuration,DC=appleseedorchards",
                ],
            "lastLogonTimestamp" => [
                    0 => "131610992411099685",
                ],
            "mail" => [
                    0 => "Jenny.Appleseed@AppleseedOrchards.com",
                ],
            "manager" => [
                    0 => "CN=Apple\, MacIntosh,OU=Managers,DC=appleseedorchards",
                ],
            ]
        );
    }

    /**
     * @test
     */
    public function canCreateUser()
    {
        $user = new ADUser($this->ldapEntry);

        $this->assertEquals('ea2e7d5e-beea-472b-8105-11e762733a34', $user->getADGuid()->toString());
        $this->assertEquals('AppleseedJ1', $user->getADSamAccountName());
        $this->assertEquals('CN=Appleseed\, Jenny A.,OU=Sowers,DC=appleseedorchards', $user->getADDn());
        $this->assertEquals($this->ldapEntry, $user->getADEntry());
    }
}
