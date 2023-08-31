@extends('admin.master')
@section('content')
    @push('stylesheets')
        <link href="{{ asset('admin/assets/lib/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('admin/assets/lib/datatables/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    @endpush
    <section class="main-content">
        <div class="row">
            <div class="col-md-12">
                @include('admin.flash-message')
                <div class="card">
                    <div class="card-header card-default">
                        <div class="row">
                            <div class="col-md-6">
                                Cms Role Listing
                            </div>
                            <div class="col-md-6">
                                <a class="btn btn-info pull-right" href="{{ route('cms-roles-management.create') }}">Add Cms Role</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="_ajax_datatable" class="table">
                            <thead>
                            <tr>
                                <th><input type="checkbox" class="checked_all"></th>
                                <th>Name</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                            <tr class="table-primary">
                                <td colspan="4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a class="btn btn-default btn-xs bulk_delete">Bulk Delete</a>
                                        </div>
                                        <div class="col-md-2"></div>
                                        <div class="col-md-4 pull-right">
                                            <form id="search_form" method="GET">
                                                <div class="input-group">
                                                    <input type="text" name="keyword" class="form-control" placeholder="Search">
                                                    <span class="input-group-btn">
                                                        <button type="button" class="btn btn-default">
                                                            <i class="fa fa-search" aria-hidden="true"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.footer')
    </section>
    @push('scripts')
        <script>
            let ajax_listing_url = '{!! route("cms-roles-management.ajax-listing") !!}';
        </script>
        <script src="{{ asset('admin/assets/lib/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('admin/assets/lib/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/datatable-ajax.js') }}"></script>
    @endpush
@endsection
