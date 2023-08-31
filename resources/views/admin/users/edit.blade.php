@extends('admin.master')
@section('content')
    <section class="main-content">
        <div class="row">
            <div class="col-sm-12">
                @include('admin.flash-message')
                <div class="card">
                    <div class="card-header card-default">
                        Edit User
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('app-users.update',['app_user' => $record->slug]) }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="PUT">
                            <div class="row">
                                @if( $record->account_approved == '0' )
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Account Request</label>
                                            <select name="account_approved" class="form-control">
                                                <option value="1" {{ $record->account_approved == '1' ? 'selected' : '' }}>Approved</option>
                                                <option value="0" {{ $record->account_approved == '0' ? 'selected' : '' }}>Pending</option>
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                           <option value="1" {{ $record->status == 1 ? 'selected' : '' }}>Active</option>
                                           <option value="0" {{ $record->status == 0 ? 'selected' : '' }}>Disabled</option>
                                        </select>
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
