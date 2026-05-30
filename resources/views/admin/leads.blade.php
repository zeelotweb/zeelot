@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="container mx-auto px-6">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-black text-slate-900">Lead Pipeline</h1>
            <span class="bg-blue-100 text-blue-700 px-4 py-1 rounded-full text-sm font-bold">
                {{ $leads->count() }} Total Enquiries
            </span>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-widest font-bold">
                    <tr>
                        <th class="px-6 py-4">Client</th>
                        <th class="px-6 py-4">Budget</th>
                        <th class="px-6 py-4">Project Goal</th>
                        <th class="px-6 py-4 text-right">Received</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($leads as $lead)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900">{{ $lead->name }}</div>
                            <div class="text-sm text-slate-500">{{ $lead->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="{{ $lead->budget >= 5000 ? 'text-emerald-600' : 'text-slate-600' }} font-black">
                                ${{ number_format($lead->budget) }}+
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate">
                            {{ $lead->message }}
                        </td>
                        <td class="px-6 py-4 text-right text-xs text-slate-400">
                            {{ $lead->created_at->diffForHumans() }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-8 text-center">
            <a href="/" class="text-sm text-slate-400 hover:text-blue-600 transition">← Back to Public Site</a>
        </div>
    </div>
</div>
@endsection
