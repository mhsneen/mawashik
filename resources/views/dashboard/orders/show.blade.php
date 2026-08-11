@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.View_order_details') }} # {{ $item->order_number }}
@endsection
@section('css')
    <style type="text/css">
        #maporder {
            height: 400px;
            width: 100%;
        }

        #contentmap {
            font-size: 12pt;
            font-family: "Cairo", sans-serif;

        }

        .nostaractive {
            color: #e1e6f1 !important;
        }

        span#lg-share {
            display: none !important;
        }

        .cursorimg:hover {
            cursor: pointer;
        }
    </style>
    <!-- Internal Gallery css -->
    <link href="{{ URL::asset('assets/dashboard/plugins/gallery/gallery.css') }}" rel="stylesheet">

    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--- Internal Sweet-Alert css-->
    <link href="{{ URL::asset('assets/dashboard/plugins/sweet-alert/sweetalert.css') }}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('dashboard.View_order_details') }} # {{ $item->order_number }}
                </h4>
            </div>
        </div>

        <div class="d-flex my-xl-auto right-content">

            <div class="mb-3 mb-xl-0 ml-3">
                <div class="btn-group dropdown">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#ModalChangeStatusOrder"
                        data-placement="top" data-toggle="tooltip" title="{{ __('dashboard.Change_Order_Status') }} ">
                        {{ __('dashboard.Change_Order_Status') }}
                    </button>


                </div>
            </div>

            <div class="mb-3 mb-xl-0 ml-3">
                <div class="btn-group dropdown">
                    <a href="{{ route($routeName . '.invoice', $item->id) }}" class="btn btn-dark " data-placement="top"
                        data-toggle="tooltip" title="{{ __('dashboard.Bill') }} ">{{ __('dashboard.Bill') }}</a>


                </div>
            </div>

            <div class="pr-1 mb-3 mb-xl-0 ml-3">
                <a href="{{ route($routeName) }}" class="btn btn-info btn-icon"><i
                        class="typcn typcn-arrow-back-outline"></i></a>
            </div>


        </div>



    </div>
    <!-- /breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    @include('dashboard.includes.alerts.errors')
    @include('dashboard.includes.alerts.success')
    @include('dashboard.orders.modal.ModalChangeStatusOrder')

    {{-- ✅ إظهار مودال التعيين فقط إذا كان الطلب توصيل، في الحالة 3 أو 4، ولم يتم تعيين سائق --}}
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
                                    <p class="main-profile-name-text" style="font-size: 15px;"> <a
                                            href="javascript:void(0);" class="contact-icon border tx-inverse"
                                            data-bs-toggle="tooltip" title="" data-bs-original-title="Call"
                                            aria-label="Call"><i class="fe fe-phone" style="color:green"></i></a>
                                        {{ $item->user->mobile }} </p>
                                </div>
                            </div><br />
                           @if ($item->note)
                                <div class="main-profile-bio">
                                    <h6>{{ __('dashboard.Note') }}</h6>
                                    {{ $item->note }}
                                </div>
                            @endif<!-- main-profile-bio -->

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
                                                <td class="tx-uppercase tx-bold tx-inverse">
                                                    {{ __('dashboard.added_tax') }} ({{ $item->added_tax }}%)
                                                </td>
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

                                    </tbody>
                                </table>
                            </div>

                        </div><!-- main-profile-overview -->
                    </div><!-- pl-0 -->
                </div><!-- card-body -->
            </div><!-- ✅ تم إغلاق الكارت الأول بشكل صحيح -->

            {{-- ✅ كارت الـ Timeline (يبدأ كأخ للكارت الأول داخل نفس العمود) --}}
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
            </div><!-- ✅ نهاية كارت الـ Timeline -->

        </div><!-- ✅ إغلاق عمود col-lg-4 -->

        <div class="col-lg-8">
           <div class="row row-sm">
                <div class="col-sm-12 col-xl-4 col-lg-12 col-md-12">
                    <div class="card ">
                        <div class="card-body">
                            <div class="counter-status d-flex md-mb-0">
                                <div class="counter-icon bg-primary-transparent">
                                    <i class="icon-clock text-primary"></i>
                                </div>
                                <div class="mr-auto">
                                    <h2 class="tx-14 " style="font-weight:bold">{{ __('dashboard.Created_at') }}</h2>
                                    <h5 class=" tx-15 mb-1 mt-1">
                                        {{ $item->created_at->format('d/m/Y') }}<br />{{ $item->created_at->format('h:i:s A') }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-4 col-lg-12 col-md-12">
                    <div class="card ">
                        <div class="card-body">
                            <div class="counter-status d-flex md-mb-0">
                                <div class="counter-icon bg-danger-transparent">
                                    <i class="icon-rocket text-success"></i>
                                </div>
                                <div class="mr-auto">
                                    <h2 class="tx-14 mt-2" style="font-weight:bold">{{ __('dashboard.Order_Status') }}</h2>
                                    <h5 class=" tx-15 mb-1 mt-1">
                                        {{ $item->status->name }}
                                        @if ($item->status_id == 6 && $item->cancel_reason_id)
                                            <br />{{ $item->cancelReason->name }}
                                            @if ($item->cancel_other_reason)
                                                : {{ $item->cancel_other_reason }}
                                            @endif
                                        @endif
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($item->payment_method !== 'cash' && ($item->payment_gateway_code || $item->payment_reference))
                    <div class="col-sm-12 col-xl-4 col-lg-12 col-md-12">
                        <div class="card ">
                            <div class="card-body">
                                <div class="counter-status d-flex md-mb-0">
                                    <div class="counter-icon bg-success-transparent">
                                        <i class="icon-paypal text-danger"></i>
                                    </div>
                                    <div class="mr-auto">
                                        <h2 class="tx-14 mt-2" style="font-weight:bold">{{ __('dashboard.Payment_Method') }}</h2>
                                        <h5 class=" tx-15 mb-1 mt-1">
                                            @if ($item->payment_gateway_code)
                                                {{ ucfirst($item->payment_gateway_code) }}<br />
                                            @endif
                                            @if ($item->payment_reference)
                                                <small class="text-muted">{{ $item->payment_reference }}</small>
                                            @endif
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ✅ الصف الجديد: حالة الدفع / نوع الاستلام / سعر التوصيل / طريقة الدفع --}}
            <div class="row row-sm">
                @if ($item->payment_status)
                    <div class="col-sm-12 col-xl-3 col-lg-6 col-md-6">
                        <div class="card ">
                            <div class="card-body">
                                <div class="counter-status d-flex md-mb-0">
                                    <div class="counter-icon bg-warning-transparent">
                                        <i class="icon-credit-card text-warning"></i>
                                    </div>
                                    <div class="mr-auto">
                                        <h2 class="tx-14" style="font-weight:bold">{{ __('dashboard.Payment_Status') }}</h2>
                                        <h5 class="tx-15 mb-1 mt-1">{{ $item->payment_status_label }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($item->delivery_type)
                    <div class="col-sm-12 col-xl-3 col-lg-6 col-md-6">
                        <div class="card ">
                            <div class="card-body">
                                <div class="counter-status d-flex md-mb-0">
                                    <div class="counter-icon bg-info-transparent">
                                        <i class="icon-basket text-info"></i>
                                    </div>
                                    <div class="mr-auto">
                                        <h2 class="tx-14" style="font-weight:bold">{{ __('dashboard.Delivery_Type') }}</h2>
                                        <h5 class="tx-15 mb-1 mt-1">{{ $item->delivery_type_label }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($item->delivery_type === 'delivery' && $item->delivery_price)
                    <div class="col-sm-12 col-xl-3 col-lg-6 col-md-6">
                        <div class="card ">
                            <div class="card-body">
                                <div class="counter-status d-flex md-mb-0">
                                    <div class="counter-icon bg-success-transparent">
                                      <i class="fa fa-truck text-success"></i>
                                    </div>
                                    <div class="mr-auto">
                                        <h2 class="tx-14" style="font-weight:bold">{{ __('dashboard.Delivery_Price') }}</h2>
                                        <h5 class="tx-15 mb-1 mt-1">{{ number_format($item->delivery_price, 2) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($item->payment_method)
                    <div class="col-sm-12 col-xl-3 col-lg-6 col-md-6">
                        <div class="card ">
                            <div class="card-body">
                                <div class="counter-status d-flex md-mb-0">
                                    <div class="counter-icon bg-primary-transparent">
                                        <i class="icon-wallet text-primary"></i>
                                    </div>
                                    <div class="mr-auto">
                                        <h2 class="tx-14" style="font-weight:bold">{{ __('dashboard.Payment_Method') }}</h2>
                                        <h5 class="tx-15 mb-1 mt-1">{{ $item->payment_method_label }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if ($item->delivery_type === 'delivery')
                <div class="card mg-b-20">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-lg brround mr-3"
                                     style="background: {{ $item->driver ? '#e9fbf0' : '#fff4e5' }}; display:flex;align-items:center;justify-content:center;">
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
                                        <form method="POST" action="{{ route($routeName . '.unassign_driver') }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من إلغاء تعيين السائق؟')">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $item->id }}">
                                            <button type="submit" class="btn btn-outline-danger">
                                                {{ __('dashboard.Unassign_Driver') }}
                                            </button>
                                        </form>
                                    @endif

                                    @if (!$item->driver)
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalAssignDriver">
                                            {{ __('dashboard.Assign_Driver') }}
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="tabs-menu ">
                        <!-- Tabs -->
                       <ul class="nav nav-tabs profile navtab-custom panel-tabs">
                            <li class="active">
                                <a href="#home" data-toggle="tab" aria-expanded="true">
                                    <span class="visible-xs"><i class="las la-shopping-cart tx-20 mr-1"></i></span>
                                    <span class="hidden-xs">{{ __('dashboard.Order_Products') }}</span>
                                </a>
                            </li>
                            @if ($item->delivery_type === 'delivery')
                                <li class="">
                                    <a href="#profile" data-toggle="tab" aria-expanded="false">
                                        <span class="visible-xs"><i class="las la-map-marker-alt tx-20 mr-1"></i></span>
                                        <span class="hidden-xs">{{ __('dashboard.Customer address') }}</span>
                                    </a>
                                </li>
                            @endif
                            <li class="">
                                <a href="#rating" data-toggle="tab" aria-expanded="false">
                                    <span class="visible-xs"><i class="lar la-star tx-20 mr-1"></i></span>
                                    <span class="hidden-xs">{{ __('dashboard.Evaluation') }}</span>
                                </a>
                            </li>
                            @if ($item->delivery_type === 'delivery')
                                <li class="">
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
                            <!-- Shopping Cart-->
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
                                                            <img src="@if (empty($order_item->product->image)) {{ URL::asset('assets/dashboard/img/faces/6.jpg') }}
                         @else
                            {{ URL::asset($order_item->product->base_image_url) }} @endif"
                                                                alt="img" class="h-60 w-60">
                                                        </div>
                                                        <div class="media-body">
                                                            @include('dashboard.orders.partials.item_details', ['order_item' => $order_item])
                                                        </div>                                        </div>
                                                </td>

                                                <td>
                                                    <div class="form-group" style="text-align: center">
                                                        {{ $order_item->quantity }}
                                                    </div>
                                                </td>
                                                <td class="text-center text-lg text-medium">{{ $order_item->unit_price }}
                                                </td>
                                                <td class="text-center text-lg text-medium">{{ $order_item->total_price }}
                                                </td>
                                            </tr>
                                        @endforeach


                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($item->delivery_type === 'delivery')
                            <div class="tab-pane" id="profile">
                                <div class="row">
                                    <div id="maporder"></div>
                                </div>
                            </div>
                        @endif

                        <div class="tab-pane" id="rating">

                            @if ($item->review)
                                <div class="rating-stars  ">
                                    <div class="rating-stars-container">
                                        <div
                                            class="rating-star @if ($item->review->rating >= 1) is--active @else nostaractive @endif">
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <div
                                            class="rating-star @if ($item->review->rating >= 2) is--active @else nostaractive @endif">
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <div
                                            class="rating-star @if ($item->review->rating >= 3) is--active @else nostaractive @endif">
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <div
                                            class="rating-star @if ($item->review->rating >= 4) is--active @else nostaractive @endif">
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <div
                                            class="rating-star @if ($item->review->rating >= 5) is--active @else nostaractive @endif">
                                            <i class="fa fa-star"></i>
                                        </div>
                                    </div>
                                </div><br />
                                <p class="m-b-5" style="text-align: center">{{ $item->review->comment }}</p>
                            @else
                                <div style="padding-top: 15px;padding-left: 15px;padding-right: 15px;">
                                    <div class="alert alert-info" role="alert">
                                        {{ __('dashboard.The_order_not_evaluated') }}
                                    </div>
                                </div>
                            @endif

                        </div>

                        @if ($item->delivery_type === 'delivery')
                            <div class="tab-pane" id="driverrating">
                                @if ($item->driverReview)
                                    <div class="rating-stars  ">
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
                                    <div style="padding-top: 15px;padding-left: 15px;padding-right: 15px;">
                                        <div class="alert alert-info" role="alert">
                                            {{ __('dashboard.No_Driver_Ratings_Yet') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Row -->

@endsection


@php
    $addressModel = $item->getRelation('address');

    if ($addressModel && $addressModel->latitude && $addressModel->longitude) {
        $mapLat = $addressModel->latitude;
        $mapLng = $addressModel->longitude;
        $mapAddressText = $addressModel->address;
    } else {
        $mapLat = $item->latitude;
        $mapLng = $item->longitude;
        $mapAddressText = $item->address;
    }
@endphp

@section('js')
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/i18n/' . app()->getLocale() . '.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/i18n/' . app()->getLocale() . '.js') }}"></script>
    <script type="text/javascript">
        function initMap() {
            const myLatLng = {
                lat: {{ $mapLat ?? 0 }},
                lng: {{ $mapLng ?? 0 }}
            };
            const map = new google.maps.Map(document.getElementById("maporder"), {
                zoom: 11,
                center: myLatLng,
            });

            var contentString = '<div id="contentmap">' + @json($mapAddressText ?? '') + '</div>';

            var infowindow = new google.maps.InfoWindow({ content: contentString });
            var marker = new google.maps.Marker({ position: myLatLng, map: map });

            google.maps.event.addListener(marker, 'click', function() {
                infowindow.open(map, marker);
            });
            infowindow.open(map, marker);
        }
        window.initMap = initMap;

         $("#maporder").on('click', function() {
            url = "https://www.google.com.sa/maps/search/@" + {{ $mapLat ?? 0 }} + "," + {{ $mapLng ?? 0 }} + ",12.21z?hl=en";
            window.open(url, '_blank');
        });

        $(document).ready(function() {
            $(function() {
                if ($('#ModalChangeStatusOrdervalid').length > 0) {
                    $('#modal_changeorder_submit_button').click(function(event) {
                        $('#ModalChangeStatusOrdervalid select').each(function() {
                            if ($(this).parsley().validate() !== true)
                                event.preventDefault();
                        })
                    });
                }
            });

            $(function() {
                if ($('#ModalMethodStatusOrdervalid').length > 0) {
                    $('#modal_changemethodstatus_submit_button').click(function(event) {
                        $('#ModalMethodStatusOrdervalid select').each(function() {
                            if ($(this).parsley().validate() !== true)
                                event.preventDefault();
                        })
                    });
                }
            });

            @if ($errors->ModalChangeStatusOrderbag->any())
                $('#ModalChangeStatusOrder').modal({
                    show: true
                });
            @endif

            @if ($errors->ModalChangeMethodStatusOrderbag->any())
                $('#ModalChangeMethodStatusOrder').modal({
                    show: true
                });
            @endif

            $(function() {
                if ($('#ModalAssignDrivervalid').length > 0) {
                    $('#modal_assigndriver_submit_button').click(function(event) {
                        $('#ModalAssignDrivervalid select').each(function() {
                            if ($(this).parsley().validate() !== true)
                                event.preventDefault();
                        });
                    });
                }
            });

            @if ($errors->AssignDriverBag->any())
                $('#ModalAssignDriver').modal({ show: true });
            @endif

        });
    </script>
    <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB556JrqytIxxt2hT5hkpLBQdUblve3w5U&language=ar&callback=initMap">
    </script>
    <script src="{{ URL::asset('assets/dashboard/plugins/gallery/lightgallery-all.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/plugins/gallery/jquery.mousewheel.min.js') }}"></script>
    <script src="{{ URL::asset('assets/dashboard/js/gallery.js') }}"></script>
@endsection
