@extends('errors::minimal')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __('Sign in required'))
@section('detail', __('You need to be signed in to view this page.'))
