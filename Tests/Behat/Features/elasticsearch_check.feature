@elasticsearch

Feature: Elasticsearch check

  Scenario: Check if Elasticsearch is available
    Given login as administrator
    When I am on "/admin/healthcheck"
    Then Page title equals to "Health Check"
    And I wait for "Health Check Status Table Rows" element to appear
    And I should see next rows in "Health Check Status Table" table
      | Name                                                         | Message |
      | Check if Elasticsearch is available in case it is configured |         |
    And I should see a "Health Check Successful Status Elasticsearch" element
