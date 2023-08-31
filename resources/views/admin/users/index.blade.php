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
                               User Listing
                            </div>
                            
                            <div class="col-md-6">
                                {{-- {{ Route::currentRouteName() == 'business-category.create' ? route('business-category.store') : route('promote-category.store') }} --}}
                                @if(Route::currentRouteName() == 'app-users.index')
                                    <a class="btn btn-info pull-right" href="{{ route('app-users.create') }}">Add User</a>
                                @else 
                                    <a class="btn btn-info pull-right" href="{{ route('vendor.add-business-account') }}">Add Business Account</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="_ajax_datatable" class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile No</th>
                                    
                                    <th>Email Verify</th>
                                    <th>Account Approved</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="8">
                                        <div class="row">
                                            <div class="col-md-6"></div>
                                            <div class="col-md-2"></div>
                                            <div class="col-md-4 pull-right">
                                                <form id="search_form" method="GET">
                                                    <div class="input-group">
                                                        <input type="text" name="keyword" class="form-control" placeholder="Search">
                                                        <span class="input-group-btn">
                                                        <button type="submit" class="btn btn-default">
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
            let ajax_listing_url  = `{{ Route::currentRouteName() == 'app-users.index' ? route('app-users.ajax-listing') :  route('vendor.ajax-listing') }}`;
        </script>
        <script src="{{ asset('admin/assets/lib/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('admin/assets/lib/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('admin/assets/js/datatable-ajax.js') }}"></script>
    @endpush
@endsection
