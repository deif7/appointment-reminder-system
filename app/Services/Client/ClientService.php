<?php

namespace App\Services\Client;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class ClientService
{
    private Client $client;

    public function index()
    {
        return Auth::user()->clients()->latest()->get();
    }

    /**
     * @throws Throwable
     */
    public function store(array $data): self
    {
        DB::beginTransaction();

        try {
            $this->client = Auth::user()->clients()->create($data);
            DB::commit();

            return $this;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    public function update(array $data): self
    {
        DB::beginTransaction();

        try {
            // Toggle on email/sms on update
            if (isset($data['prefers_email']) && $data['prefers_email']) {
                $data['prefers_sms'] = false;
            }
            if (isset($data['prefers_sms']) && $data['prefers_sms']) {
                $data['prefers_email'] = false;
            }

            $this->client->update($data);
            DB::commit();

            return $this;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    public function destroy(Client $client): self
    {
        DB::beginTransaction();

        try {
            $client->delete();
            DB::commit();

            return $this;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function setClient($client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
