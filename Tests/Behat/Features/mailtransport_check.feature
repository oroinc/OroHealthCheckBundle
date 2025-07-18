@regression
@ticket-BAP-20793

Feature: MailTransport check
  In order to check System Health Status
  As an Administrator
  I want to check if Mail Transport is available

  Scenario: Feature Background
    Given login as administrator

  Scenario: Check if Mail Transport is available
    When I go to healthcheck page
    Then Page title equals to "Health Check"
    And I wait for "Health Check Status Table Rows" element to appear
    And I should see next rows in "Health Check Status Table" table
      | Name                                 | Message |
      | Check if Mail Transport is available |         |
