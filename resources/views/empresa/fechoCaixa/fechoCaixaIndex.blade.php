@extends('layout.appEmpresa')
@section('title','Fecho de caixa')
@section('content')

<section class="content">
    <Fecho-caixa-component :fechocaixa="{{$fechocaixa}}"></Fecho-caixa-component>
</section>
@endsection