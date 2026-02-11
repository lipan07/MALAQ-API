<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    /** Super admin (and others with reports permission) see all reports. */
    public function index(Request $request)
    {
        $query = Report::with(['reportingUser', 'post.user'])
            ->orderBy('created_at', 'desc');

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 15;
        $reports = $query->paginate($perPage)->withQueryString();

        return view('admin.reports.index', compact('reports', 'perPage'));
    }

    public function show(Report $report)
    {
        $report->load(['reportingUser', 'post.user']);
        return view('admin.reports.show', compact('report'));
    }
}
