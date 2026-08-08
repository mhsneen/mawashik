{{-- $order_item --}}
<h6 class="font-weight-semibold mt-0 text-uppercase">{{ $order_item->product->name }}</h6>



@if ($order_item->variant_label_snapshot)
    <span class="badge bg-info text-white mb-1">
        {{ __('dashboard.Combination') }}: {{ $order_item->variant_label_snapshot }}
    </span><br>
@endif

@if ($order_item->product->category)
    <small class="text-muted">{{ __('dashboard.Product_Add_Category') }}: {{ $order_item->product->category->name }}</small><br>
@endif

@if ($order_item->attributes->isNotEmpty())
    <div class="mt-1">
        @foreach ($order_item->attributes as $attr)
            <span class="badge bg-primary text-white mr-1 mb-1">
                {{ $attr->attribute_name }}:
                @if ($attr->color_code)
                    <span style="display:inline-block;width:12px;height:12px;background-color:{{ $attr->color_code }};border:1px solid #ccc;margin-left:3px;vertical-align:middle;"></span>
                @endif
                {{ $attr->value_name }}
                @if ($attr->price > 0)
                    (+{{ number_format($attr->price, 2) }})
                @endif
            </span>
        @endforeach
    </div>
@endif
