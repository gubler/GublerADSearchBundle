<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gubler\ADSearchBundle\Model;

use Ramsey\Uuid\UuidInterface;

/**
 * Class ADAttributes
 */
class ADAttributes
{
    /**
     * @param UuidInterface $guid
     * @param string        $samAccountName
     * @param string        $domain
     * @param string        $email
     * @param array         $extendedAttributes
     */
    public function __construct(UuidInterface $guid, string $samAccountName, string $domain, string $email, array $extendedAttributes)
    {
        // do things
    }
}
