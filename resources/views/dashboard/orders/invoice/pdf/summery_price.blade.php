<table cellspacing="0" cellpadding="5" border="1"
    style="border-collapse:collapse; width:100%; font-size:10px; margin-top:5px;">
    <tr>
        <td style="text-align:center; width:25%;">{{ $item->total_items }}</td>
        <td style="text-align:right; width:25%;">{{ __('dashboard.number_products') }}</td>
        <td rowspan="6" style="width:50%; text-align:right;">
            <strong>{{ __('dashboard.Note') }}</strong><br />
            {{ $item->note }}
        </td>
    </tr>
    <tr>
        <td style="text-align:center;">{{ $item->currency }} {{ $item->total_price }}</td>
        <td style="text-align:right;">{{ __('dashboard.Total price before discount') }}</td>
    </tr>
    <tr>
        <td style="text-align:center;">
            @if ($item->coupon)
                {{ $item->currency }} {{ $item->discount_amount }} ({{ $item->coupon->discount_percentage }}%)
            @else
                {{ $item->currency }} {{ $item->discount_amount }}
            @endif
        </td>
        <td style="text-align:right;">{{ __('dashboard.Coupon_Discount_Value') }}</td>
    </tr>
    <tr>
        <td style="text-align:center;">{{ $item->added_tax }}%</td>
        <td style="text-align:right;">{{ __('dashboard.added_tax') }}</td>
    </tr>

    @if ($item->payment_status)
<tr>
    <td style="text-align:center;">{{ $item->payment_status_label }}</td>
    <td style="text-align:right;">{{ __('dashboard.Payment_Status') }}</td>
</tr>
@endif
@if ($item->delivery_type)
<tr>
    <td style="text-align:center;">{{ $item->delivery_type_label }}</td>
    <td style="text-align:right;">{{ __('dashboard.Delivery_Type') }}</td>
</tr>
@endif
@if ($item->delivery_type === 'delivery' && $item->delivery_price)
<tr>
    <td style="text-align:center;">{{ number_format($item->delivery_price, 2) }}</td>
    <td style="text-align:right;">{{ __('dashboard.Delivery_Price') }}</td>
</tr>
@endif
    <tr>
        <td style="text-align:center;">{{ $item->currency }} {{ $item->final_price }}</td>
        <td style="text-align:right; font-weight:bold;">{{ __('dashboard.TOTAL_DUE') }}</td>
    </tr>
</table>
