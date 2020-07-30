<section class="content">
	<div class="row">
		<!-- left column -->
		<div class="col-md-6">	
			<div class="col-md-12">
				<div class="box box-info" style="height:750px;">
					<div class="box-header with-border">
						<h3 class="box-title">Product Services Cost</h3>
					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<form class="form-horizontal">
						<div class="box-body">
							<div class="form-group"> 
								<div class="col-sm-4">
									<div id="id_product_service_cost_1" style="display: none"></div>
									<input type="text" class="form-control" id="id_product_service" style="display: none" value="0">
									<input type="text" class="form-control" id="id_dept" style="display: none" value="0">
									<input type="text" class="form-control" id="charge" style="display: none" value="0">
								</div>
							</div>
							<div class="form-group"> 
									<label class="col-sm-2 control-label">Date</label>
									<div class="col-sm-10">
										<div class="input-group date datepicker-in">
											<input type="text" name="daterange" id="daterangeServiceFromTo" class="form-control" placeholder="dd-mm-yyyy"/>
											<div class="input-group-addon">
												<span class="glyphicon glyphicon-calendar"></span>
											</div>
										</div>
									</div>
									
							</div>
							<div class="form-group pax_breaks">
								<label class="col-sm-2 control-label">Multiple Price</label>
								<div class="col-sm-10">
									<li class="checkBoxMain" id="multiple_price_1">
										<label class='with-square-checkbox'>
											<input type='checkbox' id="multiple_price_cost" onclick="multiplePriceCost()">
											<span></span>
										</label>
									</li>
								</div>	
								<label class="col-sm-2 control-label">Cost</label>
								<div class="col-sm-10">
									<div class="input-group">
										<input type="number" class="form-control" id="ps_adult_cost">
										<span class="input-group-addon" id="ps_adult_cost_addon">Adult</span>
										<input type="number" class="form-control" id="ps_teen_cost">
										<span class="input-group-addon" id="ps_teen_cost_addon">Teen</span>
										<input type="number" class="form-control" id="ps_child_cost">
										<span class="input-group-addon" id="ps_child_cost_addon">Child</span>
										<input type="number" class="form-control" id="ps_infant_cost">
										<span class="input-group-addon" id="ps_infant_cost_addon">Infant</span>							
									</div>
									<br>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Currency</label>
								<div class="col-sm-4">
									<select type="text" class="form-control" id="id_currency">
										<!-- To modify - select from db -->
										<!-- <option value="5">MUR</option> -->
									</select>
								</div>
							</div>
							<div class="pager pull-right">
								<button type="button" class="btn btn-default" id="btn-productServices" onclick="history.go(-1);"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button>
								<button type="button" class="btn btn-default" onclick="resetFormAddServiceCost()"><i class="fa fa-refresh" aria-hidden="true"></i>&nbsp;&nbsp;Reset</button>
								<button type="button" class="btn btn-success" id="btn-saveProductServicesCost">Save</button>
							</div>
							<!-- Main content -->
							<section class="content">
								<div class="row">
									<div class="col-xs-12">
										<div class="box box-info">
											<div class="box-header">
												<h3 class="box-title">Product Service Extra</h3>
											</div>
											<!-- /.box-header -->
											<div class="box-body">
												<table id="tbl-extraServiceCost" class="table table-bordered table-hover">
													<thead>
														<tr>
															<th class="col-sm-1">Code</th>
															<th class="col-sm-2">Extra</th>
															<th class="col-sm-4">Charges</th>
															<th class="col-sm-2"></th>
														</tr>
													</thead>
													<!-- <tbody>
														<tr>
															<td>100989</td>
															<td>ACCESS FEE</td>
															<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
															<td>
																<div class="btn-group">
																	<button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit" data-toggle="modal" data-target="#modal-extraServices"></i></button>
																	<button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
																</div>
															</td>
														</tr>
													</tbody> -->
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
		</div>	

		<!-- right column -->
		<div class="col-md-6">	
			<div class="col-md-12">	
				<!-- Main content -->
				<section class="content">
					<div class="row">
						<div class="col-xs-12">
							<div class="box box-info" style="height:750px;">
								<div class="box-header">
									<h3 class="box-title">Product Service Cost</h3>
								</div>
								<!-- /.box-header -->
								<div class="box-body">
									
									<div class="form-group">
										<label class="col-sm-2 control-label">Product</label>
										<div class="col-sm-7">
											<input type="text" class="form-control" id="id_product" style="display: none">
											<input type="text" class="form-control" id="product_name" placeholder="Name of the product" readonly style="text-transform: uppercase; font-size: 18px;">
										</div>
									</div>
							
									<table id="tbl-productServicesCost" class="table table-bordered table-hover">
										<thead>
											<tr>
												<th class="col-sm-1">Code</th>
												<th class="col-sm-3">Date</th>												
												<th class="col-sm-1">Currency</th>
												<th class="col-sm-1">Charges</th>
												<th class="col-sm-2"></th>
												<th class="col-sm-1"></th>
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
<div class="modal fade" id="modal-extraServices" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Extra Services Cost</h4>
			</div>
			<div class="modal-body">
				
					<form class="form-horizontal">
						<div class="box-body">
							<div class="form-group"> 
								<div class="col-sm-4">
									<div id="id_product_service_cost_extra" style="display: none">0</div>
									<input type="text" class="form-control" id="id_product_service" style="display: none" value="0">
									<input type="text" class="form-control" id="id_service_type" style="display: none" value="0">
									<input type="text" class="form-control" id="id_product_type" style="display: none" value="0">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Extra Name</label>
								<div class="col-sm-4">
									<select type="text" class="form-control" id="id_product_service_extra_1">
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Cost Charge</label>
									<div class="col-sm-2">
										<select type="text" class="form-control" id="charge_1" disabled>
											<option value="PAX">PAX</option>
											<option value="UNIT">UNIT</option>
										</select>
									</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Cost</label>
								<div class="col-sm-10">
										<div class="input-group">
											<input type="number" class="form-control" id="ps_adult_cost_ex" min="0">
											<span class="input-group-addon blockPax">Adult</span>
											<span class="input-group-addon blockUnit" style="display: none">Unit</span>
											<input type="number" class="form-control blockPax" id="ps_teen_cost_ex" min="0">
											<span class="input-group-addon blockPax">Teen</span>
											<input type="number" class="form-control blockPax" id="ps_child_cost_ex" min="0">
											<span class="input-group-addon blockPax">Child</span>
											<input type="number" class="form-control blockPax" id="ps_infant_cost_ex" min="0">
											<span class="input-group-addon blockPax">Infant</span>
										</div>
									<br>
								</div>
							</div>
							
							<div class="pager">
								<button type="button" class="btn btn-primary pull-right" id="btn-saveProductServicesExtraCost">Save</button>
								<button type="button" class="btn btn-primary pull-right" id="btn-updateProductServicesExtraCost" style="display:none;">Update</button>

							</div>
						</div>
						<!-- /.box-body -->
					</form>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal-paxBreakServicesCost" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
										<input type="number" class="form-control" id="ps_adult_cost_modal" min="0">
										<!-- <span class="input-group-addon" id="ps_adult_cost_addon_modal">Adult</span> -->
										<input type="number" class="form-control" id="ps_teen_cost_modal" min="0">
										<!-- <span class="input-group-addon" id="ps_teen_cost_addon_modal">Teen</span> -->
										<input type="number" class="form-control" id="ps_child_cost_modal" min="0">
										<!-- <span class="input-group-addon" id="ps_child_cost_addon_modal">Child</span> -->
										<input type="number" class="form-control" id="ps_infant_cost_modal" min="0">
										<!-- <span class="input-group-addon" id="ps_infant_cost_addon_modal">Infant</span> -->
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
                                	<table id="tbl-productServicesCostPaxBreaks" class="table table-bordered table-hover">
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