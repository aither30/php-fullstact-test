<?php

namespace App\Http\Controllers;

use App\Models\MyClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class MyClientController extends Controller
{
    public function index()
    {
        $clients = MyClient::whereNull('deleted_at')->get();
        return response()->json($clients);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:250',
            'slug' => 'required|string|max:100|unique:my_client',
            'is_project' => 'required|in:0,1',
            'self_capture' => 'required|string|max:1',
            'client_prefix' => 'required|string|max:4',
            'client_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:255',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('client_logo')) {
            $path = $request->file('client_logo')->store('logos', 's3');
            $validated['client_logo'] = Storage::disk('s3')->url($path);
        }

        $client = MyClient::create($validated);

        Redis::set("client:{$client->slug}", json_encode($client));

        return response()->json([
            'message' => 'Data tersimpan.',
            'data' => $client
        ], 201);
    }

    public function show($id)
    {
        $client = MyClient::find($id);

        if ($client) {
            return response()->json($client);
        }

        return response()->json(['message' => 'Data tidak ada'], 404);
    }

    public function update(Request $request, $id)
    {
        $client = MyClient::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:250',
            'slug' => "string|max:100|unique:my_client,slug,{$id}",
            'is_project' => 'in:0,1',
            'self_capture' => 'string|max:1',
            'client_prefix' => 'string|max:4',
            'client_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:255',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('client_logo')) {
            $path = $request->file('client_logo')->store('logos', 's3');
            $validated['client_logo'] = Storage::disk('s3')->url($path);
        }

        Redis::del("client:{$client->slug}");

        $client->update($validated);

        Redis::set("client:{$client->slug}", json_encode($client));

        return response()->json([
            'message' => 'Data berhasil diupdate.',
            'data' => $client
        ]);
    }

    public function destroy($id)
    {
        $client = MyClient::findOrFail($id);
        $client->update(['deleted_at' => now()]);
        Redis::del("client:{$client->slug}");

        return response()->json(['message' => 'Data dihapus.']);
    }
}
