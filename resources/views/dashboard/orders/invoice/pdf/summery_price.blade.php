<table cellspacing="0" cellpadding="5" border="1"
    style="border-collapse:collapse; width:100%; font-size:10px; margin-top:5px;">
    <tbody>
        <tr>
            <td style="text-align:center; width:40%;">{{ $item->total_items }}</td>
            <td style="text-align:right; width:60%; font-weight:bold;">{{ __('dashboard.number_products') }}</td>
        </tr>

        <tr>
            <td style="text-align:center; width:40%;">{{ number_format($item->total_price, 2) }}</td>
            <td style="text-align:right; width:60%; font-weight:bold;">{{ __('dashboard.Total price before discount') }}</td>
        </tr>

        @if ($item->discount_amount > 0)
            <tr>
                <td style="text-align:center; color:red;">- {{ number_format($item->discount_amount, 2) }}</td>
                <td style="text-align:right; font-weight:bold;">
                    {{ __('dashboard.Coupon_Discount_Value') }}
                    @if ($item->coupon)
                        <br /><small style="font-size:8px; font-weight:normal;">{{ $item->coupon->code }} ({{ $item->coupon->discount_percentage }}%)</small>
                    @endif
                </td>
            </tr>

            <tr>
                <td style="text-align:center;">{{ number_format($item->price_after_discount, 2) }}</td>
                <td style="text-align:right; font-weight:bold;">{{ __('dashboard.Price after discount') }}</td>
            </tr>
        @endif

        @if ($item->added_tax)
            <tr>
                <td style="text-align:center;">{{ number_format($item->tax_amount, 2) }}</td>
                <td style="text-align:right; font-weight:bold;">{{ __('dashboard.added_tax') }} ({{ $item->added_tax }}%)</td>
            </tr>
        @endif

        @if ($item->delivery_type === 'delivery' && $item->delivery_price)
            <tr>
                <td style="text-align:center;">{{ number_format($item->delivery_price, 2) }}</td>
                <td style="text-align:right; font-weight:bold;">{{ __('dashboard.Delivery_Price') }}</td>
            </tr>
        @endif

        @if ($item->redeemed_points > 0)
            <tr>
                <td style="text-align:center; color:red;">- {{ number_format($item->redeemed_points_value, 2) }}</td>
                <td style="text-align:right; font-weight:bold;">{{ __('dashboard.redeem_points') }} ({{ $item->redeemed_points }})</td>
            </tr>
        @endif

        <tr style="background-color:#F2EFEE;">
            <td style="text-align:center; font-weight:bold; color:green; font-size:11px;">{{ number_format($item->final_price, 2) }} {{ $item->currency }}</td>
            <td style="text-align:right; font-weight:bold; font-size:11px;">{{ __('dashboard.TOTAL_DUE') }}</td>
        </tr>
    </tbody>
</table>
@if ($item->note)
    <div style="margin-top:8px; font-size:10px; text-align:right; direction:rtl; unicode-bidi:plaintext;">
        <strong>{{ __('dashboard.Note') }}:</strong>
        <span style="unicode-bidi:plaintext;">{!! nl2br(e($item->note)) !!}</span>
    </div>
@endif
