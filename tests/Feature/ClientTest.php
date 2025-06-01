<?php

use App\Models\Client;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

});


it('validates required fields for client creation', function () {

    $response = $this->actingAs($this->user)
        ->postJson('/api/clients', []);

    expect($response->isOk())
        ->toBeEmpty()
        ->and($response->status())->toBe(422)
        ->and($response->json('errors'))->toHaveKeys(['name', 'email', 'phone']);
});

it('creates a new client', function () {
    $clientData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '1234567890',
        'timezone' => 'Europe/Sofia',
        'prefers_email' => true,
    ];

    $response = $this->actingAs($this->user)
        ->postJson('/api/clients', $clientData);

    expect($response->isOk())
        ->and($response->status())->toBe(201)
        ->and($response->json('data'))->toMatchArray($clientData)
        ->and($response->json('message'))->toBe("Client created successfully for {$this->user->name}.");
});


it('deletes an existing client', function () {

    $client = Client::factory()->create();

    $response = $this->actingAs($this->user)->deleteJson("/api/clients/{$client->id}");

    expect($response)
        ->status()->toBe(200)
        ->and($response->json('message'))->toBe('Client deleted successfully.')
        ->and(Client::find($client->id))->toBeNull();
});

it('shows an existing client', function () {
    $client = $this->user->clients()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '1234567890',
        'timezone' => 'Europe/Sofia',
        'prefers_email' => true,
    ]);
    $response = $this->actingAs($this->user)->getJson("/api/clients/{$client->id}");

    expect($response->isOk())
        ->and($response->json())->toMatchArray($client->toArray());

});



