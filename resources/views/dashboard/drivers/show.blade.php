@extends('layouts.admin_dashboard')
@section('title')
    {{ $item->name }}
@endsection
@section('css')
    <style type="text/css">
        .nostaractive {
            color: #e1e6f1 !important;
        }
        .badge-driver-pending   { background: #ffd70020; color: #b8860b; border: 1px solid #ffd70050; }
        .badge-driver-approved  { background: #28a74520; color: #28a745; border: 1px solid #28a74550; }
        .badge-driver-rejected { background: #dc354520; color: #dc3545; border: 1px solid #dc354550; }
        .badge-driver-suspended{ background: #6c757d20; color: #6c757d; border: 1px solid #6c757d50; }
        .doc-image-thumb {
            transition: transform .2s;
            cursor: pointer;
            border: 2px solid #e9ecef;
            border-radius: 6px;
        }
        .doc-image-thumb:hover { transform: scale(1.03); border-color: #0d6efd; }
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

                            {{-- ✅ حالة اعتماد السائق --}}
                            <div class="mt-3 mb-3">
                                <span class="text-muted d-block tx-12 mb-1">{{ __('dashboard.Driver_Status') }}</span>
                                @php
                                    $statusLabels = [
                                        'pending'   => ['class' => 'badge-driver-pending',   'text' => __('dashboard.Driver_Status_Pending'),   'icon' => 'fa-clock'],
                                        'approved'  => ['class' => 'badge-driver-approved',  'text' => __('dashboard.Driver_Status_Approved'),  'icon' => 'fa-check-circle'],
                                        'rejected'  => ['class' => 'badge-driver-rejected', 'text' => __('dashboard.Driver_Status_Rejected'), 'icon' => 'fa-times-circle'],
                                        'suspended' => ['class' => 'badge-driver-suspended','text' => __('dashboard.Driver_Status_Suspended'),'icon' => 'fa-pause-circle'],
                                    ];
                                    $current = $statusLabels[$item->driver_status] ?? $statusLabels['pending'];
                                @endphp
                                <span class="badge {{ $current['class'] }} tx-13 pd-8">
                                    <i class="fa {{ $current['icon'] }} mr-1"></i>{{ $current['text'] }}
                                </span>
                                @if ($item->driver_approved_at)
                                    <small class="d-block text-muted mt-1 tx-11">
                                        <i class="fa fa-calendar-check mr-1"></i>
                                        {{ __('dashboard.Approved_At') }}: {{ $item->driver_approved_at->format('Y-m-d H:i') }}
                                    </small>
                                @endif
                                @if ($item->driver_status_note)
                                    <small class="d-block text-danger mt-1 tx-11">
                                        <i class="fa fa-exclamation-triangle mr-1"></i>
                                        {{ $item->driver_status_note }}
                                    </small>
                                @endif
                            </div>

                            {{-- ✅ نسبة عمولة السائق --}}
                            <div class="mb-3">
                                <span class="text-muted d-block tx-12 mb-1">{{ __('dashboard.Driver_Commission_Rate') }}</span>
                                <span class="tx-15 font-weight-bold text-primary">
                                    @if ($item->driver_commission_rate !== null)
                                        {{ number_format($item->driver_commission_rate, 2) }}%
                                    @else
                                        <span class="text-muted">{{ __('dashboard.Using_Default_Commission') }}</span>
                                    @endif
                                </span>
                            </div>

                            {{-- ✅ الموافقة على الشروط --}}
                            <div class="mb-3">
                                <span class="text-muted d-block tx-12 mb-1">{{ __('dashboard.Terms_Accepted') }}</span>
                                @if ($item->terms_accepted ?? false)
                                    <span class="badge badge-success tx-13 pd-8"><i class="fa fa-check mr-1"></i>{{ __('dashboard.Yes') }}</span>
                                @else
                                    <span class="badge badge-danger tx-13 pd-8"><i class="fa fa-times mr-1"></i>{{ __('dashboard.No') }}</span>
                                @endif
                            </div>

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
                            <li class="">
                                <a href="#documents" data-toggle="tab" aria-expanded="false"> <span class="visible-xs"><i
                                            class="las la-id-card tx-20 mr-1"></i></span> <span
                                        class="hidden-xs">{{ __('dashboard.Driver_Documents') }}</span> </a>
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

                        {{-- ===== Tab الوثائق (صورة الهوية + الرخصة) ===== --}}
                        <div class="tab-pane" id="documents">
                            <div class="row">
                                {{-- صورة الهوية --}}
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0"><i class="fa fa-id-card text-warning mr-2"></i>{{ __('dashboard.ID_Image') }}</h6>
                                        </div>
                                        <div class="card-body text-center">
                                            @if (!empty($item->id_number))
                                                <p class="mb-2"><strong>{{ __('dashboard.ID_Number') }}:</strong> <span style="direction: ltr; display: inline-block">{{ $item->id_number }}</span></p>
                                            @endif
                                            @if (!empty($item->id_image))
                                                <a href="{{ URL::asset('uploads/users/' . $item->id_image) }}" target="_blank">
                                                    <img src="{{ URL::asset('uploads/users/thumbnail/' . $item->id_image) }}"
                                                         alt="{{ __('dashboard.ID_Image') }}"
                                                         class="img-fluid doc-image-thumb"
                                                         style="max-height: 300px;">
                                                </a>
                                                <div class="mt-2">
                                                    <a href="{{ URL::asset('uploads/users/' . $item->id_image) }}"
                                                       class="btn btn-info btn-sm" target="_blank">
                                                        <i class="fa fa-download mr-1"></i>{{ __('dashboard.View_Full_Size') }}
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-muted py-5">
                                                    <i class="fa fa-image fa-3x mb-2 d-block"></i>
                                                    <span>{{ __('dashboard.No_ID_Image_Uploaded') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- صورة الرخصة --}}
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0"><i class="fa fa-car text-info mr-2"></i>{{ __('dashboard.License_Image') }}</h6>
                                        </div>
                                        <div class="card-body text-center">
                                            @if (!empty($item->license_image))
                                                <a href="{{ URL::asset('uploads/users/' . $item->license_image) }}" target="_blank">
                                                    <img src="{{ URL::asset('uploads/users/thumbnail/' . $item->license_image) }}"
                                                         alt="{{ __('dashboard.License_Image') }}"
                                                         class="img-fluid doc-image-thumb"
                                                         style="max-height: 300px;">
                                                </a>
                                                <div class="mt-2">
                                                    <a href="{{ URL::asset('uploads/users/' . $item->license_image) }}"
                                                       class="btn btn-info btn-sm" target="_blank">
                                                        <i class="fa fa-download mr-1"></i>{{ __('dashboard.View_Full_Size') }}
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-muted py-5">
                                                    <i class="fa fa-image fa-3x mb-2 d-block"></i>
                                                    <span>{{ __('dashboard.No_License_Image_Uploaded') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- أزرار الإجراءات السريعة --}}
                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    @if ($item->driver_status !== 'approved')
                                        <form action="{{ route('drivers.approve', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success-gradient" onclick="return confirm('{{ __('dashboard.Confirm_Approve_Driver') }}')">
                                                <i class="fa fa-check"></i> {{ __('dashboard.Approve') }}
                                            </button>
                                        </form>
                                    @endif
                                    @if ($item->driver_status !== 'rejected')
                                        <a href="{{ route('drivers.edit', $item->id) }}" class="btn btn-danger-gradient">
                                            <i class="fa fa-times"></i> {{ __('dashboard.Reject') }}
                                        </a>
                                    @endif
                                    <a href="{{ route('drivers.edit', $item->id) }}" class="btn btn-warning-gradient">
                                        <i class="fa fa-edit"></i> {{ __('dashboard.Edit') }}
                                    </a>
                                </div>
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
