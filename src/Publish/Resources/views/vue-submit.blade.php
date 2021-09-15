{{-- resources/views/admin/dashboard.blade.php --}}

@extends('layouts.vue')

@section('title', 'Vue Components')

@section('content_header')
    <h1>Vue Components</h1>
@stop

@section('content')
    <card title="Request">
        <?= var_dump( $request->all()); ?>
        <br><br>
        <button-back route="{{ route("vue") }}"></button-back>
   </card>
@stop

@section('css')
    <!--<link rel="stylesheet" href="/css/admin_custom.css">-->
@stop

@section('js')

@stop
