<div class="modal fade " id="ModalChangeStatusOrder" tabindex="-1" role="dialog" aria-hidden="true">

			<div class="modal-dialog" role="document">
				<form  method="POST" id="ModalChangeStatusOrdervalid"  enctype="multipart/form-data" action="{{ route($routeName . '.changeorder_status') }}" >
					@csrf
					<input type="hidden" id="id" name="id" value="{{$item->id}}">
				<div class="modal-content ">
					<div class="modal-header">
                    <h4 class="modal-title"> {{ __('dashboard.Order_number') }} {{ $item->order_number }}</h4>
                    <h5>{{ __('dashboard.Change_Order_Status') }}</h5>
                					</div>
									<div class="modal-body">

										<div class="row row-sm">

											<div class="col-12">
												<div class="form-group">
													<label class="form-label">{{ __('dashboard.Order_Status') }}: <span class="tx-danger">*</span></label>
													<div class="parsley-select " id="slWrapperis_active">
														<select class="form-control selectwithoutsearch" data-parsley-class-handler="#slWrapperis_active" data-parsley-errors-container="#slErrorContaineris_active" data-placeholder="{{ __('dashboard.Choose') }}" id="status_id" name="status_id" required="">
															<option value="  " >
																{{ __('dashboard.Choose') }}
															</option>
															@foreach ($allowedStatuses as $order_status)

															<option value="{{ $order_status->id }}" {{ $item->status_id == $order_status->id ? "selected" :""}}>
																{{ $order_status->name }}
															</option>
															@endforeach
														</select>
														<div id="slErrorContaineris_active">
															@if ($errors->ModalChangeStatusOrderbag->has('status_id'))
															<span class="tx-danger">{{ $errors->ModalChangeStatusOrderbag->first('status_id') }} </span>
															@endif
														</div>

												</div>
											</div>
										</div>




									</div>
											</div>
					<div class="modal-footer">
						<button class="btn ripple btn-primary" id="modal_changeorder_submit_button"  type="submit" >{{ __('dashboard.Edit') }}</button>
						<button class="btn ripple btn-secondary" data-dismiss="modal" type="button">{{ __('dashboard.Close') }}</button>
					</div>
				</div>
				</form>
			</div>
		</div>
