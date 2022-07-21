<!--
MIT License

Copyright (c) 2021-2022 FoxxoSnoot

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ isset($title) ? "{$title} | " . config('site.name') : config('site.name') }}</title>

    <!-- Preconnect -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">

    <!-- Meta -->
    <link rel="shortcut icon" href="{{ config('site.icon') }}">
    <meta name="author" content="{{ config('site.name') }}">
    <meta name="description" content="Explore {{ config('site.name') }}: A free online social hangout.">
    <meta name="keywords" content="{{ strtolower(config('site.name')) }}, {{ strtolower(str_replace(' ', '', config('site.name'))) }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')

    <!-- OpenGraph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('site.name') }}">
    <meta property="og:title" content="{{ str_replace(' | ' . config('site.name'), '', $title) }}">
    <meta property="og:description" content="Explore {{ config('site.name') }}: A free online social hangout.">
    <meta property="og:image" content="{{ !isset($image) ? config('site.icon') : $image }}">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.3/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap">
    @yield('fonts')

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/stylesheet.css') }}">
    <link rel="stylesheet" href="{{ (Auth::check()) ? asset('css/themes/' . Auth::user()->setting->theme . '.css?v=' . rand()) : asset('css/themes/dark.css?v=' . rand()) }}">
    <style>
        a, a:hover, a:focus {
            text-decoration: none;
        }

        .navbar-search-dropdown-parent {
            width: 50%;
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            z-index: 1000;
            margin-left: 13%;
        }

        .navbar-search-dropdown {
            background: var(--section_bg);
            color: var(--section_color);
            border-radius: 8px;
            box-shadow: var(--section_box_shadow);
            padding: 15px 0;
            width: 100%;
            position: absolute;
            margin-top: 50px;
        }

        .navbar-search-result, .navbar-search-error {
            padding: 5px;
            padding-left: 10px;
            padding-right: 10px;
        }

        .navbar-search-result:hover {
            background: var(--section_bg_hover);
        }

        .navbar-search-result a {
            color: inherit;
            text-decoration: none;
        }

        .navbar-search-result img {
            background: var(--headshot_bg);
            border: 1px solid var(--headshot_border_color);
            border-radius: 50%;
            width: 40px;
        }
    </style>
    @yield('css')
</head>
<body>
    <nav class="navbar navbar-expand-md fixed-top">
        <button class="navbar-toggler" id="sidebarToggler" style="border:none;" type="button">
            <i class="fas fa-bars" style="font-size: 23px;"></i>
        </button>

        <a href="{{ (Auth::check()) ? route('home.dashboard') : route('home.index') }}" class="navbar-brand">
            <img src="{{ config('site.logo') }}" width="150px">
        </a>

        @guest
            <div class="nav-item show-sm-only"></div>
        @else
            <div class="nav-item dropdown headshot show-sm-only">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <img src="{{ Auth::user()->headshot() }}" width="40px">
                </a>
                <div class="dropdown-menu">
                    <a href="{{ route('users.profile', Auth::user()->username) }}" class="dropdown-item">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>

                    @if (site_setting('character_enabled'))
                        <a href="{{ route('account.character.index') }}" class="dropdown-item">
                            <i class="fas fa-tshirt"></i>
                            <span>Character</span>
                        </a>
                    @endif

                    @if (site_setting('settings_enabled'))
                        <a href="{{ route('account.settings.index', '') }}" class="dropdown-item">
                            <i class="fas fa-wrench"></i>
                            <span>Settings</span>
                        </a>
                    @endif

                    <a href="{{ route('auth.logout') }}" class="dropdown-item">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        @endguest

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav mr-auto" style="width:50%;">
                <span><i class="fas fa-search"></i></span>
                <input class="navbar-search" id="navbarSearch" placeholder="Search for users, items and groups...">
            </ul>

            <ul class="navbar-nav ml-auto">
                @guest
                    <li class="nav-item hide-sm">
                        <a href="{{ route('auth.login.index') }}" class="nav-link">
                            <i class="fas fa-user"></i>
                            <span>Login</span>
                        </a>
                    </li>
                    @if (site_setting('registration_enabled'))
                        <li class="nav-item hide-sm">
                            <a href="{{ route('auth.register.index') }}" class="nav-link">
                                <i class="fas fa-user-plus"></i>
                                <span>Register</span>
                            </a>
                        </li>
                    @endif
                @else
                    <li class="nav-item">
                        <a href="{{ route('account.money.index', '') }}" class="nav-link" title="{{ str_replace('from now', '', Auth::user()->next_currency_payout->diffForHumans()) }} until next reward" data-toggle="tooltip">
                            <i class="currency"></i>
                            <span>{{ number_format(Auth::user()->currency) }}</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown headshot hide-sm">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="{{ Auth::user()->headshot() }}" width="40px">
                        </a>
                        <div class="dropdown-menu" style="margin-left:-100px;">
                            <a href="{{ route('users.profile', Auth::user()->username) }}" class="dropdown-item">
                                <i class="fas fa-user"></i>
                                <span>Profile</span>
                            </a>

                            @if (site_setting('character_enabled'))
                                <a href="{{ route('account.character.index') }}" class="dropdown-item">
                                    <i class="fas fa-tshirt"></i>
                                    <span>Character</span>
                                </a>
                            @endif

                            @if (site_setting('settings_enabled'))
                                <a href="{{ route('account.settings.index', '') }}" class="dropdown-item">
                                    <i class="fas fa-wrench"></i>
                                    <span>Settings</span>
                                </a>
                            @endif

                            <a href="{{ route('auth.logout') }}" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </nav>
    <div class="navbar-search-dropdown-parent">
        <div class="navbar-search-dropdown" id="navbarSearchResults" style="display:none;"></div>
    </div>

    <nav class="sidebar">
        <div class="mb-2"></div>
        <div class="mt-2 show-sm-only"></div>
        <div class="show-sm-only" style="padding-left: 5px; padding-right: 5px;">
            <form action="{{ route('users.index', '') }}" method="GET">
                <input class="form-control" style="height: 38px;" type="text" name="search" placeholder="Search Avasquare..." @if (request()->route()->getName() == 'users.index') value="{{ request()->search }}" @endif>
            </form>
        </div>
        <div class="mb-1 show-sm-only"></div>
        @guest
            <div class="show-sm-only">
                <div class="section-div">AUTH</div>
                <a href="{{ route('auth.login.index') }}">
                    <i class="fas fa-user sidebar-icon"></i>
                    <span class="sidebar-text">Login</span>
                </a>
                @if (site_setting('registration_enabled'))
                    <a href="{{ route('auth.register.index') }}">
                        <i class="fas fa-user-plus sidebar-icon"></i>
                        <span class="sidebar-text">Register</span>
                    </a>
                @endif
            </div>
        @else
            <div class="show-sm-only">
                <a href="{{ route('account.money.index', '') }}">
                    <i class="currency"></i>
                    <span class="sidebar-text">{{ number_format(Auth::user()->currency) }} Currency</span>
                </a>
            </div>
        @endguest
        <div class="section-div">NAVIGATION</div>
        <a href="{{ (Auth::check()) ? route('home.dashboard') : route('home.index') }}">
            <i class="fas fa-house-user sidebar-icon"></i>
            <span class="sidebar-text">Home</span>
        </a>
        <a href="{{ route('catalog.index') }}">
            <i class="fas fa-store sidebar-icon"></i>
            <span class="sidebar-text">Catalog</span>
        </a>

        @if (site_setting('forum_enabled'))
            <a href="{{ route('forum.index') }}">
                <i class="fas fa-comment-alt sidebar-icon"></i>
                <span class="sidebar-text">Forum</span>
            </a>
        @endif

        @if (site_setting('groups_enabled'))
            <a href="{{ route('groups.index') }}">
                <i class="fas fa-building sidebar-icon"></i>
                <span class="sidebar-text">Groups</span>
            </a>
        @endif

        <a href="{{ route('users.index', '') }}">
            <i class="fas fa-telescope sidebar-icon"></i>
            <span class="sidebar-text">Search</span>
        </a>

        @auth
            <a href="{{ route('account.promocodes.index') }}">
                <i class="fas fa-ticket-alt sidebar-icon"></i>
                <span class="sidebar-text">Promocodes</span>
            </a>
            @if (site_setting('real_life_purchases_enabled'))
                <a href="{{ route('account.upgrade.index') }}">
                    <i class="fas fa-rocket sidebar-icon"></i>
                    <span class="sidebar-text">Upgrade</span>
                </a>
            @endif
            <div class="section-div">PERSONAL</div>
            <a href="{{ route('account.money.index', '') }}">
                <i class="fas fa-money-bill-alt sidebar-icon"></i>
                <span class="sidebar-text">Money</span>
            </a>
            <a href="{{ route('account.friends.index') }}">
                <i class="fas fa-user-friends sidebar-icon"></i>
                <span class="sidebar-text">Friends</span>
                @if (Auth::user()->friendRequestCount() > 0)
                    <span class="notification float-right">{{ number_format(Auth::user()->friendRequestCount()) }}</span>
                @endif
            </a>
            <a href="{{ route('account.inbox.index', '') }}">
                <i class="fas fa-inbox sidebar-icon"></i>
                <span class="sidebar-text">Inbox</span>
                @if (Auth::user()->messageCount() > 0)
                    <span class="notification float-right">{{ number_format(Auth::user()->messageCount()) }}</span>
                @endif
            </a>
            @if (site_setting('trading_enabled'))
                <a href="{{ route('account.trades.index', '') }}">
                    <i class="fas fa-exchange sidebar-icon"></i>
                    <span class="sidebar-text">Trades</span>
                    @if (Auth::user()->tradeCount() > 0)
                        <span class="notification float-right">{{ number_format(Auth::user()->tradeCount()) }}</span>
                    @endif
                </a>
            @endif

            @if (Auth::user()->isStaff())
                <a href="{{ route('admin.index') }}">
                    <i class="fas fa-gavel sidebar-icon"></i>
                    <span class="sidebar-text">Panel</span>
                    @if (pendingAssetsCount() > 0 || pendingReportsCount() > 0)
                        <span class="notification float-right">
                            @if (pendingAssetsCount() > 0)
                                <span>(A: {{ number_format(pendingAssetsCount()) }})</span>
                            @endif

                            @if (pendingReportsCount() > 0)
                                <span>(R: {{ number_format(pendingReportsCount()) }})</span>
                            @endif
                        </span>
                    @endif
                </a>
            @endif
        @endauth
    </nav>

    <div class="container-custom">
        @if (site_setting('alert_enabled') && site_setting('alert_message'))
            <div class="alert alert-site text-center mb-4" style="background:{{ site_setting('alert_background_color') }};color:{{ site_setting('alert_text_color') }};">
                <div class="row">
                    <div class="col-1 align-self-center pl-1 pr-1">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="col-10 align-self-center pl-1 pr-1">
                        <strong style="word-wrap:break-word;">{!! site_setting('alert_message') !!}</strong>
                    </div>
                    <div class="col-1 align-self-center pl-1 pr-1">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                </div>
            </div>
        @endif

        @if (site_setting('maintenance_enabled'))
            <div class="alert bg-danger text-center text-white">
                You are currently in maintenance mode. <a href="{{ route('maintenance.exit') }}" class="text-white" style="font-weight:600;">[Exit]</a>
            </div>
        @endif

        @if (count($errors) > 0)
            <div class="alert bg-danger text-white">
                @foreach ($errors->all() as $error)
                    <div>{!! $error !!}</div>
                @endforeach
            </div>
        @endif

        @if (session()->has('success_message'))
            <div class="alert bg-success text-white">
                {!! session()->get('success_message') !!}
            </div>
        @endif

        @if (!site_setting('catalog_purchases_enabled') && Str::startsWith(request()->route()->getName(), 'catalog.'))
            <div class="alert bg-warning text-center text-white" style="font-weight:600;">
                Market purchases are temporarily unavailable. Items may be browsed but are unable to be purchased.
            </div>
        @endif

        @yield('content')
    </div>

    <footer class="container-custom text-center mb-5 mt-5" style="padding-top:0;">
        <div class="mb-2" style="font-size:17px;">
            <a href="{{ route('info.index', 'terms') }}" class="text-muted mr-3" style="text-decoration:none;">TERMS</a>
            <a href="{{ route('info.index', 'privacy') }}" class="text-muted mr-3" style="text-decoration:none;">PRIVACY</a>
            <a href="{{ route('info.index', 'team') }}" class="text-muted" style="text-decoration:none;">TEAM</a>
        </div>

        <div><strong>Copyright &copy; {{ config('site.name') }} {{ date('Y') }}</strong></div>
        <div class="text-muted" style="font-size:13px;"><strong>Powered by <a href="https://github.com/FoxxoSnoot/laravel-roblox-clone" target="_blank">Laravel Roblox Clone</a></strong></div>

        @if (config('site.socials.discord') || config('site.socials.twitter'))
            <div class="mt-2">
                @if (config('site.socials.discord'))
                    <a href="{{ config('site.socials.discord') }}" style="color:#7289da;font-size:25px;text-decoration:none;" title="Join our Discord server!" target="_blank" data-toggle="tooltip">
                        <i class="fab fa-discord"></i>
                    </a>
                @endif

                @if (config('site.socials.twitter'))
                    <a href="{{ config('site.socials.twitter') }}" style="color:#00acee;font-size:26px;text-decoration:none;" title="Follow us on Twitter!" target="_blank" data-toggle="tooltip">
                        <i class="fab fa-twitter-square"></i>
                    </a>
                @endif
            </div>
        @endif
    </footer>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        var _token;

        $(() => {
            _token = $('meta[name="csrf-token"]').attr('content');

            $('[data-toggle="tooltip"]').tooltip();

            $('#sidebarToggler').click(function() {
                const enabled = !$('.sidebar').hasClass('show');

                if (enabled)
                    $('.sidebar').addClass('show');
                else
                    $('.sidebar').removeClass('show');
            });
        });
    </script>
    <script src="{{ asset('js/search.js') }}"></script>
    @yield('js')
</body>
</html>
