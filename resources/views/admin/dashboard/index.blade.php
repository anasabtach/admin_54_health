@extends('admin.master')
@section('content')
    @push('stylesheets')
        <link href="{{ asset('admin/assets/lib/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet" />
        <link href="{{ asset('admin/assets/lib/chart-c3/c3.min.css') }}" rel="stylesheet">
        <link href="{{ asset('admin/assets/lib/chartjs/chartjs-sass-default.css') }}" rel="stylesheet">
    @endpush
    <section class="main-content">
        @include('admin.flash-message')
        <div id="small_widget" class="row"></div>
        <div id="line_chart_container" class="row"></div>
        @include('admin.footer')
    </section>
    @push('scripts')
        <script src="{{ asset('admin/assets/lib/chartjs/chart.min.js') }}"></script>
        <script src="{{ asset('admin/assets/lib/chartjs/chartjs-sass.js') }}"></script>
        <script src="{{ asset('admin/assets/lib/chart-c3/c3.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/dashboard.js') }}"></script>
    @endpush
@endsection
