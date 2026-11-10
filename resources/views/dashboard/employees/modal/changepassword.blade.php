
   <div class="modal fade " id="ModalChangePassword{{$item->id}}" tabindex="-1" role="dialog" aria-hidden="true">
    <div id="modal_div_error_container"></div>

    <div class="modal-dialog" role="document">
        <form  method="POST" id="ModalChangePassword{{$item->id}}"  enctype="multipart/form-data" action="{{ route($routeName . '.changepassword', $item->id) }}" >

            @csrf
            <input type="hidden" id="key" value="{{$item->id}}">
            @php
            $nameerror = "changepassbag_".$item->id;
            @endphp
            <div class="modal-content modal-content-demo">

                <div class="modal-header">
                    <h4 class="modal-title"> {{$item->name}}</h4>
                    <h5>{{ __('dashboard.Edit_password') }}</h5>
                </div>
                <div class="modal-body">

                        <div class="row row-sm">

                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('dashboard.New_Password') }}: <span class="tx-danger">*</span></label>
                                    <input class="form-control" id="password" name="password"  required=""  type="password" >
                                    @if ($errors->$nameerror->has('password'))

                                    <span class="tx-danger">	{{ $errors->$nameerror->first('password') }}</span>
                                    @endif
                                </div>
                            </div>


                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('dashboard.Confirm_Password') }}: <span class="tx-danger">*</span></label>
                                    <input class="form-control" id="password-confirmation" name="password-confirmation"  required=""  type="password" >


                                    @if ($errors->$nameerror->has('password-confirmation'))
                                    <span class="tx-danger">	{{ $errors->$nameerror->first('password-confirmation') }}</span>
                                    @endif

                                </div>
                            </div>



                            </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button class="btn ripple btn-primary" id="modal_div_submit_button{{ $item->id }}"  type="submit" >{{ __('dashboard.Edit') }}</button>
                    <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">{{ __('dashboard.Close') }}</button>
                </div>

            </div>
    </form>
    </div>
</div>
