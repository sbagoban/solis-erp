<section class="content">
	<div class="row">
		<!-- left column -->
		<div class="col-md-6">	
			<div class="col-md-12">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Services</h3>
						<div id="idService" style="display:none;">0</div>
						<div id="chargeDetail" style="display:none;">0</div>
						<!-- Add Toggle here -->
						<div class="checkbox_tgl pull-right">
							
							<!-- <button type="button" class="btn btn-default" id="modalClosureDate" data-toggle="modal" data-target="#modal-closureDate">Special Closure Date</button> -->

							<label>
								<input id="on_approved" class="testClass" type="checkbox" data-toggle="toggle" data-on="Live" data-off="Not Live" data-onstyle="success">
							</label>
							<label>
								<input id="on_api" class="testClass"  type="checkbox" data-toggle="toggle" data-on="On Api" data-off="Off by Api" data-onstyle="success" disabled>
							</label>
						</div>
					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<form class="form-horizontal">
						<div class="box-body">
							<div class="form-group"> 
								<label class="col-sm-2 control-label">Date</label>
								<div class="col-sm-10">
									<div class="input-group date datepicker-in">
										<input type="text" name="daterange" id="daterangeServiceFromTo1" class="form-control" placeholder="dd-mm-yyyy"/>
										<div class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</div>
									</div>
								</div>
							</div>
							<!-- Package Start -->
							<div class="form-group">
								<div id="is_package_blk">
									<label class="col-sm-2 control-label">Package</label>
									<div class="col-sm-2">
										<select type="text" class="form-control" id="is_pakage">
											<option value="Y">YES</option>
											<option value="N" selected="selectd">NO</option>
										</select>
									</div>
								</div>
								<div id="services_block" style="display: none">
									<label class="col-sm-2 control-label">Services</label>
									<div class="col-sm-6">
										<select id="services_cost" name="services_cost" class="services_cost" multiple="multiple">
										</select>
									</div>
								</div>
								
							</div>
							<hr>
							<!-- Package End -->
							<div class="form-group"> 
								<div class="col-sm-4">
									<input type="text" class="form-control" id="id_product_service" style="display: none" value="0">
									<input type="text" class="form-control" id="id_service_type" style="display: none" value="0">
									<input type="text" class="form-control" id="id_product_type" style="display: none" value="0">
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-2 control-label">Product</label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="id_product" style="display: none">
									<input type="text" class="form-control" id="product_name" placeholder="Name of the product" readonly>
								</div>
								<label class="col-sm-2 control-label">Department</label>
								<div class="col-sm-4">
									<select type="text" class="form-control" id="id_dept">
										<!-- To modify - select from db -->
										<!-- <option value="2">Direct Sales</option> -->
										<option value="19">FIT</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Location</label>
								<div class="col-sm-4">
									<select type="text" class="form-control" id="id_country">
										<!-- To modify - select from db -->
										<!-- <option value="913">MAURITIUS</option> -->
									</select>
								</div>
								<label class="col-sm-2 control-label" id="id_coast_label">Coast</label>
								<div class="col-sm-4">
									<select class="form-control" id="id_coast">
										<option selected disabled hidden>Select an option</option>
										<option value="5">North</option>
										<option value="2">East</option>
										<option value="3">South</option>
										<option value="4">West</option>
										<option value="8">South East</option>
										<option value="9">South West</option>
										<option value="6">North East</option>
										<option value="7">North West</option>
										<option value="10">Centre</option>
										<option value="11" disabled style="display:none">Others</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Service</label>
								<div class="col-sm-6" id="id_service_1">
									<input type="text" class="form-control text-uppercase" id="service_name" placeholder="Name of the Service">
								</div>

								<div class="col-sm-6" id="id_service_2" style="display: none;">
									<select type="text" class="form-control" id="service_name_transfer">
										<option selected disabled hidden value="Select an Option">Select an Option</option>
										<option value="SOUTH EAST">SOUTH EAST</option>
										<option value="OTHER COAST">OTHER COAST</option>
										<option value="INTER HOTEL">INTER HOTEL</option>
										<option value="ACTIVITY">ACTIVITY</option>
									</select>
								</div>

								<div class="col-sm-4" id="special_name_all">
									<input type="text" class="form-control text-uppercase" id="special_name" placeholder="Special Name">
								</div>
								<div class="col-sm-4" id="special_name_transfer_blk">
									<select type="text" class="form-control" id="special_name_transfer">
										<!-- <option value="DROP ON">Drop on</option> -->
										<option value="DROP OFF">Drop Off</option>
										<option value="FULL DAY">Full Day</option>
										<option value="HALF DAY">Half Day</option>
										<option value="NIGHT TOUR">Night Tour</option>
										<option value="AIRPORT">Airport</option>
										<option value="PORT">Port</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<div id="id_creditor_blk">
									<label class="col-sm-2 control-label">Supplier</label>
									<div class="col-sm-6">
										<select type="text" class="form-control" id="id_creditor">
										</select>
									</div>
								</div>
								<div id="id_tax_blk">
									<label class="col-sm-1 control-label">Taxable</label>
									<div class="col-sm-3">
										<select type="text" class="form-control" id="id_tax">
											<!-- To modify - select from db -->
											<option value="2">OUTSIDE SCOPE</option>
											<option value="3" selected="selected">VAT</option>
											<option value="4">Exempt VAT</option>
										</select>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Cost Charge</label>
								<div class="col-sm-2">
									<select type="text" class="form-control" id="charge">
										<option value="PAX">PAX</option>
										<option value="UNIT">UNIT</option>
									</select>
								</div>
								<label class="col-sm-1 control-label" id="duration_label">Duration</label>
								<div class="col-sm-2">
									<input type="number" class="form-control" min="0" id="duration1" placeholder="Hrs">
								</div>
								<div class="col-sm-2">
									<input type="number" class="form-control" min="0" id="duration2" placeholder="Mins">
								</div>

								<!-- To uncomment when used for transfer -->
								<label class="col-sm-1 control-label" style="display: none">Transfer</label>
								<div class="col-sm-2" style="display: none">
									<select type="text" class="form-control" id="transfer_included">
										<option value="0" selected="selectd">NO</option>
										<option value="1">YES</option>
									</select>
								</div>
								<!-- To uncomment when used for transfer -->
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Description</label>
								<div class="col-sm-10">
									<textarea class="form-control" id="description" rows="5" style="resize: none"></textarea>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-2 control-label">Comment</label>
								<div class="col-sm-10">
									<textarea class="form-control" id="comments" rows="5" style="resize: none"></textarea>
								</div>
							</div>
							
							<div class="form-group" id="chk_operation">
								<label class="col-sm-2 control-label">Operation</label>
								<div class="col-sm-10" style="display: flex">
									<form class="checkbox">
										<li class="checkBoxMain">
											<label class='with-square-checkbox'>
												<input type='checkbox' class="requiredChkDate" id="on_monday" />
												<span>Monday</span>
											</label>
										</li>
										<li class="checkBoxMain">
											<label class='with-square-checkbox'>
												<input type='checkbox' class="requiredChkDate" id="on_tuesday" />
												<span>Tuesday</span>
											</label>
										</li>
										<li class="checkBoxMain">
											<label class='with-square-checkbox'>
												<input type='checkbox' class="requiredChkDate" id="on_wednesday" />
												<span>Wednesday</span>
											</label>
										</li>
										<li class="checkBoxMain">
											<label class='with-square-checkbox'>
												<input type='checkbox' class="requiredChkDate" id="on_thursday" />
												<span>Thursday</span>
											</label>
										</li>
										<li class="checkBoxMain">
											<label class='with-square-checkbox'>
												<input type='checkbox' class="requiredChkDate" id="on_friday" />
												<span>Friday</span>
											</label>
										</li>
										<li class="checkBoxMain">
											<label class='with-square-checkbox'>
												<input type='checkbox' class="requiredChkDate" id="on_saturday" />
												<span>Saturday</span>
											</label>
										</li>
										<li class="checkBoxMain">
											<label class='with-square-checkbox'>
												<input type='checkbox' class="requiredChkDate" id="on_sunday" />
												<span>Sunday</span>
											</label>
										</li>
									</form>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-2 control-label">Cancellation</label>
								<div class="col-sm-10">
									<textarea class="form-control" id="cancellation" rows="3" style="resize: none"></textarea>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-2 control-label">Pax Policy</label>
								<div class="col-sm-5">
									<div class="input-group">
										<input type="number" class="form-control" id="min_pax" min="0">
										<span class="input-group-addon">Min</span>
										<input type="number" class="form-control" id="max_pax" min="0">
										<span class="input-group-addon">Max</span>
									</div>
									<br>
								</div>
							</div>

							<div class="form-group adult_blk" style="display: none">
								<label class="col-sm-2 control-label">Adult</label>
								<div class="col-sm-5">
									<div class="input-group">
										<input type="number" class="form-control" id="max_adult" min="0">
										<span class="input-group-addon">Max Adult</span>
									</div>
									<br>
								</div>
							</div>

							<div class="form-group" id="ageActivity">
								<label class="col-sm-2 control-label">Age</label>
								<div class="col-sm-8">
									<div class="input-group">
										<input type="number" class="form-control" id="min_age" min="0">
										<span class="input-group-addon">Min Age</span>
										<input type="number" class="form-control" id="max_age" min="0">
										<span class="input-group-addon">Max Age</span>
									</div>
									<br>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-2 control-label" id="applyForLabel">Apply for</label>
								<div class="col-sm-10">
									<div class="checkbox" style="display: flex">
										<li class="checkBoxMain">
											<label class='with-square-checkbox' id="infantActivity">
												<input type='checkbox' class="requiredChkApplyFor" id="for_infant" />
												<span>For Infant</span>
											</label>
										</li>
										<li class="checkBoxMain">
											<label class='with-square-checkbox' id="childActivity">
												<input type='checkbox' class="requiredChkApplyFor" id="for_child" />
												<span>For Child</span>
											</label>
										</li>
										<li class="checkBoxMain" id="teenActivity">
											<label class='with-square-checkbox'>
												<input type='checkbox' class="requiredChkApplyFor" id="for_teen" />
												<span>For Teen</span>
											</label>
										</li>
										<li class="checkBoxMain" id="adultActivity">
											<label class='with-square-checkbox'>
												<input type='checkbox' class="requiredChkApplyFor" id="for_adult" />
												<span>For Adult</span>
											</label>
										</li>
									</div>
								</div>
							</div>

							<div class="form-group" id="blckAgePolicy">
								<label class="col-sm-2 control-label">Age Policy</label>
								<div class="col-sm-10">
									<div class="input-group">
										<input type="number" class="form-control" min="0" max="5" id="age_inf_from" placeholder="Infant From">
										<span class="input-group-addon">From</span>
										<input type="number" class="form-control" min="0" max="5" id="age_inf_to" placeholder="Infant To">
										<span class="input-group-addon">To</span>
									</div>
									<br>
									<div class="input-group">
										<input type="number" class="form-control" min="0" max="17" id="age_child_from" placeholder="Child From">
										<span class="input-group-addon">From</span>
										<input type="number" class="form-control" min="0" max="17" id="age_child_to" placeholder="Child To">
										<span class="input-group-addon">To</span>
									</div>
									<br>
									<div class="input-group">
										<input type="number" class="form-control" min="0" max="17" id="age_teen_from" placeholder="Teen From">
										<span class="input-group-addon">From</span>
										<input type="number" class="form-control" min="0" max="17" id="age_teen_to" placeholder="Teen To">
										<span class="input-group-addon">To</span>
									</div>
									<br>
								</div>
							</div>
							
							<div class="pager pull-right">
								<button type="button" class="btn btn-default" id="btn-productServices" onclick="history.go(-1);"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button>
								<button type="button" class="btn btn-default" onclick="resetServicesForm()"><i class="fa fa-refresh" aria-hidden="true"></i>&nbsp;&nbsp;Reset</button>
								<button type="button" class="btn btn-success" id="btn-saveProductServices">Save</button>
							</div>
						</div>
						<!-- /.box-body -->
					</form>
				</div>
			</div>
		</div>	

		<!-- right column -->
		<div class="col-md-6">	
			<div class="col-md-12">	
				<!-- Main content -->
				<section class="content">
					<div class="row">
						<div class="col-xs-12">
							<div class="box box-info">
								<div class="box-header">
									<h3 class="box-title">Product Service</h3>
								</div>
								<div id="id_prod_serv" style="display:none;"></div>
								<!-- /.box-header -->
								<div class="box-body" style="height:800px;">
									<table id="tbl-productServices" class="table table-bordered table-hover">
										<thead>
											<tr>
												<th>Code</th>
												<th>Product</th>
												<!-- <th class="col-sm-1">Supplier</th> -->
												<th>Dept</th>
												<th>Charges</th>
												<th>Date</th>
												<th class="col-sm-3"></th>
											</tr>
										</thead>
									</table>
								</div>
								<!-- /.box-body -->
							</div>
							<!-- /.box -->
						</div>
					<!-- /.col -->
					</div>
				<!-- /.row -->
				</section>
				<!-- /.content -->
			</div>
		</div>	
	</div>
</section>	
<!-- Modal -->
<div class="modal fade" id="modal-extraServices1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Extra Services</h4>
			</div>
			<div class="modal-body">
				<div class="col-md-12">
					<table class="table">
						<thead>
							<!-- <tr>
								<th>Extra Name</th>
								<th>Extra Description</th>
								<th>Charge Per</th>
								<th></th>
							</tr> -->
						</thead>
						<tbody>
							<tr id="addRow">
								<td class="col-xs-3">
									<select type="text" class="form-control" id="extra_name">
									</select>
								</td>

								<td class="col-xs-3">
									<input class="form-control addPrefer" id="extra_description" type="text" name="addDesc" placeholder="Enter Descrition" />
								</td>
								<td class="col-xs-5">
									<div class="policiesGroup">
										<select type="text" class="form-control" id="chargeExtra">
											<option value="UNIT" name="UNIT">UNIT</option>
											<option value="PAX" name="PAX">PAX</option>
										</select>
									</div>
								</td>
								<td class="col-xs-1 text-center">
									<span class="addBtn" id="btnAddExtraService">
										<i class="fa fa-plus fa-lg" data-toggle="tooltip" title="Add Extra Field"></i>
									</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="row" id="extraService">
					<div class="col-md-12">
						<div class="col-md-12">
							<table class="table responsive" id="tbl-extraService">
								<thead>
									<tr>
										<th scope="col">Name</th>
										<th scope="col">Description</th>
										<th scope="col">Charge Per Unit / Pax</th>
										<th scope="col"></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
				<div class="pager"></div>
			</div>
		</div>
	</div>
</div>
<!-- Modal 2 -->

<!-- Modal -->
<div class="modal fade" id="modal-closureDate" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Special Closure Date</h4>
			</div>
			<div class="modal-body">
				<div class="col-md-12">
					<!-- col 6 -->
						<!-- date picker -->
						<!-- description -->
						<!-- Add Button -->
					<!-- col 6 -->
						<!-- table -->
					<table class="table">
						<thead>
						</thead>
						<tbody>
							<tr id="addRow">
								<td class="col-xs-3">
									<div class="input-group date datepicker-in">
										<input type="text" name="daterange" id="closure_date" class="form-control" placeholder="dd-mm-yyyy"/>
										<div class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</div>
									</div>
								</td>

								<td class="col-xs-3">
									<input class="form-control" type="text" name="addDesc" placeholder="Enter Descrition" />
								</td>
								
								<td class="col-xs-1 text-center">
									<span class="addBtn" id="btnAddSpecialClosureDate">
										<i class="fa fa-plus fa-lg" data-toggle="tooltip" title="Add Extra Field"></i>
									</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="row" id="extraService">
					<div class="col-md-12">
						<div class="col-md-12">
							<table class="table responsive" id="tbl-extraService">
								<thead>
									<tr>
										<th scope="col">Name</th>
										<th scope="col">Description</th>
										<th scope="col">Charge Per Unit / Pax</th>
										<th scope="col"></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
				<div class="pager"></div>
			</div>
		</div>
	</div>
</div>
<!-- Modal 2 end -->


<!-- Modal 3 -->
<div class="modal fade" id="modal-pictures" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Upload Service Pictures <br> Code : <span id="id_product_service_modal"></span></h4>
			</div>
			<div class="modal-body">
					<div class="row">
						<div class="col-lg-12">
							<div id="drag-drop-area"></div>
						</div>
					</div>
				<div class="pager"></div>
			</div>
		</div>
	</div>
</div>
<!-- Modal 3 -->


<div class="toast jam toast_added_image" aria-hidden="true" style="display:none;">
    <span class="close" aria-role="button" tabindex="0">&times;</span> Images Added.
</div>

<div class="toast jam toast_added" aria-hidden="true" style="display:none;">
	<span class="close" aria-role="button" tabindex="0">&times;</span> Service Added.
</div>

<div class="toast jam toast_update" aria-hidden="true" style="display:none;">
	<span class="close" aria-role="button" tabindex="0">&times;</span> Service Updated.
</div>

<div class="toast jam toast_duplicate" aria-hidden="true" style="display:none;">
	<span class="close" aria-role="button" tabindex="0">&times;</span> Service Duplicate.
</div>

<div class="toast jam toast_duplicate_cost" aria-hidden="true" style="display:none;">
	<span class="close" aria-role="button" tabindex="0">&times;</span> <i class="fas fa-dollar-sign"></i> Cost Duplicate.
</div>

<div class="toast jam toast_duplicate_extra" aria-hidden="true" style="display:none;">
	<span class="close" aria-role="button" tabindex="0">&times;</span> <i class="fas fa-dollar-sign"></i> Extra Duplicate.
</div>

<div class="toast jam toast_error" aria-hidden="true" style="display:none;">
	<span class="close" aria-role="button" tabindex="0">&times;</span> <i class="fas fa-dollar-sign"></i> Please Choose A Service
</div>
		
