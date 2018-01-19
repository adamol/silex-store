Feature: View products
    In order to buy products
    As a customer
    I need to be able to view available products both in list format
    and also specific ones in more detail

    Background:
        Given there are 5 products
        And the product with id 3 has attributes:
        """
        {
            "display_name": "Lenovo Legion",
            "product_category": "laptop",
            "price": 10000
        }
        """

    Scenario: Viewing the products listing
        When I send a GET request to "/products"
        Then the response json should be of type "array"
        And I should see "5" products
        And the response should contain json:
        """
        {
            "display_name": "Lenovo Legion",
            "product_category": "laptop",
            "price": 10000
        }
        """

    Scenario: Viewing a single product
        When I send a GET request to "/products/3"
        Then the response json should be of type "object"
        And the response should contain json:
        """
        {
            "display_name": "Lenovo Legion",
            "product_category": "laptop",
            "price": 10000
        }
        """

    Scenario: Trying to view a non-existing product
        When I send a GET request to "/products/6"
        Then the response json should be of type "object"
        And the response should contain json:
        """
        {
            "error": {
                "message": "No Resource Found."
            }
        }
        """
