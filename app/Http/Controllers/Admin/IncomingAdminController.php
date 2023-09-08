<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\Order;
use App\Models\User;

class IncomingAdminController extends Controller
{

    public function getAllPlaced(){
        try{
            $orders = Order::where('order_type_id', 1)->where('status', 'placed')->with('user')->get();

            $orders= $orders->map(function ($order) {
                $order->item_count = $order->orderItems->count();
                return $order;
            });

            return $this->customResponse($orders, 'success', 200);
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function placedSearch($requestSearch) {
        try {
            $orders = Order::with('user')
            ->where('status', 'placed')
            ->where('order_type_id', 1)
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

    public function getAllShipment(){
        try{
            $orders = Order::where('order_type_id', 1)->where('status', 'shipment')->with('worker')->with('user')->get();

            return $this->customResponse($orders, 'success', 200);
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function shipmentSearch($requestSearch) {
        try {
            $orders = Order::with(['user', 'worker'])
                ->where('status', 'shipment')
                ->where('order_type_id', 1)
                ->where(function ($query) use ($requestSearch) {
                    $query->where('id', 'LIKE', "%$requestSearch%")
                        ->orWhere('placed_at', 'LIKE', "%$requestSearch%")
                        ->orWhereHas('user', function ($userQuery) use ($requestSearch) {
                            $userQuery->where('company_name', 'LIKE', "%$requestSearch%");
                        })
                        ->orWhereHas('worker', function ($workerQuery) use ($requestSearch) {
                            $workerQuery->where('first_name', 'LIKE', "%$requestSearch%")
                                        ->orWhere('last_name', 'LIKE', "%$requestSearch%");
                        });
                })
                ->get();
    
            return $this->customResponse($orders);
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(), 'error', 500);
        } 
    }

    public function getPlacedById(Order $order){
        try{
            $order = Order::with('orderItems.product.category')->find($order->id);
            $workers = User::where('user_type_id' , 2)->get();

            $result = [
                'order' => $order,
                'workers' => $workers,
            ];

            return $this->customResponse($result, 'success', 200);
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    function customResponse($data, $status = 'success', $code = 200){
        $response = ['status' => $status,'data' => $data];
        return response()->json($response,$code);
    }
}
