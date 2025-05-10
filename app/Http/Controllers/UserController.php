<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::get();
        return response()->json(['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'data' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'firstName' => 'sometimes|string|max:255',
            'lastName' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'phoneNumber' => 'sometimes|string|max:15',
            'businessName' => 'sometimes|string|max:255',
            'businessType' => 'sometimes|string|max:255',
            'businessAddress' => 'sometimes|string|max:255',
            'profile_image' => 'sometimes|file|image|max:2048',
            'businessWebsite' => 'sometimes|url|max:255',
            'bio' => 'sometimes|string|max:255',
        ]);

        // Update user details
        $user->update([
            'name' => $request->input('firstName') . ', ' . $request->input('lastName'),
            'email' => $request->input('email'),
            'phone_no' => $request->input('phoneNumber'),
            'address' => $request->input('businessAddress'),
            'about_me' => $request->input('bio'),
        ]);

        // Update or create company details
        $user->companyDetail()->updateOrCreate(
            ['users_id' => $user->id],
            [
                'name' => $request->input('businessName'),
                'type' => $request->input('businessType'),
                'address' => $request->input('businessAddress'),
                'website' => $request->input('businessWebsite'),
            ]
        );

        // Handle profile image
        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');

            $user->images()->updateOrCreate(
                ['imageable_id' => $user->id, 'imageable_type' => User::class],
                ['url' => config('app.url') . Storage::url($imagePath)]
            );
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->load('companyDetail', 'images'),
        ]);
    }

    /**
     * Get the profile details of the authenticated user.
     */
    public function getProfile(Request $request)
    {
        $userId = Auth::id();
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated',
            ], 401);
        }

        // Load related data: company details and profile image
        $user->load('companyDetail', 'images');

        return response()->json([
            'message' => 'Profile details retrieved successfully',
            'data' => $user,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
