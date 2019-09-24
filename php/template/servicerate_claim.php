<section class="content">
	<div class="row">
		<!-- left column -->
		<div class="col-md-6">
			<div class="box box-info" style="height: 600px">
				<div class="box-header with-border">
					<h3 class="box-title">Services Claim</h3>
				</div>
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
							<div class="col-sm-4">
								<div class="input-group">
									<input type="text" class="form-control pull-right" id="valid_from">
									<span class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</span>
								</div>
							</div>
							<label class="col-sm-2 control-label">Date To</label>
							<div class="col-sm-4">
								<div class="input-group">
									<input type="text" class="form-control pull-right" id="valid_to">
									<span class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Charge</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="charges" placeholder="UNIT/PAX">
								<!-- for display only  --->
							</div>
							<div class="col-sm-8">
								<div class="input-group">
									<input type="text" class="form-control" id="ps_adult_claim">
									<span class="input-group-addon">Adult</span>
									<input type="text" class="form-control" id="ps_teen_claim">
									<span class="input-group-addon">Teen</span>
									<input type="text" class="form-control" id="ps_child_claim">
									<span class="input-group-addon">Child</span>
									<input type="text" class="form-control" id="ps_infant_claim">
									<span class="input-group-addon">Infant</span>
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
									<option value="A">Tour Operator</option>
									<option value="B">Worldwide</option>
									<option value="C">Market</option>
								</select>
							</div>
							<div class="col-sm-7">
								<select class="form-control select2" data-live-search="true" id="id_market" multiple>
										<optgroup label="AFRICA">
											<option value="1">SOUTH AFRICA</option>
											<option value="2">TANZANIA</option>
											<option value="3">KENYA</option>
											<option value="4">ZIMBABWE</option>
											<option value="5">MALAWI</option>
										</optgroup>
										<optgroup label="EUROPE">
											<option value="6">FRANCE</option>
											<option value="7">BELGIUM</option>
											<option value="8">ITALY</option>
											<option value="9">SPAIN</option>
											<option value="1O">GERMANY</option>
										</optgroup>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Excluding</label>
							<div class="col-sm-10">
								<div class="checkbox">
									<label>
										<input type="checkbox" id="on_monday">Monday
									</label>
									<label>
										<input type="checkbox" id="on_tuesday">Tuesday
									</label>
									<label>
										<input type="checkbox" id="on_wednesday">Wednesday
									</label>
									<label>
										<input type="checkbox" id="on_thursday">Thursday
									</label>
									<label>
										<input type="checkbox" id="on_friday">Friday
									</label>
									<label>
										<input type="checkbox" id="on_saturday">Saturday
									</label>
									<label>
										<input type="checkbox" id="on_sunday">Sunday
									</label>
								</div>
							</div>
						</div>
					
						<div class="pager">
							<button type="button" class="btn btn-primary" id="btn-saveServicesClaim">Save</button>
							<button type="button" class="btn btn-primary" id="btn-deleteServicesClaim" data-dismiss="modal">Delete</button>
							<button type="button" class="btn btn-primary" id="btn-backProductServices" onclick="history.go(-1);">Back</button>
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
												<table id="tbl-productServicesExtraClaim" class="table table-bordered table-hover">
													<thead>
														<tr>
															<th class="col-sm-1">Code</th>
															<th class="col-sm-2">Extra</th>
															<th class="col-sm-5">Charges</th>
															<th class="col-sm-2"></th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td>100989</td>
															<td>ACCESS FEE</td>
															<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
															<td>
																<div class="btn-group">
																  <i class="fa fa-fw fa-edit" data-toggle="modal" data-target="#modal-extraServicesClaim"></i>
																  <i class="fa fa-fw fa-trash"></i>
																</div>
															</td>
														</tr>
														<tr>
															<td>100989</td>
															<td>ACCESS FEE</td>
															<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
															<td>
																<div class="btn-group">
																  <i class="fa fa-fw fa-edit" data-toggle="modal" data-target="#modal-extraServicesClaim"></i>
																  <i class="fa fa-fw fa-trash"></i>
																</div>
															</td>
														</tr>
														<tr>
															<td>100989</td>
															<td>ACCESS FEE</td>
															<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
															<td>
																<div class="btn-group">
																  <i class="fa fa-fw fa-edit" data-toggle="modal" data-target="#modal-extraServicesClaim"></i>
																  <i class="fa fa-fw fa-trash"></i>
																</div>
															</td>
														</tr>
														<tr>
															<td>100989</td>
															<td>ACCESS FEE</td>
															<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
															<td>
																<div class="btn-group">
																  <i class="fa fa-fw fa-edit" data-toggle="modal" data-target="#modal-extraServicesClaim"></i>
																  <i class="fa fa-fw fa-trash"></i>
																</div>
															</td>
														</tr>
													</tbody>
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
			<div class="box box-info" style="height: 600px">
				<div class="box-header with-border">
					<h3 class="box-title">Services Claim List</h3>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class="form-group">
						<label class="col-sm-2 control-label">Service</label>
						<div class="col-sm-10">
							<label class="control-label" id="product_name">product + service from product service + supplier name + Dept for + Coast</label>
							<!-- To concat product name form product + service from product service + supplier name + Dept for + Coast display none editable --->
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
						<tbody>
							<tr>
								<td>100989</td>
								<td>CATAMARAN - DEEP INTO THE BLUE - VARANDA LTD</td>
								<td>FIT</td>
								<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
								<td>USD</td>
								<td>01/10/2019 - 10/10/2020</td>
								<td>WORLDWIDE</td>
								<td>
									<div class="btn-group">
									  <i class="fa fa-fw fa-plus-circle" data-toggle="modal" data-target="#modal-extraServicesClaim"></i>
									  <i class="fa fa-fw fa-edit"></i>
									  <i class="fa fa-fw fa-trash"></i>
									</div>
								</td>
							</tr>
							<tr>
								<td>100999</td>
								<td>CATAMARAN - SOUTH - BLUE ALIZEE</td>
								<td>FIT</td>
								<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
								<td>USD</td>
								<td>01/10/2019 - 10/10/2020</td>
								<td>SPECIFIC MARKET</td>
								<td>
									<div class="btn-group">
									  <i class="fa fa-fw fa-plus-circle" data-toggle="modal" data-target="#modal-extraServicesClaim"></i>
									  <i class="fa fa-fw fa-edit"></i>
									  <i class="fa fa-fw fa-trash"></i>
									</div>
								</td>
							</tr>
							<tr>
								<td>100599</td>
								<td>QUAD - CASELA - CASELA PARK</td>
								<td>FIT</td>
								<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
								<td>USD</td>
								<td>01/10/2019 - 10/10/2020</td>
								<td>SPECIFIC TOUR OPERATOR</td>
								<td>
									<div class="btn-group">
									  <i class="fa fa-fw fa-plus-circle" data-toggle="modal" data-target="#modal-extraServicesClaim"></i>
									  <i class="fa fa-fw fa-edit"></i>
									  <i class="fa fa-fw fa-trash"></i>
									</div>
								</td>
							</tr>
							<tr>
								<td>100989</td>
								<td>SPEED BOAT - ILE AUX CERF - VARANDA LTD</td>
								<td>FIT</td>
								<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
								<td>USD</td>
								<td>01/10/2019 - 10/10/2020</td>
								<td>WORLDWIDE</td>
								<td>
									<div class="btn-group">
									  <i class="fa fa-fw fa-plus-circle" data-toggle="modal" data-target="#modal-extraServicesClaim"></i>
									  <i class="fa fa-fw fa-edit"></i>
									  <i class="fa fa-fw fa-trash"></i>
									</div>
								</td>
							</tr>
							<tr>
								<td>100999</td>
								<td>ZIPLINE - SOUTH - TELFAIR</td>
								<td>FIT</td>
								<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
								<td>USD</td>
								<td>01/10/2019 - 10/10/2020</td>
								<td>SPECIFIC MARKET</td>
								<td>
									<div class="btn-group">
									  <i class="fa fa-fw fa-plus-circle" data-toggle="modal" data-target="#modal-extraServicesClaim"></i>
									  <i class="fa fa-fw fa-edit"></i>
									  <i class="fa fa-fw fa-trash"></i>
									</div>
								</td>
							</tr>
							<tr>
								<td>100599</td>
								<td>SUBMARINE - SCOOTER - BLUE SAFARI</td>
								<td>FIT</td>
								<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
								<td>USD</td>
								<td>01/10/2019 - 10/10/2020</td>
								<td>SPECIFIC TOUR OPERATOR</td>
								<td>
									<div class="btn-group">
									  <i class="fa fa-fw fa-plus-circle" data-toggle="modal" data-target="#modal-extraServicesClaim"></i>
									  <i class="fa fa-fw fa-edit"></i>
									  <i class="fa fa-fw fa-trash"></i>
									</div>
								</td>
							</tr>
						</tbody>
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
									<input type="text" class="form-control" id="id_product_services_extra_cost" style="display: none" value="0">
									<input type="text" class="form-control" id="id_product_services_claim" style="display: none" value="0">
									<input type="text" class="form-control" id="id_product_services_cost" style="display: none" value="0">
									<input type="text" class="form-control" id="id_product_services" style="display: none" value="0">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Extra Name</label>
								<div class="col-sm-4">
									<select type="text" class="form-control" id="id_product_services_extra">
										<!-- To modify - select from db -->
										<option value="0">SELECT</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Claim</label>
								<div class="col-sm-10">
									<div class="input-group">
										<input type="text" class="form-control" id="ps_adult_cost">
										<span class="input-group-addon">Adult</span>
										<input type="text" class="form-control" id="ps_teen_cost">
										<span class="input-group-addon">Teen</span>
										<input type="text" class="form-control" id="ps_child_cost">
										<span class="input-group-addon">Child</span>
										<input type="text" class="form-control" id="ps_infant_cost">
										<span class="input-group-addon">Infant</span>
									</div>
									<br>
								</div>
							</div>
							
							<div class="pager">
								<button type="button" class="btn btn-primary" id="btn-saveProductServicesExtraCost">Save</button>
								<button type="button" class="btn btn-primary" id="btn-deleteProductServicesExtraCost" data-dismiss="modal">Delete</button>
							</div>
						</div>
						<!-- /.box-body -->
					</form>
			</div>
		</div>
	</div>
</div>
<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<script>

$(function () {
	//table
    $('#tbl-productServicesClaim').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false,
      'pageLength'  : 4
		
    })
    $('#tbl-productServicesExtraClaim').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false,
      'pageLength'  : 2
		
    })
	//Date picker
	$('#valid_from,#valid_to').datepicker({
		autoclose: true
	});  
	//Initialize Select2 Elements
	$('.select2').select2();
});
</script>