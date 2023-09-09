<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Product;
use Exception;

class StoredProductsController extends Controller
{
    public function getAll(){
        try{
            $stocks = Stock::with(['product.category', 'user'])->get();

            return $this->customResponse($stocks, 'success');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getById(Stock $stock){
        try{
            $stock = Stock::with(['product.category', 'user'])->find($stock->id);

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
            $stock = Stock::with(['user', 'product.category'])
                ->WhereHas('user', function ($userQuery) use ($requestSearch) {
                    $userQuery->where('company_name', 'LIKE', "%$requestSearch%");
                })
                ->orWhereHas('product', function ($userQuery) use ($requestSearch) {
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
