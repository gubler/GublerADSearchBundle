<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Gubler\ADSearchBundle\Tests\Unit\Exception;

use Gubler\ADSearchBundle\Exception\NonUniqueADResultException;
use PHPUnit\Framework\TestCase;

/**
 * Class NonUniqueADResultExceptionTest
 */
class NonUniqueADResultExceptionTest extends TestCase
{
    /**
     * @expectedException     Gubler\ADSearchBundle\Exception\NonUniqueADResultException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Search returned multiple results. Should only return one result
     */
    public function testThrowsDefaultMessage()
    {
        throw new NonUniqueADResultException();
    }
}