<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InviteToken;
use App\Models\Payment;
use App\Models\Post;
use App\Models\Report;
use App\Models\SupportRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isLead = $user->isLead();
        $isSuperAdmin = $user->isSuperAdmin();

        if ($isSuperAdmin) {
            $userCount = User::whereNull('admin_role')->count();
            $postCount = Post::count();
            $tokenCount = InviteToken::count();
            $paymentConfirmed = Payment::where('status', 'confirmed')->count();
            $paymentPending = Payment::where('status', 'pending')->count();
            $supportCount = SupportRequest::count();
            $reportCount = Report::count();
        } elseif ($isLead) {
            $invitedScope = fn ($q) => $q->whereNull('admin_role')->where('joined_via_invite', true);
            $userCount = User::whereNull('admin_role')->where('joined_via_invite', true)->count();
            $postCount = Post::whereHas('user', $invitedScope)->count();
            $tokenCount = InviteToken::whereHas('owner', $invitedScope)->count();
            $paymentConfirmed = Payment::where('status', 'confirmed')->whereHas('user', fn ($q) => $q->where('joined_via_invite', true))->count();
            $paymentPending = Payment::where('status', 'pending')->whereHas('user', fn ($q) => $q->where('joined_via_invite', true))->count();
            $supportCount = SupportRequest::whereHas('user', fn ($q) => $q->where('joined_via_invite', true))->count();
            $reportCount = Report::whereHas('post.user', fn ($q) => $q->where('joined_via_invite', true))->count();
        } else {
            $userCount = User::whereNull('admin_role')->count();
            $postCount = Post::count();
            $tokenCount = InviteToken::count();
            $paymentConfirmed = Payment::where('status', 'confirmed')->count();
            $paymentPending = Payment::where('status', 'pending')->count();
            $supportCount = SupportRequest::count();
            $reportCount = Report::count();
        }

        $stats = [
            'user_count' => $userCount,
            'post_count' => $postCount,
            'token_count' => $tokenCount,
            'payment_confirmed' => $paymentConfirmed,
            'payment_pending' => $paymentPending,
            'support_count' => $supportCount,
            'report_count' => $reportCount,
        ];
        $scopeLabel = $isLead ? 'Invited users' : ($isSuperAdmin ? 'All' : 'All');

        return view('admin.dashboard', compact('stats', 'scopeLabel', 'isLead', 'isSuperAdmin'));
    }

    public function permissionDenied()
    {
        return view('admin.permission-denied');
    }
}
