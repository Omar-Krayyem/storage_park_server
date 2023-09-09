<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\productCategory;
use App\Models\Stock;

class IncomingWorkerController extends Controller
{
    public function getAllShipment(){
        try{
            $worker_id = Auth::user()->id;

            $orders = Order::where('worker_id', $worker_id)->where('order_type_id', 1)->where('status', 'shipment')->with(['user' , 'orderItems.product.category'])->get();

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
            $worker_id = Auth::user()->id;
            $orders = Order::with(['user', 'worker'])
                ->where('status', 'shipment')
                ->where('order_type_id', 1)
                ->where('worker_id', $worker_id)
                ->where(function ($query) use ($requestSearch) {
                    $query->where('id', 'LIKE', "%$requestSearch%")
                        ->orWhere('placed_at', 'LIKE', "%$requestSearch%")
                        ->orWhereHas('user', function ($userQuery) use ($requestSearch) {
                            $userQuery->where('company_name', 'LIKE', "%$requestSearch%");
                        });
                })
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
            $order = Order::with(['user', 'orderItems.product.category'])->find($order->id);
            return $this->customResponse($order, 'success', 200);
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function AddToDelivered(Request $request_info){
        try{

            $validated_data = $this->validate($request_info, [
                'id' => ['required', 'numeric'],
            ]);
    
            $order = Order::find($validated_data['id']);
    
            if (!$order) {
                return $this->customResponse('Order not found', 'error', 404);
            }
    
            $order->status = "delivered";
            $order->delivered_at = now();
    
            $order->save();

            $order_items = OrderItem::where('order_id', $order->id);
            dd($order_items);
            foreach($order_items as $item){
                $product = Product::where('id', $item->product_id)->find();
                $stock = Stock::where('user_id', $order->user_id->where('product_id', $item->product_id))->first();

                if($stock){
                    $stock->quantity += $item->quantity;
                    $stock->save();
                }
                else{
                    $new_stock = new Stock();
                    $new_stock->product_id = $product->id;
                    $new_stock->user_id = $order->user_id;
                    $new_stock->quantity = $item->quantity;
                    $new_stock->save();
                }
            }
        
            return $this->customResponse($order, 'Updated Successfully');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getAllDelivered(){
        try{
            $worker_id = Auth::user()->id;

            $orders = Order::where('worker_id', $worker_id)->where('order_type_id', 1)->where('status', 'delivered')->with(['user' , 'orderItems.product.category'])->get();

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
            $worker_id = Auth::user()->id;
            $orders = Order::with(['user', 'worker'])
                ->where('status', 'delivered')
                ->where('order_type_id', 1)
                ->where('worker_id', $worker_id)
                ->where(function ($query) use ($requestSearch) {
                    $query->where('id', 'LIKE', "%$requestSearch%")
                        ->orWhere('placed_at', 'LIKE', "%$requestSearch%")
                        ->orWhere('delivered_at', 'LIKE', "%$requestSearch%")
                        ->orWhereHas('user', function ($userQuery) use ($requestSearch) {
                            $userQuery->where('company_name', 'LIKE', "%$requestSearch%");
                        });
                })
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
            $order = Order::with(['user', 'orderItems.product.category'])->find($order->id);
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
