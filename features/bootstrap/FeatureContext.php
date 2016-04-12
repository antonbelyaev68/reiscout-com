<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
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
     * @Given I am a user with :arg1 role
     */
    public function iAmAUserWithRole($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When I open page :arg1
     */
    public function iOpenPage($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then I see on page element with selector :arg1
     */
    public function iSeeOnPageElementWithSelector($arg1)
    {
        throw new PendingException();
    }
}
