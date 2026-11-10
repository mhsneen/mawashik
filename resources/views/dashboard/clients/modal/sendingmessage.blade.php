<div class="modal fade " id="ModalSendingMessage{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div id="modal_div_error_container"></div>

    <div class="modal-dialog" role="document">
        <form method="POST" id="ModalSendingMessage{{ $item->id }}" enctype="multipart/form-data"
            action="{{ route($routeName . '.sendingmessage', $item->id) }}" data-parsley-validate>

            @csrf
            <input type="hidden" id="key" value="{{ $item->id }}">
            @php
                $nameerroressage = 'sendingmessagebag_' . $item->id;
            @endphp
            <div class="modal-content modal-content-demo">

                <div class="modal-header">
                    <h4 class="modal-title"> {{ $item->name }}</h4>
                    <h5>{{ __('dashboard.Sending_Message') }}</h5>
                </div>
                <div class="modal-body">

                    <div class="row row-sm">

                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">{{ __('dashboard.Message_type') }}: <span
                                        class="tx-danger">*</span></label>



                                <div class="parsley-select " id="slWrapperis_typemsg{{ $item->id }}">
                                    <select class="form-control select2" id="msg_type_{{ $item->id }}"
                                        name="msg_type" data-parsley-required="true"
                                        data-parsley-class-handler="#slWrapperis_typemsg{{ $item->id }}"
                                        data-parsley-errors-container="#slErrorContaineris_typemsg{{ $item->id }}"
                                        required="">
                                        <option value="">
                                            {{ __('dashboard.Choose') }}
                                        </option>
                                        <option value="0" {{ old('msg_type') == '0' ? 'selected' : '' }}>
                                            {{ __('dashboard.Notice_from_app') }}
                                        </option>
                                        <option value="1" {{ old('msg_type') == '1' ? 'selected' : '' }}>
                                            {{ __('dashboard.sms') }}
                                        </option>
                                    <!--

    <option value="2" {{ old('msg_type') == '2' ? 'selected' : '' }}>
                                            {{ __('dashboard.Whatsapp') }}
                                        </option>
                                    -->
                                    </select>
                                    <div id="slErrorContaineris_typemsg{{ $item->id }}">
                                        @if ($errors->$nameerroressage->has('msg_type'))
                                            <span class="tx-danger">{{ $errors->$nameerroressage->first('msg_type') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>


                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">{{ __('dashboard.Title') }}: <span
                                        class="tx-danger">*</span></label>
                                <input class="form-control " id="msg_title_{{ $item->id }}" name="msg_title"
                                    placeholder="{{ __('dashboard.Title') }}  " required="" type="text">


                                @if ($errors->$nameerroressage->has('msg_title'))
                                    <span class="tx-danger"> {{ $errors->$nameerroressage->first('msg_title') }}</span>
                                @endif

                            </div>
                        </div>



                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">{{ __('dashboard.Message_text') }}: <span
                                        class="tx-danger">*</span></label>
                                <textarea class="form-control " placeholder="{{ __('dashboard.Message_text') }} " rows="5"
                                    id="msg_details_{{ $item->id }}" name="msg_details" required=""> </textarea>


                                @if ($errors->$nameerroressage->has('msg_details'))
                                    <span class="tx-danger">
                                        {{ $errors->$nameerroressage->first('msg_details') }}</span>
                                @endif

                            </div>
                        </div>



                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button class="btn ripple btn-primary" id="modal_sendingmsg_submit_button{{ $item->id }}"
                        type="submit">{{ __('dashboard.Send') }}</button>
                    <button class="btn ripple btn-secondary" data-dismiss="modal"
                        type="button">{{ __('dashboard.Close') }}</button>
                </div>

            </div>
        </form>
    </div>
</div>
