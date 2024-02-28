<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;
use Twilio\Rest\Client;
use App\Traits\TwilioRequest;
use App\Jobs\SendOtpMessage;


class AuthController extends Controller
{

    use TwilioRequest;

    /**
    * Create user
    *
    * @param  [string] name
    * @param  [string] email
    * @param  [string] password
    * @param  [string] phone
    */
    public function register(Request $request)
    {

        try {

                $request->validate([
                    'name' => 'required|string',
                    'phone'=> 'required|numeric|unique:users',
                    'email'=>'required|string|unique:users',
                    'password'=>'required|string'
                ]);


                    $user = new User([
                        'name'  => $request->name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'password' => bcrypt($request->password),
                    ]);
                    SendOtpMessage::dispatch($request->phone)->delay(now()->addMinutes(1));
                if($user->save()){

                    $phoneNumber = $request->phone;
                    $tokenResult = $user->createToken('Personal Access Token');
                    $token = $tokenResult->plainTextToken;

                    return response()->json([
                    'message' => 'Successfully created user!',
                    'phone' => $phoneNumber,
                    'accessToken'=> $token,
                    ],200);
                }
                else{
                    return response()->json(['error'=>'Provide proper details'],400);
                }

        }
        catch (\Exception $exception) {
                    // dd(get_class($exception));
                    return response()->json(['error' => $exception->getMessage()]);

                }
    }

                /**
             * Login user and check phone is Verified and  create token
            *
            * @param  [string] email
            * @param  [string] password
            */

            public function login(Request $request)
            {

                try {


                $request->validate([
                    'email' => 'required|string|email',
                    'password' => 'required|string',
                    ]);

                    $credentials = request(['email','password',]);
                    if(!Auth::attempt($credentials))
                    {
                    return response()->json([
                        'message' => 'Unauthorized'
                    ],401);
                    }

                    $user = $request->user();
                    $phoneNumber = $user->phone ;
                    $verify  =  $user->isVerified;

                    if ($verify == true) {
                        $tokenResult = $user->createToken('Personal Access Token');
                        $token = $tokenResult->plainTextToken;

                        return response()->json([
                        'accessToken' =>$token,
                        'token_type' => 'Bearer',
                        'phone' => $phoneNumber,
                        ]);
                    }
                    return response()->json([
                        'message' =>'Phone Number Not Verified!',
                        'phone' => $phoneNumber,
                        ],400);

                } catch (\Exception $exception) {
                    // dd(get_class($exception));
                    return response()->json(['error' => $exception->getMessage()]);

                }


            }

            /**
             * Logout user (Delete the token)
            *
            * @return [string] message
            */
            public function logout(Request $request)
            {

                try {

                    $request->user()->tokens()->delete();

                return response()->json([
                'message' => 'Successfully logged out'
                ]);

                 } catch (\Exception $exception) {
                    // dd(get_class($exception));
                    return response()->json(['error' => $exception->getMessage()]);

                }


            }

             /**
             * Get the authenticated User
            *
            * @return [json] user object
            */
            public function user(Request $request)
            {
                try {

                    return response()->json($request->user());


                } catch (\Exception $exception) {
                    // dd(get_class($exception));
                    return response()->json(['error' => $exception->getMessage()]);

                }

            }

           /**
             * Verified Phone Number
             * @param  [string] verification_code
            * @param  [string] phone
            *
            * @return [json] response
            */

            protected function verify(Request $request)
            {

                try {

                    $data = $request->validate([
                        'verification_code' => ['required', 'numeric'],
                        'phone' => ['required', 'string'],
                    ]);

                    $verification =  $this->Otpverify($data['verification_code'],$data['phone']);
                        if ($verification->valid) {
                            User::where('phone', $data['phone'])->update(['isVerified' => true]);
                            $phoneNumber =  $request->phone;
                            return response()->json([
                                'message' =>'Phone number verified',
                                'phone' => $phoneNumber,
                                ],200);
                                               }

                     $phoneNumber =  $request->phone;
                    return response()->json([
                        'message' =>'Invalid verification code entered!',
                        'phone' => $phoneNumber,
                        ],400);


                } catch (\Exception $exception) {
                    // dd(get_class($exception));
                    return response()->json(['error' => $exception->getMessage()]);

                }





}
}
