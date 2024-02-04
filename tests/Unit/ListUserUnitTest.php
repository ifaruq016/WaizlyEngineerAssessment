<?php

namespace Tests\Unit;

use App\Models\UsersModel;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Facades\JWTAuth;

class ListUserUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_valid_list(): void
    {
        $user = UsersModel::getUser('me@admin.com', ["ACTIVE"]);

        // GENERATED TOKEN
        $payload = JWTFactory::sub($user["id"])
        ->full_name($user["full_name"])
        ->email($user["email"])
        ->user_type($user["user_type"])
        ->status($user["status"])
        ->make();
        $token = JWTAuth::encode($payload);

        // Send a GET request to the API endpoint with the JWT authentication header
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
                         ->get('/api/user/all?page=1&search&sort=DESC');

        // Assert the response status is 200 OK
        $response->assertStatus(200);

        // Assert the response structure or any specific data returned
        // For example, you can assert that the response contains a 'data' key
        $response->assertJsonStructure([
            'data'
            // Add more keys as needed based on your response structure
        ]);
    }
}
