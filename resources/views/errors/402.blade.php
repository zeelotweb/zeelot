@extends('errors::minimal')

@section('title', __('Payment Required'))
@section('code', '402')
@section('message', __('Payment required'))
@section('detail', __('This resource requires payment before you can access it.'))
