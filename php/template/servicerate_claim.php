<section class="content">
	<div class="row">
		<!-- left column -->
		<div class="col-md-6">
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">Services Claim</h3>
					<!-- Add Toggle here -->
					<div class="checkbox_tgl pull-right">
							
						<!-- <button type="button" class="btn btn-default" id="modalClosureDate" data-toggle="modal" data-target="#modal-closureDate">Special Closure Date</button> -->

						<label>
							<input id="on_approved_claim" class="testClass" type="checkbox" data-toggle="toggle" data-on="Live" data-off="Not Live" data-onstyle="success">
						</label>
						<label>
							<input id="on_api_claim" class="testClass"  type="checkbox" data-toggle="toggle" data-on="On Api" data-off="Off by Api" data-onstyle="success" disabled>
						</label>
					</div>
				</div>
				
				<div id ="alert_placeholder"></div>
				<!-- /.box-header -->
				<!-- form start -->
				<form class="form-horizontal">
					<div class="box-body">
						<div class="form-group"> 
							<div class="col-sm-4">
								<input type="text" class="form-control" id="id_product_service_cost" style="display: none" value="0">
								<input type="text" class="form-control" id="id_product_service" style="display: none" value="0">
								<input type="text" class="form-control" id="id_dept" style="display: none" value="0">
							</div>
						</div>
						<div class="form-group"> 
							<label class="col-sm-2 control-label">Date From</label>
							<div class="col-sm-10">
								<div class="input-group date datepicker-in">
									<input type="text" name="daterange" id="daterangeServiceFromTo" class="form-control" placeholder="dd-mm-yyyy"/>
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</div>
								</div>
							</div>
						</div>

						<hr>
						<div class="form-group pax_breaks">
							<label class="col-sm-2 control-label">Multiple Price</label>
							<div class="col-sm-10">
								<li class="checkBoxMain" id="multiple_price_1">
									<label class='with-square-checkbox'>
										<input type='checkbox' id="multiple_price" onclick="multiplePrice()">
										<span></span>
									</label>
								</li>
							</div>					
							<label class="col-sm-2 control-label">Charge</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="charge" placeholder="UNIT/PAX" disabled>
								<!-- for display only  --->
							</div>
							<div class="col-sm-8">
								<div class="input-group">
									<input type="number" class="form-control" id="ps_adult_claim" min="0">
									<span class="input-group-addon" id="ps_adult_claim_addon">Adult</span>
									<input type="number" class="form-control" id="ps_teen_claim" min="0">
									<span class="input-group-addon" id="ps_teen_claim_addon">Teen</span>
									<input type="number" class="form-control" id="ps_child_claim" min="0">
									<span class="input-group-addon" id="ps_child_claim_addon">Child</span>
									<input type="number" class="form-control" id="ps_infant_claim" min="0">
									<span class="input-group-addon" id="ps_infant_claim_addon">Infant</span>
								</div>
							</div>
						</div>
						<hr>

						<div class="form-group">
							<label class="col-sm-2 control-label">Roll Over</label>
							<div class="col-sm-5" id="multiRollOver">
								<select type="text" class="form-control" id="roll_over">
									<option value="Same Rate" name="Same Rate">Same Rate</option>
									<option value="On Request" name="On Request">On Request</option>
									<option value="Percentage" name="Percentage">Percentage</option>
									<option value="Fix Amount" name="Fix Amount">Fix Amount</option>
								</select>
							</div>
							<div class="col-sm-3">
								<input type="number" class="form-control"  id="txtRollOver" placeholder="Percentage / Fix Rate" min="0">
								<!-- for display only  --->
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Currency</label>
							<div class="col-sm-2">
								<select type="text" class="form-control" id="id_currency">
									<!-- To modify - select from db -->
									<option value="5">MUR</option>
								</select>
							</div>
							<!--<label class="col-sm-3 control-label">Package Claim</label>
							<div class="col-sm-5">
								<select type="text" class="form-control" id="id_currency">
									
								</select>
							</div>-->
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Specific to</label>
							<div class="col-sm-3">
								<select type="text" class="form-control" id="specific_to">
									<option value="A" name="Tour Operator">Tour Operator</option>
									<option value="B" name="Worldwide">Worldwide</option>
									<option value="C" name="Market">Market</option>
									<option value="D" name="Direct Sales">Direct Sales</option>
									<!--<option value="E" name="Local Sales">Local Sales</option>
									<option value="F" name="Mice">Mice</option>-->
								</select>
							</div>
							<div class="col-sm-7" id="multiSpecificTo">
								<select id="ddlMultiSpecificTo" name="ddlMultiSpecificTo" class="ddlMultiSpecificTo" multiple="multiple">
                                </select>
							</div>
							<div class="col-sm-7" id="multiSpecificMarket">
								<select id="ddlmultiSpecificMarket" name="ddlmultiSpecificMarket" class="ddlmultiSpecificMarket" multiple="multiple">
                                </select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Excluding</label>
								<div class="col-sm-10">
									<div class="checkbox" style="display: flex">
										<li class="checkBoxMain" id="ex_monday1" style="display: none;">
											<label class='with-square-checkbox'>
												<input type='checkbox' id="ex_monday"/>
												<span>Monday</span>
											</label>
										</li>
										<li class="checkBoxMain" id="ex_tuesday1" style="display: none;">
											<label class='with-square-checkbox'>
												<input type='checkbox' id="ex_tuesday"/>
												<span>Tuesday</span>
											</label>
										</li>
										<li class="checkBoxMain" id="ex_wednesday1" style="display: none;">
											<label class='with-square-checkbox'>
												<input type='checkbox' id="ex_wednesday"/>
												<span>Wednesday</span>
											</label>
										</li>
										<li class="checkBoxMain" id="ex_thursday1" style="display: none;">
											<label class='with-square-checkbox'>
												<input type='checkbox' id="ex_thursday"/>
												<span>Thursday</span>
											</label>
										</li>
										<li class="checkBoxMain" id="ex_friday1" style="display: none;">
											<label class='with-square-checkbox'>
												<input type='checkbox' id="ex_friday"/>
												<span>Friday</span>
											</label>
										</li>
										<li class="checkBoxMain" id="ex_saturday1" style="display: none;">
											<label class='with-square-checkbox'>
												<input type='checkbox' id="ex_saturday"/>
												<span>Saturday</span>
											</label>
										</li>
										<li class="checkBoxMain" id="ex_sunday1" style="display: none;">
											<label class='with-square-checkbox'>
												<input type='checkbox' id="ex_sunday"/>
												<span>Sunday</span>
											</label>
										</li>
									</div>
								</div>
							</div>
							
						<div class="pager pull-right">
							<button type="button" class="btn btn-default" id="btn-basckProductServices" onclick="history.go(-1);"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button>
							<button type="button" class="btn btn-default" onclick="resetProductServicesClaim()"><i class="fa fa-refresh" aria-hidden="true"></i>&nbsp;&nbsp;Reset</button>
							<button type="button" class="btn btn-success" id="btn-saveServicesClaim">Save</button>
						</div>
						
							<!-- Main content -->
							<section class="content" id="block_extra">
								<div class="row">
									<div class="col-xs-12">
										<div class="box box-info">
											<div class="box-header">
												<h3 class="box-title">Product Service Extra</h3>
											</div>
											<div id="id_product_service_claim"></div>
											<!-- /.box-header -->
											<div class="box-body">
												<table id="tbl-productServicesExtraClaim" class="table table-bordered table-hover">
												<table id="tbl-productServicesExtraClaim" class="table table-bordered table-hover">
													<thead>
														<tr>
															<th class="col-sm-1">Code</th>
															<th class="col-sm-4">Extra</th>
															<th class="col-sm-2">Charges</th>
															<th class="col-sm-2"></th>
														</tr>
													</thead>
												</table>	
											</div>
										</div>
									</div>
								</div>
							</section>
					</div>
					<!-- /.box-body -->
				</form>
			</div>
		</div>
		<!-- right column -->
		<div class="col-md-6">
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">Services Claim List</h3>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class="form-group">
						<label class="col-sm-2 control-label">Service</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="product_name_dtl" readonly="" style="text-transform: uppercase; font-size: 18px;">
						</div>
					</div>	
					<table id="tbl-productServicesClaim" class="table table-bordered table-hover">
						<thead>
							<tr>
								<th class="col-sm-1">Code</th>
								<th class="col-sm-1">Product</th>
								<th class="col-sm-1">Dept</th>
								<th class="col-sm-1">Charges</th>
								<th class="col-sm-1">CUR</th>
								<th class="col-sm-2">Date</th>
								<th class="col-sm-1">Specific</th>
								<th class="col-lg-2"></th>
								<th class="col-lg-1"></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
					<div id="myDIV"></div>
				</div>
				<!-- /.box-body -->	
			</div>
		</div>
	</div>
</section>

<!-- Modal -->
<div class="modal fade" id="modal-extraServicesClaim" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Extra Services Claim</h4>
			</div>
			<div class="modal-body">
				
					<form class="form-horizontal">
						<div class="box-body">
							<div class="form-group"> 
								<div class="col-sm-4">
									<input type="text" class="form-control" id="id_product_service_extra_claim" style="display: none" value="0">
									<div id="id_product_service_extra_cost" style="display: none">0</div>
									<div id="id_product_service_claim" style="display: none">0</div>
									<div id="product_service_claim_charge" style="display: none">0</div>
									<input type="text" class="form-control" id="id_product_service_cost" style="display: none" value="0">
									<input type="text" class="form-control" id="id_product_service" style="display: none" value="0">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Extra Name</label>
								<div class="col-sm-4">
									<select type="text" class="form-control" id="id_product_service_extra">
										<!-- To modify - select from db -->
									</select>
								</div>
							</div>
								
							<div class="form-group">
								<label class="col-sm-2 control-label">Claim</label>
								<div class="col-sm-10">
										<div class="input-group">
											<input type="number" class="form-control" id="ps_adult_claim_1" min="0">
											<span class="input-group-addon blockPax">Adult</span>
											<span class="input-group-addon blockUnit" style="display: none">Unit</span>
											<input type="number" class="form-control blockPax" id="ps_teen_claim_1" min="0">
											<span class="input-group-addon blockPax">Teen</span>
											<input type="number" class="form-control blockPax" id="ps_child_claim_1" min="0">
											<span class="input-group-addon blockPax">Child</span>
											<input type="number" class="form-control blockPax" id="ps_infant_claim_1" min="0">
											<span class="input-group-addon blockPax">Infant</span>
										</div>
									<br>
								</div>
							</div>
							
							<div class="pager">
								<button type="button" class="btn btn-primary pull-right" id="btn-saveProductServicesExtraClaim">Save</button>
								<button type="button" class="btn btn-primary pull-right" id="btn-updateProductServicesExtraClaim" style="display: none;">Update</button>
							</div>
						</div>
						<!-- /.box-body -->
					</form>
			</div>
		</div>
	</div>
</div>


<!-- Modal -->
<div class="modal fade" id="modal-paxBreakServicesClaim" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Multiple Price - Pax Breaks - Service Line : <span id="serviceLineId"></span></h4>
			</div>
			<div class="modal-body">
				
					<form class="form-horizontal">
						<div class="box-body">
								<div class="form-group pax_breaks">
													
								<div class="col-sm-2">
									<label>Type</label>
									<input type="text" class="form-control" id="charge_pax_break" placeholder="UNIT/PAX" disabled>
									<!-- for display only  --->
								</div>
								<div class="col-sm-2">
									<div class="input-group">
										<label>From</label>
										<input type="number" id="pax_from" class="form-control" min="0" placeholder="from">
									</div>
								</div>

								<div class="col-sm-2">
									<div class="input-group">
										<label>To</label>
										<input type="number" id="pax_to" class="form-control" min="0" placeholder="to">
									</div>
								</div>
								<div class="col-sm-5">
									<label>Set Claim</label>
									<div class="input-group">
										<input type="number" class="form-control" id="ps_adult_claim_modal" min="0">
										<span class="input-group-addon" id="ps_adult_claim_addon_modal">Adult</span>
										<input type="number" class="form-control" id="ps_teen_claim_modal" min="0">
										<span class="input-group-addon" id="ps_teen_claim_addon_modal">Teen</span>
										<input type="number" class="form-control" id="ps_child_claim_modal" min="0">
										<span class="input-group-addon" id="ps_child_claim_addon_modal">Child</span>
										<input type="number" class="form-control" id="ps_infant_claim_modal" min="0">
										<span class="input-group-addon" id="ps_infant_claim_addon_modal">Infant</span>
									</div>
								</div>
								<div class="col-xs-1 text-center" id="enableCounter">
                                    <span class="addBtn" id="btnCounter_pax">
                                        <i class="fa fa-plus fa-lg" data-toggle="tooltip" title="" data-original-title="Add Extra Field"></i>
                                    </span>
                                </div>
                                <div class="col-xs-1 text-center" id="disableCounter" style="display: none">
                                    <span class="addBtn" >
                                        <i class="fa fa-times fa-lg" data-toggle="tooltip" title="" data-original-title="Add Extra Field"></i>
                                    </span>
                                </div>
                                <div class="col-xs-12 text-center" style="margin: 35px 0; background: #fff">
                                	<h4>Pax Break</h4>
                                </div>
                                <div class="col-xs-12 text-center">
                                	<table id="tbl-productServicesClaimPaxBreaks" class="table table-bordered table-hover">
										<thead>
											<tr>
												<th class="col-sm-1">Code</th>
												<th class="col-sm-1">Type</th>
												<th class="col-sm-1">From</th>
												<th class="col-sm-1">To</th>
												<th class="col-sm-1">Adult</th>
												<th class="col-sm-1">Teen</th>
												<th class="col-sm-1">Child</th>
												<th class="col-sm-1">Infant</th>
												<th class="col-lg-1"></th>
											</tr>
										</thead>

										<tbody></tbody>
									</table>
                                </div>
							</div>							
							<div class="pager">
							</div>
						</div>
						<!-- /.box-body -->
					</form>
			</div>
		</div>
	</div>
</div>