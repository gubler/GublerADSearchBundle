<?php

namespace Gubler\ADSearchBundle\Lib;

use Ramsey\Uuid\UuidInterface;

final class GuidTools
{
    public static function guidToADHex(UuidInterface $guid): string
    {
        $bytes = $guid->getBytes();
        $guidHex = '';
        $length = \strlen($bytes);
        for ($i = 0; $i < $length; ++$i) {
            $guidHex .= '\\'.str_pad(dechex(\ord($bytes[$i])), 2, '0', STR_PAD_LEFT);
        }

        return $guidHex;
    }

}
