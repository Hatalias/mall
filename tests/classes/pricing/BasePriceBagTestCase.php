<?php

declare (strict_types = 1);

namespace Hts\Mall\Tests\Classes\Pricing;

use Event;
use Hts\Location\Models\Country;
use Hts\Mall\Models\Address;
use Hts\Mall\Models\Cart;
use Hts\Mall\Models\Product;
use Hts\Mall\Models\Tax;
use Hts\Mall\Tests\PluginTestCase;

abstract class BasePriceBagTestCase extends PluginTestCase
{
    /**
     * Shipping Address
     * @var Address
     */
    protected $address;

    /**
     * Setup the test environment.
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        // October v3 only
        // $this->address = Address::factory()->create();

        // Legacy
        $country       = Country::inRandomOrder()->whereHas('states')->get()->first();
        $state         = $country->states()->inRandomOrder()->get()->first();
        $this->address = new Address([
            'company'     => $this->faker->company(),
            'name'        => $this->faker->name(),
            'lines'       => $this->faker->streetAddress(),
            'zip'         => $this->faker->postcode(),
            'city'        => $this->faker->city(),
            'state_id'    => $state->id,
            'country_id'  => $country->id,
            'details'     => null,
            'customer_id' => 1,
            'created_at'  => $this->faker->iso8601(),
            'updated_at'  => $this->faker->iso8601(),
            'deleted_at'  => null,
        ]);

        // Set Country
        Event::listen('mall.cart.setCountry', function ($model) {
            $model->countryId = $this->address->country_id;
        });
    }

    /**
     * Get generic product for testing.
     * @param mixed $price
     * @return Product
     */
    protected function getProduct($price)
    {
        if (is_int($price) || is_float($price)) {
            $price = ['CHF' => $price, 'EUR' => $price];
        }

        $product = Product::first()->replicate(['category_id']);
        $product->save();
        $product->price = $price;

        return Product::find($product->id);
    }

    /**
     * Get generic cart for testing.
     * @return Cart
     */
    protected function getCart(): Cart
    {
        $cart                      = new Cart();
        $cart->shipping_address_id = $this->address->id;
        $cart->save();

        return $cart;
    }

    /**
     * Get generic tax for testing.
     * @param string $name
     * @param int|float $percentage
     * @return Tax
     */
    protected function getTax($name, $percentage): Tax
    {
        $tax1             = new Tax();
        $tax1->name       = $name;
        $tax1->percentage = $percentage;
        $tax1->save();

        return $tax1;
    }
}
