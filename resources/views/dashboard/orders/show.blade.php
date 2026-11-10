@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.View_order_details') }} # {{ $item->order_number }}
@endsection
@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style type="text/css">
        #maporder {
            height: 450px;
            width: 100%;
            border-radius: 8px;
            z-index: 1;
        }
        #contentmap {
            font-size: 12pt;
            font-family: "Cairo", sans-serif;
        }
        .nostaractive { color: #e1e6f1 !important; }
        span#lg-share { display: none !important; }
        .cursorimg:hover { cursor: pointer; }
        .leaflet-popup-content-wrapper { direction: rtl; text-align: right; }
        .distance-badge {
            position: absolute; top: 10px; right: 10px; z-index: 1000;
            background: #fff; padding: 8px 16px; border-radius: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2); font-weight: 700;
            color: #333; font-size: 14px;
        }
        .distance-badge i { color: #0d6efd; margin-left: 5px; }
    </style>
    <link href="{{ URL::asset('assets/dashboard/plugins/gallery/gallery.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/dashboard/plugins/sweet-alert/sweetalert.css') }}" rel="stylesheet">
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('dashboard.View_order_details') }} # {{ $item->order_number }}</h4>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            {{-- <div class="mb-3 mb-xl-0 ml-3">
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#ModalChangeStatusOrder">
                    {{ __('dashboard.Change_Order_Status') }}
                </button>
            </div>
            <div class="mb-3 mb-xl-0 ml-3">
                <a href="{{ route($routeName . '.invoice', $item->id) }}" class="btn btn-dark">{{ __('dashboard.Bill') }}</a>
            </div> --}}
            <div class="pr-1 mb-3 mb-xl-0 ml-3">
                <a href="{{ route($routeName) }}" class="btn btn-info btn-icon"><i class="typcn typcn-arrow-back-outline"></i></a>
            </div>
        </div>
    </div>
@endsection
@section('content')
    @include('dashboard.includes.alerts.errors')
    @include('dashboard.includes.alerts.success')
    @include('dashboard.orders.modal.ModalChangeStatusOrder')

    @if ($item->delivery_type === 'delivery' && in_array($item->status_id, [3, 4]) && !$item->driver)
        @include('dashboard.orders.modal.ModalAssignDriver')
    @endif

    <div class="row row-sm">
        <div class="col-lg-4">
            {{-- ✅ الكارت الأول (بيانات العميل والفاتورة) --}}
            <div class="card mg-b-20">
                <div class="card-body">
                    <div class="pl-0">
                        <div class="main-profile-overview">
                            <div class="main-img-user profile-user">
                                <img alt="" src="{{ $item->user->image_url }}">
                            </div>
                            <div class="d-flex justify-content-between mg-b-5">
                                <div>
                                    <h5 class="main-profile-name">{{ $item->user->name }}</h5>
                                    <p class="main-profile-name-text" style="font-size: 15px;">
                                        <a href="javascript:void(0);" class="contact-icon border tx-inverse"><i class="fe fe-phone" style="color:green"></i></a>
                                        {{ $item->user->mobile }}
                                    </p>
                                </div>
                            </div><br />
                           @if ($item->note)
                                <div class="main-profile-bio">
                                    <h6>{{ __('dashboard.Note') }}</h6>
                                    {{ $item->note }}
                                </div>
                            @endif
                         @if ($item->delivery_date)
                            <h6>{{ __('dashboard.Delivery date') }} : {{ $item->delivery_date->format('Y-m-d') }}</h6>
                        @endif
                        @if ($item->delivery_time)
                            <h6>{{ __('dashboard.Delivery time') }} : {{ $item->delivery_time }}</h6>
                        @endif
                            <hr class="mg-y-10">
                            <div class="table-responsive mg-t-20">
                                <table class="table table-invoice border text-md-nowrap mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="tx-uppercase tx-bold tx-inverse">{{ __('dashboard.number_products') }}</td>
                                            <td colspan="2"><h4 class="tx-primary tx-bold">{{ $item->total_items }}</h4></td>
                                        </tr>
                                        <tr>
                                            <td class="tx-uppercase tx-bold tx-inverse">{{ __('dashboard.Total price before discount') }}</td>
                                            <td colspan="2"><h4 class="tx-primary tx-bold">{{ number_format($item->total_price, 2) }}</h4></td>
                                        </tr>
                                        @if ($item->discount_amount > 0)
                                            <tr>
                                                <td class="tx-uppercase tx-bold tx-inverse">
                                                    {{ __('dashboard.Coupon_Discount_Value') }}
                                                    @if ($item->coupon)
                                                        <br /><small>{{ $item->coupon->code }} ({{ $item->coupon->discount_percentage }}%)</small>
                                                    @endif
                                                </td>
                                                <td colspan="2"><h4 class="tx-danger tx-bold">- {{ number_format($item->discount_amount, 2) }}</h4></td>
                                            </tr>
                                            <tr>
                                                <td class="tx-uppercase tx-bold tx-inverse">{{ __('dashboard.Price after discount') }}</td>
                                                <td colspan="2"><h4 class="tx-primary tx-bold">{{ number_format($item->price_after_discount, 2) }}</h4></td>
                                            </tr>
                                        @endif
                                        @if ($item->added_tax)
                                            <tr>
                                                <td class="tx-uppercase tx-bold tx-inverse">{{ __('dashboard.added_tax') }} ({{ $item->added_tax }}%)</td>
                                                <td colspan="2"><h4 class="tx-primary tx-bold">{{ number_format($item->tax_amount, 2) }}</h4></td>
                                            </tr>
                                        @endif
                                        @if ($item->delivery_type === 'delivery' && $item->delivery_price)
                                            <tr>
                                                <td class="tx-uppercase tx-bold tx-inverse">{{ __('dashboard.Delivery_Price') }}</td>
                                                <td colspan="2"><h4 class="tx-primary tx-bold">{{ number_format($item->delivery_price, 2) }}</h4></td>
                                            </tr>
                                        @endif
                                        @if ($item->redeemed_points > 0)
                                            <tr>
                                                <td class="tx-uppercase tx-bold tx-inverse">{{ __('dashboard.redeem_points') }} ({{ $item->redeemed_points }})</td>
                                                <td colspan="2"><h4 class="tx-danger tx-bold">- {{ number_format($item->redeemed_points_value, 2) }}</h4></td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="tx-uppercase tx-bold tx-inverse">{{ __('dashboard.TOTAL_DUE') }}</td>
                                            <td colspan="2"><h4 class="tx-success tx-bold">{{ number_format($item->final_price, 2) }} {{ $item->currency }}</h4></td>
                                        </tr>

                                        {{-- ✅ التفاصيل المالية دائماً تظهر (بدون شرط > 0) --}}
                                        <tr style="background:#f8f9fa;"><td colspan="3"><strong>{{ __('dashboard.Financial_Details') }}</strong></td></tr>
                                        <tr>
                                            <td class="tx-uppercase tx-bold tx-info">{{ __('dashboard.Deposit_Amount') }} ({{ __('dashboard.Paid') }})</td>
                                            <td colspan="2"><h5 class="tx-info">- {{ number_format($item->deposit_amount ?? 0, 2) }} {{ $item->currency }}</h5></td>
                                        </tr>
                                        <tr>
                                            <td class="tx-uppercase tx-bold tx-warning">{{ __('dashboard.Remaining_Amount') }}</td>
                                            <td colspan="2"><h5 class="tx-warning">{{ number_format($item->remaining_amount ?? 0, 2) }} {{ $item->currency }}</h5></td>
                                        </tr>
                                        <tr>
                                            <td class="tx-uppercase tx-bold tx-secondary">{{ __('dashboard.Slaughter_Service_Total') }}</td>
                                            <td colspan="2"><h5 class="tx-secondary">+ {{ number_format($item->slaughter_total ?? 0, 2) }} {{ $item->currency }}</h5></td>
                                        </tr>
                                        <tr>
                                            <td class="tx-uppercase tx-bold tx-danger">{{ __('dashboard.Commission_Trader') }}</td>
                                            <td colspan="2"><h5 class="tx-danger">- {{ number_format($item->commission_trader ?? 0, 2) }} {{ $item->currency }}</h5></td>
                                        </tr>
                                        <tr>
                                            <td class="tx-uppercase tx-bold tx-success">{{ __('dashboard.Commission_Driver') }}</td>
                                            <td colspan="2"><h5 class="tx-success">+ {{ number_format($item->commission_driver ?? 0, 2) }} {{ $item->currency }}</h5></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ كارت الـ Timeline --}}
            <div class="card mg-b-20">
                <div class="card-header">
                    <h4 class="card-title mg-b-0">{{ __('dashboard.Order_Timeline') }}</h4>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex align-items-center">
                            <div class="ml-3" style="width:12px;height:12px;border-radius:50%;background:#036fe7;flex-shrink:0;"></div>
                            <div class="flex-grow-1">
                                <div class="font-weight-semibold">{{ __('dashboard.Order_Created_At') }}</div>
                                <small class="text-muted">{{ $item->created_at->format('Y-m-d h:i A') }}</small>
                            </div>
                            <i class="fa fa-file-alt text-primary"></i>
                        </li>
                        @if ($item->prepared_at)
                            <li class="list-group-item d-flex align-items-center">
                                <div class="ml-3" style="width:12px;height:12px;border-radius:50%;background:#00797d;flex-shrink:0;"></div>
                                <div class="flex-grow-1">
                                    <div class="font-weight-semibold">{{ __('dashboard.Prepared_At') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($item->prepared_at)->format('Y-m-d h:i A') }}</small>
                                </div>
                                <i class="fa fa-box text-info"></i>
                            </li>
                        @endif
                        @if ($item->out_for_delivery_at)
                            <li class="list-group-item d-flex align-items-center">
                                <div class="ml-3" style="width:12px;height:12px;border-radius:50%;background:#7b61ff;flex-shrink:0;"></div>
                                <div class="flex-grow-1">
                                    <div class="font-weight-semibold">{{ __('dashboard.Out_For_Delivery_At') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($item->out_for_delivery_at)->format('Y-m-d h:i A') }}</small>
                                </div>
                                <i class="fa fa-truck text-warning"></i>
                            </li>
                        @endif
                        @if ($item->delivered_at)
                            <li class="list-group-item d-flex align-items-center">
                                <div class="ml-3" style="width:12px;height:12px;border-radius:50%;background:#28a745;flex-shrink:0;"></div>
                                <div class="flex-grow-1">
                                    <div class="font-weight-semibold">{{ __('dashboard.Delivered_At') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($item->delivered_at)->format('Y-m-d h:i A') }}</small>
                                </div>
                                <i class="fa fa-check-circle text-success"></i>
                            </li>
                        @endif
                        @if ($item->status_id == 6)
                            <li class="list-group-item d-flex align-items-center">
                                <div class="ml-3" style="width:12px;height:12px;border-radius:50%;background:#dc3545;flex-shrink:0;"></div>
                                <div class="flex-grow-1">
                                    <div class="font-weight-semibold">{{ __('dashboard.Order_Cancelled_At') }}</div>
                                    <small class="text-muted">{{ $item->updated_at->format('Y-m-d h:i A') }}</small>
                                </div>
                                <i class="fa fa-times-circle text-danger"></i>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
           <div class="row row-sm">
                <div class="col-sm-12 col-xl-4 col-lg-12 col-md-12">
                    <div class="card"><div class="card-body"><div class="counter-status d-flex md-mb-0">
                        <div class="counter-icon bg-primary-transparent"><i class="icon-clock text-primary"></i></div>
                        <div class="mr-auto">
                            <h2 class="tx-14" style="font-weight:bold">{{ __('dashboard.Created_at') }}</h2>
                            <h5 class="tx-15 mb-1 mt-1">{{ $item->created_at->format('d/m/Y') }}<br />{{ $item->created_at->format('h:i:s A') }}</h5>
                        </div>
                    </div></div></div>
                </div>
                <div class="col-sm-12 col-xl-4 col-lg-12 col-md-12">
                    <div class="card"><div class="card-body"><div class="counter-status d-flex md-mb-0">
                        <div class="counter-icon bg-danger-transparent"><i class="icon-rocket text-success"></i></div>
                        <div class="mr-auto">
                            <h2 class="tx-14 mt-2" style="font-weight:bold">{{ __('dashboard.Order_Status') }}</h2>
                            <h5 class="tx-15 mb-1 mt-1">{{ $item->status->name }}
                                @if ($item->status_id == 6 && $item->cancel_reason_id)
                                    <br />{{ $item->cancelReason->name }}
                                    @if ($item->cancel_other_reason): {{ $item->cancel_other_reason }}@endif
                                @endif
                            </h5>
                        </div>
                    </div></div></div>
                </div>
                @if ($item->payment_method !== 'cash' && ($item->payment_gateway_code || $item->payment_reference))
                    <div class="col-sm-12 col-xl-4 col-lg-12 col-md-12">
                        <div class="card"><div class="card-body"><div class="counter-status d-flex md-mb-0">
                            <div class="counter-icon bg-success-transparent"><i class="icon-paypal text-danger"></i></div>
                            <div class="mr-auto">
                                <h2 class="tx-14 mt-2" style="font-weight:bold">{{ __('dashboard.Payment_Method') }}</h2>
                                <h5 class="tx-15 mb-1 mt-1">
                                    @if ($item->payment_gateway_code){{ ucfirst($item->payment_gateway_code) }}<br />@endif
                                    @if ($item->payment_reference)<small class="text-muted">{{ $item->payment_reference }}</small>@endif
                                </h5>
                            </div>
                        </div></div></div>
                    </div>
                @endif
            </div>

            <div class="row row-sm">
                @if ($item->payment_status)
                    <div class="col-sm-12 col-xl-3 col-lg-6 col-md-6">
                        <div class="card"><div class="card-body"><div class="counter-status d-flex md-mb-0">
                            <div class="counter-icon bg-warning-transparent"><i class="icon-credit-card text-warning"></i></div>
                            <div class="mr-auto">
                                <h2 class="tx-14" style="font-weight:bold">{{ __('dashboard.Payment_Status') }}</h2>
                                <h5 class="tx-15 mb-1 mt-1">{{ $item->payment_status_label }}</h5>
                            </div>
                        </div></div></div>
                    </div>
                @endif
                @if ($item->delivery_type)
                    <div class="col-sm-12 col-xl-3 col-lg-6 col-md-6">
                        <div class="card"><div class="card-body"><div class="counter-status d-flex md-mb-0">
                            <div class="counter-icon bg-info-transparent"><i class="icon-basket text-info"></i></div>
                            <div class="mr-auto">
                                <h2 class="tx-14" style="font-weight:bold">{{ __('dashboard.Delivery_Type') }}</h2>
                                <h5 class="tx-15 mb-1 mt-1">{{ $item->delivery_type_label }}</h5>
                            </div>
                        </div></div></div>
                    </div>
                @endif
                @if ($item->delivery_type === 'delivery' && $item->delivery_price)
                    <div class="col-sm-12 col-xl-3 col-lg-6 col-md-6">
                        <div class="card"><div class="card-body"><div class="counter-status d-flex md-mb-0">
                            <div class="counter-icon bg-success-transparent"><i class="fa fa-truck text-success"></i></div>
                            <div class="mr-auto">
                                <h2 class="tx-14" style="font-weight:bold">{{ __('dashboard.Delivery_Price') }}</h2>
                                <h5 class="tx-15 mb-1 mt-1">{{ number_format($item->delivery_price, 2) }}</h5>
                            </div>
                        </div></div></div>
                    </div>
                @endif
                @if ($item->payment_method)
                    <div class="col-sm-12 col-xl-3 col-lg-6 col-md-6">
                        <div class="card"><div class="card-body"><div class="counter-status d-flex md-mb-0">
                            <div class="counter-icon bg-primary-transparent"><i class="icon-wallet text-primary"></i></div>
                            <div class="mr-auto">
                                <h2 class="tx-14" style="font-weight:bold">{{ __('dashboard.Payment_Method') }}</h2>
                                <h5 class="tx-15 mb-1 mt-1">{{ $item->payment_method_label }}</h5>
                            </div>
                        </div></div></div>
                    </div>
                @endif
            </div>

            {{-- ✅ كارت معلومات التاجر --}}
            @if($item->trader)
            <div class="card mg-b-20">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg brround mr-3" style="background:#e7f5ff;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-store" style="font-size:22px;color:#0d6efd;"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 tx-13 text-muted">{{ __('dashboard.Trader_Name') }}</h6>
                            <h5 class="mb-0 font-weight-bold">
                                <a href="{{ route('traders.show', $item->trader->id) }}" class="text-info">{{ $item->trader->name }}</a>
                            </h5>
                            <small class="text-muted"><i class="fa fa-map-marker-alt mr-1"></i>{{ $item->trader->store_address ?? '—' }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if ($item->delivery_type === 'delivery')
                <div class="card mg-b-20">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-lg brround mr-3" style="background: {{ $item->driver ? '#e9fbf0' : '#fff4e5' }}; display:flex;align-items:center;justify-content:center;">
                                    <i class="fa fa-motorcycle" style="font-size:22px; color: {{ $item->driver ? '#198754' : '#ff7a00' }};"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 tx-13 text-muted">{{ __('dashboard.Driver') }}</h6>
                                    @if ($item->driver)
                                        <h5 class="mb-0 font-weight-bold">{{ $item->driver->name }}</h5>
                                        <small class="text-muted"><i class="fa fa-phone-alt mr-1"></i>{{ $item->driver->mobile }}</small>
                                    @else
                                        <h5 class="mb-0 text-warning">{{ __('dashboard.No_Driver_Assigned') }}</h5>
                                    @endif
                                </div>
                            </div>
                            @if (in_array($item->status_id, [3, 4]))
                                <div class="mt-2 mt-md-0">
                                    @if ($item->driver && $item->status_id == 4)
                                        <form method="POST" action="{{ route($routeName . '.unassign_driver') }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد؟')">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $item->id }}">
                                            <button type="submit" class="btn btn-outline-danger">{{ __('dashboard.Unassign_Driver') }}</button>
                                        </form>
                                    @endif
                                    @if (!$item->driver)
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalAssignDriver">{{ __('dashboard.Assign_Driver') }}</button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="tabs-menu">
                        <ul class="nav nav-tabs profile navtab-custom panel-tabs">
                            <li class="active">
                                <a href="#home" data-toggle="tab" aria-expanded="true">
                                    <span class="visible-xs"><i class="las la-shopping-cart tx-20 mr-1"></i></span>
                                    <span class="hidden-xs">{{ __('dashboard.Order_Products') }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="#maptab" data-toggle="tab" aria-expanded="false">
                                    <span class="visible-xs"><i class="fa fa-map-marked-alt tx-20 mr-1"></i></span>
                                    <span class="hidden-xs">{{ __('dashboard.Location_and_Distance') }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="#traderRating" data-toggle="tab" aria-expanded="false">
                                    <span class="visible-xs"><i class="fa fa-store tx-20 mr-1"></i></span>
                                    <span class="hidden-xs">{{ __('dashboard.Trader_Rating') }}</span>
                                </a>
                            </li>
                            @if ($item->delivery_type === 'delivery')
                                <li>
                                    <a href="#driverrating" data-toggle="tab" aria-expanded="false">
                                        <span class="visible-xs"><i class="las la-id-badge tx-20 mr-1"></i></span>
                                        <span class="hidden-xs">{{ __('dashboard.Driver_Ratings') }}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="tab-content border-left border-bottom border-right border-top-0 p-4">
                        <div class="tab-pane active" id="home">
                            <div class="product-details table-responsive text-nowrap">
                                <table class="table table-bordered table-hover mb-0 text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>{{ __('dashboard.Product') }}</th>
                                            <th class="w-150">{{ __('dashboard.Quantity') }}</th>
                                            <th>{{ __('dashboard.Price') }}</th>
                                            <th>{{ __('dashboard.Total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item->items as $order_item)
                                            <tr>
                                                <td>
                                                    <div class="media">
                                                        <div class="card-aside-img">
                                                            <img src="@if (empty($order_item->product->image)) {{ URL::asset('assets/dashboard/img/faces/6.jpg') }}@else{{ URL::asset($order_item->product->base_image_url) }}@endif" alt="img" class="h-60 w-60">
                                                        </div>
                                                        <div class="media-body">
                                                            @include('dashboard.orders.partials.item_details', ['order_item' => $order_item])
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><div class="form-group" style="text-align: center">{{ $order_item->quantity }}</div></td>
                                                <td class="text-center text-lg text-medium">{{ $order_item->unit_price }}</td>
                                                <td class="text-center text-lg text-medium">{{ $order_item->total_price }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- ✅ الخريطة الموحدة --}}
                        <div class="tab-pane" id="maptab">
                            <div style="position: relative;">
                                <div id="maporder"></div>
                                <div class="distance-badge" id="distanceBadge" style="display: none;">
                                    <i class="fa fa-route"></i>
                                    <span id="distanceText"></span>
                                </div>
                            </div>
                        </div>

                        {{-- ✅ تقييم التاجر --}}
                        <div class="tab-pane" id="traderRating">
                            @if ($item->review)
                                <div class="rating-stars">
                                    <div class="rating-stars-container">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <div class="rating-star @if ($item->review->rating >= $i) is--active @else nostaractive @endif">
                                                <i class="fa fa-star"></i>
                                            </div>
                                        @endfor
                                    </div>
                                </div><br />
                                <p class="m-b-5" style="text-align: center">{{ $item->review->comment }}</p>
                            @else
                                <div class="alert alert-info" role="alert">{{ __('dashboard.The_order_not_evaluated') }}</div>
                            @endif
                        </div>

                        @if ($item->delivery_type === 'delivery')
                            <div class="tab-pane" id="driverrating">
                                @if ($item->driverReview)
                                    <div class="rating-stars">
                                        <div class="rating-stars-container">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <div class="rating-star @if ($item->driverReview->rating >= $i) is--active @else nostaractive @endif">
                                                    <i class="fa fa-star"></i>
                                                </div>
                                            @endfor
                                        </div>
                                    </div><br />
                                    <p class="m-b-5" style="text-align: center">{{ $item->driverReview->comment }}</p>
                                @else
                                    <div class="alert alert-info" role="alert">{{ __('dashboard.No_Driver_Ratings_Yet') }}</div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@php
    // ✅ إحداثيات العميل
    $addressModel = $item->getRelation('address');
    $mapLat = ($addressModel && $addressModel->latitude) ? $addressModel->latitude : ($item->latitude ?? 0);
    $mapLng = ($addressModel && $addressModel->longitude) ? $addressModel->longitude : ($item->longitude ?? 0);
    $mapAddressText = ($addressModel && $addressModel->address) ? $addressModel->address : ($item->address ?? '');

    // ✅ إحداثيات التاجر (بأمان تام)
    $traderLat = $item->trader?->store_lat ?? null;
    $traderLng = $item->trader?->store_lng ?? null;
    $traderAddressText = $item->trader?->store_address ?? '';
    $hasTraderLocation = ($traderLat !== null && $traderLng !== null);
@endphp

@section('js')
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/i18n/' . app()->getLocale() . '.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/i18n/' . app()->getLocale() . '.js') }}"></script>

    {{-- ✅ Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script type="text/javascript">
        let mapOrder = null;

        // ✅ حساب المسافة (Haversine)
        function calculateDistance(lat1, lng1, lat2, lng2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLng/2) * Math.sin(dLng/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        // ✅ إنشاء الخريطة
        function initUnifiedMap() {
            if (mapOrder) return; // لو الخريطة موجودة ما نعملها من جديد

            const clientLat = {{ $mapLat }};
            const clientLng = {{ $mapLng }};
            const clientAddressText = @json($mapAddressText);

            // ✅ إحداثيات التاجر (آمنة)
            const hasTrader = {{ $hasTraderLocation ? 'true' : 'false' }};
            const traderLat = {{ $traderLat ?? 0 }};
            const traderLng = {{ $traderLng ?? 0 }};
            const traderAddress = @json($traderAddressText);

            let centerLat = clientLat;
            let centerLng = clientLng;
            let zoom = 15;

            if (hasTrader) {
                centerLat = (clientLat + traderLat) / 2;
                centerLng = (clientLng + traderLng) / 2;
                const distance = calculateDistance(traderLat, traderLng, clientLat, clientLng);
                if (distance > 50) zoom = 9;
                else if (distance > 20) zoom = 10;
                else if (distance > 10) zoom = 11;
                else if (distance > 5) zoom = 12;
                else if (distance > 2) zoom = 13;
                else zoom = 14;

                document.getElementById('distanceText').innerText = distance.toFixed(2) + ' كم';
                document.getElementById('distanceBadge').style.display = 'block';
            }

            // ✅ إنشاء الخريطة
            mapOrder = L.map('maporder').setView([centerLat, centerLng], zoom);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(mapOrder);

            // ✅ دبوس العميل
            const clientMarker = L.marker([clientLat, clientLng]).addTo(mapOrder);
            clientMarker.bindPopup(`<div id="contentmap"><strong>📍 موقع العميل</strong><br>${clientAddressText}</div>`).openPopup();

            // ✅ دبوس التاجر + خط + مسافة
            if (hasTrader) {
                const traderIcon = L.divIcon({
                    className: 'custom-trader-icon',
                    html: '<div style="background:#0d6efd;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.3);"><i class="fa fa-store" style="color:#fff;font-size:14px;"></i></div>',
                    iconSize: [32, 32],
                    iconAnchor: [16, 16]
                });

                const traderMarker = L.marker([traderLat, traderLng], { icon: traderIcon }).addTo(mapOrder);
                traderMarker.bindPopup(`<div id="contentmap"><strong>🏪 متجر التاجر</strong><br>${traderAddress}</div>`);

                // خط بينهما
                L.polyline([[traderLat, traderLng], [clientLat, clientLng]], {
                    color: '#0d6efd', weight: 3, opacity: 0.6, dashArray: '10, 10'
                }).addTo(mapOrder);

                // popup في المنتصف
                const midLat = (traderLat + clientLat) / 2;
                const midLng = (traderLng + clientLng) / 2;
                const distance = calculateDistance(traderLat, traderLng, clientLat, clientLng);
                L.popup({ closeButton: false, autoClose: false })
                    .setLatLng([midLat, midLng])
                    .setContent(`<div style="text-align:center;font-weight:700;color:#0d6efd;"><i class="fa fa-route"></i> ${distance.toFixed(2)} كم</div>`)
                    .openOn(mapOrder);
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr("href");
                if (target === '#maptab') {
                    setTimeout(function() {
                        initUnifiedMap();
                        if (mapOrder) mapOrder.invalidateSize();
                    }, 100);
                }
            });
        });

        $(document).ready(function() {
            $(function() {
                if ($('#ModalChangeStatusOrdervalid').length > 0) {
                    $('#modal_changeorder_submit_button').click(function(event) {
                        $('#ModalChangeStatusOrdervalid select').each(function() {
                            if ($(this).parsley().validate() !== true) event.preventDefault();
                        })
                    });
                }
            });
            @if ($errors->ModalChangeStatusOrderbag->any())
                $('#ModalChangeStatusOrder').modal({ show: true });
            @endif
            @if ($errors->AssignDriverBag->any())
                $('#ModalAssignDriver').modal({ show: true });
            @endif
        });
    </script>
    <script src="{{ URL::asset('assets/dashboard/plugins/gallery/lightgallery-all.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/gallery/jquery.mousewheel.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/js/gallery.js') }}"></script>
@endsection
