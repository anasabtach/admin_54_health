@extends('admin.master')
@section('content')
    <section class="main-content">
        <div class="row">
            <div class="col-sm-12">
                @include('admin.flash-message')
                <div class="card">
                    <div class="card-header card-default">
                       Add User
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('app-users.new-user') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="action" value="business_registration">
                            <div class="login-text-box">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form__group">
                                            <label for="business_name" class="form__label">Business Name</label>
                                            <input type="text" value="{{ old('business_name') }}" required id="business_name" name="business_name" class="form-control" placeholder="Business Name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form__group">
                                            <label for="about" class="form__label">About</label>
                                            <input type="text" required id="about" value="{{ old('about') }}" name="about" class="form-control" placeholder="Tell us about your business">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form__group">
                                            <label for="product_service" class="form__label">Product or Service Provided</label>
                                            <input type="text" required id="product_service" value="{{ old('product_service') }}" name="product_service" class="form-control" placeholder="What product service do you offer?">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form__group">
                                            <label for="open_time" class="form__label">Open</label>
                                            <input type="time" required id="open_time" value="{{ old('open_time') }}" name="open_time" class="form-control" placeholder="Open Time">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form__group">
                                            <label for="close_time" class="form__label">Close</label>
                                            <input type="time" required id="close_time" value="{{ old('close_time') }}" name="close_time" class="form-control" placeholder="Close Time">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form__group">
                                            <label for="address" class="form__label">Address</label>
                                            <input type="text" required id="address" value="{{ old('address') }}" name="address" class="form-control" placeholder="Address">
                                            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form__group">
                                            <label for="state" class="form__label">State</label>
                                            <input type="text" readonly required id="state" value="{{ old('state') }}" name="state" class="form-control" placeholder="State">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form__group">
                                            <label for="city" class="form__label">City</label>
                                            <input type="text" readonly required id="city" value="{{ old('city') }}" name="city" class="form-control" placeholder="City">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form__group">
                                            <select id="promote" required name="promote_category_id" class="form-control" placeholder="We Promote">
                                                <option value="">-- We Promote --</option>
                                                @if( !empty($promote_categories) )
                                                    @foreach( $promote_categories as $promote_categorie )
                                                        <option {{ old('promote_category_id') == $promote_categorie->id ? 'selected' : '' }} value="{{ $promote_categorie->id }}">{{ $promote_categorie->title }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form__group">
                                            <select id="web_based_service" required class="form-control" placeholder="Web Based Service">
                                                <option value=""> -- Is this a web-based service? -- </option>
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div id="website_div" class="row" style="display:none;">
                                    <div class="col-md-12">
                                        <div class="form__group">
                                            <label for="business_Site" class="form__label">Website</label>
                                            <input id="business_Site" data-toggle="tooltip" data-placement="top" title="https://www.google.com" name="site_url" value="{{ old('site_url') }}" class="form-control" placeholder="Add Business Website">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form__group">
                                            <label for="workEmail" class="form__label">Work Email Address</label>
                                            <input type="email" required id="workEmail" value="{{ old('email') }}" name="email" class="form-control" placeholder="Tell us about your business">
                                        </div>
                                    </div>
                                      <div class="col-md-6">
                                        <div class="form__group">
                                            <label for="loginPassword" class="form__label">Password</label>
                                            <input type="password" required id="loginPassword" name="password" class="form-control" placeholder="Password">
                                                {{-- <div class="form-icon">
                                                    <i class="fa-solid hide-icon toggle-password fa-eye-slash" toggle="#loginPassword"></i>
                                                </div> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                    
                                    <div class="col-md-6">
                                        <div class="form__group">
                                            <label for="confirmPassword" class="form__label">Confirm Password</label>
                                            <input type="password" required id="confirmPassword" name="confirm_password" class="form-control" placeholder="Confirm Password">
                                            {{-- <div class="form-icon">
                                                <i class="fa-solid hide-icon toggle-password fa-eye-slash" toggle="#confirmPassword"></i>
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center">
                                    <div class="col-md-12 mt03">
                                        <div class="signup-button text-end float-right">
                                            <button type="submit" class="btn btn-primary" id="signButton">
                                                Join Our Community
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.footer')
    </section>
@endsection
@push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_API_KEY') }}&libraries=places" defer></script>
    
    <script>
        var googleAutoComplete = (addressField='address') => {
            const address = document.getElementById(addressField);
            
            const autocomplete = new google.maps.places.Autocomplete(address);

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                var lat = place.geometry.location.lat();
                var long = place.geometry.location.lng();
                $('input[name="longitude"]').val(long);
                $('input[name="latitude"]').val(lat);
                if ( place.address_components ) {
                    for( var index in place.address_components ){
                        if( place.address_components[index].types[0] == 'administrative_area_level_1' ){
                            $('input[name="state"]').val(place.address_components[index].short_name)
                        }
                        if( place.address_components[index].types[0] == 'locality' ){
                            $('input[name="city"]').val(place.address_components[index].short_name)
                        }
                        if( place.address_components[index].types[0] == 'postal_code' ){
                            $('input[name="zipcode"]').val(place.address_components[index].short_name)
                        }
                    }
                }
            });
        }

        $( document ).ready(function() {
            $('#_upload_image').click( function(){
                $('#image_url').click();
            })
            $('#image_url').change( function(event){
                var output = document.getElementById('_upload_image');
                output.src = URL.createObjectURL(event.target.files[0]);
            })
            $('#web_based_service').on( 'change', function(){
                if( $(this).val() == 1 ){
                    $('#website_div').show();
                } else {
                    $('#website_div').hide();
                }
            })
            $('form').submit( function(){
                $('button').attr('disabled','disabled');
                $('input[type="submit"]').attr('disabled','disabled');
                loaderBar()
            })
            googleAutoComplete();
        });
         $('[data-toggle="tooltip"]').tooltip()
    </script>
@endpush