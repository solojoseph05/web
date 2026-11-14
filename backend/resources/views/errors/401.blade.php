{{-- Página de error 401: Sesión o credenciales requeridas --}}
@extends('errors.layout')

@section('code', '401')
@section('title', 'Autenticación Requerida')
@section('message', 'Necesitas iniciar sesión con una cuenta autorizada para acceder a este recurso administrativo.')
@section('actions')
    <a href="{{ url('/admin/login') }}" class="rounded-xl bg-[#161e2e] hover:bg-slate-800 border border-slate-700 text-slate-100 text-sm font-bold px-5 py-3 transition">
        Ir al Acceso Admin
    </a>
@endsection
