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
                    <h6 class="mb-1">{{ __('dashboard.Invoice_No') }}: #{{ $item->id }}</h6>
                    <p class="mb-0 small">{{ __('dashboard.Issue_Date') }}: {{ $item->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            <!-- Client -->
            <div class="bg-light p-3 rounded my-4">
                <h6 class="fw-bold mb-2">{{ __('dashboard.Client_Information') }}</h6>
                <p class="mb-0 small">
                    {{ __('dashboard.Client_Name') }} : {{ $item->user->name }} <br>
                    {{ __('dashboard.Mobile') }} : {{ $item->user->mobile }} <br>
                    {{ __('dashboard.Address') }} : {{ $item->address }}
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
                                <td>
                                    <strong>{{ $order_item->product->name }}</strong>
                                    <div class="text-muted small">
                                        {{ $order_item->product->category->name }}
                                    </div>

                                    {{-- Addons --}}
                                    @if ($order_item->attributes->isNotEmpty())
                                        <div class="mt-1 small">
                                            @foreach ($order_item->attributes as $attr)
                                                <div>{{ $attr->attribute_name }} ({{ $attr->value_name }}
                                                    )</div>
                                            @endforeach
                                        </div>
                                    @endif




                                </td>

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
                <div class="col-md-5">
                    <table class="table table-borderless">
                        <tr>
                            <td>{{ __('dashboard.number_products') }}</td>
                            <td class="text-end">{{ $item->total_items }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('dashboard.Total price before discount') }}</td>
                            <td class="text-end">{{ $item->total_price }} {{ $item->currency }}</td>
                        </tr>

                        <tr>
                            <td>{{ __('dashboard.Coupon_Discount_Value') }} @if ($item->coupon)
                                    ({{ $item->coupon->discount_percentage }}%)
                                @endif
                            </td>
                            <td class="text-end text-danger">- {{ $item->discount_amount }} {{ $item->currency }}</td>
                        </tr>

                        <tr>
                            <td>{{ __('dashboard.added_tax') }}</td>
                            <td class="text-end">{{ $item->added_tax }}%</td>
                        </tr>
                        <tr class="fw-bold border-top">
                            <td>{{ __('dashboard.TOTAL_DUE') }}</td>
                            <td class="text-end text-primary">
                                {{ $item->final_price }} {{ $item->currency }}
                            </td>
                        </tr>
                    </table>
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
