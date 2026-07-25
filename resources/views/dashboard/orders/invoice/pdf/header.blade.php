<br /><br />
<table style="width: 100%">
    <tr>
        <td rowspan="4" align="left">
            <img src="{{ public_path('uploads/settings/thumbnail/' . $itemstore_info['store_logo'] . '') }}"
                style="width:80px;height:80px" alt="">
        </td>
        <td align="right" style="line-height: 15px"><br /><br />
            {{ $itemstore_info['store_name'] }}</td>
    </tr>

    <tr>
        <td align="right" style="line-height: 15px">{{ __('dashboard.Contact_Number') }} :
            {{ $itemstore_info['store_mobile'] }}</td>
    </tr>
    <tr>
        <td align="right" style="line-height: 15px"> <span
                style="direction: ltr">{{ $itemstore_info['store_email'] }}</span>
            {{ __('dashboard.Email') }} :</td>
    </tr>
</table>




<br /><br />

<table style="width: 100%">
    <tr>
        <td width="20%" align="right" style="line-height: 15px; direcion:rtl"></td>

        <td width="80%" align="right" style="line-height: 15px">{{ __('dashboard.Client_Name') }} :
            {{ $item->user->name }}</td>
    </tr>
    <tr>
        <td width="20%"align="right" style="line-height: 15px; direcion:rtl">{{ __('dashboard.Invoice_No') }} :
            {{ $item->id }}</td>

        <td width="80%"align="right" style="line-height: 15px">{{ __('dashboard.Mobile') }} :
            {{ $item->user->mobile }}</td>
    </tr>
    <tr>
        <td width="20%" align="right" style="line-height: 15px; direcion:rtl">{{ __('dashboard.Issue_Date') }} :
            {{ $item->created_at->format('d/m/Y') }}</td>

        <td width="80%" align="right" style="line-height: 15px">{{ __('dashboard.Address') }} :
            {{ $item->address }}</td>
    </tr>
</table>
