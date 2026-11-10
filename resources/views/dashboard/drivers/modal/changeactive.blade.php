<div class="modal fade " id="ModalChangeActive{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div id="modal_div_error_container"></div>

    <div class="modal-dialog" role="document">
        <form method="POST" id="FormModalChangeActive{{ $item->id }}" enctype="multipart/form-data"
            action="{{ route($routeName . '.ChangeActive', $item->id) }}" data-parsley-validate>

            @csrf
            <input type="hidden" id="key" value="{{ $item->id }}">
            @php
                $nameerror = 'changeactivebag_' . $item->id;
            @endphp
            <div class="modal-content modal-content-demo">

                <div class="modal-header">
                    <h4 class="modal-title"> {{ $item->name }}</h4>
                    <h5>{{ __('dashboard.Activate_Deactivate') }}</h5>
                </div>
                <div class="modal-body">

                    <div class="row row-sm">


                        <div class="col-md-12">


                             <div class="parsley-select " id="slWrapperis_active{{ $item->id }}">
                                    <select class="form-control select2" id="is_active_{{ $item->id }}" name="is_active"
                                        data-parsley-required="true" data-parsley-class-handler="#slWrapperis_active{{ $item->id }}"
                                        data-parsley-errors-container="#slErrorContaineris_active{{ $item->id }}" required="">
                                        <option value="">
                                            {{ __('dashboard.Choose') }}
                                        </option>
                                       <option value="1" {{ $item->is_active == true ? 'selected' : '' }}>
                                        {{ __('dashboard.effective') }}
                                    </option>
                                    <option value="0" {{ $item->is_active == false ? 'selected' : '' }}>
                                        {{ __('dashboard.Inactive') }}
                                    </option>
                                    </select>
                                    <div id="slErrorContaineris_active{{ $item->id }}">
                                        @if ($errors->$nameerror->has('is_active'))
                                            <span class="tx-danger">{{ $errors->$nameerror->first('is_active') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>




                        </div>





                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button class="btn ripple btn-primary" id="modal_ChangeActive_submit_button{{ $item->id }}"
                        type="submit">{{ __('dashboard.Edit') }}</button>
                    <button class="btn ripple btn-secondary" data-dismiss="modal"
                        type="button">{{ __('dashboard.Close') }}</button>
                </div>

            </div>
        </form>
    </div>
</div>
