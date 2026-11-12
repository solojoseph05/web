<?php

namespace App\Http\Controllers;

use App\Models\AdminChangeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminChangeLogController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'action' => ['nullable', 'string', 'max:120'],
            'admin_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:50'],
        ]);

        $query = AdminChangeLog::query()
            ->with('adminUser:id,name,email,role')
            ->orderByDesc('created_at');

        if (!empty($data['action'])) {
            $query->where('action', $data['action']);
        }

        if (!empty($data['admin_user_id'])) {
            $query->where('admin_user_id', $data['admin_user_id']);
        }

        if (!empty($data['date_from'])) {
            $query->whereDate('created_at', '>=', $data['date_from']);
        }

        if (!empty($data['date_to'])) {
            $query->whereDate('created_at', '<=', $data['date_to']);
        }

        if (!empty($data['search'])) {
            $search = mb_strtolower($data['search']);

            $query->where(function ($builder) use ($search) {
                $builder
                    ->whereRaw('LOWER(action) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(target_type) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }

        $perPage = $data['per_page'] ?? 10;
        return response()->json($query->paginate($perPage));
    }

    public function export(Request $request)
    {
        $data = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'action' => ['nullable', 'string', 'max:120'],
            'admin_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $query = AdminChangeLog::query()
            ->with('adminUser:id,name,email,role')
            ->orderByDesc('created_at');

        if (!empty($data['action'])) {
            $query->where('action', $data['action']);
        }

        if (!empty($data['admin_user_id'])) {
            $query->where('admin_user_id', $data['admin_user_id']);
        }

        if (!empty($data['date_from'])) {
            $query->whereDate('created_at', '>=', $data['date_from']);
        }

        if (!empty($data['date_to'])) {
            $query->whereDate('created_at', '<=', $data['date_to']);
        }

        if (!empty($data['search'])) {
            $search = mb_strtolower($data['search']);

            $query->where(function ($builder) use ($search) {
                $builder
                    ->whereRaw('LOWER(action) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(target_type) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['Fecha', 'Acción', 'Tipo', 'Destino', 'Administrador', 'Descripción']);

        foreach ($query->cursor() as $log) {
            fputcsv($handle, [
                $log->created_at?->toDateTimeString(),
                $log->action,
                $log->target_type,
                $log->target_id,
                $log->adminUser ? Str::limit($log->adminUser->name, 80) : '-',
                $log->description,
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="admin-change-history.csv"',
        ]);
    }

    public function revertLastChange(Request $request)
    {
        $lastChange = AdminChangeLog::query()
            ->orderByDesc('created_at')
            ->first();

        if (!$lastChange) {
            return response()->json([
                'error' => 'No hay cambios para deshacer.',
            ], 404);
        }

        if (!in_array($lastChange->action, ['create', 'update', 'delete'], true)) {
            return response()->json([
                'error' => 'Este tipo de cambio no se puede deshacer.',
            ], 400);
        }

        $previousChange = AdminChangeLog::query()
            ->where('target_type', $lastChange->target_type)
            ->where('target_id', $lastChange->target_id)
            ->where('created_at', '<', $lastChange->created_at)
            ->orderByDesc('created_at')
            ->first();

        $revertData = [];
        if ($previousChange && $previousChange->changes) {
            $revertData = $previousChange->changes;
        }

        return response()->json([
            'message' => 'Cambio revertido correctamente.',
            'last_change' => $lastChange,
            'revert_data' => $revertData,
        ]);
    }
}
