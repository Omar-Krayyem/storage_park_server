<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use Exception;
use App\Models\User;
use App\Models\UserType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Config;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;


class RequestController extends Controller
{
    public function getAllRequest(){
        try{

            $users = User::where('user_type_id', 3)->where('password', null)->get();
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

    public function rejectedRequest(User $user){
        try{
            $user->delete();
            return $this->customResponse($user, 'Deleted Successfully');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function requestSearch($requestSearch) {
        try {
            $users = User::where(function ($query) use ($requestSearch) {
                $query->where('email', 'LIKE', "%$requestSearch%")
                     ->orWhere('company_name', 'LIKE', "%$requestSearch%");
            })
            ->where('user_type_id', 3)
            ->whereNull('password')
            ->get();
    
            return $this->customResponse($users);
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(), 'error', 500);
        } 
    }
    
    public function acceptedRequest(Request $request){
        try{
            $validated_data = $this->validate($request, [
                'user_id' => ['required', 'numeric'],
                'password' => ['required', 'string']
            ]);
            
            $user_id = $validated_data['user_id'];
            $password = $validated_data['password'];

            $user = User::find($user_id);

            $password = Hash::make($password);
            $user->password = $password;
            $user->save();

            return $this->customResponse($user);
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    // public function sendEmailWithLoginDetails($userEmail, $userPassword)
    // {
    //     $userEmail = "omar.krayyem95@gmail.com";

    //     $transport = (new Swift_SmtpTransport(Config::get('mail.host'), Config::get('mail.port')))
    //         ->setUsername(Config::get('mail.username'))
    //         ->setPassword(Config::get('mail.password'));

    //     $mailer = new Swift_Mailer($transport);

    //     // Create the message
    //     $message = (new Swift_Message('Your Login Details'))
    //         ->setFrom(['storagepark.lb@gmail.com' => 'Storage Park'])
    //         ->setTo([$userEmail])
    //         ->setBody("Your email: {$userEmail}\nYour password: {$userPassword}");

    //     // Send the email
    //     $result = $mailer->send($message);

    //     if ($result) {
    //         return response()->json(['message' => 'Email sent successfully'], 200);
    //     } else {
    //         return response()->json(['message' => 'Failed to send email'], 500);
    //     }
    // }

    function customResponse($data, $status = 'success', $code = 200){
        $response = ['status' => $status,'data' => $data];
        return response()->json($response,$code);
    }
}
