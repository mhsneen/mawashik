<div class="row justify-content-center mb-4">
    <div class="col-lg-10">

        <div class="card shadow-sm">
            <div class="card-body">

                <div class="product-steps">

                    <a href="{{ $id ? route('products.edit', $item->id) : '#' }}"
                        class="step {{ $active == 1 ? 'active' : 'basicdata_first' }}">
                        <span class="circle">1</span>
                        <span class="label">{{ __('dashboard.Basic_data_product') }}</span>
                    </a>

                    <a href="{{ $id ? route('products.editdescription', $item->id) : '#' }}"
                        class="step {{ $active == 2 ? 'active' : 'basicdata_first' }}">
                        <span class="circle">2</span>
                        <span class="label">{{ __('dashboard.Product_Description') }}</span>
                    </a>

                    <a href="{{ $id ? route('products.images', $item->id) : '#' }}"
                        class="step {{ $active == 3 ? 'active' : 'basicdata_first' }}">
                        <span class="circle">3</span>
                        <span class="label">{{ __('dashboard.Product_Pictures') }}</span>
                    </a>

                    <a href="{{ $id ? route('products.editattribute', $item->id) : '#' }}"
                        class="step {{ $active == 4 ? 'active' : 'basicdata_first' }}">
                        <span class="circle">4</span>
                        <span class="label">{{ __('dashboard.Product_Features') }}</span>
                    </a>

                       <a href="{{ $id ? route('products.editVariants', $item->id) : '#' }}"
                        class="step {{ $active == 5 ? 'active' : 'basicdata_first' }}">
                        <span class="circle">5</span>
                        <span class="label">{{ __('dashboard.Product_Variants') }}</span>
                    </a>

                    <a href="{{ $id ? route('products.editModifiers', $item->id) : '#' }}"
                        class="step {{ $active == 6 ? 'active' : 'basicdata_first' }}">
                        <span class="circle">6</span>
                        <span class="label">{{ __('dashboard.Product_Modifiers') }}</span>
                    </a>

                </div>

            </div>
        </div>

    </div>
</div>
