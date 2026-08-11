@extends('layouts.admin_dashboard')
@section('title')
    {{ $item->name }}
@endsection
@section('css')
    <style type="text/css">
        .nostaractive {
            color: #e1e6f1 !important;
        }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ $item->name }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    {{ __('dashboard.View') }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <div class="pr-1 mb-3 mb-xl-0 ml-3">
                <a href="{{ route($routeName) }}" class="btn btn-info btn-icon"><i
                        class="typcn typcn-arrow-back-outline"></i></a>
            </div>



        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row row-sm">
        <div class="col-lg-4">
            <div class="card mg-b-20">
                <div class="card-body">
                    <div class="pl-0">
                        <div class="main-profile-overview">
                            <div class="main-img-user profile-user">
                                <img alt=""
                                    src="@if (empty($item->image)) {{ URL::asset('assets/dashboard/img/faces/person-icon.png') }}@else{{ URL::asset('uploads/users/thumbnail/' . $item->image . '') }} @endif">
                            </div>
                            <div class="d-flex justify-content-between mg-b-20">
                                <div>
                                    <h5 class="main-profile-name">{{ $item->name }}</h5>
                                    <p class="main-profile-name-text">{{ $item->mobile }}</p>

                                </div>
                            </div>

                            <h6>{{ __('dashboard.subscribed_since') }} : {{ $item->created_at->diffForHumans() }}</h6>

                            {{-- ✅ رصيد المحفظة بشكل بارز --}}
                            <div class="card mt-4" style="background: linear-gradient(135deg,#0d6efd,#00b4d8); border:none;">
                                <div class="card-body text-center text-white py-4">
                                    <i class="fa fa-wallet mb-2" style="font-size:26px;"></i>
                                    <h6 class="mb-1 text-white-50">{{ __('dashboard.Wallet_Balance') }}</h6>
                                    <h2 class="mb-0 text-white font-weight-bold">{{ number_format($walletBalance, 2) }} {{ __('dashboard.SAR') }}</h2>
                                </div>
                            </div>

                        </div><!-- main-profile-overview -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="row row-sm">

                <div class="col-sm-12 col-xl-3 col-lg-6 col-md-6">
                    <div class="card ">
                        <div class="card-body">
                            <div class="counter-status d-flex md-mb-0">
                                <div class="counter-icon bg-primary-transparent">
                                    <i class="icon-clock text-primary"></i>
                                </div>
                                <div class="mr-auto">
                                    <h5 class="tx-13">{{ __('dashboard.Today_Completed_Orders') }}</h5>
                                    <h2 class="mb-0 tx-15 mb-1 mt-1">{{ $todayCompletedOrders }} </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-xl-3 col-lg-6 col-md-6">
                    <div class="card ">
                        <div class="card-body">
                            <div class="counter-status d-flex md-mb-0">
                                <div class="counter-icon bg-success-transparent">
                                    <i class="icon-layers text-success"></i>
                                </div>
                                <div class="mr-auto">
                                    <h5 class="tx-13">{{ __('dashboard.Total_Completed_Orders') }}</h5>
                                    <h2 class="mb-0 tx-15 mb-1 mt-1">{{ $totalCompletedOrders }} </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-xl-3 col-lg-6 col-md-6">
                    <div class="card ">
                        <div class="card-body">
                            <div class="counter-status d-flex md-mb-0">
                                <div class="counter-icon bg-warning-transparent">
                                    <i class="icon-basket text-warning"></i>
                                </div>
                                <div class="mr-auto">
                                    <h5 class="tx-13">{{ __('dashboard.Cash_Collected') }}</h5>
                                    <h2 class="mb-0 tx-15 mb-1 mt-1">{{ number_format($cashCollected, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-xl-3 col-lg-6 col-md-6">
                    <div class="card ">
                        <div class="card-body">
                            <div class="counter-status d-flex md-mb-0">
                                <div class="counter-icon bg-info-transparent">
                                    <i class="icon-credit-card text-info"></i>
                                </div>
                                <div class="mr-auto">
                                    <h5 class="tx-13">{{ __('dashboard.Online_Earnings') }}</h5>
                                    <h2 class="mb-0 tx-15 mb-1 mt-1">{{ number_format($onlineEarnings, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-xl-4 col-lg-6 col-md-6">
                    <div class="card ">
                        <div class="card-body">
                            <div class="counter-status d-flex md-mb-0">
                                <div class="counter-icon bg-danger-transparent">
                                    <i class="icon-rocket text-danger"></i>
                                </div>
                                <div class="mr-auto">
                                    <h5 class="tx-13">{{ __('dashboard.Total_Earnings') }}</h5>
                                    <h2 class="mb-0 tx-15 mb-1 mt-1">{{ number_format($totalEarnings, 2) }} {{ __('dashboard.SAR') }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-xl-4 col-lg-6 col-md-6">
                    <div class="card ">
                        <div class="card-body">
                            <div class="counter-status d-flex md-mb-0">
                                <div class="counter-icon bg-warning-transparent">
                                    <i class="fa fa-star text-warning"></i>
                                </div>
                                <div class="mr-auto">
                                    <h5 class="tx-13">{{ __('dashboard.Average_Rating') }}</h5>
                                    <h2 class="mb-0 tx-15 mb-1 mt-1">
                                        @if ($averageRating)
                                            {{ number_format($averageRating, 1) }} / 5
                                            <span class="text-muted tx-12">({{ $reviewsCount }})</span>
                                        @else
                                            {{ __('dashboard.No_Ratings_Yet') }}
                                        @endif
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card">
                <div class="card-body">
                    <div class="tabs-menu ">
                        <!-- Tabs -->
                        <ul class="nav nav-tabs profile navtab-custom panel-tabs">
                            <li class="active">
                                <a href="#home" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i
                                            class="las la-shopping-cart tx-20 mr-1"></i></span> <span
                                        class="hidden-xs">{{ __('dashboard.Orders') }}</span> </a>
                            </li>
                            <li class="">
                                <a href="#profile" data-toggle="tab" aria-expanded="false"> <span class="visible-xs"><i
                                            class="lar la-star tx-20 mr-1"></i></span> <span
                                        class="hidden-xs">{{ __('dashboard.Evaluations') }}</span> </a>
                            </li>

                        </ul>
                    </div>
                    <div class="tab-content border-left border-bottom border-right border-top-0 p-4">
                        <div class="tab-pane active" id="home">

                            <div class="product-details table-responsive text-nowrap">
                                <table class="table table-bordered table-hover mb-0 text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>{{ __('dashboard.Order_number') }}</th>
                                            <th>{{ __('dashboard.Order_Status') }}</th>
                                            <th>{{ __('dashboard.Delivery_Price') }}</th>
                                            <th>{{ __('dashboard.Out_For_Delivery_At') }}</th>
                                            <th>{{ __('dashboard.Delivered_At') }}</th>

                                            <th>{{ __('dashboard.Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($item->orders as $order)
                                            <tr id="tr_{{ $order->id }}">

                                                <td>{{ $order->order_number }}
                                                    @if ($order->read == '0')
                                                        <span class="badge bg-info me-1">{{ __('dashboard.New') }}</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    {{ $order->status->name }}
                                                </td>

                                                <td>{{ number_format($order->delivery_price, 2) }}</td>

                                                <td>
                                                    @if ($order->out_for_delivery_at)
                                                        <i class="fa fa-truck text-primary mr-1"></i>
                                                        {{ \Carbon\Carbon::parse($order->out_for_delivery_at)->format('Y-m-d h:i A') }}
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    @if ($order->delivered_at)
                                                        <i class="fa fa-check-circle text-success mr-1"></i>
                                                        {{ \Carbon\Carbon::parse($order->delivered_at)->format('Y-m-d h:i A') }}
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    <div class="d-flex my-xl-auto right-content">
                                                        <div class="pr-1 mb-3 mb-xl-0">
                                                            <a href="{{ route('orders.show', $order->id) }}"
                                                                class=" btn btn-success btn-icon ml-2" data-placement="top"
                                                                data-toggle="tooltip"
                                                                title="{{ __('dashboard.View') }} "><i
                                                                    class="las la-eye"></i></a>
                                                        </div>
                                                    </div>
                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">{{ __('dashboard.No_Data_in_this_category') }}</td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>

                            </div>

                        </div>
                        <div class="tab-pane" id="profile">
                            <div class="product-details table-responsive text-nowrap">
                                <table class="table table-bordered table-hover mb-0 text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>{{ __('dashboard.Order_number') }}</th>
                                            <th>{{ __('dashboard.Evaluation') }}</th>
                                            <th>{{ __('dashboard.Customer_Opinion') }}</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($reviews as $review)
                                            <tr style="text-align: center">

                                                <td>{{ $review->order?->order_number ?? $review->order_id }}</td>
                                                <td>


                                                    <div class="rating-stars  ">
                                                        <div class="rating-stars-container">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <div
                                                                    class="rating-star @if ($review->rating >= $i) is--active @else nostaractive @endif">
                                                                    <i class="fa fa-star"></i>
                                                                </div>
                                                            @endfor
                                                        </div>
                                                    </div>

                                                </td>

                                                <td>
                                                    {{ $review->comment }}

                                                </td>



                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">{{ __('dashboard.No_Ratings_Yet') }}</td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>

                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
@endsection
