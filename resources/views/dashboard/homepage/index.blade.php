@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.dashboard') }}
@endsection
@section('css')
    <!--  Owl-carousel css-->
    <link href="{{ URL::asset('assets/dashboard/plugins/owl-carousel/owl.carousel.css') }}" rel="stylesheet" />
    <!-- Maps css -->
    <link href="{{ URL::asset('assets/dashboard/plugins/jqvmap/jqvmap.min.css') }}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">{{ __('dashboard.success_login') }} </h2>
                <p class="mg-b-0"></p>
            </div>
        </div>
        @can('Home')

            <div class="main-dashboard-header-right">




                <div style="text-align: center">
                    <label class="tx-15">{{ __('dashboard.Number_of_clients') }} </label>
                    <h5>{{ $count_clients }} </h5>
                </div>
                <div style="text-align: center">
                    <label class="tx-15">{{ __('dashboard.number_of_orders') }} </label>
                    <h5>{{ $count_orders }} </h5>

                </div>




            </div>
        </div>
        <!-- /breadcrumb -->
    @endsection
@section('content')
    <!-- row -->
    <div class="row row-sm">
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-primary-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-15 text-white">{{ __('dashboard.TODAY_ORDERS') }}</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $today_Orders_Sales['total_orders'] }}
                                </h4>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-success-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-15 text-white">{{ __('dashboard.TODAY_SALES') }}</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $today_Orders_Sales['total_sales'] }}

                                </h4>

                            </div>



                        </div>
                    </div>
                </div>

            </div>
        </div>





        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-warning-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-15 text-white">{{ __('dashboard.MONTH_ORDERS') }}</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">
                                    {{ $month_Orders_Sales['total_orders'] }}</h4>
                                {{-- <p class="mb-0 tx-12 text-white ">Compared to last week</p> --}}
                            </div>
                            {{-- <span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-up text-white"></i>
											<span class="text-white "> +427</span>
										</span> --}}
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-danger-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-15 text-white">{{ __('dashboard.MONTH_SALES') }}</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $month_Orders_Sales['total_sales'] }}

                                </h4>

                            </div>



                        </div>
                    </div>
                </div>

            </div>
        </div>













        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-primary-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-15 text-white">{{ __('dashboard.YEAR_ORDERS') }}</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $year_Orders_Sales['total_orders'] }}
                                </h4>
                                {{-- <p class="mb-0 tx-12 text-white ">Compared to last week</p> --}}
                            </div>
                            {{-- <span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-up text-white"></i>
											<span class="text-white "> +427</span>
										</span> --}}
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-success-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-15 text-white">{{ __('dashboard.YEAR_SALES') }}</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $year_Orders_Sales['total_sales'] }}

                                </h4>

                            </div>



                        </div>
                    </div>
                </div>

            </div>
        </div>





        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-warning-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-15 text-white">{{ __('dashboard.TOTAL_ORDERS') }}</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $All_Orders_Sales['total_orders'] }}
                                </h4>
                                {{-- <p class="mb-0 tx-12 text-white ">Compared to last week</p> --}}
                            </div>
                            {{-- <span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-up text-white"></i>
											<span class="text-white "> +427</span>
										</span> --}}
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-danger-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-15 text-white">{{ __('dashboard.TOTAL_SALES') }}</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $All_Orders_Sales['total_sales'] }}

                                </h4>


                            </div>



                        </div>
                    </div>
                </div>

            </div>
        </div>


        {{-- ✅ كارد المنتجات اللي شارفت على الانتهاء --}}
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-warning-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-15 text-white">
                            {{ __('dashboard.Products_Near_Expiry') }}
                            <small>(≤ {{ $expired_threshold }})</small>
                        </h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $low_stock_count }}</h4>
                            </div>
                            <span class="float-right my-auto mr-auto">
                                <i class="fas fa-exclamation-triangle text-white"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ✅ كارد المنتجات اللي نفذت --}}
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-dark">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-15 text-white">{{ __('dashboard.Out_Of_Stock_Products') }}</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $out_of_stock_count }}</h4>
                            </div>
                            <span class="float-right my-auto mr-auto">
                                <i class="fas fa-times-circle text-white"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- row closed -->



    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-md-12 col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-0" style="font-size:18px">{{ __('dashboard.Order_Statuses') }}</h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                    {{-- 								<p class="tx-12 text-muted mb-0">Order Status and Tracking. Track your order from ship date to arrival. To begin, enter your order number.</p>
 --}}
                </div>
                <div class="card-body">
                   <div class="total-revenue">
    @php
        // مصفوفة الألوان لكل حالة (مبنية على الترتيب 1 إلى 6)
        $statusColors = [
            1 => '#036fe7',   // طلب جديد - أزرق
            2 => '#D8E311FF', // قيد التجهيز - أصفر
            3 => '#00797DFF', // تم التجهيز - تركواز
            4 => '#34D104FF', // قيد التوصيل - أخضر فاتح
            5 => '#28a745',   // تم التسليم - أخضر غامق
            6 => '#dc3545'    // ملغي - أحمر
        ];
    @endphp

    @foreach ($orderstatus as $status)
        <div>
            <h4>{{ \App\Models\Order::where('status_id', $status->id)->count() }}</h4>
            <label>
                <span style="background-color: {{ $statusColors[$status->id] ?? '#ccc' }}; display:inline-block; width:10px; height:10px; border-radius:50%;"></span>
                {{ $status->name }}
            </label>
        </div>
    @endforeach
</div>
                    <div id="bar" class="sales-bar mt-4"></div>
                </div>
            </div>
        </div>
    </div>


    <!-- row opened -->
    <div class="row row-sm row-deck">
        {{-- <div class="col-md-12 col-lg-4 col-xl-4">
						<div class="card card-dashboard-eight pb-2">
							<h6 class="card-title">Your Top Countries</h6><span class="d-block mg-b-10 text-muted tx-12">Sales performance revenue based by country</span>
							<div class="list-group">
								<div class="list-group-item border-top-0">
									<i class="flag-icon flag-icon-us flag-icon-squared"></i>
									<p>United States</p><span>$1,671.10</span>
								</div>
								<div class="list-group-item">
									<i class="flag-icon flag-icon-nl flag-icon-squared"></i>
									<p>Netherlands</p><span>$1,064.75</span>
								</div>
								<div class="list-group-item">
									<i class="flag-icon flag-icon-gb flag-icon-squared"></i>
									<p>United Kingdom</p><span>$1,055.98</span>
								</div>
								<div class="list-group-item">
									<i class="flag-icon flag-icon-ca flag-icon-squared"></i>
									<p>Canada</p><span>$1,045.49</span>
								</div>
								<div class="list-group-item">
									<i class="flag-icon flag-icon-in flag-icon-squared"></i>
									<p>India</p><span>$1,930.12</span>
								</div>
								<div class="list-group-item border-bottom-0 mb-0">
									<i class="flag-icon flag-icon-au flag-icon-squared"></i>
									<p>Australia</p><span>$1,042.00</span>
								</div>
							</div>
						</div>
					</div>
				--}}




        <div class="col-md-12 col-lg-7 col-xl-7">
            <div class="card card-table-two">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title mb-1" style="font-size:16px">{{ __('dashboard.Latest_Orders') }}</h4>
                    <i class="mdi mdi-dots-horizontal text-gray"></i>
                </div>
                <span class="tx-12 tx-muted mb-3 "></span>

                <div class="table-responsive country-table">
                    <table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
                        <thead>
                            <tr>
                                <th class="wd-lg-15p tx-center">{{ __('dashboard.Order_Number') }}</th>
                                <th class="wd-lg-30p tx-center">{{ __('dashboard.Client') }}</th>
                                <th class="wd-lg-25p tx-center">{{ __('dashboard.Order_Status') }}</th>
                                <th class="wd-lg-30p">{{ __('dashboard.TOTAL_DUE') }}</th>
                                <th>{{ __('dashboard.View') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recently_last_five_order as $rlfo)
                                <tr>

                                    <td class="tx-center tx-medium tx-inverse">{{ $rlfo->id }}</td>
                                    <td class="tx-center tx-medium tx-inverse">{{ $rlfo->user->name }}</td>
                                    <td class="tx-center tx-medium tx-danger">{{ $rlfo->status->name }}</td>
                                    <td class="tx-center tx-medium tx-inverse">
                                        {{ floatval($rlfo->final_price) }} {{ $rlfo->currency }}</td>
                                    <td class="tx-center tx-medium tx-inverse"> <a
                                            href="{{ route('orders.show', $rlfo->id) }}"
                                            class=" btn btn-success btn-icon ml-2" data-placement="top"
                                            data-toggle="tooltip" title="{{ __('dashboard.View') }} "><i
                                                class="las la-eye"></i></a></td>

                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="col-md-12 col-lg-5 col-xl-5">
            <div class="card card-table-two">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title mb-1" style="font-size:16px">{{ __('dashboard.Latest_Reviews') }}</h4>
                    <i class="mdi mdi-dots-horizontal text-gray"></i>
                </div>
                <span class="tx-12 tx-muted mb-3 "></span>

                <div class="table-responsive country-table">
                    <table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
                        <thead>
                            <tr>
                                <th class="wd-lg-30p tx-center">{{ __('dashboard.Client') }}</th>
                                <th class="wd-lg-25p tx-center">{{ __('dashboard.Order_Number') }}</th>
                                <th class="wd-lg-45p tx-center">{{ __('dashboard.Evaluation') }}</th>


                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recently_last_five_order_reviews as $rlfor)
                                <tr>

                                    <td class="tx-center tx-medium tx-inverse"> {{ $rlfor->client->name }}</td>
                                    <td class="tx-center tx-medium tx-inverse"> {{ $rlfor->order_id }}</td>
                                    <td class="tx-center tx-medium tx-inverse">
                                        <div class="rating-stars  ">
                                            <div class="rating-stars-container ">
                                                <div class="rating-star @if ($rlfor->rating >= 1) is--active @else nostaractive @endif"
                                                    style="font-size: 12pt;padding: 4px 5px;">
                                                    <i class="fa fa-star"></i>
                                                </div>
                                                <div class="rating-star @if ($rlfor->rating >= 2) is--active @else nostaractive @endif"
                                                    style="font-size: 12pt;padding: 4px 5px;">
                                                    <i class="fa fa-star"></i>
                                                </div>
                                                <div class="rating-star @if ($rlfor->rating >= 3) is--active @else nostaractive @endif"
                                                    style="font-size: 12pt;padding: 4px 5px;">
                                                    <i class="fa fa-star"></i>
                                                </div>
                                                <div class="rating-star @if ($rlfor->rating >= 4) is--active @else nostaractive @endif"
                                                    style="font-size: 12pt;padding: 4px 5px;">
                                                    <i class="fa fa-star"></i>
                                                </div>
                                                <div class="rating-star @if ($rlfor->rating >= 5) is--active @else nostaractive @endif"
                                                    style="font-size: 12pt;padding: 4px 5px;">
                                                    <i class="fa fa-star"></i>
                                                </div>
                                            </div>
                                        </div>

                                    </td>


                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>




    </div>
    <!-- /row -->
    <!-- row opened -->
    <div class="row row-sm row-deck">
        {{-- <div class="col-md-12 col-lg-4 col-xl-4">
						<div class="card card-dashboard-eight pb-2">
							<h6 class="card-title">Your Top Countries</h6><span class="d-block mg-b-10 text-muted tx-12">Sales performance revenue based by country</span>
							<div class="list-group">
								<div class="list-group-item border-top-0">
									<i class="flag-icon flag-icon-us flag-icon-squared"></i>
									<p>United States</p><span>$1,671.10</span>
								</div>
								<div class="list-group-item">
									<i class="flag-icon flag-icon-nl flag-icon-squared"></i>
									<p>Netherlands</p><span>$1,064.75</span>
								</div>
								<div class="list-group-item">
									<i class="flag-icon flag-icon-gb flag-icon-squared"></i>
									<p>United Kingdom</p><span>$1,055.98</span>
								</div>
								<div class="list-group-item">
									<i class="flag-icon flag-icon-ca flag-icon-squared"></i>
									<p>Canada</p><span>$1,045.49</span>
								</div>
								<div class="list-group-item">
									<i class="flag-icon flag-icon-in flag-icon-squared"></i>
									<p>India</p><span>$1,930.12</span>
								</div>
								<div class="list-group-item border-bottom-0 mb-0">
									<i class="flag-icon flag-icon-au flag-icon-squared"></i>
									<p>Australia</p><span>$1,042.00</span>
								</div>
							</div>
						</div>
					</div>
				--}}




        <div class="col-md-12 col-lg-7 col-xl-7">
            <div class="card card-table-two">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title mb-1" style="font-size:16px">{{ __('dashboard.Most_purchased_products') }}</h4>
                    <i class="mdi mdi-dots-horizontal text-gray"></i>
                </div>
                <span class="tx-12 tx-muted mb-3 "></span>

                <div class="table-responsive country-table">
                    <table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
                        <thead>
                            <tr>
                                <th class="wd-lg-50p tx-center">{{ __('dashboard.Product') }}</th>
                                <th class="wd-lg-20p tx-center">{{ __('dashboard.Number_of_Sales') }}</th>
                                <th class="wd-lg-30p tx-center">{{ __('dashboard.TOTAL_SALES') }}</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products_most_purchased_ten as $pmpt)
                                <tr>

                                    <td class="tx-center tx-medium tx-inverse">
                                        <div class="media mt-0">
                                            <a href="{{ route('products.edit', $pmpt->product->id) }}"> <img
                                                    class="avatar-lg rounded-circle ml-3 my-auto"
                                                    src="{{ $pmpt->product->image_url }}" alt="Image description">
                                            </a>
                                            <div class="media-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="mt-1">
                                                        <a href="{{ route('products.edit', $pmpt->product->id) }}"
                                                            class="tx-inverse">
                                                            <h5 class="mb-1 tx-15" style="padding-top:12px">
                                                                {{ $pmpt->product->name }}</h5>
                                                        </a>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="tx-center tx-medium tx-inverse" style="font-weight:bold">
                                        {{ floatval($pmpt->total_qty) }}</td>
                                    <td class="tx-center tx-medium tx-inverse" style="font-weight:bold">
                                        {{ floatval($pmpt->total_revenue) }} SAR</td>


                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>


            <div class="col-md-12 col-lg-5 col-xl-5">
            <div class="card card-table-two">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title mb-1" style="font-size:16px">
                        {{ __('dashboard.Products_that_are_about_to_expire') }}
                        <small class="text-muted">(  {{ __('dashboard.Limit') }}: {{ $expired_threshold }})</small>
                    </h4>
                    <i class="mdi mdi-dots-horizontal text-gray"></i>
                </div>
                <span class="tx-12 tx-muted mb-3 "></span>
                <div class="table-responsive country-table">
                    <table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
                        <thead>
                            <tr>
                                <th class="wd-lg-60p tx-center">{{ __('dashboard.Product') }}</th>
                                <th class="wd-lg-20p tx-center">{{ __('dashboard.Quantity') }}</th>
                                <th class="wd-lg-20p tx-center">{{ __('dashboard.Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($expire_products_quantity as $epq)
                                <tr>
                                    <td class="tx-center tx-medium tx-inverse">
                                        <div class="media mt-0">
                                            <a href="{{ route('products.edit', $epq->id) }}">
                                                <img class="avatar-lg rounded-circle ml-3 my-auto"
                                                    src="{{ $epq->image_url }}" alt="Image description">
                                            </a>
                                            <div class="media-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="mt-1">
                                                        <a href="{{ route('products.edit', $epq->id) }}" class="tx-inverse">
                                                            <h5 class="mb-1 tx-15" style="padding-top:12px">
                                                                {{ $epq->name }}</h5>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="tx-center tx-medium tx-inverse">
                                        <span class="badge {{ $epq->quantity <= 0 ? 'bg-dark' : 'bg-warning' }}">
                                            {{ $epq->quantity }}
                                        </span>
                                    </td>
                                    <td class="tx-center tx-medium tx-inverse">
                                        @if($epq->quantity <= 0)
                                            <span class="badge bg-dark">{{ __('dashboard.Expired') }}</span>
                                        @else
                                            <span class="badge bg-warning">{{ __('dashboard.Expiring_Soon') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
{{ __('dashboard.No_products_near_expiry') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    @endcan

</div>
<!-- /row -->

<!-- row closed -->
@endsection
@section('js')
<!--Internal  Chart.bundle js -->
<script src="{{ URL::asset('assets/dashboard/plugins/chart.js/Chart.bundle.min.js') }}"></script>
<!-- Moment js -->
<script src="{{ URL::asset('assets/dashboard/plugins/raphael/raphael.min.js') }}"></script>
<!--Internal  Flot js-->
<script src="{{ URL::asset('assets/dashboard/plugins/jquery.flot/jquery.flot.js') }}"></script>
<script src="{{ URL::asset('assets/dashboard/plugins/jquery.flot/jquery.flot.pie.js') }}"></script>
<script src="{{ URL::asset('assets/dashboard/plugins/jquery.flot/jquery.flot.resize.js') }}"></script>
<script src="{{ URL::asset('assets/dashboard/plugins/jquery.flot/jquery.flot.categories.js') }}"></script>
<script src="{{ URL::asset('assets/dashboard/js/dashboard.sampledata.js') }}"></script>
<script src="{{ URL::asset('assets/dashboard/js/chart.flot.sampledata.js') }}"></script>
<!--Internal Apexchart js-->
<script src="{{ URL::asset('assets/dashboard/js/apexcharts.js') }}"></script>
<!-- Internal Map -->
<script src="{{ URL::asset('assets/dashboard/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ URL::asset('assets/dashboard/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
<script src="{{ URL::asset('assets/dashboard/js/modal-popup.js') }}"></script>
<!--Internal  index js -->
<script src="{{ URL::asset('assets/dashboard/js/index.js') }}"></script>
<script src="{{ URL::asset('assets/dashboard/js/jquery.vmap.sampledata.js') }}"></script>

<script type="text/javascript">
    /* Apexcharts (#bar) */
    var optionsBar = {
        chart: {
            height: 249,
            type: 'bar',
            toolbar: {
                show: false,
            },
            fontFamily: 'Nunito, sans-serif',
        },
        // ✅ جلب الألوان ديناميكياً
        colors: [
            @foreach ($orderstatus as $status)
                "{{ $statusColors[$status->id] ?? '#cccccc' }}",
            @endforeach
        ],
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: false
                },
                columnWidth: '42%',
                endingShape: 'rounded',
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            endingShape: 'rounded',
            colors: ['transparent'],
        },
        responsive: [{
            breakpoint: 576,
            options: {
                stroke: {
                    show: true,
                    width: 1,
                    endingShape: 'rounded',
                    colors: ['transparent'],
                },
            },
        }],
        // ✅ جلب بيانات الشارت ديناميكياً لكل الحالات
        series: [
            @foreach ($orderstatus as $status)
            {
                name: '{{ $status->name }}',
                data: [
                    {{ \App\Models\Order::where('status_id', $status->id)->whereMonth('created_at', 1)->count() }},
                    {{ \App\Models\Order::where('status_id', $status->id)->whereMonth('created_at', 2)->count() }},
                    {{ \App\Models\Order::where('status_id', $status->id)->whereMonth('created_at', 3)->count() }},
                    {{ \App\Models\Order::where('status_id', $status->id)->whereMonth('created_at', 4)->count() }},
                    {{ \App\Models\Order::where('status_id', $status->id)->whereMonth('created_at', 5)->count() }},
                    {{ \App\Models\Order::where('status_id', $status->id)->whereMonth('created_at', 6)->count() }},
                    {{ \App\Models\Order::where('status_id', $status->id)->whereMonth('created_at', 7)->count() }},
                    {{ \App\Models\Order::where('status_id', $status->id)->whereMonth('created_at', 8)->count() }},
                    {{ \App\Models\Order::where('status_id', $status->id)->whereMonth('created_at', 9)->count() }},
                    {{ \App\Models\Order::where('status_id', $status->id)->whereMonth('created_at', 10)->count() }},
                    {{ \App\Models\Order::where('status_id', $status->id)->whereMonth('created_at', 11)->count() }},
                    {{ \App\Models\Order::where('status_id', $status->id)->whereMonth('created_at', 12)->count() }}
                ]
            }@if(!$loop->last),@endif
            @endforeach
        ],
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        },
        fill: {
            opacity: 1
        },
        legend: {
            show: false,
            floating: true,
            position: 'top',
            horizontalAlign: 'left',
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val
                }
            }
        }
    }
    new ApexCharts(document.querySelector('#bar'), optionsBar).render();
</script>
@endsection
