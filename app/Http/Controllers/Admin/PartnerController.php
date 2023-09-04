<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\User;

class PartnerController extends Controller
{
    public function getAllPartner(){
        try{

            $users = User::where('user_type_id', 3)->whereNotNull('password')->get();
            return $this->customResponse($users->load('type'));
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getById(User $user){
        try{
            return $this->customResponse($user->load('type'));
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function deletePartner(User $user){
        try{
            $user->delete();
            return $this->customResponse($user, 'Deleted Successfully');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function partnerSearch($requestSearch) {
        try {
            $users = User::where(function ($query) use ($requestSearch) {
                $query->where('email', 'LIKE', "%$requestSearch%")
                     ->orWhere('company_name', 'LIKE', "%$requestSearch%");
            })
            ->where('user_type_id', 3)
            ->whereNotNull('password')
            ->get();
    
            return $this->customResponse($users);
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(), 'error', 500);
        } 
    }

    public function updatePartner(Request $request_info)
    {
        try {
            $user = User::find($request_info->user_id);
            $user->first_name = $request_info->first_name;
            $user->last_name = $request_info->last_name;
            $user->email = $request_info->email;
            $user->phone = $request_info->phone;
            $user->address = $request_info->address;
            $user->company_name = $request_info->company_name;
    
            $user->save();
    
            return $this->customResponse($user, 'Updated Successfully');
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    function customResponse($data, $status = 'success', $code = 200){
        $response = ['status' => $status,'data' => $data];
        return response()->json($response,$code);
    }
}
