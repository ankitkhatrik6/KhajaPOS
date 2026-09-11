@extends('layouts.app')

@section('title', 'Restaurant Settings')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Restaurant Settings</h1>
        <p class="text-sm text-slate-500">Configure business info, PAN / VAT details, billing footer, and tax rates for Nepal.</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Restaurant Name *</label>
                    <input type="text" name="restaurant_name" value="{{ old('restaurant_name', $settings['restaurant_name'] ?? '') }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Address *</label>
                    <input type="text" name="address" value="{{ old('address', $settings['address'] ?? '') }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Phone Number *</label>
                    <input type="text" name="phone" value="{{ old('phone', $settings['phone'] ?? '') }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $settings['email'] ?? '') }}"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">PAN Number</label>
                    <input type="text" name="pan_number" value="{{ old('pan_number', $settings['pan_number'] ?? '') }}"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">VAT Status / Number</label>
                    <input type="text" name="vat_number" value="{{ old('vat_number', $settings['vat_number'] ?? '') }}"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Currency</label>
                    <input type="text" readonly value="NPR (Nepalese Rupee - Rs.)"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-200 bg-slate-100 text-sm font-bold text-slate-700">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">VAT Tax Percentage (%) *</label>
                    <input type="number" step="0.01" min="0" max="100" name="tax_percentage" value="{{ old('tax_percentage', $settings['tax_percentage'] ?? '13.00') }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm font-mono font-bold text-emerald-700 focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Invoice / Receipt Footer Message</label>
                    <textarea name="invoice_footer" rows="2" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">{{ old('invoice_footer', $settings['invoice_footer'] ?? '') }}</textarea>
                    <span class="text-[11px] text-slate-400">Printed at the bottom of customer receipts.</span>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end">
                <button type="submit" class="px-6 py-2.5 text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg shadow transition">
                    Save Restaurant Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
