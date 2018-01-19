<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Gubler\ADSearchBundle\Tests\Unit\Entity;

use Gubler\ADSearchBundle\Entity\ADUser;
use PHPUnit\Framework\TestCase;

/**
 * Class ADUserTest
 */
class ADUserTest extends TestCase
{

    /** @test */
    public function canCreateWithUsername()
    {
        $user = new ADUser('moo');

        self::assertInstanceOf(ADUser::class, $user);
    }

    /**
     * @test
     *
     * @expectedException \Error
     */
    public function canNotCreateWithoutUsername()
    {
        $user = new ADUser();
    }

    /** @test */
    public function canSetAndGetFields()
    {
        $user = new ADUser('moo');
        self::assertEquals('moo', $user->getUsername());
        self::assertNull($user->getEmail());
        self::assertNull($user->getName());
        self::assertNull($user->getDomain());
        self::assertNull($user->getPhone());
        self::assertNull($user->getOffice());
        self::assertNull($user->getTitle());
        self::assertFalse($user->hasAccount());
        self::assertFalse($user->isActive());
        $asArray = array(
            'username' => 'moo',
            'domain' => null,
            'name' => null,
            'title' => null,
            'office' => null,
            'phone' => null,
            'email' => null,
            'hasAccount' => false,
            'isActive' => false,
        );

        self::assertEquals($asArray, $user->asArray());

        $user->setEmail('moo@moo.com')
            ->setName('Moo')
            ->setDomain('TEST')
            ->setOffice('here')
            ->setPhone('123-123-1234')
            ->setTitle('Test Person')
            ->setAccount(true)
            ->setActive(true)
        ;

        self::assertEquals('moo@moo.com', $user->getEmail());
        self::assertEquals('Moo', $user->getName());
        self::assertEquals('TEST', $user->getDomain());
        self::assertEquals('123-123-1234', $user->getPhone());
        self::assertEquals('here', $user->getOffice());
        self::assertEquals('Test Person', $user->getTitle());
        self::assertTrue($user->hasAccount());
        self::assertTrue($user->isActive());
        $asArray = array(
            'username' => 'moo',
            'domain' => 'TEST',
            'name' => 'Moo',
            'title' => 'Test Person',
            'office' => 'here',
            'phone' => '123-123-1234',
            'email' => 'moo@moo.com',
            'hasAccount' => true,
            'isActive' => true,
        );

        self::assertEquals($asArray, $user->asArray());
    }
}
