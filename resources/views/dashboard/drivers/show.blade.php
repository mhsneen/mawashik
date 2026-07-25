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

                            {{--  	<div class="row">
											<div class="col-md-4 col mb20">
												<h5>947</h5>
												<h6 class="text-small text-muted mb-0">Followers</h6>
											</div>
											<div class="col-md-4 col mb20">
												<h5>583</h5>
												<h6 class="text-small text-muted mb-0">Tweets</h6>
											</div>
											<div class="col-md-4 col mb20">
												<h5>48</h5>
												<h6 class="text-small text-muted mb-0">Posts</h6>
											</div>
										</div>
										<hr class="mg-y-30">
										<label class="main-content-label tx-13 mg-b-20">Social</label>
										<div class="main-profile-social-list">
											<div class="media">
												<div class="media-icon bg-primary-transparent text-primary">
													<i class="icon ion-logo-github"></i>
												</div>
												<div class="media-body">
													<span>Github</span> <a href="">github.com/spruko</a>
												</div>
											</div>
											<div class="media">
												<div class="media-icon bg-success-transparent text-success">
													<i class="icon ion-logo-twitter"></i>
												</div>
												<div class="media-body">
													<span>Twitter</span> <a href="">twitter.com/spruko.me</a>
												</div>
											</div>
											<div class="media">
												<div class="media-icon bg-info-transparent text-info">
													<i class="icon ion-logo-linkedin"></i>
												</div>
												<div class="media-body">
													<span>Linkedin</span> <a href="">linkedin.com/in/spruko</a>
												</div>
											</div>
											<div class="media">
												<div class="media-icon bg-danger-transparent text-danger">
													<i class="icon ion-md-link"></i>
												</div>
												<div class="media-body">
													<span>My Portfolio</span> <a href="">spruko.com/</a>
												</div>
											</div>
										</div>
										<hr class="mg-y-30">
										<h6>Skills</h6>
										<div class="skill-bar mb-4 clearfix mt-3">
											<span>HTML5 / CSS3</span>
											<div class="progress mt-2">
												<div class="progress-bar bg-primary-gradient" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 85%"></div>
											</div>
										</div>
										<!--skill bar-->
										<div class="skill-bar mb-4 clearfix">
											<span>Javascript</span>
											<div class="progress mt-2">
												<div class="progress-bar bg-danger-gradient" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 89%"></div>
											</div>
										</div>
										<!--skill bar-->
										<div class="skill-bar mb-4 clearfix">
											<span>Bootstrap</span>
											<div class="progress mt-2">
												<div class="progress-bar bg-success-gradient" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 80%"></div>
											</div>
										</div>
										<!--skill bar-->
										<div class="skill-bar clearfix">
											<span>Coffee</span>
											<div class="progress mt-2">
												<div class="progress-bar bg-info-gradient" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 95%"></div>
											</div>
										</div> --}}
                            <!--skill bar-->
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
                                    {{--
<p class="text-muted mb-0 tx-11"><i class="si si-arrow-up-circle text-success mr-1"></i>increase</p>

                                                    --}}
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
                                    <h2 class="mb-0 tx-15 mb-1 mt-1"> {{ $totalCompletedOrdersPrice }} SAR </h2>
                                    {{--
<p class="text-muted mb-0 tx-11"><i class="si si-arrow-up-circle text-success mr-1"></i>increase</p>

                                                    --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-4 col-lg-12 col-md-12">
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
                                            <th>{{ __('dashboard.Created_at') }}</th>
                                            <th>{{ __('dashboard.number_products') }}</th>

                                            <th>{{ __('dashboard.TOTAL_DUE') }}</th>


                                            <th>{{ __('dashboard.Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item->orders as $order)
                                            <tr id="tr_{{ $order->id }}">

                                                <td>{{ $order->id }}
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
                                        @foreach ($reviews as $review)
                                            <tr style="text-align: center">

                                                <td>{{ $review->order_id }}</td>
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
                                        @endforeach

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
