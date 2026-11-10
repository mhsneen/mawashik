<div class="modal fade " id="ModalChangeMethodStatusOrder" tabindex="-1" role="dialog" aria-hidden="true">

			<div class="modal-dialog" role="document">
				<form  method="POST" id="ModalMethodStatusOrdervalid"  enctype="multipart/form-data" action="{{ route($routeName . '.changeorder_payment_status') }}" >
					@csrf
					<input type="hidden" id="id" name="id" value="{{$item->id}}">
				<div class="modal-content ">
					<div class="modal-header">
                    <h4 class="modal-title"> {{ __('dashboard.Order_number') }} {{ $item->order_number }}</h4>
                    <h5>{{ __('dashboard.Change_payment_status') }}</h5>
                					</div>
									<div class="modal-body">

										<div class="row row-sm">

											<div class="col-12">
												<div class="form-group">
													<label class="form-label">{{ __('dashboard.Payment_Status') }}: <span class="tx-danger">*</span></label>
													<div class="parsley-select " id="slWrapperis_active">
														<select class="form-control selectwithoutsearch" data-parsley-class-handler="#slWrapperis_active" data-parsley-errors-container="#slErrorContaineris_activepaymentstatus" data-placeholder="{{ __('dashboard.Choose') }}" id="payment_status" name="payment_status" required="">
															<option value="  " >
																{{ __('dashboard.Choose') }}
															</option>
															@foreach ($paymentstatus as $payment_status)

															<option value="{{ $payment_status->id }}" {{ $item->payment_status == $payment_status->id ? "selected" :""}}>
																{{ $payment_status->name }}
															</option>
															@endforeach
														</select>
														<div id="slErrorContaineris_activepaymentstatus">
															@if ($errors->ModalChangeMethodStatusOrderbag->has('payment_status'))
															<span class="tx-danger">{{ $errors->ModalChangeMethodStatusOrderbag->first('payment_status') }} </span>
															@endif
														</div>

												</div>
											</div>
										</div>




									</div>
											</div>
					<div class="modal-footer">
						<button class="btn ripple btn-primary" id="modal_changemethodstatus_submit_button"  type="submit" >{{ __('dashboard.Edit') }}</button>
						<button class="btn ripple btn-secondary" data-dismiss="modal" type="button">{{ __('dashboard.Close') }}</button>
					</div>
				</div>
				</form>
			</div>
		</div>
