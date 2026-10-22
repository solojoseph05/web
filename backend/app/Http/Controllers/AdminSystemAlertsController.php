<?php

namespace App\Http\Controllers;

use App\Models\AdminApprovalRequest;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class AdminSystemAlertsController extends Controller
{
    public function index(Request $request)
    {
        $pendingApprovals = AdminApprovalRequest::query()->where('status', 'pending')->count();
        $pendingMessages = ContactMessage::query()->where('status', 'pending')->count();

        $recentApprovals = AdminApprovalRequest::query()
            ->with('adminUser:id,name,role')
            ->where('status', 'pending')
            ->latest('id')
            ->limit(5)
            ->get();

        return response()->json([
            'pending_approvals' => $pendingApprovals,
            'pending_messages' => $pendingMessages,
            'recent_pending_approvals' => $recentApprovals,
            'alerts' => [
                [
                    'type' => 'approval',
                    'level' => $pendingApprovals > 0 ? 'warning' : 'ok',
                    'message' => $pendingApprovals > 0
                        ? "Hay {$pendingApprovals} cambio(s) pendiente(s) de revisión."
                        : 'No hay cambios pendientes de revisión.',
                ],
                [
                    'type' => 'messages',
                    'level' => $pendingMessages >= 10 ? 'warning' : 'ok',
                    'message' => $pendingMessages >= 10
                        ? "Hay {$pendingMessages} mensajes pendientes de respuesta."
                        : "Mensajes pendientes: {$pendingMessages}.",
                ],
            ],
            'generated_at' => now()->toISOString(),
        ]);
    }
}
