<?php

namespace Gubler\ADSearchBundle\Lib;

use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\FeatureSet;
use Ramsey\Uuid\UuidFactory;

final class GuidTools
{
    public static function convertBytesToGuid(string $guidBytes): Guid
    {
        $useGuids = true;
        $featureSet = new FeatureSet($useGuids);
        $factory = new UuidFactory($featureSet);

        return $factory->fromBytes($guidBytes);
    }

    public static function convertStringtoGuid(string $guidString): Guid
    {
        $useGuids = true;
        $featureSet = new FeatureSet($useGuids);
        $factory = new UuidFactory($featureSet);

        return $factory->fromString($guidString);
    }

    public static function guidToADHex(Guid $guid): string
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
