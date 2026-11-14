{{-- Página de error 404: Ruta o recurso no encontrado --}}
@extends('errors.layout')

@section('code', '404')
@section('title', 'Página No Encontrada')
@section('message', 'La dirección que estás buscando no existe, fue movida o el contenido ya no está disponible.')
@section('hint', 'Puedes regresar a la página principal para consultar nuestro menú, promociones y sucursales en Durango.')
@section('actions')
    <a href="{{ url('/#branches') }}" class="rounded-xl bg-[#161e2e] hover:bg-slate-800 border border-slate-700 text-slate-100 text-sm font-bold px-5 py-3 transition">
        Ver Sucursales
    </a>
@endsection
