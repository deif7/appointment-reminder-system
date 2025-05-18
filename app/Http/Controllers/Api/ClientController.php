<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Client\StoreClientRequest;
use App\Http\Requests\Api\Client\UpdateClientRequest;
use App\Models\Client;
use App\Services\Client\ClientService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Throwable;

class ClientController extends Controller
{
    protected ClientService $service;

    public function __construct(ClientService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        return response()->json($this->service->index());
    }

    public function show(Client $client): JsonResponse
    {
        try {
            $this->authorize('view', $client);

            return response()->json($client);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        } catch (Throwable $e) {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();

            $this->service->store($request->validated());

            return response()->json([
                'message' => "Client created successfully for {$user->name}.",
                'data' => $this->service->getClient(),
            ], 201);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to create client.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateClientRequest $request, Client $client): JsonResponse
    {
        try {
            $this->service
                ->setClient($client)
                ->update($request->validated());

            return response()->json([
                'message' => "Client updated successfully.",
                'data' => $this->service->getClient(),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Client not found.'], 404);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to update client.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Client $client): JsonResponse
    {
        try {
            $this->service->destroy($client);

            return response()->json([
                'message' => "Client deleted successfully."
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Client not found.'], 404);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to delete client.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
