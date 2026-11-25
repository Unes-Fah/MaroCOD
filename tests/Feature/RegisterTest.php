<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function test_register_returns_201_and_a_token(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Sara', 'store_name' => 'Sara Shop',
            'email' => 'sara@test.com', 'password' => 'secret123',
        ])->assertCreated()->assertJsonStructure(['merchant' => ['id', 'email'], 'token']);
    }
}
