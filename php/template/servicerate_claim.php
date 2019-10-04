<section class="content">
	<div class="row">
		<!-- left column -->
		<div class="col-md-6">
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">Services Claim</h3>
				</div>
				
<div id ="alert_placeholder"></div>
				<!-- /.box-header -->
				<!-- form start -->
				<form class="form-horizontal">
					<div class="box-body">
						<div class="form-group"> 
							<div class="col-sm-4">
								<input type="text" class="form-control" id="id_product_services_cost" style="display: none" value="0">
								<input type="text" class="form-control" id="id_product_services" style="display: none" value="0">
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
						<div class="form-group">
							<label class="col-sm-2 control-label">Charge</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="charges" placeholder="UNIT/PAX" disabled>
								<!-- for display only  --->
							</div>
							<div class="col-sm-8">
								<div class="input-group">
									<input type="number" class="form-control" id="ps_adult_claim">
									<span class="input-group-addon" id="ps_adult_claim_addon">Adult</span>
									<input type="number" class="form-control" id="ps_teen_claim">
									<span class="input-group-addon" id="ps_teen_claim_addon">Teen</span>
									<input type="number" class="form-control" id="ps_child_claim">
									<span class="input-group-addon" id="ps_child_claim_addon">Child</span>
									<input type="number" class="form-control" id="ps_infant_claim">
									<span class="input-group-addon" id="ps_infant_claim_addon">Infant</span>
								</div>
								<br>
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
							<button type="button" class="btn btn-primary" id="btn-saveServicesClaim">Save</button>
						</div>
						
							<!-- Main content -->
							<section class="content">
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
													<thead>
														<tr>
															<th class="col-sm-1">Code</th>
															<th class="col-sm-2">Extra</th>
															<th class="col-sm-5">Charges</th>
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
							<label class="control-label" id="product_name">product + service from product service + supplier name + Dept for + Coast</label>
						</div>
					</div>
					<table id="tbl-productServicesClaim" class="table table-bordered table-hover">
						<thead>
							<tr>
								<th class="col-sm-1">Code</th>
								<th class="col-sm-2">Product</th>
								<th class="col-sm-1">Dept</th>
								<th class="col-sm-1">Charges</th>
								<th class="col-sm-1">CUR</th>
								<th class="col-sm-2">Date</th>
								<th class="col-sm-2">Specific</th>
								<th class="col-sm-2"></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
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
									<input type="text" class="form-control" id="id_product_services_extra_claim" style="display: none" value="0">
									<div id="id_product_services_extra_cost" style="display: none">0</div>
									<div id="id_product_services_claim" style="display: none">0</div>
									<div id="product_services_claim_charges" style="display: none">0</div>
									<input type="text" class="form-control" id="id_product_services_cost" style="display: none" value="0">
									<input type="text" class="form-control" id="id_product_services" style="display: none" value="0">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Extra Name</label>
								<div class="col-sm-4">
									<select type="text" class="form-control" id="id_product_services_extra">
										<!-- To modify - select from db -->
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Claim</label>
								<div class="col-sm-10">
									<div class="input-group">
											<input type="number" class="form-control" id="ps_adult_claim_1" name="ps_adult_claim_1" style="display:none;" placeholder="Adult">
											<span class="input-group-addon" id="ps_adult_claim_11" style="display:none;">Adult</span>
											<input type="number" class="form-control" id="ps_teen_claim_1" style="display:none;" placeholder="Teen">
											<span class="input-group-addon" id="ps_teen_claim_11" style="display:none;">Teen</span>
											<input type="number" class="form-control" id="ps_child_claim_1" style="display:none;" placeholder="Child">
											<span class="input-group-addon" id="ps_child_claim_11" style="display:none;">Child</span>
											<input type="number" class="form-control" id="ps_infant_claim_1" style="display:none;" placeholder="Infant">
											<span class="input-group-addon" id="ps_infant_claim_11" style="display:none;">Infant</span>
									</div>
									<br>
								</div>
							</div>
							
							<div class="pager">
								<button type="button" class="btn btn-primary pull-right" id="btn-saveProductServicesExtraCost">Save</button>
							</div>
						</div>
						<!-- /.box-body -->
					</form>
			</div>
		</div>
	</div>
</div>