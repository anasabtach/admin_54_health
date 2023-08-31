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
                               User Rating
                            </div>

                        </div>
                    </div>
                    <div class="card-body">
                        <table id="_ajax_datatable" class="table">
                            <thead>
                            <tr>
                                <th>Reviewed By</th>
                                <th>Review</th>
                                <th>Rating</th>
                                <th>Action</th>
                            </tr>

                            </thead>
                            <tbody>
                                @if(count($ratings))
                                    @foreach($ratings as $rating)
                                        <tr>
                                            <td>{{ $rating->name }}</td>
                                            <td>{{ $rating->review }}</td>
                                            <td>{{ $rating->rating }}</td>
                                            <td>
                                                <a style="margin-left:10px" href="{{ route('deleteRating',$rating->id) }}" title="Edit" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.footer')
    </section>

@endsection
