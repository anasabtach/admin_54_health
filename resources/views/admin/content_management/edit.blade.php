@extends('admin.master')
@section('content')
    @push('stylesheets')
        <link href="{{ asset('admin/assets/lib/summernote/summernote.css') }}" rel="stylesheet">
    @endpush
    <section class="main-content">
        <div class="row">
            <div class="col-sm-12">
                @include('admin.flash-message')
                <div class="card">
                    <div class="card-header card-default">
                        Edit {{ $record->title }}
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('content-management.update',['content_management' => $record->slug]) }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="PUT">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Content</label>
                                        <textarea rows="6" name="content" class="form-control summernote">
                                            {!! $record->content !!}
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.footer')
    </section>
    @push('scripts')
        <script src="{{ asset('admin/assets/lib/summernote/summernote.js') }}"></script>
        <script>
            $(function () {
                $('.summernote').summernote({
                    height:'400px',
                });
            });
        </script>
    @endpush
@endsection
