<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Client\StoreClientRequest;
use App\Http\Requests\Api\Client\UpdateClientRequest;
use App\Models\Client;
use App\Services\Client\ClientService;

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

        $this->service->store($request->validated());

        return response()->json([
            'message' => "Client created successfully for {$user->name}.",
            'data' => $this->service->getClient(),
        ], 201);
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $this->service
            ->setClient($client)
            ->update($request->validated());

        return response()->json([
            'message' => "Client updated successfully.",
            'data' => $this->service->getClient(),
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
