<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use PDO;

class IncomingController extends Controller
{
    public function createOrder(Request $request_info){
        try{
            $user_id = Auth::user()->id;
            $validated_data = $this->validate($request_info, [
                'longitude' => ['required', 'numeric'],
                'latitude' => ['required', 'numeric'],
                'products' => ['required', 'array'],
            ]);


            $order = Order::create([
                'user_id' => $user_id,
                'placed_at' => now(),
                'order_type_id' => 1,
                'status' => 'placed',
                'longitude' => $validated_data['longitude'],
                'latitude' => $validated_data['latitude'],
            ]);

            $total_price = 0;

            foreach ($validated_data['products'] as $product) {
                $existingProduct = Product::where('name', $product['name'])
                ->where('description', $product['description'])
                ->where('price', $product['price'])
                ->where('product_category_id', $product['product_category_id'])
                ->first();

                echo $existingProduct;

                if (!$existingProduct) {
                    $newProduct = Product::create([
                        'name' => $product['name'],
                        'description' => $product['description'],
                        'price' => $product['price'],
                        'product_category_id' => $product['product_category_id']
                    ]);
                } else {
                    $newProduct = $existingProduct;
                }
    
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $newProduct->id,
                    'quantity' => $product['quantity'],
                ]);

                $total_price +=  ($product['quantity'] * $product['price']);
            }
            
            $order->update(['total_price' => $total_price]);

            return $this->customResponse('Order created successfully', 'success', 200);

        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getAll(){
        try{
            $user_id = Auth::user()->id;

            $orders = Order::where('user_id', $user_id)->where('order_type_id', 1)->where('status', 'placed')->get();

            $orders= $orders->map(function ($order) {
                $order->item_count = $order->orderItems->count();
                return $order;
            });

            return $this->customResponse($orders, 'success', 200);
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getProducts(){
        try{
            $products = Product::get();

            return $this->customResponse($products, 'success', 200);
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    function customResponse($data, $status = 'success', $code = 200){
        $response = ['status' => $status,'data' => $data];
        return response()->json($response,$code);
    }
}
