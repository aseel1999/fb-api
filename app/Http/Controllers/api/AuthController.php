<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use App\Helpers\Messages;
use Illuminate\Support\Facades\Validator;
use laravel\Sanctum;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator($request->all(), [
            'name' => 'required|string|min:3|max:45',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:3',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }
    public function login(Request $request){
            $validator =  Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
    
            if (! $token = auth()->attempt($validator->validated())) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
    
            return $this->createNewToken($token);
        }
    public function forgetPassword(Request $request){
        $validator=Validator($request->all(),[
            'email'=>'required|email|exists:users,email',
        ]);
        if(!$validator->fails()){
            $user=User::where('email','=',$request->input('email'))->first();
            $authCode=random_int(1000,9999);
            $user->auth_code=Hash::make($authCode);
            $isSaved=$user->save();
            return response()->json(
                [
                'status' =>$isSaved,
                'message' => $isSaved ? 'Reset code sent successfully' :' Failed to send reset code !',
                'code'   => $authCode,
            ], $isSaved ? 200 : 400
        );

        }
        else {
            return response()->json(['message' => $validator->getMessageBag()->first()],401);
        }
    }
    public function resetPassword(Request $request){
        $validator=Validator($request->all(),[
            'email'=>'required|email|exists:users,email',
            'auth_code' => 'required|numeric|digits:4',
            'password' => 'required|string|min:3|max:15|confirmed'
        ]);
        if(!$validator->fails()){
            $user=User::where('email','=',$request->input('email'))->first();
            if(!is_null($user->auth_code)){
                if (Hash::check($request->input('auth_code'),$user->auth_code)){
                    $user->password=Hash::make($request->input('password'));
                    $user->auth_code=null;
                    $isSaved=$user->save();
                    return Response::json([
                        'status' => $isSaved,
                        'message'=>Messages::getMessage($isSaved ? 'RESET_PASSWORD_SUCCESS' : 'RESET_PASSWORD_FAILED'),
                    ],
                    $isSaved ? 200:401
                );
            }else {
                return response()->json([
                    'status' => false,
                    'message' => Messages::getMessage('AUTH_CODE_ERROR')
                ], 401);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => Messages::getMessage('NO_PASSWORD_RESET_CODE')
            ], 401);
        }
    } else {
        return response()->json([
            'message' => $validator->getMessageBag()->first()
        ], 401);
    }
}
public function logout($id){
    $user = auth()->user();
    $user->tokens()->findOrFail($id)->delete();
    return [
        'message' => 'Token deleted'
    ];

}
protected function createNewToken($token){
    return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'user' => Auth::guard('sanctum')->user()
    ]);
}
public function userProfile() {
    return response()->json(Auth::guard('sanctum')->user());
}
}






        