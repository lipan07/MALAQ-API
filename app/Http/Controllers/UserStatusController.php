<?php

namespace App\Http\Controllers;

use App\Events\UserStatusChanged;
use Illuminate\Http\Request;

class UserStatusController extends Controller
{
    public function storeOnlineStatus(Request $request)
    {
        $user = auth()->user();
        $user->update([
            'status' => 'online',
            'last_activity' => now()
        ]);

        broadcast(new UserStatusChanged($user->id, 'online',  $user->last_activity));

        return response()->json(['status' => 'online']);
    }

    public function storeOfflineStatus(Request $request)
    {
        $user = auth()->user();
        $user->update([
            'status' => 'offline',
            'last_activity' => now()
        ]);

        broadcast(new UserStatusChanged($user->id, 'offline', $user->last_activity));

        return response()->json(['status' => 'offline']);
    }
}
