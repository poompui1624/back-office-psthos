@php
    $logoUrl = function_exists('hospital_logo_url') ? hospital_logo_url() : null;

    $hospitalName = function_exists('hospital_name')
        ? hospital_name()
        : config('app.name', 'Hospital Backoffice');

    $hospitalAddress = function_exists('setting')
        ? setting('hospital.address', '')
        : '';

    $hospitalPhone = function_exists('setting')
        ? setting('hospital.phone', '')
        : '';

    $hospitalEmail = function_exists('setting')
        ? setting('hospital.email', '')
        : '';
@endphp

<div class="document-header">
    @if ($logoUrl)
        <div class="document-logo">
            <img src="{{ $logoUrl }}" alt="logo">
        </div>
    @endif

    <div class="document-hospital-name">
        {{ $hospitalName }}
    </div>

    @if ($hospitalAddress)
        <div class="document-hospital-info">
            {{ $hospitalAddress }}
        </div>
    @endif

    <div class="document-hospital-info">
        @if ($hospitalPhone)
            โทร. {{ $hospitalPhone }}
        @endif

        @if ($hospitalPhone && $hospitalEmail)
            |
        @endif

        @if ($hospitalEmail)
            Email: {{ $hospitalEmail }}
        @endif
    </div>
</div>
