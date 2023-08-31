<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta rel="icon" type="image/png" sizes="16x16" content="{{ appSetting('application_setting','favicon') }}">
    <title>{{ appSetting('application_setting','application_name') }} - Admin Panel</title>
    <link href="{{ asset('admin/assets/lib/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/assets/scss/style.css') }}" rel="stylesheet">
    <style type="text/css">
        html,body{
            height: 100%;
        }
    </style>
</head>
<body class="bg-light">
<div class="misc-wrapper">
    <div class="misc-content">
        <div class="container">
            <div class="row justify-content-center">
                @yield('content')
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('admin/assets/lib/jquery/dist/jquery.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>
<script src="{{ asset('admin/assets/lib/bootstrap/js/bootstrap.min.js') }}"></script>
</body>
</html>
