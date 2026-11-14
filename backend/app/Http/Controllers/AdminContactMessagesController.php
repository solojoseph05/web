<?php

namespace App\Http\Controllers;

use App\Models\AdminChangeLog;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controlador administrativo para consultar, actualizar y archivar mensajes de contacto recibidos.
 */
class AdminContactMessagesController extends Controller
{
    /**
     * Lista los mensajes de contacto ordenados del más reciente al más antiguo.
     */
    public function index()
    {
        $messages = ContactMessage::query()
            ->orderByDesc('created_at')
            ->get();

        return response()->json($messages);
    }

    /**
     * Actualiza el estado y notas de seguimiento de un mensaje de contacto dentro de una transacción.
     */
    public function update(Request $request, ContactMessage $contactMessage)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:pending,attended,discarded'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            DB::transaction(function () use ($request, $contactMessage, $data): void {
                $contactMessage->fill($data);
                $contactMessage->save();

                $adminUser = $request->attributes->get('adminUser');
                AdminChangeLog::create([
                    'admin_user_id' => $adminUser?->id,
                    'action' => 'contact_message.update',
                    'target_type' => 'ContactMessage',
                    'target_id' => (string) $contactMessage->id,
                    'description' => 'Se actualizó el estado o notas de un mensaje de contacto.',
                    'changes' => [
                        'status' => $contactMessage->status,
                        'notes' => $contactMessage->notes,
                        'email' => $contactMessage->email,
                    ],
                ]);
            });

            return response()->json([
                'message' => 'Estado actualizado correctamente.',
                'contact_message' => $contactMessage->fresh(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error actualizando mensaje de contacto', [
                'id' => $contactMessage->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'No se pudo actualizar el mensaje en este momento.',
            ], 500);
        }
    }

    /**
     * Elimina un mensaje de contacto de forma segura y registra el evento en auditoría.
     */
    public function destroy(Request $request, ContactMessage $contactMessage)
    {
        try {
            DB::transaction(function () use ($request, $contactMessage): void {
                $snapshot = $contactMessage->toArray();
                $contactMessage->delete();

                $adminUser = $request->attributes->get('adminUser');
                AdminChangeLog::create([
                    'admin_user_id' => $adminUser?->id,
                    'action' => 'contact_message.delete',
                    'target_type' => 'ContactMessage',
                    'target_id' => (string) ($snapshot['id'] ?? ''),
                    'description' => 'Se eliminó un mensaje de contacto.',
                    'changes' => $snapshot,
                ]);
            });

            return response()->json([
                'message' => 'Mensaje eliminado correctamente.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error eliminando mensaje de contacto', [
                'id' => $contactMessage->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'No se pudo eliminar el mensaje en este momento.',
            ], 500);
        }
    }
}
