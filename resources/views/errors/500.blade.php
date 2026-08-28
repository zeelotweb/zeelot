@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Something went wrong'))
@section('detail', __("It's not you, it's us. We've logged the issue and are looking into it."))
