{{-- Página de error 403: Acceso denegado por control de roles (RBAC) --}}
@extends('errors.layout')

@section('code', '403')
@section('title', 'Acceso Denegado')
@section('message', 'Tu usuario no cuenta con los permisos necesarios para realizar esta acción o visualizar este módulo.')
@section('hint', 'Si necesitas acceso adicional, solicita autorización al Super Administrador.')
@section('actions')
    <a href="{{ url('/admin/login') }}" class="rounded-xl bg-[#161e2e] hover:bg-slate-800 border border-slate-700 text-slate-100 text-sm font-bold px-5 py-3 transition">
        Cambiar de Cuenta
    </a>
@endsection
