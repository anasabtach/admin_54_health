@extends('admin.master')
@section('content')
    <section class="main-content">
        <div class="row">
            <div class="col-sm-12">
                @include('admin.flash-message')
                <div class="card">
                    <div class="card-header card-default">
                       {{ Route::currentRouteName() == 'business-category.create' ? 'Business Category' : 'Promote Category' }}
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ Route::currentRouteName() == 'business-category.create' ? route('business-category.store') : route('promote-category.store') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input required type="text" value="{{ old('title') }}" name="title" class="form-control">
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
@endsection
