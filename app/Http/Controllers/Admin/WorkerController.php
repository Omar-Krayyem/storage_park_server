<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class WorkerController extends Controller
{
    public function createWorker(Request $request_info)
    {
        try {
            $validated_data = $this->validate($request_info, [
                'first_name' => ['required', 'string'],
                'last_name' => ['required', 'string'],
                'email' => ['required', 'email', 'string'],
                'password' => ['required', 'string', 'min: 6'],
                'phone' => ['required', 'numeric', 'min:8'],
                'address' => ['required','string'],
            ]); 

            $existingUser = User::where('email', $validated_data['email'])->first();
            if ($existingUser) {
                return self::customResponse('Email already exists', 'error', 400);
            }

            $user_type_id = 2;
    
            $user = User::create([
                'first_name' => $validated_data['first_name'],
                'last_name' => $validated_data['last_name'],
                'email' => $validated_data['email'],
                'password' => Hash::make($validated_data['password']),
                'phone' => $validated_data['phone'],
                'address' => $validated_data['address'],
                'user_type_id' => $user_type_id,
            ]);
    
            return $this->customResponse($user, 'Created Successfully');
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getAllWorker(){
        try{

            $users = User::where('user_type_id', 2)->get();
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

    public function deleteWorker(User $user){
        try{
            $user->delete();
            return $this->customResponse($user, 'Deleted Successfully');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function workerSearch($requestSearch) {
        try {
            $users = User::where(function ($query) use ($requestSearch) {
                $query->where('email', 'LIKE', "%$requestSearch%")
                     ->orWhere('first_name', 'LIKE', "%$requestSearch%")
                     ->orWhere('last_name', 'LIKE', "%$requestSearch%");
            })
            ->where('user_type_id', 2)
            ->get();
    
            return $this->customResponse($users);
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(), 'error', 500);
        } 
    }

    public function updateWorker(Request $request_info)
    {
        try {
            $user = User::find($request_info->id);
            $user->first_name = $request_info->first_name;
            $user->last_name = $request_info->last_name;
            $user->email = $request_info->email;
            $user->phone = $request_info->phone;
            $user->address = $request_info->address;
    
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
