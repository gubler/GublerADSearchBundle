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

namespace Gubler\ADSearchBundle\Test\Exception;

use Gubler\ADSearchBundle\Exception\NonUniqueADResultException;
use PHPUnit\Framework\TestCase;

class NonUniqueADResultExceptionTest extends TestCase
{
    /**
     * @covers \Gubler\ADSearchBundle\Exception\NonUniqueADResultException
     */
    public function testThrowsDefaultMessage(): void
    {
        $this->expectException(NonUniqueADResultException::class);
        $this->expectExceptionMessage('Search returned multiple results. Should only return one result');
        $this->expectExceptionCode(500);

        throw new NonUniqueADResultException();
    }
}
