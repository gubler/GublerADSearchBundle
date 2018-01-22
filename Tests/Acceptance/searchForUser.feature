Feature: User Search
  In order generate a new user
  As an admin
  I need to be able to search Active Directory for a user

  Scenario: With Available Active Directory Server
    Given setting connection_type is server
    When I search for "vha08odevsc1"
    Then I should find 1 user
    And the user's name should be "vhav08odevsc1"

  Scenario: With Test User Array
    Given setting connection_type is array
    When I search for "vha08odevsc1"
    Then I should find 1 user
    And the user's name should be "vhav08odevsc1"
