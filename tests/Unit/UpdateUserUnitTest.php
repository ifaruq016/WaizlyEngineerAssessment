<?php

namespace Tests\Unit;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use App\Models\UsersModel;
use Tests\TestCase;

class UpdateUserUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    function setUserForUpdate(): string {
        $user = UsersModel::getLastUser();
        return $user->id;
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

    public function test_valid_update_user(): void
    {
        $user_id = $this->setUserForUpdate();
        $token= $this->getToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token]);
        $response = $this->json('PUT', '/api/user/profile/update/'.$user_id, [
            "full_name" => "Brian James",
            "email" => "james@admin.com",
            "user_type" => "USER",
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
        $user_id = $this->setUserForUpdate();
        $token = $this->getToken();
        $response =  $this->withHeaders(['Authorization' => 'Bearer '.$token]);
        $response = $this->json('PUT', '/api/user/profile/update/'.$user_id, [
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
        $user_id = $this->setUserForUpdate();
        $token = $this->getToken();
        $response =  $this->withHeaders(['Authorization' => 'Bearer '.$token]);
        $response = $this->json('PUT', '/api/user/profile/update/'.$user_id, [
            "full_name" => "Brian Adams",
            "email" => "me@admin.com",
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
