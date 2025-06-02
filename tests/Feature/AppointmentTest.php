<?php

use App\Models\User;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('stores appointment', function () {
    $client = $this->user->clients()->create();

    $response = $this->actingAs($this->user)
        ->postJson('/api/appointments', [
            'client_id' => $client->id,
            'title' => fake()->title,
            'description' => fake()->paragraph,
            'start_time' => Carbon::today(),
        ]);


    expect($response->isOk())
       ->and($response->json('message'))->toBe('Appointment created successfully for your client.');

});
