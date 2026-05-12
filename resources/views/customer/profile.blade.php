@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <section class="max-w-8xl mx-auto px-4 md:px-8 py-8" style="background: var(--color-bg); min-height: 60vh;">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            @include('customer.partials.nav')

            <div class="lg:col-span-3">
                <div class="card p-6">
                    <h1 class="text-xl font-bold mb-6" style="color: var(--color-text);">My Profile</h1>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @php
                            $fields = [
                                ['label' => 'Full Name', 'value' => $user->name],
                                ['label' => 'Email Address', 'value' => $user->email ?: 'Not provided'],
                                ['label' => 'Phone Number', 'value' => $user->phone ?: 'Not provided'],
                                ['label' => 'Referral Code', 'value' => $user->referral_code ?: 'Not generated'],
                            ];
                        @endphp

                        @foreach ($fields as $field)
                            <div class="p-4 rounded-xl"
                                style="background: var(--color-bg-soft); border: 1px solid var(--color-border);">
                                <p class="text-xs font-bold uppercase tracking-widest mb-1"
                                    style="color: var(--color-text-muted);">
                                    {{ $field['label'] }}
                                </p>
                                <p class="text-base font-semibold" style="color: var(--color-text);">
                                    {{ $field['value'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
