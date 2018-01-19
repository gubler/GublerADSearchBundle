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
 * Interface ADUserInterface
 */
interface ADUserInterface
{
    /**
     * @return UuidInterface
     */
    public function getADGuid(): UuidInterface;

    /**
     * @return string
     */
    public function getADSamAccountName(): string;

    /**
     * @return string
     */
    public function getADDomain(): string;

    /**
     * @return string
     */
    public function getADEmail(): string;

    /**
     * @return ADAttributes
     */
    public function getADAttributes(): ADAttributes;

    /**
     * @param ADAttributes $attributes
     *
     * @return ADUserInterface
     */
    public function setADAttributes(ADAttributes $attributes): self;
}
