<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ClientService
{
    public function index()
    {
        return Auth::user()->clients()->latest()->get();
    }

    public function store(array $data): Client
    {
        return Auth::user()->clients()->create($data);
    }


    public function update(Client $client, array $data): Client
    {
        $client->update($data);
        return $client;
    }

    public function destroy(Client $client): void
    {
        $client->delete();
    }
}
