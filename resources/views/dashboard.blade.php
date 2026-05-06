<!DOCTYPE html>
<html lang="en">

@include('admin.includes.head')

<body class="hold-transition sidebar-mini">

    <div class="wrapper">
        <header class="main-header">


            @include('admin.includes.navbar')

        </header>

        @include('admin.includes.sidebar')


        <div class="content-wrapper">

            <section class="content-header">
                <form action="#" method="get"
                    class="sidebar-form search-box pull-right hidden-md hidden-lg hidden-sm">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Search...">
                        <span class="input-group-btn">
                            <button type="submit" name="search" id="search-btn" class="btn"><i
                                    class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
                <div class="header-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <div class="header-title">
                    <h1> {{ __('messages.dashboard') }}</h1>
                    <small> {{ __('messages.dashboard_features') }}</small>
                    <ol class="breadcrumb hidden-xs">
                        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
                        <li class="active">{{ __('messages.dashboard') }}</li>
                    </ol>
                </div>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
                        <a href="{{ route('users.index') }}" class="text-decoration-none">
                            <div class="panel panel-bd cardbox">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <h2><span class="count-number">
                                                {{ \App\Models\User::where('status', 'active')->count() }}
                                            </span>
                                        </h2>
                                    </div>
                                    <div class="items pull-left">
                                        <i class="fa fa-users fa-2x"></i>
                                        <h4>{{ __('messages.active_users') }} </h4>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
                        <a href="{{ route('news.index') }}" class="text-decoration-none">
                            <div class="panel panel-bd cardbox">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <h2><span class="count-number">{{ \App\Models\News::where('status', 1)->count() }}</span>
                                        </h2>
                                    </div>
                                    <div class="items pull-left">
                                        <i class="fa-regular fa-newspaper fa-2x"></i>
                                        <h4>{{ __('messages.active_news') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
                        <a href="{{ route('signals.index') }}" class="text-decoration-none">
                            <div class="panel panel-bd cardbox">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <h2><span class="count-number">{{ \App\Models\Signal::where('status', 1)->count() }}</span>
                                        </h2>
                                    </div>
                                    <div class="items pull-left">
                                        <i class="fa-solid fa-signal fa-2x"></i>
                                        <h4>{{ __('messages.active_signals') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
                        <a href="{{ route('admin.chat.index') }}" class="text-decoration-none">
                            <div class="panel panel-bd cardbox">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <h2><span class="count-number">{{ \App\Models\Message::where('is_read', 0)->count() }}</span>
                                        </h2>
                                    </div>
                                    <div class="items pull-left">
                                        <i class="fa-solid fa-message fa-2x"></i>
                                        <h4>{{ __('messages.unread_messages') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>

            </section>

        </div>
        @include('admin.includes.footer')


    </div>


    @include('admin.includes.jslinks')

</body>



</html>