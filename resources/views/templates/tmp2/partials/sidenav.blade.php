<nav class="main-sidebar bg-default">
    <button class="sidebar-close"><i class="fa fa-times"></i></button>
    <div class="navbar-brand-wrapper d-flex justify-content-start align-items-center">
        <a href="/" class="navbar-brand">
            <span class="logo-one"><img src="{{ get_image(config('constants.logoIcon.path') .'/logo.png') }}"
                                        alt="logo-image"/></span>
            <span class="logo-two"><img src="{{ get_image(config('constants.logoIcon.path') .'/favicon.png') }}"
                                        alt="logo-image"/></span>
        </a>
    </div>
    <div id="main-sidebar">
        <ul class="nav">
            <li class="nav-item {{ sidenav_active('user.home') }}">
                <a href="{{ route('user.home') }}" class="nav-link">
                    <span class="menu-icon"><i class="fa fa-th-large text-facebook"></i></span>
                    <span class="menu-title">@lang('Dashboard')</span>
                </a>
            </li>
            {{--<li class="nav-item {{ sidenav_active('user.deposit*') }}">
                <a href="#" class="nav-link">
                    <span class="menu-icon"><i class="fa fa-credit-card text-primary"></i></span>
                    <span class="menu-title">@lang('Deposit')</span>
                    <span class="menu-arrow"><i class="fa fa-chevron-right"></i></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ sidenav_active('user.deposit') }}">
                        <a class="nav-link" href="{{ route('user.deposit') }}">
                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                            <span class="menu-title">@lang('Deposit Now')</span>
                        </a>
                    </li>
                    <li class="nav-item {{ sidenav_active('user.deposit.history') }}">
                        <a class="nav-link" href="{{ route('user.deposit.history') }}">
                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                            <span class="menu-title">@lang('Deposit History')</span>
                        </a>
                    </li>
                </ul>
            </li>--}}

            @if(auth()->user()->plan_id == 0)

                <li class="nav-item {{ sidenav_active('user.plan.index') }}">
                    <a href="{{ route('user.plan.index') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-lightbulb-o text-facebook"></i></span>
                        <span class="menu-title">@lang('Subscribe Plan')</span>
                    </a>
                </li>

            @endif

            @if(auth()->user()->plan_id != 0)

                <li class="nav-item {{ sidenav_active('user.pronostics') }}">
                    <a href="{{route('user.pronostics')}}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-lightbulb-o text-facebook"></i></span>
                        <span class="menu-title">@lang('Pronostics')</span>
                    </a>
                </li>

            @endif

            {{--<li class="nav-item {{ sidenav_active('user.withdraw*') }}">
                <a href="#" class="nav-link">
                    <span class="menu-icon"><i class="fa fa-credit-card text-primary"></i></span>
                    <span class="menu-title">@lang('Withdrawal')</span>
                    <span class="menu-arrow"><i class="fa fa-chevron-right"></i></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ sidenav_active('user.withdraw') }}">
                        <a class="nav-link" href="{{ route('user.withdraw') }}">
                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                            <span class="menu-title">@lang('Withdraw Now')</span>
                        </a>
                    </li>
                    <li class="nav-item {{ sidenav_active('user.withdraw.history') }}">
                        <a class="nav-link" href="{{ route('user.withdraw.history') }}">
                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                            <span class="menu-title">@lang('Withdraw History')</span>
                        </a>
                    </li>
                </ul>
            </li>--}}
            {{--<li class="nav-item {{ sidenav_active('user.pin.recharge') }}">
                <a href="{{ route('user.pin.recharge') }}" class="nav-link">
                    <span class="menu-icon"><i class="fa fa-credit-card text-facebook"></i></span>
                    <span class="menu-title">@lang('E-Pin Recharge')</span>
                </a>
            </li>--}}
            {{--<li class="nav-item {{ sidenav_active('user.balance.transfer') }}">
                <a href="{{ route('user.balance.transfer') }}" class="nav-link">
                    <span class="menu-icon"><i class="fa fa-exchange text-facebook"></i></span>
                    <span class="menu-title">@lang('Transfer Balance')</span>
                </a>
            </li>--}}
            
            <li class="nav-item {{ sidenav_active('user.matrix*') }}">
                <a href="{{route('user.matrix.index',['lv_no' => 1])}}" class="nav-link">
                    <span class="menu-icon"><i class="fa fa-sitemap text-facebook"></i></span>
                    <span class="menu-title">@lang('My Matrix')</span>
                </a>
            </li>
            <li class="nav-item {{ sidenav_active('user.ref.index') }}">
                <a href="{{route('user.ref.index')}}" class="nav-link">
                    <span class="menu-icon"><i class="fa fa-users text-facebook"></i></span>
                    <span class="menu-title">@lang('My Referral')</span>
                </a>
            </li>
            <li class="nav-item {{ sidenav_active('user.transaction.index') }}">
                <a href="{{route('user.transaction.index')}}" class="nav-link">
                    <span class="menu-icon"><i class="fa fa-money text-facebook"></i></span>
                    <span class="menu-title">@lang('Transactions')</span>
                </a>
            </li>
            @if (Auth::user() && !Auth::user()->stripe_user_id)
                <li class="nav-item">
                    <a href="{{'https://connect.stripe.com/express/oauth/authorize?client_id='.Config::get('app.stripe_connect_id').'&state='.Auth::user()->stripe_connect_state.'&suggested_capabilities[]=transfers&stripe_user[email]='.Auth::user()->email.''}}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-shield text-facebook"></i></span>
                        <span class="menu-title">@lang('Get paid')</span>
                    </a>
                </li>
            @endif
            <li class="nav-item {{ sidenav_active('user.go2fa') }}">
                <a href="{{route('user.go2fa')}}" class="nav-link">
                    <span class="menu-icon"><i class="fa fa-shield text-facebook"></i></span>
                    <span class="menu-title">@lang('2FA Security')</span>
                </a>
            </li>

            <li class="nav-item {{ sidenav_active('user.ticket*') }}">
                <a href="{{route('user.ticket')}}" class="nav-link">
                    <span class="menu-icon"><i class="fa fa-ticket text-facebook"></i></span>
                    <span class="menu-title">@lang('Support')</span>
                </a>
            </li>

            <li class="nav-item {{ sidenav_active('user.profile') }}">
                <a href="{{route('user.profile')}}" class="nav-link">
                    <span class="menu-icon"><i class="fa fa-cog text-facebook"></i></span>
                    <span class="menu-title">@lang('Account settings')</span>
                </a>
            </li>
            <li class="nav-item {{ sidenav_active('user.login.history') }}">
                <a href="{{route('user.login.history')}}" class="nav-link">
                    <span class="menu-icon"><i class="fa fa-history text-facebook"></i></span>
                    <span class="menu-title">@lang('Login History')</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
