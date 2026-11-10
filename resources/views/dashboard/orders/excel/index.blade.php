<table>
   <thead style=" text-align:center">
    <tr>
        <th>{{ __('dashboard.Order_number') }}</th>
        <th>{{ __('dashboard.Client') }}</th>
        <th>{{ __('dashboard.Order_Status') }}</th>
        <th>{{ __('dashboard.Created_at') }}</th>
        <th>{{ __('dashboard.number_products') }}</th>
        <th>{{ __('dashboard.Total price before discount') }}</th>
        <th>{{ __('dashboard.Discount value') }}</th>
        <th>{{ __('dashboard.Coupon') }}</th>
        <th>{{ __('dashboard.Tax amount') }}</th>
        <th>{{ __('dashboard.Added_Tax_Value') }}</th>
        <th>{{ __('dashboard.Payment_Status') }}</th>
        <th>{{ __('dashboard.Delivery_Type') }}</th>
        <th>{{ __('dashboard.Delivery_Price') }}</th>
                <th>{{ __('dashboard.Driver') }}</th>

        <th>{{ __('dashboard.Payment_Method') }}</th>
        <th>{{ __('dashboard.TOTAL_DUE') }}</th>
    </tr>
</thead>
<tbody style=" text-align:center">
    @foreach ($items as $item)
        <tr>
            <td>{{ $item->order_number }}</td>
            <td>{{ $item->user->name }}</td>
            <td>{{ $item->status->name }}</td>
            <td>{{ $item->created_at->diffForHumans() }}</td>
            <td>{{ $item->total_items }}</td>
            <td>{{ $item->total_price }}</td>
            <td>{{ $item->discount_amount }}</td>
            <td>
                @if ($item->coupon)
                    ({{ $item->coupon->discount_percentage }} %) {{ $item->coupon->code }}
                @endif
            </td>
            <td>{{ $item->added_tax }} %</td>
            <td>{{ number_format($item->tax_amount, 2) }}</td>
            <td>{{ $item->payment_status_label }}</td>
            <td>{{ $item->delivery_type_label }}</td>
            <td>
                @if ($item->delivery_type === 'delivery')
                    {{ number_format($item->delivery_price, 2) }}
                @else
                    —
                @endif
            </td>
            <td>
    @if ($item->delivery_type === 'delivery' && $item->driver)
        {{ $item->driver->name }} ({{ $item->driver->mobile }})
    @else
        —
    @endif
</td>
            <td>{{ $item->payment_method_label }}</td>
            <td>{{ $item->final_price }} {{ $item->currency }}</td>
        </tr>
    @endforeach
</tbody>
</table>
