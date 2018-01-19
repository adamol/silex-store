<?php

require_once __DIR__.'/../../vendor/autoload.php';

use App\Products\Repository as ProductsRepository;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    protected $app;

    /**
     * @var \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected $response;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->app = require __DIR__ . '/../../app.php';
        $this->app['db'] = new PDO('sqlite::memory:');

        $this->app['db']->query("
          CREATE TABLE products (
              id INTEGER PRIMARY KEY,
              display_name VARCHAR(50) NOT NULL,
              product_type VARCHAR(50) NOT NULL,
              price INTEGER NOT NULL
            )
        ");
    }

    /**
     * @Given there are :arg1 products
     */
    public function thereAreProducts($amount)
    {
        for ($i = 0; $i < $amount; $i++) {
            $this->createRandomProduct();
        }
    }

    /**
     * @Given the product with id :arg1 has attributes:
     */
    public function theProductWithIdHasAttributes($id, PyStringNode $json)
    {
        $attributes = json_decode($json->getRaw(), true);

        $this->app[ProductsRepository::class]->updateById($id, $attributes);
    }

    /**
     * @When I send a :method request to :url
     */
    public function iSendARequestTo($method, $url)
    {
        $request = Request::create($url, $method);

        $this->response = $this->app->handle($request);
    }

    /**
     * @When I send a :method request to :url with parameters:
     */
    public function iSendARequestToWithParameters($method, $url, PyStringNode $json)
    {
        $parameters = json_decode($json->getRaw(), true);
        $request = Request::create($url, $method, $parameters);

        $this->response = $this->app->handle($request);
    }

    /**
     * @Then the response json should be of type :type
     */
    public function theResponseJsonShouldBeOfType($type)
    {
        $decoded = json_decode($this->response->getContent());
        PHPUnit\Framework\Assert::assertEquals(
            $type, gettype($decoded)
        );
    }

    /**
     * @Then I should see :amount products
     */
    public function iShouldSeeProducts($amount)
    {
        PHPUnit\Framework\Assert::assertCount(
            (int) $amount, json_decode($this->response->getContent())
        );
    }

    /**
     * @Then the response should contain json:
     */
    public function theResponseShouldContainJson(PyStringNode $json)
    {
        $needle = json_encode(json_decode($json->getRaw(), true));
        $haystack = $this->response->getContent();

        PHPUnit\Framework\Assert::assertContains($needle, $haystack);
    }

    private function createRandomProduct()
    {
        $getRandomString = function($minLength, $maxLength) {
            $stringLength = rand($minLength, $maxLength);
            $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

            return substr(str_shuffle(str_repeat($alphabet, $stringLength)), 1, $stringLength);
        };

        $data = [
            'display_name' => $getRandomString(5, 8),
            'product_type' => rand(1, 2) === 2 ? 'laptop' : 'tv',
            'price' => rand(10000, 20000)
        ];

        $this->app[ProductsRepository::class]->insert($data);
    }
}
