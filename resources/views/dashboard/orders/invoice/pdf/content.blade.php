<table cellspacing="0" cellpadding="5" border="1" style="width:100%; table-layout: fixed; border-collapse: collapse;">
    <tr style="background-color:#F2EFEE; text-align: center; font-weight:bold; font-size:12px;">
        <td style="width: 15%;">{{ __('dashboard.Total') }}</td>
        <td style="width: 15%;">{{ __('dashboard.Price') }}</td>
        <td style="width: 15%;">{{ __('dashboard.Quantity') }}</td>
        <td style="width: 55%; text-align: right;">{{ __('dashboard.Product') }}</td>
    </tr>
    @foreach ($item->items as $order_item)
        <tr style="font-size:10px;">
            <td style="width:15%; text-align:center;">{{ $item->currency }} {{ $order_item->total_price }}</td>
            <td style="width:15%; text-align:center;">{{ $item->currency }} {{ $order_item->unit_price }}</td>
            <td style="width:15%; text-align:center;">
                {{ $order_item->quantity }}
                @if ($order_item->product->unit)
                    ({{ $order_item->product->unit->name }})
                @endif
            </td>
            <td style="width:55%; text-align:right;">
    {{ $order_item->product->name }}<br />
    @if ($order_item->variant_label_snapshot)
        {{ __('dashboard.Combination') }}: {{ $order_item->variant_label_snapshot }}<br />
    @endif
    {{ __('dashboard.Product_Add_Category') }}: {{ $order_item->product->category->name }}<br />
    @if ($order_item->attributes->isNotEmpty())
        @foreach ($order_item->attributes as $attr)
            {{ $attr->attribute_name }} ({{ $attr->value_name }})<br />
        @endforeach
    @endif
</td>
        </tr>
    @endforeach
</table>
