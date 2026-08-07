<div class="modal fade " id="ModalChangeBlock{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div id="modal_div_error_container"></div>

    <div class="modal-dialog" role="document">
        <form method="POST" id="FormModalChangeBlock{{ $item->id }}" enctype="multipart/form-data"
            action="{{ route($routeName . '.ChangeBlock', $item->id) }}" data-parsley-validate>

            @csrf
            <input type="hidden" id="key" value="{{ $item->id }}">
            @php
                $nameerror = 'changeblockbag_' . $item->id;
            @endphp
            <div class="modal-content modal-content-demo">

                <div class="modal-header">
                    <h4 class="modal-title"> {{ $item->name }}</h4>
                    <h5>{{ __('dashboard.Block_Unblock') }}</h5>
                </div>
                <div class="modal-body">

                    <div class="row row-sm">

                        <div class="col-md-12">

                             <div class="parsley-select " id="slWrapperis_blocked{{ $item->id }}">
                                    <select class="form-control select2" id="is_blocked_{{ $item->id }}" name="is_blocked"
                                        data-parsley-required="true" data-parsley-class-handler="#slWrapperis_blocked{{ $item->id }}"
                                        data-parsley-errors-container="#slErrorContaineris_blocked{{ $item->id }}" required="">
                                        <option value="">
                                            {{ __('dashboard.Choose') }}
                                        </option>
                                       <option value="0" {{ $item->is_blocked == false ? 'selected' : '' }}>
                                        {{ __('dashboard.Active') }}
                                    </option>
                                    <option value="1" {{ $item->is_blocked == true ? 'selected' : '' }}>
                                        {{ __('dashboard.Blocked') }}
                                    </option>
                                    </select>
                                    <div id="slErrorContaineris_blocked{{ $item->id }}">
                                        @if ($errors->$nameerror->has('is_blocked'))
                                            <span class="tx-danger">{{ $errors->$nameerror->first('is_blocked') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>

                        </div>

                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button class="btn ripple btn-primary" id="modal_ChangeBlock_submit_button{{ $item->id }}"
                        type="submit">{{ __('dashboard.Edit') }}</button>
                    <button class="btn ripple btn-secondary" data-dismiss="modal"
                        type="button">{{ __('dashboard.Close') }}</button>
                </div>

            </div>
        </form>
    </div>
</div>
