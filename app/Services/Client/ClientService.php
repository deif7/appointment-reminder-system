<?php

namespace App\Services\Client;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ClientService
{
    private Client $client;

    public function index()
    {
        return Auth::user()->clients()->latest()->get();
    }

    public function store(array $data): self
    {
        $this->client = Auth::user()->clients()->create($data);
        return $this;
    }


    public function update(array $data): self
    {
        // Toggle on email/sms on update
        if (isset($data['prefers_email']) && $data['prefers_email']) {
            $data['prefers_sms'] = false;
        }
        if (isset($data['prefers_sms']) && $data['prefers_sms']) {
            $data['prefers_email'] = false;
        }

        $this->client->update($data);

        return $this;

    }

    public function destroy(Client $client): self
    {
        $client->delete();

        return $this;
    }

    /**
     * @param $client
     * @return mixed
     */
    public function setClient($client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}
