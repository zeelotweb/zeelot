@extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Your session expired'))
@section('detail', __('Please refresh the page and try again.'))
