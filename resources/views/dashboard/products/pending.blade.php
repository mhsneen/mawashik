@extends('layouts.admin_dashboard')
@section('title', __('dashboard.Pending_Products'))
@section('css')
    {{-- Lightbox2 CSS --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
    <style>
        .description-text { display: inline-block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; vertical-align: bottom; transition: max-width 0.3s; }
        .description-text.expanded { white-space: normal; max-width: 100%; }
        .read-more { display: none; cursor: pointer; font-size: 12px; color: #007bff; }
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <h4 class="content-title mb-0">{{ __('dashboard.Pending_Products_Approval') }}</h4>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if($products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('dashboard.Image') }}</th>
                                    <th>{{ __('dashboard.Name') }}</th>
                                    <th>{{ __('dashboard.Trader') }}</th>
                                    <th>{{ __('dashboard.Category') }}</th>
                                    <th>{{ __('dashboard.Price') }}</th>
                                    <th>{{ __('dashboard.Slaughter') }}</th>
                                    <th>{{ __('dashboard.Created_at') }}</th>
                                    <th>{{ __('dashboard.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $p)
                                <tr class="table-warning">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{-- الصور والفيديو --}}
                                        <div style="display: flex; gap: 5px; flex-wrap: wrap; max-width: 150px;">
                                            @if($p->image)
                                                <a href="{{ $p->base_image_url }}" data-lightbox="product-{{ $p->id }}" data-title="{{ $p->translate(app()->getLocale())?->name }}">
                                                    <img src="{{ $p->image_url }}" width="50" style="border-radius: 5px;" loading="lazy">
                                                </a>
                                            @endif

                                            {{-- عرض باقي صور المنتج من مجلد imagestore --}}
                                            @if($p->productimages && $p->productimages->count() > 0)
                                                @foreach($p->productimages as $img)
                                                    <a href="{{ asset('uploads/imagestore/' . $img->filename) }}" data-lightbox="product-{{ $p->id }}" data-title="{{ $p->translate(app()->getLocale())?->name }}">
                                                        <img src="{{ asset('uploads/imagestore/' . $img->filename) }}" width="50" style="border-radius: 5px;" loading="lazy">
                                                    </a>
                                                @endforeach
                                            @endif

                                            {{-- عرض الفيديو إن وجد عبر مودال --}}
  {{-- عرض الفيديو إن وجد عبر مودال --}}
@if($p->video_url)
    <a href="javascript:void(0)" onclick="playVideo('{{ asset('uploads/products/' . $p->video_url) }}')">
        <div style="width: 50px; height: 50px; background: #000; color: #fff; display: flex; align-items: center; justify-content: center; border-radius: 5px;">
            <i class="fa fa-play"></i>
        </div>
    </a>
@endif
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $p->translate(app()->getLocale())?->name }}</strong>
                                        {{-- وصف المنتج مع زر المزيد --}}
                                        @if($p->translate(app()->getLocale())?->description)
                                            <div class="description-container mt-1">
                                                <span class="description-text">
                                                    {{ $p->translate(app()->getLocale())->description }}
                                                </span>
                                                <a href="javascript:void(0)" class="read-more">المزيد</a>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $p->trader?->name }}</td>
                                    <td>{{ $p->category?->translate(app()->getLocale())?->name }}</td>
                                    <td>{{ number_format($p->netPrice, 2) }}</td>
                                    <td>
                                        @if($p->slaughter_available)
                                            <span class="badge badge-success">{{ __('dashboard.Available') }}</span>
                                            @if($p->slaughter_price)
                                                <small class="d-block text-muted">{{ number_format($p->slaughter_price, 2) }}</small>
                                            @endif
                                        @else
                                            <span class="badge badge-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($p->created_at)->format('Y-m-d h:i A') }}
                                    </td>
                                    <td>
                                        <form action="{{ route('products-approvals.approve', $p->id) }}" method="POST" style="display:inline">
                                            @csrf
                                            <button class="btn btn-success btn-sm" onclick="return confirm('{{ __('dashboard.Confirm_Approve_Product') }}')">
                                                <i class="fa fa-check"></i> {{ __('dashboard.Approve') }}
                                            </button>
                                        </form>
                                        <button class="btn btn-danger btn-sm mt-1" data-toggle="modal" data-target="#reject-p-{{ $p->id }}">
                                            <i class="fa fa-times"></i> {{ __('dashboard.Reject') }}
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $products->links() }}
                    @else
                        @include('dashboard.includes.alerts.nodata')
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- مودال عرض الفيديو --}}
    <div class="modal fade" id="videoModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <button type="button" class="close position-absolute" style="top: 10px; left: 10px; z-index: 1051;" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white; font-size: 24px;">&times;</span>
                    </button>
                    <video id="productVideo" width="100%" controls autoplay></video>
                </div>
            </div>
        </div>
    </div>

    @foreach($products as $p)
    <div class="modal fade" id="reject-p-{{ $p->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('products-approvals.reject', $p->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.Reject_Product') }}: {{ $p->translate(app()->getLocale())?->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <label>{{ __('dashboard.Rejection_Reason') }}</label>
                        <textarea name="rejection_reason" class="form-control" required rows="3"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('dashboard.Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('dashboard.Reject') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
@endsection

@section('js')
    {{-- Lightbox2 JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
    <script>
        // إعدادات Lightbox
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'albumLabel': "صورة %1 من %2"
        });

        // تشغيل الفيديو
        function playVideo(url) {
            $('#productVideo').attr('src', url);
            $('#videoModal').modal('show');
        }
        $('#videoModal').on('hidden.bs.modal', function () {
            $('#productVideo').attr('src', '');
        });

        // وصف المنتج - إظهار وإخفاء
        $('.description-container').each(function() {
            var text = $(this).find('.description-text');
            if (text[0].scrollWidth > text.innerWidth()) {
                $(this).find('.read-more').show();
            }
        });
        $(document).on('click', '.read-more', function() {
            var text = $(this).siblings('.description-text');
            if (text.hasClass('expanded')) {
                text.removeClass('expanded');
                $(this).text('المزيد');
            } else {
                text.addClass('expanded');
                $(this).text('إخفاء');
            }
        });
    </script>
@endsection
