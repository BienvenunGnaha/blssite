<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="ga-site-verification" content="LbMvlsO8jVCmMFmqSqA_Pg7J" />
    <title>{{ $general->sitename(__($page_title) ?? '') }}</title>
    <link rel="shortcut icon" type="image/png"
          href="{{ get_image(config('constants.logoIcon.path') .'/favicon.png') }}"/>
    @stack('style-lib')
    <link rel="stylesheet" href="{{asset(activeTemplate(true) .'users/css/dashboard.min.css')}}">
    <link rel="stylesheet" href="{{asset(activeTemplate(true) .'users/css/custom.css')}}">
    @stack('style')
    @stack('css')
    <link rel="stylesheet"
          href='{{ asset(activeTemplate(true) . "users/css/color.php?color=$general->bclr&color2=$general->sclr")}}'>
</head>
<body>
@yield('panel')

<script src="{{asset(activeTemplate(true) .'users/js/dashboard.min.js')}}"></script>
<script src="{{asset(activeTemplate(true) .'users/js/main.js')}}"></script>
<script src="https://js.stripe.com/v3/"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

@stack('script-lib')

<!-- Load toast -->
@include('partials.notify')

<script src="{{asset(activeTemplate(true) .'users/js/nicEdit.js')}}"></script>
{{-- LOAD NIC EDIT --}}
<script type="text/javascript">
    bkLib.onDomLoaded(function () {
        $(".nicEdit").each(function (index) {
            $(this).attr("id", "nicEditor" + index);
            new nicEditor({fullPanel: true}).panelInstance('nicEditor' + index, {hasPanel: true});
        });
    });
</script>

<script>$('[data-toggle=tooltip]').tooltip();</script>
@stack('script')
@stack('js')
</body>
</html>
