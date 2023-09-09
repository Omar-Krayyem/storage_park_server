<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\User;
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
                'password' => ['required', 'string', 'min:6'],
                'address' => ['required', 'string'],
                'company_name' => ['string', 'nullable'],
            ]);

            $user = User::find($user_id);

            $user->first_name = $validated_data['first_name'];
            $user->last_name = $validated_data['last_name'];
            $user->email = $validated_data['email'];
            $user->phone = $validated_data['phone'];
            $user->address = $validated_data['address'];

            $password = Hash::make($validated_data['password']);
            $user->password = $password;

            $user->company_name = null;
            if (array_key_exists('company_name', $validated_data)) {
                $user->company_name = $validated_data['company_name'];
            }

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
