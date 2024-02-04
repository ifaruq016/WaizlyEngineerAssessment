<?php

namespace App\Http\Middleware;

use App\Models\UsersModel;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTVerificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        date_default_timezone_set('Asia/Jakarta');
        $result = [
            "status_code" => 401,
            "message_action" => "UNAUTHORIZED",
            "data" => (object)[],
            "response_datetime" => date("Y-m-d H:i:s")
        ];

        try {
            // Retrieve the JWT token from the request headers
            $token = $request->bearerToken();

            // Decode the JWT token
            $decodedToken = JWTAuth::setToken($token)->getPayload();

            // Check if the token is expired
            if ($decodedToken["exp"] < time()) {
                throw new TokenExpiredException('TOKEN_EXPIRED');
            }

            $userId = $decodedToken->get('sub');
            $user = UsersModel::getUserById($userId);

            // Check if the user is active
            if (!$user || $user["status"] != 'ACTIVE') {
                $result["status_code"] = 401;
                $result["message_action"] = "UNAUTHORIZED";
                return response()->json($result, $result["status_code"]);
            }

            return $next($request);
        } catch (TokenExpiredException $err) {
            $result["status_code"] = 401;
            $result["message_action"] = 'TOKEN_EXPIRED';
            return response()->json($result, $result["status_code"]);
        } catch (Exception $err) {
            return response()->json($result, $result["status_code"]);
        }
    }
}
