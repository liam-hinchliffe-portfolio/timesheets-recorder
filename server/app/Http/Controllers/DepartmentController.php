<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Department;
use \App\Models\Client;
use Validator;

class DepartmentController extends Controller
{
    public function index () {
        return response()->json(Department::whereNull('deleted_at')->with('users', 'clients')->get());
    }

    public function create (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'max:50|required',
        ]);

        if ($validator->fails()) {
            // get the error messages from the validator
            $messages = $validator->messages();
            // redirect our user back to the form with the errors from the validator
            return response()->json(['errors' => $messages], 422);
        }

        $department = Department::create($request->all());
        return response()->json($department);
    }

    public function destroy (Request $request, $id) {
        $department = Department::findOrFail($id);
        if ($department->deleted_at) return response()->json(["The department cannot be deleted"], 422);
        $department->deleted_at = date('Y-m-d H:i:s');
        $department->save();
        return response()->json("Department has been deleted", 204);
    }

    public function addUsers (Request $request, $id) {
        $department = Department::findOrFail($id);
        
        // Detach all users before reattaching the users in the request payload
        $department->users()->detach();

        // Attach each user in the request payload
        foreach ($request->input('users') as $key => $userId) $department->users()->attach($userId);
    }

    public function addClients (Request $request, $id) {
        $department = Department::findOrFail($id);
        
        // Detach all clients from the department
        foreach ($department->clients as $key => $client) {
            $client->department_id = null;
            $client->save();
        }

        // Attach each client in the request payload to the department
        foreach ($request->input('clients') as $key => $clientId) {
            $client = Client::findOrFail($clientId);
            $client->department_id = $department->id;
            $client->save();
        }
    }
}
