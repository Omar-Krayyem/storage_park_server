<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Product;
use Exception;

class OutgoingController extends Controller
{
    public function createOrder(Request $request_info){
        try {
            $user = auth()->user();
            $user_id = $user->id;
            $validated_data = $this->validate($request_info, [
                'longitude' => ['required'],
                'latitude' => ['required'],
                'customerName' => ['required', 'string'],
                'customerEmail' => ['required', 'string'],
                'customerPhone' => ['required', 'string'],
                'items' => ['required', 'array'],
            ]);

            $customer = Customer::where('email', $validated_data['customerEmail'])->first();

            if (!$customer) {
                $customer = Customer::create([
                    'name' => $validated_data['customerName'],
                    'email' => $validated_data['customerEmail'],
                    'phone' => $validated_data['customerPhone'],
                ]);
            }
    
            $order = Order::create([
                'user_id' => $user_id,
                'placed_at' => now(),
                'order_type_id' => 2,
                'status' => 'placed',
                'longitude' => $validated_data['longitude'],
                'latitude' => $validated_data['latitude'],
                'customer_id' => $customer->id,
            ]);
    
            $total_price = 0;
    
            foreach ($validated_data['items'] as $item) {
                if (isset($item['id']) && isset($item['quantity'])) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['id'],
                        'quantity' => $item['quantity'],
                    ]);
    
                    $stock = Stock::where('product_id', $item['id'])->with('product')->first();
    
                    if ($stock) {
                        $total_price += ($item['quantity'] * $stock->product->price);
    
                        $stock->decrement('quantity', $item['quantity']);
                    }
                } 
            }
    
            $order->update(['total_price' => $total_price]);
    
            return $this->customResponse($order, 'success', 200);
    
        } catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getAllPlaced(){
        try{
            $user_id = Auth::user()->id;

            $orders = Order::where('user_id', $user_id)->where('order_type_id', 2)->where('status', 'placed')->with('customer')->get();

            $orders= $orders->map(function ($order) {
                $order->item_count = $order->orderItems->count();
                return $order;
            });

            return $this->customResponse($orders, 'success', 200);
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getPlacedById(Order $order){
        try{
            $order = Order::with(['orderItems.product.category', 'customer'])->find($order->id);
            return $this->customResponse($order, 'success', 200);
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getStock(){
        try{
            $user_id = Auth::user()->id;
            $stocks = Stock::with('product.category')->where('user_id', $user_id)->whereNot('quantity', 0)->get();            

            return $this->customResponse($stocks, 'success', 200);
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function placedSearch($requestSearch) {
        try {
            $user_id = Auth::user()->id;
            $orders = Order::with(['orderItems.product.category', 'customer'])->where(function ($query) use ($requestSearch) {
                $query->where('id', 'LIKE', "%$requestSearch%")
                     ->orWhere('placed_at', 'LIKE', "%$requestSearch%")
                     ->orWhereHas('customer', function ($workerQuery) use ($requestSearch) {
                        $workerQuery->where('name', 'LIKE', "%$requestSearch%");
                    });
            })
            ->where('status', 'placed')
            ->where('order_type_id', 2)
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

            $orders = Order::where('user_id', $user_id)->where('order_type_id', 2)->where('status', 'shipment')->with('customer')->get();

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
                $query->with('customer')
                    ->where('id', 'LIKE', "%$requestSearch%")
                    ->orWhere('placed_at', 'LIKE', "%$requestSearch%")
                    ->orWhereHas('customer', function ($workerQuery) use ($requestSearch) {
                        $workerQuery->where('name', 'LIKE', "%$requestSearch%");
                    });
            })
            ->where('status', 'shipment')
            ->where('order_type_id', 2)
            ->where('user_id', $user_id)
            ->with('customer')
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

    public function getShipmentById(Order $order){
        try{
            $order = Order::with(['orderItems.product.category', 'customer'])->find($order->id);
            return $this->customResponse($order, 'success', 200);
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getAllDelivered(){
        try{
            $user_id = Auth::user()->id;

            $orders = Order::where('user_id', $user_id)->where('order_type_id', 2)->where('status', 'delivered')->with('customer')->get();

            $orders= $orders->map(function ($order) {
                $order->item_count = $order->orderItems->count();
                return $order;
            });

            return $this->customResponse($orders, 'success', 200);
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function deliveredSearch($requestSearch) {
        try {
            $user_id = Auth::user()->id;
            $orders = Order::where(function ($query) use ($requestSearch) {
                $query->where('id', 'LIKE', "%$requestSearch%")
                    ->orWhere('placed_at', 'LIKE', "%$requestSearch%")
                    ->orWhere('delivered_at', 'LIKE', "%$requestSearch%")
                    ->orWhereHas('customer', function ($workerQuery) use ($requestSearch) {
                        $workerQuery->where('name', 'LIKE', "%$requestSearch%");
                    });
            })
            ->where('status', 'delivered')
            ->where('order_type_id', 2)
            ->where('user_id', $user_id)
            ->with('customer')
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

    public function getDeliveredtById(Order $order){
        try{
            $order = Order::with(['orderItems.product.category', 'customer'])->find($order->id);
            return $this->customResponse($order, 'success', 200);
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    function customResponse($data, $status = 'success', $code = 200){
        $response = ['status' => $status,'data' => $data];
        return response()->json($response,$code);
    }
}
