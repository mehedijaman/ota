@extends('site-layout')

@section('meta-title', 'Triptonic:Online Travel Agency')

@section('meta-description', 'Triptonic:Online Travel Agency')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content') 
<x-hero></x-hero>

<x-exclusive-offers></x-exclusive-offers>

<x-explore-bangladesh></x-explore-bangladesh>

<x-top-airlines></x-top-airlines>

<x-popular-destinations></x-popular-destinations>

<x-best-hotels></x-best-hotels>

<x-tour-packages></x-tour-packages>

<x-business-cta></x-business-cta>

<x-app-download></x-app-download>

@endsection
