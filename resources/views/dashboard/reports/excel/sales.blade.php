<table>
    <thead>
        <tr>


            <th>#</th>
            <th>{{ __('dashboard.Order_Number') }}</th>
            <th>{{ __('dashboard.Client') }}</th>
            <th>{{ __('dashboard.TOTAL_DUE') }}</th>
            <th>{{ __('dashboard.Created_at') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->user->name ?? '-' }}</td>
                <td>{{ $order->final_price }}</td>
                <td>{{ $order->created_at->format('Y-m-d') }}</td>
            </tr>
        @endforeach

        {{-- ✅ الصف الإجمالي --}}
        <tr style="font-weight: bold;">
            <td colspan="3">{{ __('dashboard.Total') }}</td>
            <td>{{ $summary['total_sales'] }}</td>
            <td></td>
        </tr>
    </tbody>
</table>
