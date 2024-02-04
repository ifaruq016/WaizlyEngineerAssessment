<?php

namespace App\Http\Controllers;

use App\Models\UsersModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class AuthController extends Controller
{
    public function login(Request $request) {
        date_default_timezone_set('Asia/Jakarta');
        $result = [
            "status_code" => 200,
            "message_action" => "SUCCESS",
            "data" => (object)[],
            "response_datetime" => date("Y-m-d H:i:s")
        ];

        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
        
            if ($validator->fails()) {
                // Log validation failure
                Log::error('VALIDATION_FAILED', ['errors' => $validator->errors()->all()]);

                $result["status_code"] = 400;
                $result["message_action"] = "INVALID_PAYLOAD";

                $errors = $validator->errors();
                $result["data"] = (object)["errors" => $errors];
                return response()->json($result, $result["status_code"]);
            }

            $user = UsersModel::getUser($request->get('email'), ["ACTIVE"]);
            if (is_null($user->first())) {
                // Log user not found
                Log::warning('USER_NOT_FOUND', ['email' => $request->get('email')]);

                $result["status_code"] = 404;
                $result["message_action"] = "USER_NOT_FOUND";
                return response()->json($result, $result["status_code"]);
            }

            if (!Hash::check($request->get("password"), $user["password"])) {
                // Log invalid credential
                Log::warning('INVALID_CREDENTIAL', ['email' => $request->get('email')]);

                $result["status_code"] = 401;
                $result["message_action"] = "INVALID_CREDENTIAL";
                return response()->json($result, $result["status_code"]);
            }
            
            // GENERATED TOKEN
            $payload = JWTFactory::sub($user["id"])
                ->full_name($user["full_name"])
                ->email($user["email"])
                ->user_type($user["user_type"])
                ->status($user["status"])
                ->make();
            $token = JWTAuth::encode($payload);
            $user["token"] = (string)$token;
            
            // DELETE FROM DATA USER
            unset($user['password']);
            unset($user['created_at']);
            unset($user['updated_at']);

            // SET RESPONSE
            $result["data"] = $user;
            // Process Checking Success
            Log::info('LOGIN_CHECKER_SUCCESFULLY', ['result' => $result]);

            return response()->json($result, $result["status_code"]);
        } catch (Exception $err) {
            // Log general error
            Log::error('GENERAL_ERROR_REQUEST', ['error' => $err->getMessage()]);

            $result["status_code"] = 500;
            $result["message_action"] = "GENERAL_ERROR_REQUEST";
            return response()->json($result, $result["status_code"]);
        }
    }
}
