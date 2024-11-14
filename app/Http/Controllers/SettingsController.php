<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SettingsController extends Controller
{
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        $user = Auth::user();

        // Check if the current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 403);
        }

        // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password successfully updated']);
    }

    // Log out from all devices
    public function logoutAllDevices(Request $request)
    {
        $user = Auth::user();
        $user->tokens->each(function ($token, $key) {
            $token->delete();  // This deletes the token and effectively logs out the user from all devices
        });

        return response()->json(['message' => 'Logged out from all devices successfully.']);
    }

    // Delete account permanently
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();
        $user->delete();  // This will delete the user and all related data if cascading is properly set up

        return response()->json(['message' => 'Account deleted successfully.']);
    }
}
