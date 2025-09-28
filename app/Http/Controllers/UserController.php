<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'phone_no' => 'required|string|max:15',
            'about_me' => 'nullable|string|max:500',
            'profile_image' => 'nullable|file|image|max:2048',

            // Company details validation
            'company_detail.name' => 'nullable|string|max:255',
            'company_detail.type' => 'nullable|string|max:255',
            'company_detail.address' => 'nullable|string|max:500',
            'company_detail.website' => 'nullable|url|max:255',

            // Contact person validation
            'company_detail.contact_person_name' => 'nullable|string|max:255',
            'company_detail.contact_person_role' => 'nullable|string|max:255',
            'company_detail.contact_person_email' => 'nullable|email|max:255',
            'company_detail.contact_person_phone' => 'nullable|string|max:20',
        ]);

        // Update user details
        $user->update([
            'name' => $request->input('first_name') . ', ' . $request->input('last_name'),
            'email' => $request->input('email'),
            'phone_no' => $request->input('phone_no'),
            'about_me' => $request->input('about_me'),
        ]);

        // Update or create company details with contact person information
        $user->companyDetail()->updateOrCreate(
            ['users_id' => $user->id],
            [
                'name' => $request->input('company_detail.name'),
                'type' => $request->input('company_detail.type'),
                'address' => $request->input('company_detail.address'),
                'website' => $request->input('company_detail.website'),
                'contact_person_name' => $request->input('company_detail.contact_person_name'),
                'contact_person_role' => $request->input('company_detail.contact_person_role'),
                'contact_person_email' => $request->input('company_detail.contact_person_email'),
                'contact_person_phone' => $request->input('company_detail.contact_person_phone'),
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
     * Get the profile details of the authenticated user.
     */
    public function sellerInfo(Request $request, User $user)
    {
        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated',
            ], 401);
        }

        // Load related data: company details and profile image
        $user->load('companyDetail', 'images');

        $authUser = Auth::user();
        $user->isFollowing = false;
        if ($authUser) {
            $user->isFollowing = $authUser->following()->where('following_id', $user->id)->exists();
        }

        // Count active and sold posts
        $user->activePostCount = $user->posts()->where('status', \App\Enums\PostStatus::Active)->count();
        $user->soldPostCount = $user->posts()->where('status', \App\Enums\PostStatus::Sold)->count();

        $user->last_activity = $user->last_activity ? Carbon::parse($user->last_activity)->diffForHumans() : null;

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
