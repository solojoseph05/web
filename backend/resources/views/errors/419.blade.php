{{-- Página de error 419: Token CSRF o sesión expirada --}}
@extends('errors.layout')

@section('code', '419')
@section('title', 'Tu Sesión Expiró')
@section('message', 'Por seguridad, el formulario permaneció inactivo demasiado tiempo y su token de verificación caducó.')
@section('hint', 'Recarga la página e intenta enviar tus datos nuevamente.')
@section('actions')
    <a href="{{ url('/admin/login') }}" class="rounded-xl bg-[#161e2e] hover:bg-slate-800 border border-slate-700 text-slate-100 text-sm font-bold px-5 py-3 transition">
        Iniciar Sesión de Nuevo
    </a>
@endsection
