@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Page not found'))
@section('detail', __("The page you're looking for doesn't exist or may have moved."))
