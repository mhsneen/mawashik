@extends('layouts.admin_dashboard')
@section('title', __('dashboard.Reviews_Management'))
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <h4 class="content-title mb-0">{{ __('dashboard.Reviews_Management') }}</h4>
        </div>
    </div>
@endsection
@section('content')
    @include('dashboard.includes.alerts.success')

    <ul class="nav nav-pills mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $type === 'order' ? 'active' : '' }}" href="{{ route('reviews', ['type' => 'order']) }}">
                {{ __('dashboard.Trader_Order_Reviews') }}
                <span class="badge badge-light">{{ $stats['order']['count'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $type === 'driver' ? 'active' : '' }}" href="{{ route('reviews', ['type' => 'driver']) }}">
                {{ __('dashboard.Driver_Reviews') }}
                <span class="badge badge-light">{{ $stats['driver']['count'] }}</span>
            </a>
        </li>
    </ul>

    <div class="row mb-3">
        <div class="col-md-4"><div class="card bg-warning text-white"><div class="card-body text-center">
            <h3>{{ $stats[$type]['avg'] ?: '—' }} ★</h3><small>{{ __('dashboard.Average_Rating') }}</small>
        </div></div></div>
        <div class="col-md-4"><div class="card bg-info text-white"><div class="card-body text-center">
            <h3>{{ $stats[$type]['count'] }}</h3><small>{{ __('dashboard.Total_Reviews') }}</small>
        </div></div></div>
        <div class="col-md-4"><div class="card bg-secondary text-white"><div class="card-body text-center">
            <h3>{{ $stats[$type]['hidden'] }}</h3><small>{{ __('dashboard.Hidden_Reviews') }}</small>
        </div></div></div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row mb-3">
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="{{ __('dashboard.Search') }}" value="{{ request('search') }}"></div>
                <div class="col-md-2">
                    <select name="rating" class="form-control">
                        <option value="">{{ __('dashboard.Rating') }}</option>
                        @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" @selected(request('rating') == $i)>{{ $i }} ★</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="visibility" class="form-control">
                        <option value="">{{ __('dashboard.All') }}</option>
                        <option value="visible" @selected(request('visibility') === 'visible')>{{ __('dashboard.Visible') }}</option>
                        <option value="hidden" @selected(request('visibility') === 'hidden')>{{ __('dashboard.Hidden') }}</option>
                    </select>
                </div>
                <div class="col-md-3"><button class="btn btn-primary btn-block">{{ __('dashboard.Search') }}</button></div>
            </form>

            @if ($items->count())
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('dashboard.Order') }}</th>
                                <th>{{ __('dashboard.Client') }}</th>
                                <th>{{ $type === 'driver' ? __('dashboard.Driver') : __('dashboard.Trader') }}</th>
                                <th>{{ __('dashboard.Rating') }}</th>
                                <th>{{ __('dashboard.Comment') }}</th>
                                <th>{{ __('dashboard.Date') }}</th>
                                <th>{{ __('dashboard.Status') }}</th>
                                <th>{{ __('dashboard.Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $r)
                                <tr>
                                    <td>{{ $r->id }}</td>
                                    <td>
                                        @if ($r->order)
                                            <a href="{{ route('orders.show', $r->order->id) }}">{{ $r->order->order_number }}</a>
                                        @else — @endif
                                    </td>
                                    <td>{{ $r->client?->name }}<br><small dir="ltr">{{ $r->client?->mobile }}</small></td>
                                    <td>{{ $type === 'driver' ? $r->driver?->name : $r->trader?->name }}</td>
                                    <td><span class="text-warning">{{ str_repeat('★', (int) $r->rating) }}</span><span class="text-muted">{{ str_repeat('★', 5 - (int) $r->rating) }}</span></td>
                                    <td style="max-width:280px; white-space: normal;">{{ $r->comment ?: '—' }}</td>
                                    <td>{{ $r->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $r->is_hidden ? 'secondary' : 'success' }}">
                                            {{ $r->is_hidden ? __('dashboard.Hidden') : __('dashboard.Visible') }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('reviews.toggle', [$type, $r->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-{{ $r->is_hidden ? 'success' : 'warning' }}">
                                                {{ $r->is_hidden ? __('dashboard.Show') : __('dashboard.Hide') }}
                                            </button>
                                        </form>
                                        <form action="{{ route('reviews.delete', [$type, $r->id]) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('{{ __('dashboard.Are_You_Sure') }}')">
                                            @csrf
                                            <button class="btn btn-sm btn-danger">{{ __('dashboard.Delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $items->links() }}
            @else
                @include('dashboard.includes.alerts.nodata')
            @endif
        </div>
    </div>
@endsection
