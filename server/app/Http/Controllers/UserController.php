<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login() {
        // Verify the user credentials
        if (Auth::guard('web')->attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            // Create Passport access token
            $token = $user->createToken('_token')->accessToken;
            // Create cookie to be returned to frontend
            $cookie = cookie('_token', $token, 525600, null, null, null, true, false, 'strict');
            // Return JSON response with HTTP-only cookie
            return response()->json(['valid' => true, 'token' => $token, 'user' => $user])->cookie($cookie);
        }
        return response()->json("Incorrect credentials", 403);
    }

    // Get the details of the authenticated user
    public function authUser (Request $request) {
        $user = Auth::user();
        $user = User::with('timeRecords', 'departments', 'departments.clients', 'departments.clients.projects')->whereNull('deleted_at')->findOrFail($user->id);
        return response()->json($user);
    }

    public function get(Request $request, $id) {
        $user = User::findOrFail($id);
        return response()->json(['user' => $user]);
    }

    public function index (Request $request) {
        $users = User::whereNull('deleted_at')->get();
        return response(['users' => $users]);
    }

    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name' => 'max:50|required',
            'last_name' => 'max:50|required',
            'email' => 'email|max:100|min:4|required|unique:users,email|email:rfc,dns',
            'password' => 'max:100|min:8|required',
            'confirmPassword' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            // get the error messages from the validator
            $messages = $validator->messages();
            // redirect our user back to the form with the errors from the validator
            return response()->json(['errors' => $messages], 422);
        }

        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'wage' => $request->input('wage') != null ? $request->input('wage') : null,
            'auth_level' => $request->input('auth_level') != null ? $request->input('auth_level') : 1,
            'email' => $request->input('email'),
            'password' =>  Hash::make($request->input('password')),
        ]);

        return response()->json($user);
    }

    public function softDelete (Request $request, $userId) {
        $user = User::findOrFail($userId);
        if ($user->deleted_at) return response()->json(["The user cannot be deleted"], 422);
        $user->deleted_at = date('Y-m-d H:i:s');
        $user->save();
        // Revoke the access token
        $token = $user->token();
        $token->revoke();    
        return response()->json(['user' => $user, "msg" => "The user has been deleted"], 202);    
    }

    public function restore (Request $request, $userId) {
        $user = User::findOrFail($userId);
        $user->deleted_at = null;
        $user->save();
        return response()->json(['msg' => "The user has been restored"], 202);
    }

    public function logout (Request $request) {
        $user = Auth::user();
        if ($user) {
            // Revoke the access token
            $token = $user->token();
            $token->revoke();    
        }
        // Clear the token on the frontend
        $cookie = \Cookie::forget('_token');
        return response()->json("Logging out")->cookie($cookie);
    }
}
