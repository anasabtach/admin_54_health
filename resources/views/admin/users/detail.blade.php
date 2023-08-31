@extends('admin.master')
@section('content')
    <section class="main-content">
        <div class="row">
            <div class="col-sm-12">
                @include('admin.flash-message')
                <div class="card">
                    <div class="card-header card-default">
                        User Details
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4"><b>Name: </b></div>
                            <div class="col-md-8">{{ $record->name }}</div>
                        </div>

                        <div class="row">
                            <div class="col-md-4"><b>Email: </b></div>
                            <div class="col-md-8">{{ $record->email }}</div>
                        </div>

                        <div class="row">
                            <div class="col-md-4"><b>Mobile No: </b></div>
                            <div class="col-md-8">{{ $record->mobile_no }}</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4"><b>User Type:</b></div>
                            <div class="col-md-8">{{ is_null($record->organization_name) ? 'Regular Member' : 'Organization' }}</div>
                        </div>

                        @if(!is_null($record->organization_name))
                            <div class="row">
                                <div class="col-md-4"><b>Orgnization Name:</b></div>
                                <div class="col-md-8">{{ $record->organization_name }}</div>
                            </div>
                        @endif

                        @if($record->user_group == "User")
                            <div class="row">
                           
                                <div class="col-md-4"><b>Image: </b></div>
                                <div class="col-md-8"><img width="150" src="{{ url('storage/').$record->id_card }}" alt=""></div>
                            </div>

                            <div class="row">
                                <div class="col-md-4"><b>Profession: </b></div>
                                <div class="col-md-8">{{ $record->profession }}</div>
                            </div>

                        @else
                            <div class="row">
                                <div class="col-md-4"><b>City: </b></div>
                                <div class="col-md-8">{{ $record->city }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-4"><b>State: </b></div>
                                <div class="col-md-8">{{ $record->state }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-4"><b>Zipcode: </b></div>
                                <div class="col-md-8">{{ $record->zipcode }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-4"><b>Address: </b></div>
                                <div class="col-md-8">{{ $record->address }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-4"><b>Open Time: </b></div>
                                <div class="col-md-8">{{ $record->open_time }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-4"><b>Close Time: </b></div>
                                <div class="col-md-8">{{ $record->close_time }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-4"><b>About: </b></div>
                                <div class="col-md-8">{{ $record->about }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-4"><b>Product Service: </b></div>
                                <div class="col-md-8">{{ $record->product_service }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-4"><b>Image: </b></div>
                                <div class="col-md-8"><img width="150" src="{{ url('storage/'.$record->id_card) }}" alt=""></div>
                            </div>


                        @endif
                    </div>
                </div>
            </div>
        </div>
        @include('admin.footer')
    </section>
@endsection
