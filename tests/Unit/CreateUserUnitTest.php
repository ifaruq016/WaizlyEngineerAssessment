<?php

namespace Tests\Unit;

use App\Models\UsersModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CreateUserUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    function clearDataUser() {
        DB::table('users')->where('email', '<>', 'me@admin.com')->delete();
    }

    function getToken(): string {
        $user = UsersModel::getUser('me@admin.com', ["ACTIVE"]);

        // GENERATED TOKEN
        $payload = JWTFactory::sub($user["id"])
        ->full_name($user["full_name"])
        ->email($user["email"])
        ->user_type($user["user_type"])
        ->status($user["status"])
        ->make();
        $token = JWTAuth::encode($payload);
        return $token;
    }

    public function test_valid_create_user(): void
    {
        // clear data users
        $this->clearDataUser();

        $token= $this->getToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token]);
        $response = $this->json('POST', '/api/user/insert', [
            "full_name" => "James Doe",
            "email" => "james@admin.com",
            "password" => "admin",
            "user_type" => "ADMINISTRATOR",
            "status" => "ACTIVE"
        ]);

        $response->assertSuccessful();
        $response->assertJson([
            'status_code' => 200,
            'message_action' => 'SUCCESS'
        ]);
    }

    public function test_invalid_payload(): void
    {
        $token = $this->getToken();
        $response =  $this->withHeaders(['Authorization' => 'Bearer '.$token]);
        $response = $this->json('POST', '/api/user/insert', [
            'name' => 'me@admin.com'
        ]);

        $response->assertBadRequest();
        $response->assertJson([
            'status_code' => 400,
            'message_action' => 'INVALID_PAYLOAD'
        ]);
    }

    public function test_email_exist(): void
    {
        $token = $this->getToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token]);
        $response = $this->json('POST', '/api/user/insert', [
            "full_name" => "James Doe",
            "email" => "james@admin.com",
            "password" => "admin",
            "user_type" => "ADMINISTRATOR",
            "status" => "ACTIVE"
        ]);

        $response->assertUnprocessable();
        $response->assertJson([
            'status_code' => 422,
            'message_action' => 'EMAIL_ALREADY_REGISTERED'
        ]);
    }
}
