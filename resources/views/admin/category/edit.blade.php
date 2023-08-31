@extends('admin.master')
@section('content')
    <section class="main-content">
        <div class="row">
            <div class="col-sm-12">
                @include('admin.flash-message')
                <div class="card">
                    <div class="card-header card-default">
                        {{ Route::currentRouteName() == 'business-category.edit' ? 'Business Category' : 'Promote Category' }}
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ Route::currentRouteName() == 'business-category.edit' ? route('business-category.update',['business_category' => $record->slug ]) : route('promote-category.update',['promote_category' => $record->slug]) }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="PUT">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input type="text" required value="{{ $record->title }}" name="title" class="form-control">
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
