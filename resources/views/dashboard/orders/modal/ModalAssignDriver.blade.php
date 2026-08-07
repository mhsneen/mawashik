<div class="modal fade " id="ModalAssignDriver" tabindex="-1" role="dialog" aria-hidden="true">

				<div class="modal-dialog" role="document">
					<form  method="POST" id="ModalAssignDrivervalid"  action="{{ route($routeName . '.assign_driver') }}" >
						@csrf
					<input type="hidden" id="assign_id" name="id" value="{{$item->id}}">
					<div class="modal-content ">
						<div class="modal-header">
                    <h4 class="modal-title"> {{ __('dashboard.Order_number') }} {{ $item->order_number }}</h4>
                    <h5>{{ __('dashboard.Assign_Driver') }}</h5>
                				</div>
										<div class="modal-body">

											<div class="row row-sm">

												<div class="col-12">
													<div class="form-group">
														<label class="form-label">{{ __('dashboard.Driver') }}: <span class="tx-danger">*</span></label>
														<div class="parsley-select " id="slWrapperdriver_id">
															<select class="form-control selectwithoutsearch" data-parsley-class-handler="#slWrapperdriver_id" data-parsley-errors-container="#slErrorContainerdriver_id" data-placeholder="{{ __('dashboard.Choose_Driver') }}" id="driver_id" name="driver_id" required="">
																<option value="  " >
																	{{ __('dashboard.Choose_Driver') }}
																</option>
																@forelse ($availableDrivers as $driver)
																<option value="{{ $driver->id }}" {{ $item->driver_id == $driver->id ? "selected" :""}}>
																	{{ $driver->name }} — {{ $driver->mobile }}
																</option>
																@empty
																<option value="" disabled>{{ __('dashboard.No_Available_Drivers') }}</option>
																@endforelse
															</select>
															<div id="slErrorContainerdriver_id">
																@if ($errors->AssignDriverBag->has('driver_id'))
																<span class="tx-danger">{{ $errors->AssignDriverBag->first('driver_id') }} </span>
																@endif
															</div>

														</div>
													</div>
												</div>
											</div>



										</div>
										</div>
						<div class="modal-footer">
							<button class="btn ripple btn-primary" id="modal_assigndriver_submit_button"  type="submit" >{{ __('dashboard.Edit') }}</button>
							<button class="btn ripple btn-secondary" data-dismiss="modal" type="button">{{ __('dashboard.Close') }}</button>
						</div>
					</div>
					</form>
				</div>
			</div>
