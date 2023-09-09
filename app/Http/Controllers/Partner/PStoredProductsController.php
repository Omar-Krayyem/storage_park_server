<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;
use Exception;
use Illuminate\Support\Facades\Auth;

class PStoredProductsController extends Controller
{
    public function getAll(){
        try{
            $user_id = Auth::user()->id;
            $stocks = Stock::with('product.category')->where('user_id', $user_id)->get();

            return $this->customResponse($stocks, 'success');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getById(Stock $stock){
        try{
            $user_id = Auth::user()->id;
            $stock = Stock::with('product.category')->where('user_id', $user_id)->find($stock->id);

            if (!$stock) {
                return $this->customResponse('Stock not found', 'error', 404);
            }

            return $this->customResponse($stock, 'success');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function search($requestSearch) {
        try {
            $user_id = Auth::user()->id;
            $stock = Stock::with('product.category')
                ->where('user_id', $user_id)
                ->WhereHas('product', function ($userQuery) use ($requestSearch) {
                    $userQuery->where('name', 'LIKE', "%$requestSearch%");
                })
                ->get();
    
            return $this->customResponse($stock);
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(), 'error', 500);
        } 
    }

    function customResponse($data, $status = 'success', $code = 200){
        $response = ['status' => $status,'data' => $data];
        return response()->json($response,$code);
    }
}
