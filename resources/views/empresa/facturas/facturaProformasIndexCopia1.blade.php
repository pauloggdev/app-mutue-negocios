@extends('layout.appEmpresa')
@section('title','Listar Facturas Proformas')
@section('content')

<section class="content">
    <Factura-proformas-Index :formapagamentos="{{$formapagamentos}}" :proformas="{{$proformas}}" :guard="{{$guard}}"></Factura-proformas-Index>
</section>
@endsection