<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
    <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active" href="{{ route('admin.dashboard') }}"><img
                src="{{ URL::asset('uploads/settings/thumbnail/' . $store_logo->plain_value . '') }}" class="main-logo"
                alt="logo"></a>
        <a class="desktop-logo logo-dark active" href="{{ route('admin.dashboard') }}"><img
                src="{{ URL::asset('uploads/settings/thumbnail/' . $store_logo->plain_value . '') }}"
                class="main-logo dark-theme" alt="logo"></a>
        <a class="logo-icon mobile-logo icon-light active" href="{{ route('admin.dashboard') }}"><img
                src="{{ URL::asset('uploads/settings/thumbnail/' . $store_logo->plain_value . '') }}" class="logo-icon"
                alt="logo"></a>
        <a class="logo-icon mobile-logo icon-dark active" href="{{ route('admin.dashboard') }}"><img
                src="{{ URL::asset('uploads/settings/thumbnail/' . $store_logo->plain_value . '') }}"
                class="logo-icon dark-theme" alt="logo"></a>
    </div>
    <div class="main-sidemenu">
        <div class="app-sidebar__user clearfix">
            <div class="dropdown user-pro-body">
                <div class="">
                    <img alt="user-img" class="avatar avatar-xl brround"
                        src="@if (empty(auth('admin')->user()->image)) {{ URL::asset('assets/dashboard/img/faces/person-icon.png') }}@else{{ URL::asset('uploads/users/thumbnail/' . auth('admin')->user()->image . '') }} @endif"><span
                        class="avatar-status profile-status bg-green"></span>
                </div>
                <div class="user-info">
                    <h4 class="font-weight-semibold mt-3 mb-0">{{ auth('admin')->user()->name }}</h4>
                </div>
            </div>
        </div>
        <ul class="side-menu">
            <li class="side-item side-item-category">{{ __('dashboard.Home') }}</li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin.dashboard') }}"><svg
                        xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3" />
                        <path
                            d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z" />
                    </svg><span class="side-menu__label">{{ __('dashboard.Home') }}</span>
                </a>
            </li>

            <li class="side-item side-item-category">{{ __('dashboard.Settings') }}</li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                        xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M4 12c0 4.08 3.06 7.44 7 7.93V4.07C7.05 4.56 4 7.92 4 12z" opacity=".3" />
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93s3.05-7.44 7-7.93v15.86zm2-15.86c1.03.13 2 .45 2.87.93H13v-.93zM13 7h5.24c.25.31.48.65.68 1H13V7zm0 3h6.74c.08.33.15.66.19 1H13v-1zm0 9.93V19h2.87c-.87.48-1.84.8-2.87.93zM18.24 17H13v-1h5.92c-.2.35-.43.69-.68 1zm1.5-3H13v-1h6.93c-.04.34-.11.67-.19 1z" />
                    </svg><span class="side-menu__label">{{ __('dashboard.Settings') }}</span><i
                        class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    @can('General_Settings')
                        <li><a class="slide-item"
                                href="{{ route('edit.generalsettings') }}">{{ __('dashboard.General_Settings') }}</a></li>
                    @endcan

                    @can('Store_Settings')
                        <li><a class="slide-item"
                                href="{{ route('edit.generalstoresettings') }}">{{ __('dashboard.Store_Settings') }}</a>
                        </li>
                    @endcan

                    @can('Delivery_Settings')
                        <li><a class="slide-item"
                                href="{{ route('edit.generaldeliverysettings') }}">{{ __('dashboard.Delivery_Settings') }}</a>
                        </li>
                    @endcan

                    @can('Social_Media')
                        <li><a class="slide-item"
                                href="{{ route('socialmedialinks') }}">{{ __('dashboard.Social_Media') }}</a></li>
                    @endcan

                    @can('Cancel_Reason_Order')
                        <li><a class="slide-item"
                                href="{{ route('ordercancelreasons') }}">{{ __('dashboard.Cancel_Reason_Order') }}</a>
                        </li>
                    @endcan
                </ul>
            </li>

            @can('Geo_Areas')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('geo.countries.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="10" r="3"/>
                            <path d="M12 21s-8-7.5-8-13a8 8 0 0116 0c0 5.5-8 13-8 13z"/>
                        </svg><span class="side-menu__label">{{ __('dashboard.Geo_Areas') }}</span>
                    </a>
                </li>
            @endcan

            <li class="side-item side-item-category">{{ __('dashboard.Users') }}</li>

            @can('Clients')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('clients') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon ionicon" viewBox="0 0 512 512">
                            <circle cx="152" cy="184" r="72" />
                            <path
                                d="M234 296c-28.16-14.3-59.24-20-82-20-44.58 0-136 27.34-136 82v42h150v-16.07c0-19 8-38.05 22-53.93 11.17-12.68 26.81-24.45 46-34z" />
                            <path d="M340 288c-52.07 0-156 32.16-156 96v48h312v-48c0-63.84-103.93-96-156-96z" />
                            <circle cx="340" cy="168" r="88" />
                        </svg><span class="side-menu__label">{{ __('dashboard.Clients') }}</span></a>
                </li>
            @endcan

            @can('Drivers')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('drivers') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon ionicon" viewBox="0 0 512 512">
                            <path d="M416 160h-64V64c0-35.3-28.7-64-64-64H112C76.7 0 48 28.7 48 64v384c0 35.3 28.7 64 64 64h224c35.3 0 64-28.7 64-64v-32h16c35.3 0 64-28.7 64-64V224c0-35.3-28.7-64-64-64zM240 448H112V64h128v384zm64-160V160h48v128h-48zm80 128h-16V224h16v192z"/>
                        </svg><span class="side-menu__label">{{ __('dashboard.Drivers') }}</span></a>
                </li>
            @endcan

            @can('Traders')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('traders.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 21h18M5 21V7l8-4 8 4v14M9 9h1m4 0h1M9 13h1m4 0h1M9 17h1m4 0h1"/>
                        </svg><span class="side-menu__label">{{ __('dashboard.Traders') }}</span>
                    </a>
                </li>
            @endcan

            @can('Trader_Approvals')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('traders.pending') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11l3 3L22 4M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                        </svg><span class="side-menu__label">{{ __('dashboard.Pending_Trader_Approvals') }}</span>
                    </a>
                </li>
            @endcan

            @role('admin')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('employees') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon ionicon" viewBox="0 0 512 512">
                            <path
                                d="M288 256c52.79 0 99.43-49.71 104-110.82 2.27-30.7-7.36-59.33-27.12-80.6C345.33 43.57 318 32 288 32c-30.24 0-57.59 11.5-77 32.38-19.63 21.11-29.2 49.8-27 80.78C188.49 206.28 235.12 256 288 256zM495.38 439.76c-8.44-46.82-34.79-86.15-76.19-113.75C382.42 301.5 335.83 288 288 288s-94.42 13.5-131.19 38c-41.4 27.6-67.75 66.93-76.19 113.75-1.93 10.73.69 21.34 7.19 29.11A30.94 30.94 0 00112 480h352a30.94 30.94 0 0024.21-11.13c6.48-7.77 9.1-18.38 7.17-29.11zM104 288v-40h40a16 16 0 000-32h-40v-40a16 16 0 00-32 0v40H32a16 16 0 000 32h40v40a16 16 0 0032 0z" />
                        </svg><span class="side-menu__label">{{ __('dashboard.Employees') }}</span></a>
                </li>
            @endrole

            {{-- === مواشي وسيط: المالية === --}}
            <li class="side-item side-item-category">{{ __('dashboard.Mawashik_Finance') }}</li>
            @can('Trader_Commissions')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('commissions.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 1v22M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6"/>
                        </svg><span class="side-menu__label">{{ __('dashboard.Commissions') }}</span>
                    </a>
                </li>
            @endcan
            @can('Deposits_Settings')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('deposits.settings') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 6l9-3 9 3v15l-9 3-9-3V6zM3 6l9 3 9-3M12 9v15"/>
                        </svg><span class="side-menu__label">{{ __('dashboard.Deposits') }}</span>
                    </a>
                </li>
            @endcan
            @can('Slaughter_Service')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('slaughters.settings') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12h18M3 6h18M3 18h18"/>
                        </svg><span class="side-menu__label">{{ __('dashboard.Slaughter_Service') }}</span>
                    </a>
                </li>
            @endcan

            @can('Orders')
                <li class="side-item side-item-category">{{ __('dashboard.Orders') }}</li>
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('orders') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon side-menu__icon" viewBox="0 0 512 512">
                            <circle cx="176" cy="416" r="32" />
                            <circle cx="400" cy="416" r="32" />
                            <path
                                d="M456.8 120.78a23.92 23.92 0 00-18.56-8.78H133.89l-6.13-34.78A16 16 0 00112 64H48a16 16 0 000 32h50.58l45.66 258.78A16 16 0 00160 368h256a16 16 0 000-32H173.42l-5.64-32h241.66A24.07 24.07 0 00433 284.71l28.8-144a24 24 0 00-5-19.93z" />
                        </svg>
                        <span class="side-menu__label">{{ __('dashboard.View') }}</span><span
                            class="badge badge-danger side-badge"> {{ \App\Models\Order::unreadCount() }}</span></a>
                </li>
            @endcan

            <li class="side-item side-item-category">{{ __('dashboard.Store') }}</li>
            @can('Products')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('products-approvals.pending') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 7L12 3 4 7m16 0v10l-8 4-8-4V7m16 0L12 11M4 7l8 4m0 0v10"/>
                        </svg><span class="side-menu__label">{{ __('dashboard.Pending_Products_Approval') }}</span>
                    </a>
                </li>
            @endcan
            @can('Products')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('products') }}">
                        <svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                            <path d="M0 0h24v24H0V0z" fill="none" />
                            <path d="M5 5h4v4H5zm10 10h4v4h-4zM5 15h4v4H5zM16.66 4.52l-2.83 2.82 2.83 2.83 2.83-2.83z"
                                opacity=".3" />
                            <path
                                d="M16.66 1.69L11 7.34 16.66 13l5.66-5.66-5.66-5.65zm-2.83 5.65l2.83-2.83 2.83 2.83-2.83 2.83-2.83-2.83zM3 3v8h8V3H3zm6 6H5V5h4v4zM3 21h8v-8H3v8zm2-6h4v4H5v-4zm8-2v8h8v-8h-8zm6 6h-4v-4h4v4z" />
                        </svg><span class="side-menu__label">{{ __('dashboard.Products') }}</span><span
                            class="badge badge-success side-badge">{{ \App\Models\Product::count() }} </span></span>
                    </a>
                </li>
            @endcan

            @can('Categories')
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                            <path d="M0 0h24v24H0V0z" fill="none" />
                            <path
                                d="M5 9h14V5H5v4zm2-3.5c.83 0 1.5.67 1.5 1.5S7.83 8.5 7 8.5 5.5 7.83 5.5 7 6.17 5.5 7 5.5zM5 19h14v-4H5v4zm2-3.5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5-1.5-.67-1.5-1.5.67-1.5 1.5-1.5z"
                                opacity=".3" />
                            <path
                                d="M20 13H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1zm-1 6H5v-4h14v4zm-12-.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5.67 1.5 1.5 1.5zM20 3H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1zm-1 6H5V5h14v4zM7 8.5c.83 0 1.5-.67 1.5-1.5S7.83 5.5 7 5.5 5.5 6.17 5.5 7 6.17 8.5 7 8.5z" />
                        </svg><span class="side-menu__label">{{ __('dashboard.Categories') }}</span> <span
                            class="badge badge-success side-badge">{{ \App\Models\Category::count() }} <i
                                class="angle fe fe-chevron-down" style="color:white !important"></i></span></a>
                    <ul class="slide-menu">
                        <li><a class="slide-item" href="{{ route('categories') }}">{{ __('dashboard.View_All') }}</a>
                        </li>
                        <li><a class="slide-item"
                                href="{{ route('categories.create') }}">{{ __('dashboard.Add_New') }}</a></li>
                    </ul>
                </li>
            @endcan

            @can('Offers and discounts')
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.0" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-percent">
                            <line x1="19" y1="5" x2="5" y2="19"></line>
                            <circle cx="6.5" cy="6.5" r="2.5"></circle>
                            <circle cx="17.5" cy="17.5" r="2.5"></circle>
                        </svg><span class="side-menu__label">{{ __('dashboard.Offers and discounts') }}</span><span
                            class="badge badge-success side-badge">{{ \App\Models\DiscountCode::count() }} <i
                                class="angle fe fe-chevron-down" style="color:white !important"></i></span></a>
                    <ul class="slide-menu">
                        <li><a class="slide-item" href="{{ route('discountcodes') }}">{{ __('dashboard.View_All') }}</a>
                        </li>
                        <li><a class="slide-item"
                                href="{{ route('discountcodes.create') }}">{{ __('dashboard.Add_New') }}</a>
                        </li>
                    </ul>
                </li>
            @endcan

            <li class="side-item side-item-category">{{ __('dashboard.Wallets') }}</li>
            @can('Client_Wallets')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('walletsclients') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon ionicon" viewBox="0 0 512 512">
                            <path d="M448 128H80a16 16 0 010-32h304a16 16 0 000-32H80a48 48 0 00-48 48v256a48 48 0 0048 48h368a32 32 0 0032-32V160a32 32 0 00-32-32zm-64 240a32 32 0 1132-32 32 32 0 01-32 32z"/>
                        </svg>
                        <span class="side-menu__label">{{ __('dashboard.Client_Wallets') }}</span>
                    </a>
                </li>
            @endcan

            @can('Trader_Wallets')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('walletstraders') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon ionicon" viewBox="0 0 512 512">
                            <path d="M448 128H80a16 16 0 010-32h304a16 16 0 000-32H80a48 48 0 00-48 48v256a48 48 0 0048 48h368a32 32 0 0032-32V160a32 32 0 00-32-32zm-64 240a32 32 0 1132-32 32 32 0 01-32 32z"/>
                        </svg>
                        <span class="side-menu__label">{{ __('dashboard.Trader_Wallets') }}</span>
                    </a>
                </li>
            @endcan

            @can('Driver_Wallets')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('walletsdrivers') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon ionicon" viewBox="0 0 512 512">
                            <path d="M448 128H80a16 16 0 010-32h304a16 16 0 000-32H80a48 48 0 00-48 48v256a48 48 0 0048 48h368a32 32 0 0032-32V160a32 32 0 00-32-32zm-64 240a32 32 0 1132-32 32 32 0 01-32 32z"/>
                        </svg>
                        <span class="side-menu__label">{{ __('dashboard.Driver_Wallets') }}</span>
                    </a>
                </li>
                 @endcan
 @can('Withdrawal_requests')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('walletsdrivers.withdrawalRequests') }}">
                        <svg  xmlns="http://www.w3.org/2000/svg" class="side-menu__icon ionicon" viewBox="0 -32 576 576" xmlns="http://www.w3.org/2000/svg"><path d="M271.06,144.3l54.27,14.3a8.59,8.59,0,0,1,6.63,8.1c0,4.6-4.09,8.4-9.12,8.4h-35.6a30,30,0,0,1-11.19-2.2c-5.24-2.2-11.28-1.7-15.3,2l-19,17.5a11.68,11.68,0,0,0-2.25,2.66,11.42,11.42,0,0,0,3.88,15.74,83.77,83.77,0,0,0,34.51,11.5V240c0,8.8,7.83,16,17.37,16h17.37c9.55,0,17.38-7.2,17.38-16V222.4c32.93-3.6,57.84-31,53.5-63-3.15-23-22.46-41.3-46.56-47.7L282.68,97.4a8.59,8.59,0,0,1-6.63-8.1c0-4.6,4.09-8.4,9.12-8.4h35.6A30,30,0,0,1,332,83.1c5.23,2.2,11.28,1.7,15.3-2l19-17.5A11.31,11.31,0,0,0,368.47,61a11.43,11.43,0,0,0-3.84-15.78,83.82,83.82,0,0,0-34.52-11.5V16c0-8.8-7.82-16-17.37-16H295.37C285.82,0,278,7.2,278,16V33.6c-32.89,3.6-57.85,31-53.51,63C227.63,119.6,247,137.9,271.06,144.3ZM565.27,328.1c-11.8-10.7-30.2-10-42.6,0L430.27,402a63.64,63.64,0,0,1-40,14H272a16,16,0,0,1,0-32h78.29c15.9,0,30.71-10.9,33.25-26.6a31.2,31.2,0,0,0,.46-5.46A32,32,0,0,0,352,320H192a117.66,117.66,0,0,0-74.1,26.29L71.4,384H16A16,16,0,0,0,0,400v96a16,16,0,0,0,16,16H372.77a64,64,0,0,0,40-14L564,377a32,32,0,0,0,1.28-48.9Z"/></svg>
                        <span class="side-menu__label">{{ __('dashboard.Withdrawal_Requests') }}</span>
                        <span class="badge badge-success side-badge">{{ \App\Models\WalletWithdrawalRequest::pending()->count() }}</span>
                    </a>
                </li>
            @endcan

            <li class="side-item side-item-category">{{ __('dashboard.Reports') }}</li>
            @can('The_Sales')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('reports.sales') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon side-menu__icon" viewBox="0 0 512 512">
                            <path
                                d="M448 400H64a16 16 0 010-32h384a16 16 0 010 32zM416 448H96a16 16 0 010-32h320a16 16 0 010 32zM32 272H16v48a32 32 0 0032 32h48v-16a64.07 64.07 0 00-64-64z" />
                            <path
                                d="M480 240h16v-64h-16a96.11 96.11 0 01-96-96V64H128v16a96.11 96.11 0 01-96 96H16v64h16a96.11 96.11 0 0196 96v16h256v-16a96.11 96.11 0 0196-96zm-224 64a96 96 0 1196-96 96.11 96.11 0 01-96 96z" />
                            <circle cx="256" cy="208" r="64" />
                            <path
                                d="M416 336v16h48a32 32 0 0032-32v-48h-16a64.07 64.07 0 00-64 64zM480 144h16V96a32 32 0 00-32-32h-48v16a64.07 64.07 0 0064 64zM96 80V64H48a32 32 0 00-32 32v48h16a64.07 64.07 0 0064-64z" />
                        </svg>
                        <span class="side-menu__label">{{ __('dashboard.The_Sales') }}</span></a>
                </li>
            @endcan
            @can('Products_purchased')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('reports.product_purchased') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon side-menu__icon" viewBox="0 0 512 512">
                            <path
                                d="M256 256c-13.47 0-26.94-2.39-37.44-7.17l-148-67.49C63.79 178.26 48 169.25 48 152.24s15.79-26 22.58-29.12l149.28-68.07c20.57-9.4 51.61-9.4 72.19 0l149.37 68.07c6.79 3.09 22.58 12.1 22.58 29.12s-15.79 26-22.58 29.11l-148 67.48C282.94 253.61 269.47 256 256 256zm176.76-100.86z" />
                            <path
                                d="M441.36 226.81L426.27 220l-38.77 17.74-94 43c-10.5 4.8-24 7.19-37.44 7.19s-26.93-2.39-37.42-7.19l-94.07-43L85.79 220l-15.22 6.84C63.79 229.93 48 239 48 256s15.79 26.08 22.56 29.17l148 67.63C229 357.6 242.49 360 256 360s26.94-2.4 37.44-7.19l147.87-67.61c6.81-3.09 22.69-12.11 22.69-29.2s-15.77-26.07-22.64-29.19z" />
                            <path
                                d="M441.36 330.8l-15.09-6.8-38.77 17.73-94 42.95c-10.5 4.78-24 7.18-37.44 7.18s-26.93-2.39-37.42-7.18l-94.07-43L85.79 324l-15.22 6.84C63.79 333.93 48 343 48 360s15.79 26.07 22.56 29.15l148 67.59C229 461.52 242.54 464 256 464s26.88-2.48 37.38-7.27l147.92-67.57c6.82-3.08 22.7-12.1 22.7-29.16s-15.77-26.07-22.64-29.2z" />
                        </svg>
                        <span class="side-menu__label">{{ __('dashboard.Products_purchased') }}</span></a>
                </li>
            @endcan

            @can('Alerts')
                <li class="side-item side-item-category">{{ __('dashboard.Alerts') }}</li>
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('notifications.create') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon side-menu__icon" viewBox="0 0 512 512">
                            <path
                                d="M440.08 341.31c-1.66-2-3.29-4-4.89-5.93-22-26.61-35.31-42.67-35.31-118 0-39-9.33-71-27.72-95-13.56-17.73-31.89-31.18-56.05-41.12a3 3 0 01-.82-.67C306.6 51.49 282.82 32 256 32s-50.59 19.49-59.28 48.56a3.13 3.13 0 01-.81.65c-56.38 23.21-83.78 67.74-83.78 136.14 0 75.36-13.29 91.42-35.31 118-1.6 1.93-3.23 3.89-4.89 5.93a35.16 35.16 0 00-4.65 37.62c6.17 13 19.32 21.07 34.33 21.07H410.5c14.94 0 28-8.06 34.19-21a35.17 35.17 0 00-4.61-37.66zM256 480a80.06 80.06 0 0070.44-42.13 4 4 0 00-3.54-5.87H189.12a4 4 0 00-3.55 5.87A80.06 80.06 0 00256 480z" />
                        </svg>
                        <span class="side-menu__label">{{ __('dashboard.Send_alerts_to_users') }}</span></a>
                </li>
            @endcan

            <li class="side-item side-item-category">{{ __('dashboard.other_sections') }}</li>

            @can('TextPages')
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" class="side-menu__icon"
                            viewBox="0 0 24 24">
                            <g>
                                <rect fill="none" />
                            </g>
                            <g>
                                <g />
                                <g>
                                    <path
                                        d="M21,5c-1.11-0.35-2.33-0.5-3.5-0.5c-1.95,0-4.05,0.4-5.5,1.5c-1.45-1.1-3.55-1.5-5.5-1.5S2.45,4.9,1,6v14.65 c0,0.25,0.25,0.5,0.5,0.5c0.1,0,0.15-0.05,0.25-0.05C3.1,20.45,5.05,20,6.5,20c1.95,0,4.05,0.4,5.5,1.5c1.35-0.85,3.8-1.5,5.5-1.5 c1.65,0,3.35,0.3,4.75,1.05c0.1,0.05,0.15,0.05,0.25,0.05c0.25,0,0.5-0.25,0.5-0.5V6C22.4,5.55,21.75,5.25,21,5z M3,18.5V7 c1.1-0.35,2.3-0.5,3.5-0.5c1.34,0,3.13,0.41,4.5,0.99v11.5C9.63,18.41,7.84,18,6.5,18C5.3,18,4.1,18.15,3,18.5z M21,18.5 c-1.1-0.35-2.3-0.5-3.5-0.5c-1.34,0-3.13,0.41-4.5,0.99V7.49c1.37-0.59,3.16-0.99,4.5-0.99c1.2,0,2.4,0.15,3.5,0.5V18.5z" />
                                    <path
                                        d="M11,7.49C9.63,6.91,7.84,6.5,6.5,6.5C5.3,6.5,4.1,6.65,3,7v11.5C4.1,18.15,5.3,18,6.5,18 c1.34,0,3.13,0.41,4.5,0.99V7.49z"
                                        opacity=".3" />
                                </g>
                                <g>
                                    <path
                                        d="M17.5,10.5c0.88,0,1.73,0.09,2.5,0.26V9.24C19.21,9.09,18.36,9,17.5,9c-1.28,0-2.46,0.16-3.5,0.47v1.57 C14.99,10.69,16.18,10.5,17.5,10.5z" />
                                    <path
                                        d="M17.5,13.16c0.88,0,1.73,0.09,2.5,0.26V11.9c-0.79-0.15-1.64-0.24-2.5-0.24c-1.28,0-2.46,0.16-3.5,0.47v1.57 C14.99,13.36,16.18,13.16,17.5,13.16z" />
                                    <path
                                        d="M17.5,15.83c0.88,0,1.73,0.09,2.5,0.26v-1.52c-0.79-0.15-1.64-0.24-2.5-0.24c-1.28,0-2.46,0.16-3.5,0.47v1.57 C14.99,16.02,16.18,15.83,17.5,15.83z" />
                                </g>
                            </g>
                        </svg><span class="side-menu__label">{{ __('dashboard.TextPages') }}</span><i
                            class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @foreach ($menu_textpages as $textpagem)
                            <li><a class="slide-item"
                                    href="{{ route('textpages.edittext', $textpagem->slug) }}">{{ $textpagem->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endcan
            @can('Slideshow')
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                            <path
                                d="M8 18h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v12a2 2 0 0 0 8 2Zm0-2V4h12v12H8Z" />
                            <path d="M2 8h2v12h12v2H4a2 2 0 0 1-2-2V8Z" />
                            <path
                                d="M10 14h9l-3.75-5.444l-2.25 3.11l-.75-.777L10 14Zm.75-5.833c0 .644.504 1.166 1.125 1.166S13 8.811 13 8.167C13 7.522 12.496 7 11.875 7s-1.125.522-1.125 1.167Z" />
                        </svg>
                        <span class="side-menu__label">{{ __('dashboard.Slideshow') }}</span> <span
                            class="badge badge-success side-badge">{{ \App\Models\Slideshow::count() }} <i
                                class="angle fe fe-chevron-down" style="color:white !important"></i></span></a>

                    <ul class="slide-menu">
                        <li><a class="slide-item" href="{{ route('slideshows') }}">{{ __('dashboard.View_All') }}</a>
                        </li>
                        <li><a class="slide-item"
                                href="{{ route('slideshows.create') }}">{{ __('dashboard.Add_New') }}</a></li>

                    </ul>
                </li>
            @endcan
            @can('Contact_Us')
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('messagescontactus') }}"><svg
                            xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                            <path d="M0 0h24v24H0V0z" fill="none" />
                            <path d="M15 11V4H4v8.17l.59-.58.58-.59H6z" opacity=".3" />
                            <path
                                d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-5 7c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10zM4.59 11.59l-.59.58V4h11v7H5.17l-.58.59z" />
                        </svg><span class="side-menu__label">{{ __('dashboard.Contact_Us') }}</span><span
                            class="badge badge-success side-badge">
                            {{ \App\Models\Messagecontactus::Notread()->count() }}</span></a>
                </li>
            @endcan

        </ul>
    </div>
</aside>
<!-- main-sidebar -->
