<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportRequest;
use Illuminate\Http\Request;

class SupportTicketsController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportRequest::with('user')
            ->orderBy('created_at', 'desc');

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 15;
        $requests = $query->paginate($perPage)->withQueryString();

        return view('admin.support-tickets.index', compact('requests', 'perPage'));
    }

    public function show(SupportRequest $support_request)
    {
        $support_request->load('user');
        return view('admin.support-tickets.show', compact('support_request'));
    }
}
