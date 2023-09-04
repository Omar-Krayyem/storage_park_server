<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
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

            $company_name = Auth::user()->company_name;
            $lastOrder = Order::where('company_name', $company_name)->orderBy('id', 'desc')->first();

            if ($lastOrder) {
                $order_id = substr(strtoupper($company_name), 0, 3) . ($lastOrder->id + 1);
            } else {
                $order_id = substr(strtoupper($company_name), 0, 3) . '1000';
            }

            $order = Order::create([
                'id' => $order_id,
                'user_id' => $user_id,
                'placed_at' => now(),
                'order_type_id' => 1,
                'status' => 'placed',
            ]);

            foreach ($validated_data['products'] as $product) {
                $existingProduct = Product::where('name', $product['name'])->where('description', $product['description'])->where('price', $product['price'])->where('product_category_id', $product['product_category_id'])->first();
    
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
            }
    
            return $this->customResponse('Order created successfully', 'success', 200);

        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    function customResponse($data, $status = 'success', $code = 200){
        $response = ['status' => $status,'data' => $data];
        return response()->json($response,$code);
    }
}
