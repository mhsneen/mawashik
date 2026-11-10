@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Products') }} - {{ __('dashboard.Edit') }} - {{ __('dashboard.Product_Pictures') }}
@endsection
@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/bootstrap-fileinput-master/css/fileinput.css') }}" rel="stylesheet">
    <link
        href="{{ URL::asset('assets/dashboard/plugins/bootstrap-fileinput-master/css/fileinput-' . app()->getLocale() . '.css') }}"
        rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ $item->name }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">
                    / {{ __('dashboard.Edit') }} / {{ __('dashboard.Product_Pictures') }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('products') }}" class="btn btn-info btn-icon"><i
                    class="typcn typcn-arrow-back-outline"></i></a>

        </div>
    </div>
    <!-- /breadcrumb -->
@endsection
@section('content')
    @include('dashboard.products.steps', ['active' => 3, 'id' => $item->id])
    <!-- row -->
    <div class="row row-sm">




        <!-- Col -->
        <div class="col-lg-12">
            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')
            <div class="card">
                <div class="card-body ">
                    <input id="multipleimage" name="multipleimage[]" class="file-ar" type="file" multiple
                        data-over-initial="false">
                </div>
            </div>
        </div>
        <!-- /Col -->
    </div>
    <!-- row closed -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <!--Internal  Parsley.min js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/bootstrap-fileinput-master/js/fileinput.js') }}"></script>
    <script
        src="{{ URL::asset('assets/dashboard/plugins/bootstrap-fileinput-master/js/locales/' . app()->getLocale() . '.js') }}">
    </script>
    <script type="text/javascript">
        $('.file-ar').fileinput({
            language: '{{ app()->getLocale() }}',
            uploadUrl: "{{ route('imagesstore.storeproduct') }}",
            maxFileCount: 10,
            maxFileSize: 50000,
            showCancel: false,
            uploadExtraData: {
                product_id: '{{ $item->id }}',
                _token: '{{ csrf_token() }}'
            },
            deleteExtraData: {
                product_id: '{{ $item->id }}',
                _token: '{{ csrf_token() }}'
            },
            deleteUrl: '/file-upload-batch/2',
            uploadAsync: true,
            overwriteInitial: false,
            validateInitialCount: true,


            initialPreviewAsData: true, // identify if you are sending preview data only and not the raw markup
            initialPreviewFileType: 'image', // image is the default and can be overridden in config below
            initialPreview: [
                @foreach ($item->productimages as $image)
                    '{{ url('/uploads/imagestore/' . $image->filename . '') }}',
                @endforeach
            ],

            initialPreviewConfig: [
                @foreach ($item->productimages as $image)
                    {
                        caption: '{{ $image->filename }}',
                        size: '{{ $image->size }}',
                        url: "{{ route('imagesstore.delete') }}",
                        key: '{{ $image->id }}'
                    },
                @endforeach
            ]


        });
    </script>
@endsection
