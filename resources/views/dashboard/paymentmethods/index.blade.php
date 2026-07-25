@extends('layouts.admin_dashboard')
@section('title')
    {{ __($namesection) }}
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto"> {{ __($namesection) }}</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.View_All') }}</span>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')

            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <h4 class="card-title mg-b-0">{{ __($namesection) }}</h4>
                    <p class="tx-13 text-muted mg-b-0">{{ __('dashboard.Payment_Methods_Desc') }}</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive border-top">
                        <table class="table card-table table-striped table-vcenter mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.Name') }}</th>
                                    <th>{{ __('dashboard.Payment_Mode') }}</th>
                                    <th>{{ __('dashboard.Status') }}</th>
                                    <th>{{ __('dashboard.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    @php($catalog = $item->catalog())
                                    <tr id="row_{{ $item->code }}">
                                        <td class="font-weight-bold">
                                            {{ app()->getLocale() == 'ar' ? ($catalog['name_ar'] ?? $item->code) : ($catalog['name_en'] ?? $item->code) }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $item->mode === 'live' ? 'badge-success' : 'badge-warning' }}">
                                                {{ $item->mode === 'live' ? __('dashboard.Live_Mode') : __('dashboard.Test_Mode') }}
                                            </span>
                                        </td>
                                        <td>
                                            <label class="custom-switch">
                                                <input type="checkbox" class="custom-switch-input toggle-gateway"
                                                    data-code="{{ $item->code }}"
                                                    data-url="{{ route('paymentmethods.toggle', $item->code) }}"
                                                    {{ $item->is_enabled ? 'checked' : '' }}>
                                                <span class="custom-switch-indicator"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <a href="{{ route('paymentmethods.edit', $item->code) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fa fa-cog"></i> {{ __('dashboard.Settings') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).on('change', '.toggle-gateway', function() {
            var checkbox = $(this);
            var enable = checkbox.is(':checked');

            $.ajax({
                url: checkbox.data('url'),
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    enable: enable ? 1 : 0
                },
                success: function(data) {
                    if (!data.success) {
                        checkbox.prop('checked', !enable);
                        swal({
                            title: data.message,
                            type: "error",
                            showConfirmButton: true
                        });
                    }
                },
                error: function(xhr) {
                    checkbox.prop('checked', !enable);
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ?
                        xhr.responseJSON.message :
                        "{{ __('dashboard.Error_DB') }}";
                    swal({
                        title: msg,
                        type: "error",
                        showConfirmButton: true
                    });
                }
            });
        });
    </script>
@endsection
