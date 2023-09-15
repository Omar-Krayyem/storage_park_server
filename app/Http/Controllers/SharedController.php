<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\productCategory;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SharedController extends Controller
{
    public function updateProfile(Request $request_info)
    {
        try {
            $user_id = Auth::user()->id;
            $validated_data = $this->validate($request_info, [
                'first_name' => ['required', 'string'],
                'last_name' => ['required', 'string'],
                'phone' => ['required', 'string'],
                'email' => ['required', 'string'],
                'password' => ['string', 'min:6', 'nullable'],
                'address' => ['required', 'string'],
                'company_name' => ['string', 'nullable'],
            ]);
    
            $user = User::find($user_id);
    
            $user->first_name = $validated_data['first_name'];
            $user->last_name = $validated_data['last_name'];
            $user->email = $validated_data['email'];
            $user->phone = $validated_data['phone'];
            $user->address = $validated_data['address'];
    
            if (array_key_exists('password', $validated_data)) {
                $password = Hash::make($validated_data['password']);
                $user->password = $password;
            }
    
            if (array_key_exists('company_name', $validated_data)) {
                $user->company_name = $validated_data['company_name'];
            }
    
            $user->save();
    
            return $this->customResponse($user, 'Updated Successfully');
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getAdminStat(){
        try{

            $requset = User::where('user_type_id', 3)->where('password', null)->count();
            $partner = User::where('user_type_id', 3)->whereNotNull('password')->count();
            $worker = User::where('user_type_id', 2)->count();

            $categoriesWithCount = productCategory::withCount('products')->get();

            $placedInc = Order::where('status', 'placed')->where('order_type_id', 1)->count();
            $shipmentInc = Order::where('status', 'shipment')->where('order_type_id', 1)->count();
            $deliveredInc = Order::where('status', 'delivered')->where('order_type_id', 1)->count();

            $placedOut = Order::where('status', 'placed')->where('order_type_id', 2)->count();
            $shipmentOut = Order::where('status', 'shipment')->where('order_type_id', 2)->count();
            $deliveredOut = Order::where('status', 'delivered')->where('order_type_id', 2)->count();

            $result =[
                'requests' => $requset,  
                'partner' => $partner,
                'worker' => $worker,
                'category' => $categoriesWithCount,
                'placedInc' => $placedInc,
                'shipmentInc' => $shipmentInc,
                'deliveredInc' => $deliveredInc,
                'placedOut' => $placedOut,
                'shipmentOut' => $shipmentOut,
                'deliveredOut' => $deliveredOut,
            ];

            return $this->customResponse($result, 'Success');
        }catch (Exception $e) {
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getPartnerStat(){
        try{
            $user_id = Auth::user()->id;

            $order_count = Order::where('user_id', $user_id)->count();
            $product_count = Stock::where('user_id', $user_id)->count();
            $item_count = Stock::where('user_id', $user_id)->sum('quantity');

            $categoriesWithCount = productCategory::whereHas('products', function ($query) use ($user_id) {
                $query->whereHas('stocks', function ($subquery) use ($user_id) {
                    $subquery->where('user_id', $user_id);
                });
            })->withCount(['products' => function ($query) use ($user_id) {
                $query->whereHas('stocks', function ($subquery) use ($user_id) {
                    $subquery->where('user_id', $user_id);
                });
            }])->get();

            $placedInc = Order::where('user_id', $user_id)->where('status', 'placed')->where('order_type_id', 1)->count();
            $shipmentInc = Order::where('user_id', $user_id)->where('status', 'shipment')->where('order_type_id', 1)->count();
            $deliveredInc = Order::where('user_id', $user_id)->where('status', 'delivered')->where('order_type_id', 1)->count();

            $placedOut = Order::where('user_id', $user_id)->where('status', 'placed')->where('order_type_id', 2)->count();
            $shipmentOut = Order::where('user_id', $user_id)->where('status', 'shipment')->where('order_type_id', 2)->count();
            $deliveredOut = Order::where('user_id', $user_id)->where('status', 'delivered')->where('order_type_id', 2)->count();

            $result =[
                'order_count' => $order_count,
                'product_count' => $product_count,
                'item_count' => $item_count,
                'category' => $categoriesWithCount,
                'placedInc' => $placedInc,
                'shipmentInc' => $shipmentInc,
                'deliveredInc' => $deliveredInc,
                'placedOut' => $placedOut,
                'shipmentOut' => $shipmentOut,
                'deliveredOut' => $deliveredOut,
            ];

            return $this->customResponse($result, 'Success');
        }catch (Exception $e) {
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getWorkerStat(){
        try{
            $user_id = Auth::user()->id;

            $delivered = Order::where('worker_id', $user_id)->where('status', 'delivered')->count();
            $shipmentInc = Order::where('worker_id', $user_id)->where('status', 'shipment')->where('order_type_id', 1)->count();
            $shipmentOut = Order::where('worker_id', $user_id)->where('status', 'shipment')->where('order_type_id', 2)->count();

            $lastShipmentInc = Order::where('worker_id', $user_id)
                ->where('status', 'shipment')
                ->where('order_type_id', 1)
                ->orderBy('placed_at', 'desc')
                ->limit(4)
                ->get();

            $lastShipmentOut = Order::where('worker_id', $user_id)
                ->where('status', 'shipment')
                ->where('order_type_id', 2)
                ->orderBy('placed_at', 'desc')
                ->limit(4)
                ->get();

            $result =[
                'delivered' => $delivered,
                'shipmentInc' => $shipmentInc,
                'shipmentOut' => $shipmentOut,
                'lastShipmentInc' => $lastShipmentInc,
                'lastShipmentOut' => $lastShipmentOut,
            ];

            return $this->customResponse($result, 'Success');
        }catch (Exception $e) {
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getUser(){
        try{
            $user = Auth::user();
            return $this->customResponse($user, 'Success');
        }catch (Exception $e) {
            return self::customResponse($e->getMessage(),'error',500);
        }
    }


    function customResponse($data, $status = 'success', $code = 200){
        $response = ['status' => $status,'data' => $data];
        return response()->json($response,$code);
    }
}
