<?php

namespace Tests\Unit;

use Tests\TestCase;

class LoginUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_valid_login(): void
    {
        $response = $this->json('POST', '/api/login', [
            'email' => 'me@admin.com',
            'password' => 'admin',
        ]);

        // Assert the response status is 200 OK
        $response->assertStatus(200);

        // Assert the response structure and data
        $response->assertJsonStructure([
            'status_code',
            'message_action',
            'data' => [
                'id',
                'full_name',
                'email',
                'user_type',
                'status',
                'token'
            ],
            'response_datetime'
        ]);
    }

    public function test_invalid_payload(): void
    {
        $response = $this->json('POST', '/api/login', [
            'email' => 'me@admin.com'
        ]);

        $response->assertBadRequest();
        $response->assertJson([
            'status_code' => 400,
            'message_action' => 'INVALID_PAYLOAD'
        ]);
    }

    public function test_invalid_credential_user(): void
    {
        $response = $this->json('POST', '/api/login', [
            'email' => 'me@admin.com',
            'password' => 'password'
        ]);

        $response->assertUnauthorized();
        $response->assertJson([
            'status_code' => 401,
            'message_action' => 'INVALID_CREDENTIAL'
        ]);
    }
}
