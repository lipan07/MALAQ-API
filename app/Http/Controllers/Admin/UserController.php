<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query();

        // Advanced filter: status
        if ($request->filled('status')) {
            $users->where('status', $request->status);
        }

        // Advanced filter: search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $users->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $users = $users->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.users.index', compact('users'));
    }
}
