<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Gubler\ADSearchBundle\Command;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateTestUserJsonCommand extends Command
{
    protected static $defaultName = 'ad-search:create-user-json';

    protected function configure(): void
    {
        $this
            ->setName('ad-search:create-user-json')
            ->setDescription('Creates a JSON file for use with `array` connection type')
            ->setHelp('This generates a JSON file that can be loaded when using the `array` connection type')
            ->addArgument('outputPath', InputArgument::OPTIONAL, 'Path to put the generated JSON file.')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $outputPath = $input->getArgument('outputPath') ?? 'config/packages/dev';
        $ldapUsers = [];
        // create users
        foreach ($this->getUserArray() as $user) {
            $ldapUsers[] = $this->createLdapData($user);
        }

        // write user array to json file
        $path = $outputPath.'/test_users.json';
        file_put_contents($path, json_encode($ldapUsers, JSON_PRETTY_PRINT));

        $output->writeln('File Generated at: '.$path);

        return 0;
    }

    /**
     * @return array
     */
    protected function getUserArray(): array
    {
        return [
            [
                'dn_ou' => 'admins',
                'guid' => '7DFB34A4-085C-4578-8882-55D8D323DEBB',
                'samAccountName' => 'admin',
                // Name
                'firstName' => 'System',
                'lastName' => 'Admin',
                // Location
                'country' => 'Cosmos',
                'state' => 'Milky Way',
                'city' => 'Sol',
                'postalCode' => '00000',
                // Job
                'title' => 'System Administrator',
                'description' => 'System Administrator',
                'department' => 'Information Technology',
                // office
                'office' => 'Omnipresence',
                'phone' => '1234',
                'address' => '123 The Cosmos',
            ],
            [
                'dn_ou' => 'atom',
                'guid' => '192D7590-6036-4358-9239-BEA350285CA1',
                'samAccountName' => 'ATOMproton',
                // Name
                'firstName' => 'Proton',
                'lastName' => 'Particle',
                // Location
                'country' => 'Cosmos',
                'state' => 'Atom',
                'city' => 'Nucleus',
                'postalCode' => '00001',
                // Job
                'title' => 'Nuclear Particle',
                'description' => 'Positively Charged',
                'department' => 'Atoms',
                // office
                'office' => 'The Atom',
                'phone' => '123123',
                'address' => '123 The Atom',
            ],
            [
                'dn_ou' => 'atom',
                'guid' => '5199FB62-A76F-45B1-B01B-8FB7B7C9248C',
                'samAccountName' => 'ATOMneutron',
                // Name
                'firstName' => 'Neutron',
                'lastName' => 'Particle',
                // Location
                'country' => 'Cosmos',
                'state' => 'Atom',
                'city' => 'Nucleus',
                'postalCode' => '00001',
                // Job
                'title' => 'Nuclear Particle',
                'description' => 'No Charge',
                'department' => 'Atoms',
                // office
                'office' => 'The Atom',
                'phone' => '234234',
                'address' => '123 The Atom',
            ],
            [
                'dn_ou' => 'atom',
                'guid' => 'D9744279-E09F-4F5A-AA9B-58C09E9D04D7',
                'samAccountName' => 'ATOMelectron',
                // Name
                'firstName' => 'Electron',
                'lastName' => 'Particle',
                // Location
                'country' => 'Cosmos',
                'state' => 'Atom',
                'city' => 'Nucleus',
                'postalCode' => '00001',
                // Job
                'title' => 'Atomic Particle',
                'description' => 'Negatively Charged',
                'department' => 'Atoms',
                // office
                'office' => 'The Atom',
                'phone' => '345345',
                'address' => '123 The Atom',
            ],
            [
                'dn_ou' => 'atom',
                'guid' => '342B7C89-953E-4292-8A88-7C1643C5D722',
                'samAccountName' => 'ATOMquark',
                // Name
                'firstName' => 'Quark',
                'lastName' => 'Boson',
                // Location
                'country' => 'Cosmos',
                'state' => 'Atom',
                'city' => 'Nucleus',
                'postalCode' => '00001',
                // Job
                'title' => 'Subatomic Particle',
                'description' => 'Colorful',
                'department' => 'Atoms',
                // office
                'office' => 'The Atom',
                'phone' => '456456',
                'address' => '123 The Atom',
            ],
            [
                'dn_ou' => 'alpha',
                'guid' => 'BC2170C3-FD5B-46C1-B925-6C35837537A9',
                'samAccountName' => 'ALPHAcat',
                // Name
                'firstName' => 'Generic',
                'lastName' => 'Cat',
                // Location
                'country' => 'Cosmos',
                'state' => 'Felis',
                'city' => 'Catus',
                'postalCode' => '12121',
                // Job
                'title' => 'Cat',
                'description' => 'A Cat',
                'department' => 'Cats',
                // office
                'office' => 'Silvestris',
                'phone' => '321321',
                'address' => '321 Cat Place',
            ],
            [
                'dn_ou' => 'alpha',
                'guid' => '6783C9BF-DBF6-4FA3-B9D8-682D507812F3',
                'samAccountName' => 'ALPHAsiamese',
                // Name
                'firstName' => 'Siamese',
                'lastName' => 'Cat',
                // Location
                'country' => 'Cosmos',
                'state' => 'Felis',
                'city' => 'Catus',
                'postalCode' => '12121',
                // Job
                'title' => 'Cat',
                'description' => 'A Cat',
                'department' => 'Cats',
                // office
                'office' => 'Silvestris',
                'phone' => '432432',
                'address' => '321 Cat Place',
            ],
            [
                'dn_ou' => 'alpha',
                'guid' => '23D3122B-F3E4-4624-8F52-0DB1DF0A57FB',
                'samAccountName' => 'ALPHAsphinx',
                // Name
                'firstName' => 'Sphinx',
                'lastName' => 'Cat',
                // Location
                'country' => 'Cosmos',
                'state' => 'Felis',
                'city' => 'Catus',
                'postalCode' => '12121',
                // Job
                'title' => 'Cat',
                'description' => 'A Cat',
                'department' => 'Cats',
                // office
                'office' => 'Silvestris',
                'phone' => '543543',
                'address' => '321 Cat Place',
            ],
            [
                'dn_ou' => 'alpha',
                'guid' => '97366C42-E7A3-4979-8D19-A3C59DE79B9D',
                'samAccountName' => 'ALPHAbengal',
                // Name
                'firstName' => 'Bengal',
                'lastName' => 'Cat',
                // Location
                'country' => 'Cosmos',
                'state' => 'Felis',
                'city' => 'Catus',
                'postalCode' => '12121',
                // Job
                'title' => 'Cat',
                'description' => 'A Cat',
                'department' => 'Cats',
                // office
                'office' => 'Silvestris',
                'phone' => '321321',
                'address' => '321 Cat Place',
            ],
            [
                'dn_ou' => 'alpha',
                'guid' => 'F77AEB70-5D29-4165-A58A-86AAB2B35427',
                'samAccountName' => 'ALPHApersian',
                // Name
                'firstName' => 'Persian',
                'lastName' => 'Cat',
                // Location
                'country' => 'Cosmos',
                'state' => 'Felis',
                'city' => 'Catus',
                'postalCode' => '12121',
                // Job
                'title' => 'Cat',
                'description' => 'A Cat',
                'department' => 'Cats',
                // office
                'office' => 'Silvestris',
                'phone' => '321321',
                'address' => '321 Cat Place',
            ],
            [
                'dn_ou' => 'beta',
                'guid' => 'EA690B8A-BDA4-45D8-8623-9C72B55D8DF2',
                'samAccountName' => 'BETAdog',
                // Name
                'firstName' => 'Generic',
                'lastName' => 'Dog',
                // Location
                'country' => 'Cosmos',
                'state' => 'Canis',
                'city' => 'Lupus',
                'postalCode' => '21212',
                // Job
                'title' => 'Dog',
                'description' => 'A Dog',
                'department' => 'Dogs',
                // office
                'office' => 'Familiaris',
                'phone' => '789789',
                'address' => '789 Dog House',
            ],
            [
                'dn_ou' => 'beta',
                'guid' => 'C99C1EBB-18A7-4F5C-96CB-75C3B8C947C8',
                'samAccountName' => 'BETAcorgi',
                // Name
                'firstName' => 'Corgi',
                'lastName' => 'Dog',
                // Location
                'country' => 'Cosmos',
                'state' => 'Canis',
                'city' => 'Lupus',
                'postalCode' => '21212',
                // Job
                'title' => 'Dog',
                'description' => 'A Dog',
                'department' => 'Dogs',
                // office
                'office' => 'Familiaris',
                'phone' => '789789',
                'address' => '789 Dog House',
            ],
            [
                'dn_ou' => 'beta',
                'guid' => '284EDCEE-92B2-4251-9A3A-95C30D041FD7',
                'samAccountName' => 'BETAmastiff',
                // Name
                'firstName' => 'Mastiff',
                'lastName' => 'Dog',
                // Location
                'country' => 'Cosmos',
                'state' => 'Canis',
                'city' => 'Lupus',
                'postalCode' => '21212',
                // Job
                'title' => 'Dog',
                'description' => 'A Dog',
                'department' => 'Dogs',
                // office
                'office' => 'Familiaris',
                'phone' => '789789',
                'address' => '789 Dog House',
            ],
            [
                'dn_ou' => 'beta',
                'guid' => '0D90E106-B716-4A97-8D64-82A492D6C5E9',
                'samAccountName' => 'BETApoodle',
                // Name
                'firstName' => 'Poodle',
                'lastName' => 'Dog',
                // Location
                'country' => 'Cosmos',
                'state' => 'Canis',
                'city' => 'Lupus',
                'postalCode' => '21212',
                // Job
                'title' => 'Dog',
                'description' => 'A Dog',
                'department' => 'Dogs',
                // office
                'office' => 'Familiaris',
                'phone' => '789789',
                'address' => '789 Dog House',
            ],
            [
                'dn_ou' => 'beta',
                'guid' => '0915C1AA-1D6F-4751-BD18-5CA7F9B76CEE',
                'samAccountName' => 'BETAboxer',
                // Name
                'firstName' => 'Boxer',
                'lastName' => 'Dog',
                // Location
                'country' => 'Cosmos',
                'state' => 'Canis',
                'city' => 'Lupus',
                'postalCode' => '21212',
                // Job
                'title' => 'Dog',
                'description' => 'A Dog',
                'department' => 'Dogs',
                // office
                'office' => 'Familiaris',
                'phone' => '789789',
                'address' => '789 Dog House',
            ],
        ];
    }

    /**
     * @param array $user
     *
     * @return array
     */
    protected function createLdapData(array $user): array
    {
        return [
            'objectClass' => [
                0 => 'top',
                1 => 'person',
                2 => 'organizationalPerson',
                3 => 'user',
            ],
            'cn' => [
                0 => $user['lastName'].', '.$user['firstName'],
            ],
            'sn' => [
                0 => $user['firstName'],
            ],
            'l' => [
                0 => $user['city'],
            ],
            'st' => [
                0 => $user['state'],
            ],
            'title' => [
                0 => $user['title'],
            ],
            'description' => [
                0 => $user['description'],
            ],
            'postalCode' => [
                0 => $user['postalCode'],
            ],
            'physicalDeliveryOfficeName' => [
                0 => $user['office'],
            ],
            'telephoneNumber' => [
                0 => $user['phone'],
            ],
            'givenName' => [
                0 => $user['firstName'],
            ],
            'initials' => [
                0 => $user['firstName'][0].$user['lastName'][0],
            ],
            'distinguishedName' => [
                0 => 'CN='.$user['lastName'].', '.$user['firstName'].',OU='.$user['dn_ou'].',DC=acme,DC=com',
            ],
            'instanceType' => [
                0 => '4',
            ],
            'whenCreated' => [
                0 => '20180101100000.0Z',
            ],
            'whenChanged' => [
                0 => '20180102160000.0Z',
            ],
            'displayName' => [
                0 => $user['lastName'].', '.$user['firstName'],
            ],
            'uSNCreated' => [
                0 => '1001',
            ],
            'uSNChanged' => [
                0 => '1004',
            ],
            'co' => [
                0 => $user['country'],
            ],
            'department' => [
                0 => $user['department'],
            ],
            'company' => [
                0 => 'Acme, Inc.',
            ],
            'proxyAddresses' => [
                0 => 'SMTP:'.strtolower($user['firstName']).'.'.strtolower($user['lastName']).'@acme.com',
            ],
            'streetAddress' => [
                0 => $user['address'],
            ],
            'name' => [
                0 => $user['lastName'].', '.$user['firstName'],
            ],
            'objectGUID' => [
                0 => utf8_encode((Uuid::fromString($user['guid']))->getBytes()),
            ],
            'userAccountControl' => [
                0 => '111222',
            ],
            'pwdLastSet' => [
                0 => '131145256860505663',
            ],
            'primaryGroupID' => [
                0 => '5521',
            ],
            'objectSid' => [
                0 => 'S-1-5-21-3165297888-301567370-'.random_int(100, 1000).'-1103',
            ],
            'sAMAccountName' => [
                0 => $user['samAccountName'],
            ],
            'sAMAccountType' => [
                0 => '805306368',
            ],
            'userPrincipalName' => [
                0 => strtolower($user['firstName']).'.'.strtolower($user['lastName']).'@acme.com',
            ],
            'objectCategory' => [
                0 => 'CN=Person,CN=Schema,CN=Configuration,DC=acme,DC=com',
            ],
            'lastLogonTimestamp' => [
                0 => '131610992411099685',
            ],
            'mail' => [
                0 => strtolower($user['firstName']).'.'.strtolower($user['lastName']).'@acme.com',
            ],
        ];
    }
}
