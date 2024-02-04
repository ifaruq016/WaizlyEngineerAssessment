<?php

namespace Tests\Unit;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use App\Models\UsersModel;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DeleteUserUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    function createdNewUser() {
        $user = UsersModel::create([
            'full_name' => 'James Jacob',
            'email' => 'james@admin.com',
            'user_type' => 'ADMINISTRATOR',
            'status' => 'ACTIVE',
            'password' => Hash::make('password'), // Hash the password
        ]);

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

    public function test_valid_delete_user(): void
    {
        $user_id = $this->createdNewUser();
        $token= $this->getToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token]);
        $response = $this->json('DELETE', '/api/user/delete/'.$user_id);

        $response->assertSuccessful();
        $response->assertJson([
            'status_code' => 200,
            'message_action' => 'DELETED'
        ]);
    }
}
