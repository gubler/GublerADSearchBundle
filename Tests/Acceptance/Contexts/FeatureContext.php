<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gubler\ADSearchBundle\Tests\Acceptance\Contexts;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given connection setting is :connectionType
     *
     * @param string $connectionType
     */
    public function connectionSettingIs(string $connectionType)
    {
        throw new PendingException();
    }

    /**
     * @When I search for :term
     *
     * @param string $term
     */
    public function iSearchFor(string $term)
    {
        throw new PendingException();
    }

    /**
     * @Then I should find :numberOfUsers user(s)
     *
     * @param int $numberOfUsers
     */
    public function iShouldFindUser(int $numberOfUsers)
    {
        throw new PendingException();
        // Assert::assertEquals($numberOfUsers, 1);
    }

    /**
     * @Then the user's name should be :name
     *
     * @param string $name
     */
    public function theUsersNameShouldBe(string $name)
    {
        throw new PendingException();
    }
}
