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

namespace Gubler\ADSearchBundle\Exception;

class NonUniqueADResultException extends \Exception
{
    public function __construct(int $code = 500, \Throwable $previous = null)
    {
        $message = 'Search returned multiple results. Should only return one result';

        parent::__construct($message, $code, $previous);
    }
}
