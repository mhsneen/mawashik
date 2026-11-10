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
                                    <p class="main-profile-name-text" style="direction: ltr">{{ $item->mobile }}</p>

                                </div>
                            </div>

                            <h6>{{ __('dashboard.subscribed_since') }} : {{ $item->created_at->diffForHumans() }}</h6>

                            {{-- ✅ حالة العميل (فعّال/محظور) --}}
                            <div class="mt-3 mb-3">
                                <span class="text-muted d-block tx-12 mb-1">{{ __('dashboard.Client_Status') }}</span>
                                @if ($item->is_blocked)
                                    <span class="badge badge-danger tx-13 pd-8">
                                        <i class="fa fa-ban mr-1"></i>{{ __('dashboard.Blocked') }}
                                    </span>
                                @elseif ($item->is_active)
                                    <span class="badge badge-success tx-13 pd-8">
                                        <i class="fa fa-check-circle mr-1"></i>{{ __('dashboard.Active') }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary tx-13 pd-8">
                                        <i class="fa fa-pause mr-1"></i>{{ __('dashboard.Inactive') }}
                                    </span>
                                @endif
                            </div>

                            {{-- ✅ البريد الإلكتروني --}}
                            @if (!empty($item->email))
                            <div class="mb-2">
                                <span class="text-muted d-block tx-12 mb-1">{{ __('dashboard.Email') }}</span>
                                <span class="tx-13" dir="ltr">{{ $item->email }}</span>
                            </div>
                            @endif

                            {{-- ✅ الموافقة على الشروط --}}
                            <div class="mb-3">
                                <span class="text-muted d-block tx-12 mb-1">{{ __('dashboard.Terms_Accepted') }}</span>
                                @if ($item->terms_accepted ?? false)
                                    <span class="badge badge-success tx-13 pd-8"><i class="fa fa-check mr-1"></i>{{ __('dashboard.Yes') }}</span>
                                @else
                                    <span class="badge badge-danger tx-13 pd-8"><i class="fa fa-times mr-1"></i>{{ __('dashboard.No') }}</span>
                                @endif
                            </div>

                            {{-- ✅ الموقع الجغرافي --}}
                            @if ($item->city || $item->area || $item->neighborhood)
                            <div class="mb-3">
                                <span class="text-muted d-block tx-12 mb-1">{{ __('dashboard.Location') }}</span>
                                <span class="tx-13">
                                    @if ($item->neighborhood)
                                        {{ $item->neighborhood?->translate(app()->getLocale())?->name }}،
                                    @endif
                                    @if ($item->area)
                                        {{ $item->area?->translate(app()->getLocale())?->name }}،
                                    @endif
                                    @if ($item->city)
                                        {{ $item->city?->translate(app()->getLocale())?->name }}
                                    @endif
                                </span>
                            </div>
                            @endif

                        </div><!-- main-profile-overview -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="row row-sm">




                <div class="col-sm-12 col-xl-4 col-lg-12 col-md-12">
                    <div class="card ">
                        <div class="card-body">
                            <div class="counter-status d-flex md-mb-0">
                                <div class="counter-icon bg-primary-transparent">
                                    <i class="icon-layers text-primary"></i>
                                </div>
                                <div class="mr-auto">
                                    <h5 class="tx-13">{{ __('dashboard.number_of_orders') }}</h5>
                                    <h2 class="mb-0 tx-15 mb-1 mt-1">{{ $item->orders_count }} </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-xl-4 col-lg-12 col-md-12">
                    <div class="card ">
                        <div class="card-body">
                            <div class="counter-status d-flex md-mb-0">
                                <div class="counter-icon bg-success-transparent">
                                    <i class="icon-rocket text-success"></i>
                                </div>
                                <div class="mr-auto">
                                    <h5 class="tx-13">{{ __('dashboard.Total_Purchases') }}</h5>
                                    <h2 class="mb-0 tx-15 mb-1 mt-1"> {{ number_format($totalCompletedOrdersPrice, 2) }} SAR </h2>
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
                            <li class="">
                                <a href="#driverratings" data-toggle="tab" aria-expanded="false"> <span class="visible-xs"><i
                                            class="las la-id-badge tx-20 mr-1"></i></span> <span
                                        class="hidden-xs">{{ __('dashboard.Driver_Ratings') }}</span> </a>
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
                                            <th>{{ __('dashboard.Created_at') }}</th>
                                            <th>{{ __('dashboard.number_products') }}</th>

                                            <th>{{ __('dashboard.TOTAL_DUE') }}</th>


                                            <th>{{ __('dashboard.Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item->orders as $order)
                                            <tr id="tr_{{ $order->id }}">

                                                <td>{{ $order->order_number }}
                                                    @if ($order->read == '0')
                                                        <span class="badge bg-info me-1">{{ __('dashboard.New') }}</span>
                                                    @endif

                                                </td>

                                                <td>
                                                    {{ $order->status->name }}

                                                </td>


                                                <td>{{ $order->created_at->diffForHumans() }}</td>

                                                <td>{{ $order->total_items }}</td>
                                                <td>{{ $order->final_price }}</td>


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
                                        @endforeach

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
                                                            <div
                                                                class="rating-star @if ($review->rating >= 1) is--active @else nostaractive @endif">
                                                                <i class="fa fa-star"></i>
                                                            </div>
                                                            <div
                                                                class="rating-star @if ($review->rating >= 2) is--active @else nostaractive @endif">
                                                                <i class="fa fa-star"></i>
                                                            </div>
                                                            <div
                                                                class="rating-star @if ($review->rating >= 3) is--active @else nostaractive @endif">
                                                                <i class="fa fa-star"></i>
                                                            </div>
                                                            <div
                                                                class="rating-star @if ($review->rating >= 4) is--active @else nostaractive @endif">
                                                                <i class="fa fa-star"></i>
                                                            </div>
                                                            <div
                                                                class="rating-star @if ($review->rating >= 5) is--active @else nostaractive @endif">
                                                                <i class="fa fa-star"></i>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>

                                                <td>
                                                    {{ $review->comment }}

                                                </td>



                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">{{ __('dashboard.The_order_not_evaluated') }}</td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>

                            </div>
                        </div>

                        <div class="tab-pane" id="driverratings">
                            <div class="product-details table-responsive text-nowrap">
                                <table class="table table-bordered table-hover mb-0 text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>{{ __('dashboard.Order_number') }}</th>
                                            <th>{{ __('dashboard.Driver') }}</th>
                                            <th>{{ __('dashboard.Evaluation') }}</th>
                                            <th>{{ __('dashboard.Customer_Opinion') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($driverReviews as $dReview)
                                            <tr style="text-align: center">
                                                <td>{{ $dReview->order?->order_number ?? $dReview->order_id }}</td>
                                                <td>{{ $dReview->driver->name ?? '-' }}</td>
                                                <td>
                                                    <div class="rating-stars  ">
                                                        <div class="rating-stars-container">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <div class="rating-star @if ($dReview->rating >= $i) is--active @else nostaractive @endif">
                                                                    <i class="fa fa-star"></i>
                                                                </div>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $dReview->comment }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">{{ __('dashboard.No_Driver_Ratings_Yet') }}</td>
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
