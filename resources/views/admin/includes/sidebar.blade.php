<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<aside class="main-sidebar">

    <div class="sidebar">

        <div class="user-panel">
            <div class="image pull-left">
                <img src="{{ asset('assets/dist/img/avatar5.png') }}" class="img-circle" alt="User Image">
            </div>
            
            <div class="info">
                <h4>{{ __('messages.welcome') }}</h4>
                <p>
                    @if(auth()->check())
                        {{ __('messages.mr') }}. 
                        @if(app()->getLocale() == 'ar')
                            {{-- If Arabic is selected, show the name in Arabic format (e.g., last name first if you want) --}}
                            {{ auth()->user()->last_name }} {{ auth()->user()->f_name }}
                        @else
                            {{-- English --}}
                            {{ auth()->user()->f_name }} {{ auth()->user()->last_name }}
                        @endif
                    @else
                        {{ __('messages.guest') }}
                    @endif
                </p>
            </div>


        </div>

        <!-- sidebar menu -->
        <ul class="sidebar-menu">
            @php
            function isActive($routeName)
            {
            return request()->routeIs($routeName) ? 'active' : '';
            }
            @endphp

            <li class="{{ isActive('dashboard') }}">
                <a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i><span>{{ __('messages.dashboard') }}</span>
                </a>
            </li>
            @can('manageNews')
            <li class="treeview {{ isActive('news.index') || isActive('news.create') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa-solid fa-newspaper"></i><span>{{ __('messages.news') }}</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ isActive('news.create') }}">
                        <a href="{{ route('news.create') }}">{{ __('messages.add_news') }}</a>
                    </li>
                    <li class="{{ isActive('news.index') }}">
                        <a href="{{ route('news.index') }}">{{ __('messages.list') }}</a>
                    </li>
                </ul>
            </li>
            @endcan
            <!--@can('manageSignal')-->
            <!--<li class="treeview {{ isActive('signals.index') || isActive('signals.create') ? 'active' : '' }}">-->
            <!--    <a href="#">-->
            <!--        <i class="fa-solid fa-signal"></i><span>{{ __('messages.signals') }}</span>-->
            <!--        <span class="pull-right-container">-->
            <!--            <i class="fa fa-angle-left pull-right"></i>-->
            <!--        </span>-->
            <!--    </a>-->
            <!--    <ul class="treeview-menu">-->
            <!--        <li class="{{ isActive('signals.create') }}">-->
            <!--            <a href="{{ route('signals.create') }}">{{ __('messages.add_signals') }}</a>-->
            <!--        </li>-->
            <!--        <li class="{{ isActive('signals.index') }}">-->
            <!--            <a href="{{ route('signals.index') }}">{{ __('messages.list') }}</a>-->
            <!--        </li>-->
            <!--    </ul>-->
            <!--</li>-->
            <!--@endcan-->
            @can('manageValues')
            <li class="treeview {{ isActive('values.index') || isActive('values.create') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa-solid fa-money-check-dollar"></i><span>{{ __('messages.values') }}</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ isActive('values.create') }}">
                        <a href="{{ route('values.create') }}">{{ __('messages.add_values') }}</a>
                    </li>
                    <li class="{{ isActive('values.index') }}">
                        <a href="{{ route('values.index') }}">{{ __('messages.list') }}</a>
                    </li>
                </ul>
            </li>

            @endcan
            @can('manageValues_subscription')
            <li class="treeview {{ isActive('admin.value-subscriptions.index') || isActive('admin.value-subscriptions.create') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa-solid fa-link"></i><span>{{ __('messages.value_subscriptions') }}</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ isActive('admin.value-subscriptions.create') }}">
                        <a href="{{ route('admin.value-subscriptions.create') }}">{{ __('messages.add_subscription') }}</a>
                    </li>
                    <li class="{{ isActive('admin.value-subscriptions.index') }}">
                        <a href="{{ route('admin.value-subscriptions.index') }}">{{ __('messages.list') }}</a>
                    </li>
                </ul>
            </li>
            @endcan
            @can('manageUsers')
            <li class="treeview {{ isActive(['users.create','users.index','users.staff']) ? 'active' : '' }}">
                <a href="#">
                    <i class="fa-solid fa-users"></i><span>{{ __('messages.users_and_staff') }}</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ isActive('users.create') }}">
                        <a href="{{ route('users.create') }}">{{ __('messages.add_staff') }}</a>
                    </li>
                    <li class="{{ isActive('users.staff') }}">
                        <a href="{{ route('users.staff') }}">{{ __('messages.staff_list') }}</a>
                    </li>
                    <li class="{{ isActive('users.index') }}">
                        <a href="{{ route('users.index') }}">{{ __('messages.users_list') }}</a>
                    </li>
                </ul>
            </li>
            @endcan
            @can('manageInvoices')
            <li class="treeview {{ isActive('invoices.index') ? 'active' : '' }}">
                <a href="javascript:void(0)">
                    <i class="fa-solid fa-credit-card"></i><span>{{ __('messages.invoices') }}</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ isActive('invoices.index') ? 'active' : '' }}">
                        <a href="{{ route('invoices.index') }}">{{ __('messages.invoices_list') }}</a>
                    </li>
                    <li class="{{ isActive('invoices.newUser') ? 'active' : '' }}">
                        <a href="{{ route('invoices.newUser') }}">{{ __('messages.newUser') }}</a>
                    </li>
                </ul>
            </li>
            @endcan
            @can('managePackages')
            <li class="treeview {{ isActive('packages.index') || isActive('packages.create') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa-solid fa-box"></i><span>{{ __('messages.packages') }}</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ isActive('packages.create') }}">
                        <a href="{{ route('packages.create') }}">{{ __('messages.add_package') }}</a>
                    </li>
                    <li class="{{ isActive('packages.index') }}">
                        <a href="{{ route('packages.index') }}">{{ __('messages.list') }}</a>
                    </li>
                </ul>
            </li>
            @endcan
            @can('manageAnalysis')
            <li class="treeview {{ isActive('userMessages.index') || isActive('userMessages.create') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa-solid fa-magnifying-glass-chart"></i><span>{{ __('messages.analysis') }}</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ isActive('userMessages.create') }}">
                        <a href="{{ route('userMessages.create') }}">{{ __('messages.add_message') }}</a>
                    </li>
                    <li class="{{ isActive('userMessages.index') }}">
                        <a href="{{ route('userMessages.index') }}">{{ __('messages.list') }}</a>
                    </li>
                </ul>
            </li>
            @endcan

            @can('manageBuyer')
            <li class="treeview {{ isActive('admin.package.purchases') ? 'active' : '' }}">
                <a href="javascript:void(0)">
                    <i class="fa-solid fa-credit-card"></i><span>{{ __('messages.deposit_history') }}</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ isActive('admin.package.purchases') ? 'active' : '' }}">
                        <a href="{{ route('admin.package.purchases') }}">{{ __('messages.buyer_list') }}</a>
                    </li>
                </ul>
            </li>
            @endcan
            @can('manageNotifications')
            <li class="treeview {{ isActive('notifications.index') || isActive('notifications.create') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa-solid fa-bell"></i><span>{{ __('messages.notifications') }}</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ isActive('notifications.create') }}">
                        <a href="{{ route('notifications.create') }}">{{ __('messages.send_notification') }}</a>
                    </li>
                    <li class="{{ isActive('notifications.index') }}">
                        <a href="{{ route('notifications.index') }}">{{ __('messages.notification_list') }}</a>
                    </li>
                </ul>
            </li>
            @endcan
            @can('manageSetting')
            <li class="treeview {{ isActive('roles.index') || isActive('permissions.index') || isActive('admin.getroles') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa-solid fa-gear"></i><span>{{ __('messages.settings') }}</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ isActive('roles.index') }}">
                        <a href="{{ route('roles.index') }}">{{ __('messages.roles_list') }}</a>
                    </li>
                    <li class="{{ isActive('permissions.index') }}">
                        <a href="{{ route('permissions.index') }}">{{ __('messages.permissions_list') }}</a>
                    </li>
                    <li class="{{ isActive('admin.getroles') }}">
                        <a href="{{ route('admin.getroles') }}">{{ __('messages.assign') }} </a>
                    </li>
                </ul>
            </li>
            @endcan
            @can('managChats')
            <li class="treeview {{ isActive(['admin.chat.index', 'admin.chat.start_new']) ? 'active' : '' }}">
                <a href="#">
                    <i class="fa-solid fa-comments"></i><span>{{ __('messages.messages') }}</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ isActive('admin.chat.start_new') }}">
                        <a href="{{ route('admin.chat.start_new') }}">{{ __('messages.start_new_chat') }}</a>
                    </li>
                    <li class="{{ isActive('admin.chat.index') }}">
                        <a href="{{ route('admin.chat.index') }}">{{ __('messages.chat_list') }}</a>
                    </li>
                </ul>
            </li>
            @endcan
             @can('managChats')
            <li class="treeview {{ isActive(['bankDetails.index', 'bankDetails.create', 'wallet.index']) ? 'active' : '' }}">
                <a href="#">
                    <i class="fa-solid fa-wallet"></i>
                    <span>{{ __('messages.bank_section') }}</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ isActive('bankDetails.start_new') }}">
                        <a href="{{ route('bankDetails.create') }}">{{ __('messages.add_new_bank') }}</a>
                    </li>
                    <li class="{{ isActive('bankDetails.index') }}">
                        <a href="{{ route('bankDetails.index') }}">{{ __('messages.bank_list') }}</a>
                    </li>
                    <li class="{{ isActive('wallet.index') }}">
                        <a href="{{ route('wallet.index') }}">{{ __('messages.wallet') }}</a>
                    </li>
                </ul>
            </li>
            @endcan

        </ul>
    </div>
</aside>