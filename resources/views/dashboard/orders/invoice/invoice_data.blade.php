<div class="main-content-body-invoice">
    <div class="card card-invoice">
        <div class="card-body">

            <!-- Header -->
            <div class="d-flex justify-content-between invoice-header">
                <div class="d-flex align-items-center">
                    <img src="{{ URL::asset('uploads/settings/thumbnail/' . $itemstore_info['store_logo'] . '') }}"
                        alt="" class="rounded" width="65" height="65" style="margin: 10px">
                    <div class="ms-3">
                        <h5 class="mb-1">{{ $itemstore_info['store_name'] }}</h5>
                        <p class="mb-0 small">
                            {{ __('dashboard.Contact_Number') }} : {{ $itemstore_info['store_mobile'] }} <br>
                            {{ __('dashboard.Email') }} : {{ $itemstore_info['store_email'] }}
                        </p>
                    </div>
                </div>

                <div class="text-end">
                    <h6 class="mb-1">{{ __('dashboard.Invoice_No') }}: #{{ $item->order_number }}</h6>
                    <p class="mb-0 small">{{ __('dashboard.Issue_Date') }}: {{ $item->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            <!-- Client -->
            <div class="bg-light p-3 rounded my-4">
                <h6 class="fw-bold mb-2">{{ __('dashboard.Client_Information') }}</h6>
                <p class="mb-0 small">
                    {{ __('dashboard.Client_Name') }} : {{ $item->user->name }} <br>
                    {{ __('dashboard.Mobile') }} : {{ $item->user->mobile }} <br>
                </p>
            </div>

            <!-- Items -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>{{ __('dashboard.Product') }}</th>
                            <th class="text-center">{{ __('dashboard.Quantity') }}</th>
                            <th class="text-center">{{ __('dashboard.Price') }}</th>
                            <th class="text-center">{{ __('dashboard.Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($item->items as $order_item)
                            <tr>
    <td>@include('dashboard.orders.partials.item_details', ['order_item' => $order_item])</td>

                                <td class="text-center">
                                    {{ $order_item->quantity }}
                                    @if ($order_item->product->unit)
                                        ({{ $order_item->product->unit->name }})
                                    @endif
                                </td>
                                <td class="text-center">{{ $order_item->unit_price }} {{ $item->currency }}</td>
                                <td class="text-center">{{ $order_item->total_price }} {{ $item->currency }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div class="row justify-content-end mt-4">
                <div class="col-md-6 col-lg-5">
                    <div class="table-responsive">
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
                </div>
            </div>

            <!-- Note -->
            @if ($item->note)
                <div class="invoice-notes mt-3">
                    <label class="fw-bold">{{ __('dashboard.Note') }}</label>
                    <p class="small text-dark">{{ $item->note }}</p>
                </div>
            @endif

        </div>
    </div>
</div>
