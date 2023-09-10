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

class OutgoingWorkerController extends Controller
{
    public function getAllShipment(){
        try{
            $worker_id = Auth::user()->id;

            $orders = Order::where('worker_id', $worker_id)
                ->where('order_type_id', 2)
                ->where('status', 'shipment')
                ->with(['user' , 'orderItems.product.category', 'customer'])
                ->get();

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
                ->where('order_type_id', 2)
                ->where('worker_id', $worker_id)
                ->where(function ($query) use ($requestSearch) {
                    $query->where('id', 'LIKE', "%$requestSearch%")
                        ->orWhere('placed_at', 'LIKE', "%$requestSearch%")
                        ->orWhereHas('user', function ($userQuery) use ($requestSearch) {
                            $userQuery->where('company_name', 'LIKE', "%$requestSearch%");
                        })
                        ->orWhereHas('customer', function ($userQuery) use ($requestSearch) {
                            $userQuery->where('name', 'LIKE', "%$requestSearch%");
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
            $order = Order::with(['user', 'orderItems.product.category', 'customer'])->find($order->id);
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
