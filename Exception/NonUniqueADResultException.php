<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gubler\ADSearchBundle\Exception;

use \Throwable;

/**
 * Class NonUniqueADResultException
 */
class NonUniqueADResultException extends \Exception
{
    /**
     * @param int            $code
     * @param \Throwable|null $previous
     */
    public function __construct(int $code = 0, \Throwable $previous = null)
    {
        $message = 'Search returned multiple results. Should only return one result';

        parent::__construct($message, $code, $previous);
    }
}
