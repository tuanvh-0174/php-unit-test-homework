<?php

namespace Modules\Tests\Exercise03\Tests\Http\Controllers;

use Dotenv\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Exercise03\Http\Controllers\ProductController;
use Modules\Exercise03\Http\Requests\CheckoutRequest;
use Modules\Exercise03\Http\Requests\ProductCreateRequest;
use Modules\Exercise03\Models\Product;
use Modules\Exercise03\Services\ProductService;
use Tests\TestCase;
use Modules\Exercise03\Database\Seeders;

class ProductControllerTest extends TestCase
{
    private $productService;
    private $productController;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->productService = \Mockery::mock(ProductService::class);
        $this->productController = new ProductController(
            $this->productService
        );

        // test request
        $this->validator = app()->get('validator');
        $this->rules = (new CheckoutRequest())->rules();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex()
    {
        //$response = $this->get(action([ProductController::class, 'index']));
        $products = [
            'name' => 'test',
            'status' => 1
        ];
        $this->productService->shouldReceive('getAllProducts')
            ->andReturn($products);
        $response = $this->productController->index();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('exercise03::index', $response->getName());
    }

/*    public function testCheckoutRequest()
    {
        /*$faker = Product::factory()->create();
        dd($faker);
        $this->productService->shouldReceive('calculateDiscount')->
    }
    */

    /*public function testStore()
    {
        $request = new ProductCreateRequest();

        $this->assertEquals([
            'name' => ['required', 'max:255'],
            'image' => ['nullable', 'mimes:jpg,png'],
            'quantity' => ['required', 'integer', 'min:1'],
            'description' => ['required'],
            'short_description' => ['nullable', 'max:255'],
        ], $request->rules());
    }*/

    // test từng case
    public function testItFailsWhenNameIsMissing()
    {
        $request = new ProductCreateRequest();
        $validator = \Illuminate\Support\Facades\Validator::make([
            'quantity' => 1,
            'description' => 'Description',
        ], $request->rules());

        $this->assertFalse($validator->passes());
    }

    public function testItFailsWhenQuantityIsMissing()
    {
        $request = new ProductCreateRequest();
        $validator = \Illuminate\Support\Facades\Validator::make([
            'name' => 'name',
//            'quantity' => 1, missing quantity
            'description' => 'Description',
        ], $request->rules());

        $this->assertFalse($validator->passes());
    }

    // Test gộp sử dụng data provider
    /**
     * @dataProvider provideInvalidData
     */
    public function testInvalidData($data)
    {
        $request = new ProductCreateRequest();

        $validator = \Illuminate\Support\Facades\Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
    }

    public function makeInvalidData($invalidInputs)
    {
        $validInput = [
            'quantity' => 1,
            'description' => 'Desc',
        ];

        return array_merge($validInput, $invalidInputs);
    }

    public function provideInvalidData()
    {
        return [
            [[]], // missing fields
            [$this->makeInvalidData(['name' => ''])],
            [$this->makeInvalidData(['name' => 'tuan' . str_repeat('t', 256)])],
            [$this->makeInvalidData(['name' => 'tuan', 'quantity' => ''])],
            [$this->makeInvalidData(['name' => 'tuan', 'quantity' => 1, 'description' => null])],
        ];
    }

/*    public function testStoreSuccess()
    {
        $input = [
            'name' => 'TuanVH',
            'thumbnail' => 123,
            'type' => 1,
        ];

//        $request = ProductCreateRequest::create(null, 'POST', $input);
//        $response = $this->post(action([ProductController::class, 'store']), $input);
//        $response = $this->productService->create($input);
//        $this->assertEquals(302, $response->status());
//        $product = Product::factory()->create();

        $this->productService->shouldReceive('create')
            ->with($input)
            ->andReturn(true);

        $response = $this->productService->create($input);

        $this->assertEquals(true, $response);
    }*/

    /**
     * @dataProvider provideInvalidCheckout
     */

    public function testCheckoutRequestInvalid($data) {
        $request = new CheckoutRequest();
        $validator = \Illuminate\Support\Facades\Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
    }

    public function provideInvalidCheckout()
    {
        return [
            [[]],
            [['total_products' => []]],
        ];
    }

    public function testCheckoutSuccess() {
//        $product = Product::factory()->cravat()->create();
        $input['total_products'] = [
            1 => 1,
            2 => 2,
            3 => 3
        ];

        //$request = CheckoutRequest::create(null, 'POST', $input);

        $this->productService->shouldReceive('calculateDiscount')
            ->with($input)
            ->andReturn(5);
        $mockRequest = \Mockery::mock(CheckoutRequest::class);
        $mockRequest->shouldReceive('input')->andReturn($input);

        $response = $this->productController->checkout($mockRequest);

        $this->assertEquals(['discount' => 5], $response->getOriginalContent());
    }

    public function testCheckoutSuccess2() {
//        $product = Product::factory()->cravat()->create();
        $input['total_products'] = [
            1 => 1,
            2 => 2,
            3 => 3
        ];

        //$res = $this->post(action([ProductController::class, 'checkout'], $input));

        $res = $this->call('POST', route('exercise03.checkout'), [
            'total_products' => [
                1 => 2,
                2 => 3,
                3 => 1,
            ],
            '_token' => csrf_token(),
        ]);

        $res->assertStatus(200);


        //$request = CheckoutRequest::create(null, 'POST', $input);
    }


}
