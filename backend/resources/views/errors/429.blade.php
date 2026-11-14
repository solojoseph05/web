{{-- Página de error 429: Límite de peticiones excedido (Rate Limiting) --}}
@extends('errors.layout')

@section('code', '429')
@section('title', 'Demasiadas Solicitudes')
@section('message', 'Detectamos varios envíos consecutivos en un corto periodo. Por favor espera un minuto antes de intentarlo de nuevo.')
@section('hint', 'Esta medida protege nuestros formularios y servicios contra tráfico automatizado.')
