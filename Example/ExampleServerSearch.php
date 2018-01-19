<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gubler\ADSearchBundle\Example;

use Gubler\ADSearchBundle\Domain\Search\AbstractServerSearchInterface;

/**
 * Class ExampleServerSearch
 */
class ExampleServerSearch extends AbstractServerSearchInterface
{
    /**
     * Choose the name between the `cn` and `displayname` fields
     *
     * If `displayname` has a comma (due to First, Last) return `displayname`,
     * otherwise return the longer value.
     *
     * @param array $adFields
     *
     * @return string
     */
    protected function chooseNameForAccount($adFields)
    {
        $commonName = $adFields[0]['cn'][0];
        $displayName = $adFields[0]['displayname'][0];

        if (str_contains(',', $displayName)) {
            return $displayName;
        }

        return (strlen($commonName) > strlen($displayName)) ? $commonName : $displayName;
    }

    /**
     * Convert AD `dn` (Distinguished Name) field to domain
     *
     * DN is expected to be CN=Last\, First MI.,OU=Section,OU=Users,OU=Region,OU=DOMAIN,DC=XX,DC=example,DC=com
     *
     * Returns domain or NULL if no domain found
     *
     * @param string $adDn
     *
     * @return string|bool
     */
    protected function dnToDomain($adDn)
    {
        $dnParts = explode(',', $adDn);
        // reverse to process right to left
        $dnParts = array_reverse($dnParts);

        foreach ($dnParts as $node) {
            // process the first OU, which should be DOMAIN
            if (substr($node, 0, 2) === 'OU') {
                return substr($node, 2);
            }
        }

        // if no OU, then return false so AD object can be skipped
        return false;
    }
}
