<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Services\ClientService;

class ClientController extends Controller
{
    protected ClientService $service;

    public function __construct(ClientService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json($this->service->index());
    }

    public function show(Client $client)
    {
        $this->authorize('view', $client);

        return response()->json($client);
    }


    public function store(StoreClientRequest $request)
    {
        $user = auth()->user();
        $client = $this->service->store($request->validated());

        return response()->json([
            'message' => "Client created successfully for {$user->name}.",
            'data' => $client,
        ], 201);
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $updatedClient = $this->service->update($client, $request->validated());

        return response()->json([
            'message' => "Client updated successfully.",
            'data' => $updatedClient,
        ], 200);
    }

    public function destroy(Client $client)
    {
        $this->service->destroy($client);

        return response()->json([
            'message' => "Client deleted successfully."
        ], 200);
    }

}
