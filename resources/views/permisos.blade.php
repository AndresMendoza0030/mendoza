@extends('layouts.template')


@section('styles')
	  <link href="{{ asset('assets/css/components/tabs-accordian/custom-tabs.css') }}" rel="stylesheet" type="text/css" />
@endsection


@section('content')

     @livewire('permisos-controller')

@endsection