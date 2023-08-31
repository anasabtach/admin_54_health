@extends('admin.master')
@section('content')
    <section class="main-content">
        <div class="row">
            <div class="col-sm-12">
                @include('admin.flash-message')
                <div class="card">
                    <div class="card-header card-default">
                        Edit Faq
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('faq.update',['faq' => $record->slug]) }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="PUT">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Question</label>
                                        <input required name="question" class="form-control" value="{!! $record->question !!}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Answer</label>
                                        <textarea style="height: 200px;" required name="answer">
                                            {!! $record->answer !!}
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
@endsection
