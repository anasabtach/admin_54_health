@if ($errors->any())
    <div class="alert bg-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
        </button>
        @foreach ($errors->all() as $error)
            <p><strong>Error!</strong> {{ $error }}</p>
        @endforeach
    </div>
@endif
@if( Session::has('warning') )
<div class="alert bg-warning alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
    </button>
    <strong>Warning!</strong> {{ Session::get('warning') }}
</div>
@endif
@if( Session::has('error') )
<div class="alert bg-danger alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
    </button>
    <strong>Error!</strong> {{ Session::get('error') }}
</div>
@endif
@if( Session::has('info') )
<div class="alert bg-info alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
    </button>
    <strong>Information!</strong> {{ Session::get('info') }}
</div>
@endif
@if( Session::has('success') )
<div class="alert bg-success alert-dismissible margin-b-0" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
    </button>
    <strong>Success!</strong> {{ Session::get('success') }}
</div>
@endif
