<?php

namespace App\Http\Controllers;

use App\Models\AdminChangeLog;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class AdminContactMessagesController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::query()
            ->orderByDesc('created_at')
            ->get();

        return response()->json($messages);
    }

    public function update(Request $request, ContactMessage $contactMessage)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:pending,attended,discarded'],
            'notes' => ['nullable', 'string'],
        ]);

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

        return response()->json([
            'message' => 'Estado actualizado correctamente.',
            'contact_message' => $contactMessage->fresh(),
        ]);
    }
}
