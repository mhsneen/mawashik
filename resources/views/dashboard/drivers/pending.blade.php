@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Pending_Drivers') }} - {{ __($namesection) }}
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">
                    <i class="fa fa-clock text-warning mr-2"></i>{{ __('dashboard.Pending_Drivers') }}
                </h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.Pending_Approval_List') }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route($routeName) }}" class="btn btn-info btn-icon"><i class="typcn typcn-arrow-back-outline"></i></a>
        </div>
    </div>
@endsection
@section('content')
    <div class="row row-sm">
        <div class="col-lg-12">
            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered table-hover mb-0 text-nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('dashboard.Name') }}</th>
                                    <th>{{ __('dashboard.Mobile') }}</th>
                                    <th>{{ __('dashboard.Image') }}</th>
                                    <th>{{ __('dashboard.ID_Number') }}</th>
                                    <th>{{ __('dashboard.License_Image') }}</th>
                                    <th>{{ __('dashboard.subscribed_since') }}</th>
                                    <th>{{ __('dashboard.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td dir="ltr">{{ $item->mobile }}</td>
                                          <td>
                                            @if (!empty($item->image))
                                                <a href="{{ URL::asset('uploads/users/' . $item->image) }}" target="_blank">
                                                    <img src="{{ URL::asset('uploads/users/thumbnail/' . $item->image) }}"
                                                         alt="ID" class="img-fluid"
                                                         style="max-height: 50px; border-radius: 4px;">
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>


                                        <td dir="ltr">{{ $item->id_number ?: '—' }}</td>
                                        <td>
                                            @if (!empty($item->license_image))
                                                <a href="{{ URL::asset('uploads/users/' . $item->license_image) }}" target="_blank">
                                                    <img src="{{ URL::asset('uploads/users/thumbnail/' . $item->license_image) }}"
                                                         alt="License" class="img-fluid"
                                                         style="max-height: 50px; border-radius: 4px;">
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->created_at->diffForHumans() }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                {{-- عرض --}}
                                                <a href="{{ route($routeName . '.show', $item->id) }}"
                                                   class="btn btn-info btn-icon" data-toggle="tooltip"
                                                   title="{{ __('dashboard.View') }}">
                                                    <i class="las la-eye"></i>
                                                </a>
                                                {{-- اعتماد --}}
                                                <form action="{{ route('drivers.approve', $item->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-icon"
                                                            data-toggle="tooltip"
                                                            title="{{ __('dashboard.Approve') }}"
                                                            onclick="return confirm('{{ __('dashboard.Confirm_Approve_Driver') }}')">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                </form>
                                                {{-- رفض --}}
                                                <a href="{{ route($routeName . '.edit', $item->id) }}"
                                                   class="btn btn-danger btn-icon" data-toggle="tooltip"
                                                   title="{{ __('dashboard.Reject') }}">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5">
                                            <i class="fa fa-check-circle text-success fa-3x mb-2 d-block"></i>
                                            {{ __('dashboard.No_Pending_Drivers') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
