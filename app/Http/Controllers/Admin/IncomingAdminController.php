<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\Order;

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


    function customResponse($data, $status = 'success', $code = 200){
        $response = ['status' => $status,'data' => $data];
        return response()->json($response,$code);
    }
}
