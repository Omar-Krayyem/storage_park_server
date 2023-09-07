<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\productCategory;
use PDO;

class IncomingController extends Controller
{
    public function createOrder(Request $request_info){
        try{
            $user = auth()->user();
            $user_id = $user->id;
            $validated_data = $this->validate($request_info, [
                'longitude' => ['required'],
                'latitude' => ['required'],
                'newProducts' => ['required', 'array'],
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

            foreach ($validated_data['newProducts'] as $product) {
                if($product['id'] == 0){

                    $newProduct = Product::create([
                        'name' => $product['productName'],
                        'description' => $product['description'],
                        'price' => $product['price'],
                        'product_category_id' => $product['product_category_id']
                    ]);
        
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $newProduct->id,
                        'quantity' => $product['quantity'],
                    ]);
                }
                else{
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product['id'],
                        'quantity' => $product['quantity'],
                    ]);        
                }

                $total_price +=  ($product['quantity'] * $product['price']);
            }
            
            $order->update(['total_price' => $total_price]);

            return $this->customResponse($order, 'success', 200);

        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getAllPlaced(){
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

    public function getProductsandCategories(){
        try{
            $products = Product::with('category')->get();

            $categories = productCategory::get();

            $result = [
                'products' => $products,
                'categories' => $categories,
            ];
            

            return $this->customResponse($result, 'success', 200);
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function placedSearch($requestSearch) {
        try {
            $user_id = Auth::user()->id;
            $orders = Order::where(function ($query) use ($requestSearch) {
                $query->where('id', 'LIKE', "%$requestSearch%")
                     ->orWhere('placed_at', 'LIKE', "%$requestSearch%");
            })
            ->where('status', 'placed')
            ->where('order_type_id', 1)
            ->where('user_id', $user_id)
            ->get();

            $orders= $orders->map(function ($order) {
                $order->item_count = $order->orderItems->count();
                return $order;
            });
    
            return $this->customResponse($orders);
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(), 'error', 500);
        } 
    }


    public function getAllShipment(){
        try{
            $user_id = Auth::user()->id;

            $orders = Order::where('user_id', $user_id)->where('order_type_id', 1)->where('status', 'shipment')->get();

            $orders= $orders->map(function ($order) {
                $order->item_count = $order->orderItems->count();
                return $order;
            });

            return $this->customResponse($orders, 'success', 200);
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function shipmentSearch($requestSearch) {
        try {
            $user_id = Auth::user()->id;
            $orders = Order::where(function ($query) use ($requestSearch) {
                $query->where('id', 'LIKE', "%$requestSearch%")
                     ->orWhere('placed_at', 'LIKE', "%$requestSearch%");
            })
            ->where('status', 'shipment')
            ->where('order_type_id', 1)
            ->where('user_id', $user_id)
            ->get();

            $orders= $orders->map(function ($order) {
                $order->item_count = $order->orderItems->count();
                return $order;
            });
    
            return $this->customResponse($orders);
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(), 'error', 500);
        } 
    }

    function customResponse($data, $status = 'success', $code = 200){
        $response = ['status' => $status,'data' => $data];
        return response()->json($response,$code);
    }
}
