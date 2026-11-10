@extends('layouts.admin_dashboard')
@section('title', __('dashboard.Trader_Details'))
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <h4 class="content-title mb-0">{{ __('dashboard.Trader_Details') }} - {{ $trader->name }}</h4>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('traders.edit', $trader->id) }}" class="btn btn-warning ml-2">
                <i class="fa fa-edit"></i> {{ __('dashboard.Edit') }}
            </a>
            <a href="{{ route('traders.index') }}" class="btn btn-secondary ml-2">
                <i class="fa fa-arrow-right"></i> {{ __('dashboard.Back') }}
            </a>
        </div>
    </div>
@endsection

@section('css')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #traderMap { height: 400px; width: 100%; border-radius: 8px; z-index: 1; }
        .leaflet-popup-content-wrapper { direction: rtl; text-align: right; }
    </style>
@endsection

@section('content')
    <div class="row">
        <!-- بيانات التاجر -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('dashboard.Trader_Info') }}</h6></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        {{-- تم إزالة ID --}}
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('dashboard.Trader_Name') }}:</span> <strong>{{ $trader->name }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('dashboard.Mobile') }}:</span> <strong style="direction: ltr">{{ $trader->mobile }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('dashboard.Commercial_Register') }}:</span>
                            <strong>{{ $trader->commercial_register }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('dashboard.City') }}:</span>
                            <strong>{{ $trader->city?->translate(app()->getLocale())?->name }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('dashboard.Store_Address') }}:</span>
                            <strong>{{ $trader->store_address }}</strong>
                        </li>
                        {{-- تم إزالة الموقع من الجدول --}}
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('dashboard.Status') }}:</span>
                            @php
                                $statusColors = ['pending' => 'warning', 'approved' => 'success', 'suspended' => 'danger', 'rejected' => 'dark'];
                                $statusLabel = 'dashboard.'.ucfirst($trader->trader_status);
                            @endphp
                            <span class="badge badge-{{ $statusColors[$trader->trader_status] ?? 'secondary' }}">{{ __($statusLabel) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('dashboard.Commission_Rate') }}:</span>
                            <strong>
                                @if ($trader->commission_rate !== null)
                                    {{ number_format($trader->commission_rate, 2) }}%
                                @else
                                    <span class="text-muted">{{ __('dashboard.Using_Default_Commission') }}</span>
                                @endif
                            </strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('dashboard.Terms_Accepted') }}:</span>
                            @if ($trader->terms_accepted ?? false)
                                <span class="badge badge-success"><i class="fa fa-check mr-1"></i>{{ __('dashboard.Yes') }}</span>
                            @else
                                <span class="badge badge-danger"><i class="fa fa-times mr-1"></i>{{ __('dashboard.No') }}</span>
                            @endif
                        </li>
                        @if ($trader->approved_at)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('dashboard.Approved_At') }}:</span>
                            <strong>{{ $trader->approved_at->format('Y-m-d H:i') }}</strong>
                        </li>
                        @endif
                        @if ($trader->admin_status_note)
                        <li class="list-group-item">
                            <span class="d-block mb-1">{{ __('dashboard.Admin_Note') }}:</span>
                            <strong class="text-danger">{{ $trader->admin_status_note }}</strong>
                        </li>
                        @endif
                    </ul>
                </div>
                @if($trader->trader_status === 'approved')
                <div class="card-footer">
                    <form action="{{ route('traders.updateCommission', $trader->id) }}" method="POST" class="form-inline">
                        @csrf
                        <input type="number" name="commission_rate" class="form-control mr-2" step="0.01" min="0" max="100" placeholder="%" value="{{ $trader->commission_rate }}">
                        <button class="btn btn-primary">{{ __('dashboard.Update_Commission') }}</button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <!-- إحصائيات -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('dashboard.Trader_Stats') }}</h6></div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3>{{ $stats['products_count'] }}</h3>
                                    <small>{{ __('dashboard.Products') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-success">{{ $stats['approved_count'] }}</h3>
                                    <small>{{ __('dashboard.Approved') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-warning">{{ $stats['pending_count'] }}</h3>
                                    <small>{{ __('dashboard.Pending') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3>{{ $stats['orders_count'] }}</h3>
                                    <small>{{ __('dashboard.Orders') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-success">{{ $stats['delivered_count'] }}</h3>
                                    <small>{{ __('dashboard.Delivered') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3>{{ number_format($stats['revenue_total'], 2) }}</h3>
                                    <small>{{ __('dashboard.Total_Revenue') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- إجراءات الاعتماد -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('dashboard.Approval_Actions') }}</h6></div>
                <div class="card-body">
                    @include('dashboard.includes.alerts.success')
                    @if($trader->trader_status === 'pending')
                        <form action="{{ route('traders.approve', $trader->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-success btn-block mb-2"><i class="fa fa-check"></i> {{ __('dashboard.Approve_Trader') }}</button>
                        </form>
                        <button class="btn btn-danger btn-block" data-toggle="modal" data-target="#reject-modal">
                            <i class="fa fa-times"></i> {{ __('dashboard.Reject_Trader') }}
                        </button>
                    @elseif($trader->trader_status === 'approved')
                        <form action="{{ route('traders.suspend', $trader->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="note" value="إيقاف من الإدارة">
                            <button class="btn btn-danger btn-block" onclick="return confirm('{{ __('dashboard.Confirm_Suspend') }}')">
                                <i class="fa fa-ban"></i> {{ __('dashboard.Suspend_Trader') }}
                            </button>
                        </form>
                    @elseif($trader->trader_status === 'suspended')
                        <form action="{{ route('traders.reactivate', $trader->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-success btn-block"><i class="fa fa-redo"></i> {{ __('dashboard.Reactivate_Trader') }}</button>
                        </form>
                    @endif

                    <hr>
                    <h6 class="mt-3">{{ __('dashboard.Set_New_Commission_Rate') }}</h6>
                    <form action="{{ route('traders.setCommissionRate', $trader->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('dashboard.Rate_Percent') }}</label>
                            <input type="number" name="rate" step="0.01" min="0" max="100" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('dashboard.Note') }}</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                        <button class="btn btn-warning btn-block">{{ __('dashboard.Set_Rate') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- خريطة موقع التاجر (Leaflet) -->
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fa fa-map-marker-alt text-danger"></i> {{ __('dashboard.Location_on_Map') }}</h6>
                </div>
                <div class="card-body">
                    <div id="traderMap"></div>
                </div>
            </div>
        </div>

    </div> <!-- نهاية صف الكروت -->

    <!-- Reject Modal -->
    <div class="modal fade" id="reject-modal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('traders.reject', $trader->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.Reject_Trader') }}</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <label>{{ __('dashboard.Rejection_Reason') }}</label>
                        <textarea name="note" class="form-control" required rows="3"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('dashboard.Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('dashboard.Reject') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // جلب الإحداثيات (مع قيم افتراضية للرياض لو كانت فاضية)
            const lat = parseFloat('{{ $trader->store_lat }}') || 24.7136;
            const lng = parseFloat('{{ $trader->store_lng }}') || 46.6753;

            // تهيئة الخريطة
            const map = L.map('traderMap').setView([lat, lng], 15);

            // إضافة طبقة الخريطة (OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // إضافة الدبوس (Marker)
            const marker = L.marker([lat, lng]).addTo(map);

            // إضافة نافذة معلومات (Popup) تظهر اسم المتجر وعنوانه
            const traderName = '{{ $trader->name }}';
            const traderAddress = '{{ $trader->store_address ?? "" }}';

            marker.bindPopup(`<div style="text-align: right;"><strong>${traderName}</strong><br><span>${traderAddress}</span></div>`).openPopup();
        });
    </script>
@endsection
