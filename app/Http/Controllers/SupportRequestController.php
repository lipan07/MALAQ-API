<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupportRequest;
use App\Models\SupportRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportRequestController extends Controller
{
    public function store(StoreSupportRequest $request): JsonResponse
    {
        $supportRequest = SupportRequest::create([
            'user_id' => auth()->id(),
            'issue'   => $request->issue,
            'message' => $request->message,
        ]);

        return response()->json([
            'message' => 'Support request submitted successfully.',
            'data'    => $supportRequest,
        ], 201);
    }
}
