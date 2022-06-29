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

use Gubler\ADSearchBundle\Exception\NotFoundADResultException;
use PHPUnit\Framework\TestCase;

class NotFoundADResultExceptionTest extends TestCase
{
    /**
     * @covers \Gubler\ADSearchBundle\Exception\NotFoundADResultException
     */
    public function testThrowsDefaultMessage(): void
    {
        $this->expectException(NotFoundADResultException::class);
        $this->expectExceptionMessage('No Matching User Found');
        $this->expectExceptionCode(500);

        throw new NotFoundADResultException();
    }
}
