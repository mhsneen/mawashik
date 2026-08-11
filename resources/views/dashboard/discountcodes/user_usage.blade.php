@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Beneficiaries of the discount code') }} {{ $discountcode->code }} (
    {{ $discountcode->discount_percentage }} % )
@endsection
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto"> {{ __('dashboard.Beneficiaries of the discount code') }}</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ ( {{ $discountcode->discount_percentage }} % )
                    {{ $discountcode->code }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route($routeName) }}" class="btn btn-info btn-icon"><i class="typcn typcn-arrow-back-outline"></i></a>

        </div>

    </div>
    <!-- /breadcrumb -->
@endsection
@section('content')
    <!-- row opened -->
    <div class="row row-sm">



        <div class="col-xl-12">




            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')




            <div class="card mg-b-20">
                @if (count($items) > 0)
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title mg-b-0"> {{ __('dashboard.Beneficiaries of the discount code') }} (
                                {{ $discountcode->code }} ) ( {{ $discountcode->discount_percentage }} % )</h4>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive border-top userlist-table">
                            <table class="table card-table table-striped table-vcenter text-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="master"></th>
                                        <th>{{ __('dashboard.Client') }}</th>
                                        <th>{{ __('dashboard.Order_Number') }}</th>
                                        <th>{{ __('dashboard.Date and time of use') }}</th>
                                       <th> {{ __('dashboard.Total price before discount') }}</th>
                                       <th> {{ __('dashboard.Discount value') }}</th>
                                       <th> {{ __('dashboard.Price after discount') }}</th>
                                       <th> {{ __('dashboard.Tax amount') }}</th>
                                       <th> {{ __('dashboard.Final price after tax') }}</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr id="tr_{{ $item->id }}">
                                            <th><input type="checkbox" class="sub_chk" data-id="{{ $item->id }}">
                                            </th>
                                           <td> {{ $item->user->name ?? '-' }}
    <br />
    {{ $item->user->mobile ?? '' }}
</td>

<td> {{ optional($item->order)->order_number ?? '-' }}</td>
<td>{{ $item->used_at->diffForHumans() }}</td>
<td> {{ optional($item->order)->total_price ?? '-' }}</td>
<td> {{ optional($item->order)->discount_amount ?? '-' }}</td>
<td> {{ optional($item->order)->price_after_discount ?? '-' }}</td>
<td> {{ optional($item->order)->tax_amount ?? 0 }} ( {{ optional($item->order)->added_tax ?? 0 }} % )</td>
<td> {{ optional($item->order)->price_after_tax ?? '-' }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>







                        </div>
                        <br />
                        {{ $items->links() }}
                    </div>
                @else
                    @include('dashboard.includes.alerts.nodata')
                @endif
            </div>
        </div>
        <!--/div-->

        <!--/div-->
    </div>
    <!-- /row -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
@endsection
