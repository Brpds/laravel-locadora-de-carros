@extends('layouts.app')

@section('content')

    <login-component
        csrf_token = "{{ @csrf_token() }}"
        {{-- xyz = "Propriedade 1"
        abc = "Propriedade 2"
        numero-parcelas = 12 --}}
    ></login-component>
    
@endsection
