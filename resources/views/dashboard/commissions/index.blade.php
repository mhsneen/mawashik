@extends('layouts.admin_dashboard')
@section('title', __('dashboard.Commissions'))
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <h4 class="content-title mb-0">{{ __('dashboard.Commissions_Management') }}</h4>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('commissions.report') }}" class="btn btn-info ml-2">
                <i class="fa fa-chart-line"></i> {{ __('dashboard.Report') }}
            </a>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <!-- Defaults card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('dashboard.Default_Commission_Rates') }}</h6></div>
                <div class="card-body">
                    @include('dashboard.includes.alerts.success')
                    <form action="{{ route('commissions.updateDefaults') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('dashboard.Trader_Default_Commission') }} %</label>
                            <input type="number" name="commission_trader_default" step="0.01" min="0" max="100" class="form-control" value="{{ $defaults['commission_trader_default'] }}" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('dashboard.Driver_Default_Commission') }} %</label>
                            <input type="number" name="commission_driver_default" step="0.01" min="0" max="100" class="form-control" value="{{ $defaults['commission_driver_default'] }}" required>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="commission_active" class="form-check-input" id="comActive" {{ $defaults['commission_active']=='1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="comActive">{{ __('dashboard.Commission_Active') }}</label>
                        </div>
                        <button class="btn btn-primary btn-block mt-2">{{ __('dashboard.Save') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Set new rate -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('dashboard.Set_User_Commission') }}</h6></div>
                <div class="card-body">
                    <form action="{{ route('commissions.setRate') }}" method="POST">
                        @csrf
                        {{-- اختيار الدور أولاً --}}
                        <div class="form-group">
                            <label>{{ __('dashboard.Role') }} <span class="tx-danger">*</span></label>
                            <select name="role" id="role_select" class="form-control" required onchange="toggleUserSelect()">
                                <option value="">{{ __('dashboard.Choose') }}</option>
                                <option value="trader">{{ __('dashboard.Trader') }}</option>
                                <option value="driver">{{ __('dashboard.Driver') }}</option>
                            </select>
                        </div>

                        {{-- قائمة التجار --}}
                        <div class="form-group" id="trader_select_group" style="display:none;">
                            <label>{{ __('dashboard.Trader') }} <span class="tx-danger">*</span></label>
                            <select name="user_id" class="form-control select2" id="trader_select">
                                <option value="">{{ __('dashboard.Choose') }}</option>
                                @foreach ($traders as $t)
                                    <option value="{{ $t->id }}" data-status="{{ $t->trader_status }}">
                                        {{ $t->name }} — {{ $t->mobile }}
                                        @if ($t->trader_status === 'pending')   [{{ __('dashboard.Pending') }}]
                                        @elseif ($t->trader_status === 'approved')  [{{ __('dashboard.Approved') }}]
                                        @elseif ($t->trader_status === 'suspended') [{{ __('dashboard.Suspended') }}]
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- قائمة المندوبين --}}
                        <div class="form-group" id="driver_select_group" style="display:none;">
                            <label>{{ __('dashboard.Driver') }} <span class="tx-danger">*</span></label>
                            <select name="user_id" class="form-control select2" id="driver_select">
                                <option value="">{{ __('dashboard.Choose') }}</option>
                                @foreach ($drivers as $d)
                                    <option value="{{ $d->id }}" data-status="{{ $d->driver_status }}">
                                        {{ $d->name }} — {{ $d->mobile }}
                                        @if ($d->driver_status === 'pending')   [{{ __('dashboard.Pending') }}]
                                        @elseif ($d->driver_status === 'approved')  [{{ __('dashboard.Approved') }}]
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>{{ __('dashboard.Rate_Percent') }} <span class="tx-danger">*</span></label>
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

        <!-- Recent commissions -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">{{ __('dashboard.Recent_Commission_Changes') }}</h6></div>
                <div class="card-body">
                    @if($commissions->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($commissions as $c)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <span>{{ $c->user?->name }}</span>
                                <span class="badge badge-{{ $c->role === 'trader' ? 'info' : 'success' }}">{{ $c->role }}</span>
                            </div>
                            <small>نسبة: {{ $c->rate }}% — من: {{ $c->effective_from?->format('Y-m-d') }} {{ $c->note ? '— ' . $c->note : '' }}</small>
                        </li>
                        @endforeach
                    </ul>
                    {{ $commissions->links() }}
                    @else
                        @include('dashboard.includes.alerts.nodata')
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        // تنشيط Select2 على القوائم
        $('.select2').select2({
            placeholder: '{{ __('dashboard.Choose') }}',
            searchInputPlaceholder: '{{ __('dashboard.Search') }}',
            width: '100%'
        });

        // تبديل ظهور قائمة التاجر/المندوب حسب الدور المختار
        function toggleUserSelect() {
            var role = document.getElementById('role_select').value;
            var traderGroup = document.getElementById('trader_select_group');
            var driverGroup = document.getElementById('driver_select_group');

            if (role === 'trader') {
                traderGroup.style.display = 'block';
                driverGroup.style.display = 'none';
                // تعطيل حقل المندوب لمنع إرساله
                var driverSelect = document.getElementById('driver_select');
                if (driverSelect) driverSelect.removeAttribute('name');
                var traderSelect = document.getElementById('trader_select');
                if (traderSelect) traderSelect.setAttribute('name', 'user_id');
            } else if (role === 'driver') {
                traderGroup.style.display = 'none';
                driverGroup.style.display = 'block';
                var traderSelect = document.getElementById('trader_select');
                if (traderSelect) traderSelect.removeAttribute('name');
                var driverSelect = document.getElementById('driver_select');
                if (driverSelect) driverSelect.setAttribute('name', 'user_id');
            } else {
                traderGroup.style.display = 'none';
                driverGroup.style.display = 'none';
            }
        }
    </script>
@endsection
