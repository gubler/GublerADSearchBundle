<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gubler\ADSearchBundle\Lib;

use Ramsey\Uuid\Codec\GuidStringCodec;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;

/**
 * @package Gubler\ADSearchBundle\Lib
 */
class ADGuid
{
    /**
     * Converts a binary GUID from AD (objectGUID property) to a UUID
     *
     * @param mixed $guid
     * @return UuidInterface
     */
    public static function fromBytes($guid): UuidInterface
    {
        $factory = new UuidFactory();

        $codec = new GuidStringCodec($factory->getUuidBuilder());

        $factory->setCodec($codec);

        return $factory->fromBytes($guid);
    }

    /**
     * Converts a string GUID to a UUID
     *
     * @param mixed $guid
     * @return UuidInterface
     */
    public static function fromString($guid): UuidInterface
    {
        $factory = new UuidFactory();

        $codec = new GuidStringCodec($factory->getUuidBuilder());

        $factory->setCodec($codec);

        return $factory->fromString($guid);
    }
}
