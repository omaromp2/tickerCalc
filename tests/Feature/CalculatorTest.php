<?php

use App\Models\User;

beforeEach(function () {
    $user = User::factory()->create();
    $this->actingAs($user);
});

test('calculator can add', function () {
    $response = $this->postJson('/api/calculations', [
        'expression' => '2+3',
    ]);

    $response->assertOk();
    $response->assertJsonPath('result', '5');
});

test('calculator can subtract', function () {
    $response = $this->postJson('/api/calculations', [
        'expression' => '10-4',
    ]);

    $response->assertOk();
    $response->assertJsonPath('result', '6');
});

test('calculator can multiply', function () {
    $response = $this->postJson('/api/calculations', [
        'expression' => '3*4',
    ]);

    $response->assertOk();
    $response->assertJsonPath('result', '12');
});

test('calculator can divide', function () {
    $response = $this->postJson('/api/calculations', [
        'expression' => '10/2',
    ]);

    $response->assertOk();
    $response->assertJsonPath('result', '5');
});

test('calculator can handle complex sqrt expression 1', function () {
    $response = $this->postJson('/api/calculations', [
        'expression' => 'sqrt((((9*9)/12)+(13-4))*2*2)^2',
    ]);

    $response->assertOk();
    $response->assertJsonPath('result', '63');
});

test('calculator can handle complex sqrt expression 2', function () {
    $response = $this->postJson('/api/calculations', [
        'expression' => 'sqrt(((((9*9)/12)+(13-4))*2)^2)',
    ]);

    $response->assertOk();
    $response->assertJsonPath('result', '31.5');
});
