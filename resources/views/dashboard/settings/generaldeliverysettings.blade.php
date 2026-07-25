@extends('layouts.admin_dashboard')
@section('title')
    {{ __('dashboard.Delivery_Settings') }}
@endsection
@section('css')
    <link href="{{ URL::asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--Internal  Datetimepicker-slider css -->
    <link href="{{ URL::asset('assets/dashboard/plugins/TimePicki-master/css/timepicki.css') }}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('dashboard.Delivery_Settings') }}</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('dashboard.Edit') }}</span>
            </div>
        </div>
        {{--  	<div class="d-flex my-xl-auto right-content">
						<div class="pr-1 mb-3 mb-xl-0">
							<button type="button" class="btn btn-info btn-icon ml-2"><i class="mdi mdi-filter-variant"></i></button>
						</div>
						<div class="pr-1 mb-3 mb-xl-0">
							<button type="button" class="btn btn-danger btn-icon ml-2"><i class="mdi mdi-star"></i></button>
						</div>
						<div class="pr-1 mb-3 mb-xl-0">
							<button type="button" class="btn btn-warning  btn-icon ml-2"><i class="mdi mdi-refresh"></i></button>
						</div>
						<div class="mb-3 mb-xl-0">
							<div class="btn-group dropdown">
								<button type="button" class="btn btn-primary">14 Aug 2019</button>
								<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" id="dropdownMenuDate" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
								</button>
								<div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuDate" data-x-placement="bottom-end">
									<a class="dropdown-item" href="#">2015</a>
									<a class="dropdown-item" href="#">2016</a>
									<a class="dropdown-item" href="#">2017</a>
									<a class="dropdown-item" href="#">2018</a>
								</div>
							</div>
						</div>
					</div> --}}
    </div>
    <!-- /breadcrumb -->
@endsection
@section('content')
    <div class="row">


        <div class="col-lg-12 col-md-12">
            @include('dashboard.includes.alerts.errors')
            @include('dashboard.includes.alerts.success')
            <div class="card">

                <div class="card-body">
                    {{--   <div class="main-content-label mg-b-5">
                    Required Input Validation
                </div>
                <p class="mg-b-20">It is Very Easy to Customize and it uses in your website apllication.</p> --}}
                    <form method="post" data-parsley-validate="" enctype="multipart/form-data"
                        action="{{ route('update.updategeneraldeliverysettings') }}">
                        @csrf


                        <div class="row row-xs formgroup-wrapper">


                            <div class="col-md-12">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label pd-t-10"> {{ __('dashboard.Store') }}  {{ __('dashboard.longitude') }} : <span
                                                    class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control  " id="longitude" required="" name="longitude"
                                                placeholder="{{ __('dashboard.Store') }} {{ __('dashboard.longitude') }}  " type="text"
                                                value="{{ $setting['longitude']->plain_value }}" data-parsley-type="number">
                                            @error('longitude')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label pd-t-10">{{ __('dashboard.Store') }} {{ __('dashboard.Latitude') }}  : <span
                                                    class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control  " id="Latitude" required="" name="Latitude"
                                                placeholder="{{ __('dashboard.Store') }} {{ __('dashboard.Latitude') }}   " type="text"
                                                value="{{ $setting['Latitude']->plain_value }}" data-parsley-type="number">
                                            @error('Latitude')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-12">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label pd-t-10">{{ __('dashboard.delivery_max_distance') }} :
                                                <span class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control  " id="delivery_max_distance" required=""
                                                name="delivery_max_distance"
                                                placeholder="{{ __('dashboard.delivery_max_distance') }}  " type="text"
                                                value="{{ $setting['delivery_max_distance']->plain_value }}"
                                                data-parsley-type="number">
                                            @error('delivery_max_distance')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>






                             <div class="col-md-12">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label
                                                class="form-label pd-t-10">{{ __('dashboard.delivery_price_type') }}
                                                :
                                                <span class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-6 parsley-select" id="slWrapperdelivery_price_type">
                                            <select class="form-control select2" id="delivery_price_type" name="delivery_price_type"
                                                required="">
                                                <option value="1" @if ($setting['delivery_price_type']->plain_value == 1) selected @endif>
                                                    {{ __('dashboard.fixed') }}</option>
                                                <option value="2" @if ($setting['delivery_price_type']->plain_value == 2) selected @endif>
                                                    {{ __('dashboard.According_To_The_Formula') }}</option>
                                            </select>
                                            @error('delivery_price_type')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

 <div class="col-md-12 ">
                                <div class="form-group " id="fixed_price_div" style="display: {{ $setting['delivery_price_type']->plain_value == 1 ? 'block' : 'none' }};">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label pd-t-10">{{ __('dashboard.fixed_delivery_price') }} : <span
                                                    class="tx-danger">*</span></label>

                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control  " id="fixed_delivery_price" name="fixed_delivery_price"
                                                placeholder="{{ __('dashboard.fixed_delivery_price') }}  "
                                                type="text" value="{{ $setting['fixed_delivery_price']->plain_value }}"
                                                data-parsley-type="number">
                                            @error('fixed_delivery_price')
                                                <span class="tx-danger">{{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12" id="dynamic_price_div" style="display: {{ $setting['delivery_price_type']->plain_value == 2 ? 'block' : 'none' }};">
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label pd-t-10">{{ __('dashboard.dynamic_delivery_pricing') }} : <span
                                                    class="tx-danger">*</span></label>
                                        </div>
                                        <div class="col-md-8">
                                            <table class="table table-bordered" id="dynamic-pricing-table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('dashboard.From_KM') }}</th>
                                                        <th>{{ __('dashboard.To_KM') }}</th>
                                                        <th>{{ __('dashboard.Price') }}</th>
                                                        <th>{{ __('dashboard.Action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $dynamicPricing = json_decode($setting['dynamic_delivery_pricing']->plain_value ?? '[]', true);
                                                    @endphp
                                                    @if(is_array($dynamicPricing) && count($dynamicPricing) > 0)
                                                        @foreach($dynamicPricing as $index => $row)
                                                            <tr>
                                                                <td><input type="number" step="0.01" class="form-control" name="dynamic_pricing[{{$index}}][from]" value="{{ $row['from'] ?? '' }}" required></td>
                                                                <td><input type="number" step="0.01" class="form-control" name="dynamic_pricing[{{$index}}][to]" value="{{ $row['to'] ?? '' }}" required></td>
                                                                <td><input type="number" step="0.01" class="form-control" name="dynamic_pricing[{{$index}}][price]" value="{{ $row['price'] ?? '' }}" required></td>
                                                                <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td><input type="number" step="0.01" class="form-control" name="dynamic_pricing[0][from]" required></td>
                                                            <td><input type="number" step="0.01" class="form-control" name="dynamic_pricing[0][to]" required></td>
                                                            <td><input type="number" step="0.01" class="form-control" name="dynamic_pricing[0][price]" required></td>
                                                            <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4">
                                                            <button type="button" class="btn btn-success btn-sm" id="add-pricing-row"><i class="fa fa-plus"></i>  {{ __('dashboard.Add_Pricing') }}</button>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>




                        </div>
                        <div class="card-footer text-center">

                            <button class="btn btn-success-gradient mg-10" type="submit">{{ __('dashboard.Edit') }} <i
                                    class="typcn typcn-clipboard"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('js')
    <!--Internal  Parsley.min js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/parsley.min.js') }}"></script>
    <!-- Internal Form-validation js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/parsleyjs/i18n/' . app()->getLocale() . '.js') }}"></script>
    <!-- Internal Select2 js-->
    <script src="{{ URL::asset('assets/dashboard/plugins/select2/js/select2.min.js') }}"></script>

    <!--Internal  jquery-simple-datetimepicker js -->
    <script src="{{ URL::asset('assets/dashboard/plugins/TimePicki-master/js/timepicki.js') }}"></script>






    <script>
        $('.select2').select2({
            placeholder: '{{ __('dashboard.Choose') }}',
            searchInputPlaceholder: '{{ __('dashboard.Search') }}',
            width: '100%'
        });

        $('.timepicker').timepicki({
            show_meridian: false
        });

        $(document).ready(function() {
            $('#delivery_price_type').change(function() {
                if ($(this).val() == '1') {
                    $('#fixed_price_div').show();
                    $('#dynamic_price_div').hide();
                    $('#fixed_price_div input').attr('required', 'required');
                    $('#dynamic_price_div input').removeAttr('required');
                } else if ($(this).val() == '2') {
                    $('#fixed_price_div').hide();
                    $('#dynamic_price_div').show();
                    $('#fixed_price_div input').removeAttr('required');
                    $('#dynamic_price_div input').attr('required', 'required');
                }
            });
            $('#delivery_price_type').trigger('change');

            let rowIdx = $('#dynamic-pricing-table tbody tr').length;
            $('#add-pricing-row').click(function() {
                $('#dynamic-pricing-table tbody').append(`
                    <tr>
                        <td><input type="number" step="0.01" class="form-control" name="dynamic_pricing[${rowIdx}][from]" required></td>
                        <td><input type="number" step="0.01" class="form-control" name="dynamic_pricing[${rowIdx}][to]" required></td>
                        <td><input type="number" step="0.01" class="form-control" name="dynamic_pricing[${rowIdx}][price]" required></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></td>
                    </tr>
                `);
                rowIdx++;
            });

            $(document).on('click', '.remove-row', function() {
                if ($('#dynamic-pricing-table tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                } else {
                    alert('يجب أن تحتوي التسعيرة الديناميكية على صف واحد على الأقل.');
                }
            });
        });
    </script>
@endsection
