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
            <th>{{ __('dashboard.TOTAL_DUE') }}</th>


        </tr>
    </thead>
    <tbody style=" text-align:center">
        @foreach ($items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->user->name }}</td>

                <td>
                    {{ $item->status->name }}
                </td>

                <td>{{ $item->created_at->diffForHumans() }}</td>

                <td>{{ $item->total_items }}</td>
                <td>{{ $item->total_price }}</td>
                <td>{{ $item->discount_amount }}


                </td>

                <td>
                    @if ($item->coupon)
                        ({{ $item->coupon->discount_percentage }} %)
                        {{ $item->coupon->code }}
                    @endif


                </td>
                <td>{{ $item->added_tax }} %</td>

                <td> {{ $item->final_price }} {{ $item->currency }}

                </td>


            </tr>
        @endforeach
    </tbody>
</table>
