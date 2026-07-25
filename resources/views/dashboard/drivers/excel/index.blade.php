<table>
    <thead style=" text-align:center">
        <tr>
            <th>{{ __('dashboard.Name') }}</th>
            <th>{{ __('dashboard.Mobile') }}</th>
            <th>{{ __('dashboard.Efficiency_Status') }}</th>
            <th>{{ __('dashboard.Created_at') }}</th>


        </tr>
    </thead>
    <tbody style=" text-align:center">
        @foreach ($items as $item)
            <tr>
                <td>{{ $item->name }} </td>


                <td>{{ $item->mobile }} </td>

                <td>
                    @if ($item->is_active == 1)
                        {{ __('dashboard.effective') }}
                    @endif
                    @if ($item->is_active == 0)
                        {{ __('dashboard.Inactive') }}
                    @endif

                </td>

                <td>{{ $item->created_at->format('d/m/Y') }} {{ $item->created_at->format('h:i:s A') }}</td>


            </tr>
        @endforeach
    </tbody>
</table>
