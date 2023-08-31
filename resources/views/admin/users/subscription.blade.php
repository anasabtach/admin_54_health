@extends('admin.master')
@section('content')
    <section class="main-content">
        <div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header card-default">
                        User Subscription

                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4"><b>Package: </b></div>
                            <div class="col-md-8">{{ $userSubscription->package->title }}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><b>Expiry: </b></div>
                            <div class="col-md-8">{{ $userSubscription->expiry_date }}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><b>Amount Charged: </b></div>
                            <div class="col-md-8">{{ $userSubscription->charge_amount }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                @include('admin.flash-message')
                <div class="card">
                    <div class="card-header card-default">
                        Add Months

                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('app-users.subscriptionUpdate') }}">
                            <input type="hidden" name="subscription_id" value="{{ $userSubscription->id }}">
                            <input type="hidden" name="user_slug" value="{{ $user->slug }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-8"><input required type="number" name="months" placeholder="Add month" class="form-control"></div>
                                <div class="col-md-4"><input type="submit" class="btn btn-primary" value="Submit"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.footer')
    </section>
@endsection
