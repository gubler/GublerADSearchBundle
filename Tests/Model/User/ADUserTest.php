<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Gubler\ADSearchBundle\Tests\Model\User;

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
        $this->ldapEntry = new Entry(
            'CN=Appleseed\, Jenny A.,OU=Sowers,DC=appleseedorchards',
            [
                'objectClass' => ['top', 'person', 'organizationalPerson', 'user'],
                'cn' => ['Appleseed, Jenny A.'],
                'sn' => ['Jenny'],
                'l' => ['Leominster'],
                'st' => ['MA'],
                'title' => ['Sower of Apples'],
                'description' => ['Apple Bringer'],
                'postalCode' => ['01453'],
                'physicalDeliveryOfficeName' => ['Appleseed Orchard'],
                'telephoneNumber' => ['508-555-1212 x2222'],
                'givenName' => ['Jenny'],
                'initials' => ['A'],
                'distinguishedName' => ['CN=Appleseed\, Jenny A.,OU=Sowers,DC=appleseedorchards'],
                'instanceType' => ['4'],
                'whenCreated' => ['20180101100000.0Z'],
                'whenChanged' => ['20180102160000.0Z'],
                'displayName' => ['Appleseed, Jenny A.'],
                'uSNCreated' => ['1001'],
                'memberOf' => ['CN=Sowers,OU=Teams,DC=appleseedorchards', 'CN=Public Relations,OU=Teams,DC=appleseedorchards'],
                'uSNChanged' => ['1004'],
                'co' => ['USA'],
                'department' => ['Sowers'],
                'company' => ['Appleseed Orchards'],
                'proxyAddresses' => ['SMTP:Jenny.Appleseed@AppleseedOrchards.com'],
                'streetAddress' => ['123 Orchards Lane'],
                'mailNickname' => ['appleseedj1'],
                'name' => ['Appleseed, Jenny A.'],
                'objectGUID' => [$this->uuid->getBytes()],
                'userAccountControl' => ['111222'],
                'pwdLastSet' => ['131145256860505663'],
                'primaryGroupID' => ['5521'],
                'objectSid' => ['S-1-5-21-1004336348-1177238915-682003330-512'],
                'sAMAccountName' => ['AppleseedJ1'],
                'sAMAccountType' => ['805306368'],
                'userPrincipalName' => ['Jenny.Appleseed@AppleseedOrchards.com'],
                'objectCategory' => ['CN=Person,CN=Schema,CN=Configuration,DC=appleseedorchards'],
                'lastLogonTimestamp' => ['131610992411099685'],
                'mail' => ['Jenny.Appleseed@AppleseedOrchards.com'],
                'manager' => ['CN=Apple\, MacIntosh,OU=Managers,DC=appleseedorchards'],
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
