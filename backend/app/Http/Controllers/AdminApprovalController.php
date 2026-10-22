<?php

namespace App\Http\Controllers;

use App\Models\AdminApprovalRequest;
use Illuminate\Http\Request;

class AdminApprovalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        $allowedStatuses = ['pending', 'approved', 'rejected'];
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'pending';
        }

        $requests = AdminApprovalRequest::query()
            ->with(['adminUser:id,name,role', 'reviewer:id,name,role'])
            ->where('status', $status)
            ->latest('id')
            ->limit(50)
            ->get();

        return response()->json([
            'requests' => $requests,
        ]);
    }

    public function approve(Request $request, AdminApprovalRequest $approvalRequest)
    {
        if ($approvalRequest->status !== 'pending') {
            return response()->json([
                'message' => 'La solicitud ya fue revisada.',
            ], 422);
        }

        $approvalRequest->status = 'approved';
        $approvalRequest->reviewed_by = $request->attributes->get('adminUser')?->id;
        $approvalRequest->review_notes = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ])['notes'] ?? null;
        $approvalRequest->reviewed_at = now();
        $approvalRequest->save();

        return response()->json([
            'message' => 'Solicitud aprobada.',
            'request' => $approvalRequest->fresh(['adminUser:id,name,role', 'reviewer:id,name,role']),
        ]);
    }

    public function reject(Request $request, AdminApprovalRequest $approvalRequest)
    {
        if ($approvalRequest->status !== 'pending') {
            return response()->json([
                'message' => 'La solicitud ya fue revisada.',
            ], 422);
        }

        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $approvalRequest->status = 'rejected';
        $approvalRequest->reviewed_by = $request->attributes->get('adminUser')?->id;
        $approvalRequest->review_notes = $data['notes'] ?? null;
        $approvalRequest->reviewed_at = now();
        $approvalRequest->save();

        return response()->json([
            'message' => 'Solicitud rechazada.',
            'request' => $approvalRequest->fresh(['adminUser:id,name,role', 'reviewer:id,name,role']),
        ]);
    }
}
