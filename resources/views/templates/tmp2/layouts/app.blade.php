@extends(activeTemplate().'layouts.user-master')

@section('panel')
<div class="main-container">
    <div class="container-fluid main-body-wrapper">
        @include(activeTemplate().'partials.sidenav')
        <div class="main-panel-wrapper">
          @include(activeTemplate().'partials.topnav')
          @include(activeTemplate().'partials.breadcrumb')
          <div class="content-wrapper">
            <div class="container-fluid p-0">
              @yield('content')
            </div>
          </div>
          <footer class="footer"></footer>
      </div>
  </div>
</div>
@endsection