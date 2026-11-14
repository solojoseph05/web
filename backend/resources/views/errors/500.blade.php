{{-- Página de error 500: Error interno del servidor --}}
@extends('errors.layout')

@section('code', '500')
@section('title', 'Error Interno del Servidor')
@section('message', 'Ocurrió un inconveniente inesperado al procesar tu solicitud. Nuestro equipo técnico ya fue notificado.')
@section('hint', 'Intenta nuevamente en unos momentos o escríbenos a soporte@pollofelizdgo.com.')
