<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Client;
use Validator;

class ClientController extends Controller
{
    public function index () {
        return response()->json(Client::whereNull('deleted_at')->get());
    }

    public function create (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'max:50|required',
            'reference' => 'max:50|required',
            'department_id' => 'required'
        ]);

        if ($validator->fails()) {
            // get the error messages from the validator
            $messages = $validator->messages();
            // redirect our user back to the form with the errors from the validator
            return response()->json(['errors' => $messages], 422);
        }

        $client = Client::make($request->all());
        $client->department()->associate($request->input('department_id'));
        $client->save();
        return response()->json($client);
    }
    
    public function destroy (Request $request, $id) {
        $client = Client::findOrFail($id);
        if ($client->deleted_at) return response()->json(["The client cannot be deleted"], 422);
        $client->deleted_at = date('Y-m-d H:i:s');
        $client->save();
        return response()->json("Client has been deleted", 204);
    }
}
