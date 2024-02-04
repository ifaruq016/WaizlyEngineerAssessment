<?php

namespace App\Http\Controllers;

use App\Models\UsersModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function list(Request $request) {
        date_default_timezone_set('Asia/Jakarta');
        $result = [
            "status_code" => 200,
            "message_action" => "SUCCESS",
            "data" => (object)[],
            "response_datetime" => date("Y-m-d H:i:s")
        ];

        try {
            $validator = Validator::make($request->query(), [
                'page' => 'nullable|integer',
                'search' => 'nullable|string',
                'sort' => 'nullable|string|in:ASC,DESC'
            ]);
        
            if ($validator->fails()) {
                // Log validation failure
                Log::error('INVALID_PAYLOAD', ['errors' => $validator->errors()->all()]);
                
                $result["status_code"] = 400;
                $result["message_action"] = "INVALID_PAYLOAD";

                $errors = $validator->errors();
                $result["data"] = (object)["errors" => $errors];
                return response()->json($result, $result["status_code"]);
            }

            $users = UsersModel::getAllUser(
                $request->query("page", 1),
                $request->query("sort", "ASC"),
                $request->query("search", null),
            );

            $result_data = [
                "current_page" => $users->currentPage(),
                "total_pages" => $users->lastPage(),
                "per_page" => $users->perPage(),
                "total_users" => $users->total(),
                "rows" => [],
            ];

            if ($users) {
                $result_data["rows"] = $users->items();
            }

            $result["data"] = $result_data; 

            // Log successful response
            Log::info('LIST_METHOD_SUCCESSFULLY', ['result' => $result]);

            return response()->json($result, $result["status_code"]);
        } catch (Exception $err) {
            // Log general error
            Log::error('GENERAL_ERROR_REQUEST', ['error' => $err->getMessage()]);

            $result["status_code"] = 500;
            $result["message_action"] = "GENERAL_ERROR_REQUEST";

            return response()->json($result, $result["status_code"]);
        }
    }

    public function create(Request $request) {
        date_default_timezone_set('Asia/Jakarta');
        $result = [
            "status_code" => 200,
            "message_action" => "SUCCESS",
            "data" => (object)[],
            "response_datetime" => date("Y-m-d H:i:s")
        ];

        $data = $request->all();

        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'full_name' => 'required|string',
                'password' => 'required|string',
                'user_type' => 'required|string|in:ADMINISTRATOR,USER',
                'status' => 'required|string|in:ACTIVE,INACTIVE',
            ]);
        
            if ($validator->fails()) {
                // Log validation failure
                Log::error('INVALID_PAYLOAD', ['errors' => $validator->errors()->all()]);

                $result["status_code"] = 400;
                $result["message_action"] = "INVALID_PAYLOAD";

                $errors = $validator->errors();
                $result["data"] = (object)["errors" => $errors];
                return response()->json($result, $result["status_code"]);
            }

            $checkUser = UsersModel::getUser($request->get('email'), ["ACTIVE"]);
            if ($checkUser->first()) {
                // Log user not found
                Log::warning('EMAIL_ALREADY_REGISTERED', ['email' => $request->get('email')]);

                $result["status_code"] = 422;
                $result["message_action"] = "EMAIL_ALREADY_REGISTERED";
                return response()->json($result, $result["status_code"]);
            }

            // INSERT NEW USER
            // Process Save to DB
            $user = new UsersModel();
            $user->full_name = strval($data['full_name']);
            $user->email = strval($data['email']);
            $user->user_type = strval($data['user_type']);
            $user->status = strval($data['status']);
            $user->password =Hash::make( strval($data['password']));
            $user->save();

            // Log successful response
            Log::info('CREATED_USER_SUCCESSFULLY', ['result' => $result]);

            return response()->json($result, $result["status_code"]);
        } catch (Exception $err) {
            // Log general error
            Log::error('GENERAL_ERROR_REQUEST', ['error' => $err->getMessage()]);

            $result["status_code"] = 500;
            $result["message_action"] = "GENERAL_ERROR_REQUEST";

            return response()->json($result, $result["status_code"]);
        }
    }

    public function update($id, Request $request) {
        date_default_timezone_set('Asia/Jakarta');
        $result = [
            "status_code" => 200,
            "message_action" => "SUCCESS",
            "data" => (object)[],
            "response_datetime" => date("Y-m-d H:i:s")
        ];

        $data = $request->all();

        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'full_name' => 'required|string',
                'user_type' => 'required|string|in:ADMINISTRATOR,USER',
                'status' => 'required|string|in:ACTIVE,INACTIVE',
            ]);
        
            if ($validator->fails()) {
                $result["status_code"] = 400;
                $result["message_action"] = "INVALID_PAYLOAD";

                $errors = $validator->errors();
                $result["data"] = (object)["errors" => $errors];
                return response()->json($result, $result["status_code"]);
            }

            $checkUser = UsersModel::getUser($request->get('email'), ["ACTIVE"]);
            if ($checkUser->first()) {
                if ($checkUser->id != $id){
                    // Log user not found
                    Log::warning('EMAIL_ALREADY_REGISTERED', ['email' => $request->get('email')]);
    
                    $result["status_code"] = 422;
                    $result["message_action"] = "EMAIL_ALREADY_REGISTERED";
                    return response()->json($result, $result["status_code"]);
                }
            }

            $user = UsersModel::findOrFail($id);
            // Process Save to DB
            $user->full_name = strval($data['full_name']);
            $user->email = strval($data['email']);
            $user->user_type = strval($data['user_type']);
            $user->status = strval($data['status']);
            $user->save();

            // Log successful response
            Log::info('UPDATED_USER_SUCCESSFULLY', ['result' => $result]);

            return response()->json($result, $result["status_code"]);
        } catch (Exception $err) {
            // Log general error
            Log::error('GENERAL_ERROR_REQUEST', ['error' => $err->getMessage()]);

            $result["status_code"] = 500;
            $result["message_action"] = "GENERAL_ERROR_REQUEST";

            return response()->json($result, $result["status_code"]);
        }
    }

    public function delete($id) {
        date_default_timezone_set('Asia/Jakarta');
        $result = [
            "status_code" => 200,
            "message_action" => "DELETED",
            "data" => (object)[],
            "response_datetime" => date("Y-m-d H:i:s")
        ];

        try {
            $user = UsersModel::findOrFail($id);
            //Process delete
            $user->delete();

            // Log successful response
            Log::info('DELETED_USER_SUCCESSFULLY', ['result' => $result]);

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
