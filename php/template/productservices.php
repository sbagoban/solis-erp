<section class="content">
	<div class="row">
		<!-- left column -->
		<div class="col-md-6">	
			<div class="col-md-12">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Services</h3>
					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<form class="form-horizontal">
						<div class="box-body">
							<div class="form-group"> 
								<div class="col-sm-4">
									<input type="text" class="form-control" id="id_product_services" style="display: none" value="0">
									<input type="text" class="form-control" id="id_service_type" style="display: none" value="0">
									<input type="text" class="form-control" id="id_product_type" style="display: none" value="0">
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
								<label class="col-sm-2 control-label">Product</label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="id_product" style="display: none">
									<input type="text" class="form-control" id="product_name" placeholder="Name of the product">
								</div>
								<label class="col-sm-2 control-label">Department</label>
								<div class="col-sm-4">
									<select type="text" class="form-control" id="id_dept">
										<!-- To modify - select from db -->
										<option value="19">FIT</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Location</label>
								<div class="col-sm-4">
									<select type="text" class="form-control" id="id_countries">
										<!-- To modify - select from db -->
										<option value="913">MAURITIUS</option>
									</select>
								</div>
								<label class="col-sm-2 control-label">Coast</label>
								<div class="col-sm-4">
									<select type="text" class="form-control" id="id_coasts">
										<!-- To modify - select from db -->
										<option value="2">EAST</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Service</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" id="service_name" placeholder="Name of the product">
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="special_name" placeholder="Special Name">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Supplier</label>
								<div class="col-sm-6">
									<select type="text" class="form-control" id="id_creditor">
										<!-- To modify - select from db -->
										<option value="2">TEST</option>
									</select>
								</div>
								<label class="col-sm-1 control-label">Taxable</label>
								<div class="col-sm-3">
									<select type="text" class="form-control" id="id_tax">
										<!-- To modify - select from db -->
										<option value="1">EXEMPT</option>
										<option value="2">OUSIDE SCOPE</option>
										<option value="3">VALUE ADDED TAX</option>
										<option value="4" selected="selected">ZERO RATED</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Cost Charge</label>
								<div class="col-sm-2">
									<select type="text" class="form-control" id="charges">
										<option value="PAX">PAX</option>
										<option value="UNIT">UNIT</option>
									</select>
								</div>
								<label class="col-sm-2 control-label">Duration</label>
								<div class="col-sm-2">
									<input type="text" class="form-control" id="duration" placeholder="0.00">
								</div>
								<label class="col-sm-2 control-label">Transfer</label>
								<div class="col-sm-2">
									<select type="text" class="form-control" id="transfer_included">
										<option value="0" selected="selectd">NO</option>
										<option value="1">YES</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Description</label>
								<div class="col-sm-4">
									<textarea class="form-control" id="description" rows="3" style="resize: none"></textarea>
								</div>
								<label class="col-sm-2 control-label">Comment</label>
								<div class="col-sm-4">
									<textarea class="form-control" id="comments" rows="3" style="resize: none"></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Package</label>
								<div class="col-sm-2">
									<select type="text" class="form-control" id="is_pakage">
										<option value="Y">YES</option>
										<option value="N" selected="selectd">NO</option>
									</select>
								</div>
								<label class="col-sm-2 control-label">Services</label>
								<div class="col-sm-6">
									<select class="form-control select2" multiple="multiple">
										<option value="1">CHAMAREL LUNCH</option>
										<option value="2">ACCESS CHAMAREL</option>>
										<option value="3">ACCESS CHAMAREL</option>>
										<option value="4">ACCESS CHAMAREL</option>>
										<option value="5">ACCESS CHAMAREL</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Operation</label>
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
							<div class="form-group">
								<label class="col-sm-2 control-label">Cancellation</label>
								<div class="col-sm-10">
									<textarea class="form-control" id="cancellation" rows="3" style="resize: none"></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Age Policy</label>
								<div class="col-sm-10">
									<div class="input-group">
										<input type="text" class="form-control" id="age_inf_to">
										<span class="input-group-addon">Infant</span>
										<input type="text" class="form-control" id="age_child_to">
										<span class="input-group-addon">Child</span>
										<input type="text" class="form-control" id="age_teen_to">
										<span class="input-group-addon">Teen</span>
									</div>
									<br>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Pax Policy</label>
								<div class="col-sm-5">
									<div class="input-group">
										<input type="text" class="form-control" id="min_pax">
										<span class="input-group-addon">Min</span>
										<input type="text" class="form-control">
										<span class="input-group-addon" id="max_pax">Max</span>
									</div>
									<br>
								</div>
							</div>
							<div class="pager">
								<button type="button" class="btn btn-primary" id="btn-saveProductServices">Save</button>
								<button type="button" class="btn btn-primary" id="btn-deleteProductServices" data-dismiss="modal">Delete</button>
								<button type="button" class="btn btn-primary" id="btn-productServices" onclick="history.go(-1);">Back</button>
								<button type="button" class="btn btn-primary" id="btn-addProductServicesCost">Add Cost</button>
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
								<!-- /.box-header -->
								<div class="box-body" style="height:800px;">
									<table id="tbl-productServices" class="table table-bordered table-hover">
										<thead>
											<tr>
												<th class="col-sm-1">Code</th>
												<th class="col-sm-2">Product</th>
												<th class="col-sm-1">Supplier</th>
												<th class="col-sm-1">Dept</th>
												<th class="col-sm-1">Charges</th>
												<th class="col-sm-2">Date</th>
												<th class="col-sm-4"></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>100989</td>
												<td>DEEP INTO THE BLUE - INDIA</td>
												<td>VERANDA LTD</td>
												<td>FIT</td>
												<td>PAX</td>
												<td>01/10/2019 - 10/10/2020</td>
												<td>
													<div class="btn-group">
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-files-o"></i>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
													</div>
												</td>
											</tr>
											<tr>
												<td>100990</td>
												<td>DEEP INTO THE BLUE - INDIA</td>
												<td>VERANDA LTD</td>
												<td>FIT</td>
												<td>PAX</td>
												<td>01/10/2019 - 10/10/2020</td>
												<td>
													<div class="btn-group">
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-files-o"></i>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
													</div>
												</td>
											</tr>
											<tr>
												<td>23467</td>
												<td>DEEP INTO THE BLUE - INDIA</td>
												<td>VERANDA LTD</td>
												<td>FIT</td>
												<td>PAX</td>
												<td>01/10/2019 - 10/10/2020</td>
												<td>
													<div class="btn-group">
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-files-o"></i>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
													</div>
												</td>
											</tr>
										</tbody>
										<tfoot>
											<tr>
												<th>Code</th>
												<th>Product</th>
												<th>Supplier</th>
												<th>Dept</th>
												<th>Charges</th>
												<th>Date</th>
												<th></th>
											</tr>
										</tfoot>
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

<section class="content">
	<div class="row">
		<!-- left column -->
		<div class="col-md-6">	
			<div class="col-md-12">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Product Services Cost</h3>
					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<form class="form-horizontal">
						<div class="box-body">
							<div class="form-group"> 
								<div class="col-sm-4">
									<input type="text" class="form-control" id="id_product_services" style="display: none" value="0">
									<input type="text" class="form-control" id="id_service_type" style="display: none" value="0">
									<input type="text" class="form-control" id="id_product_type" style="display: none" value="0">
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
								<label class="col-sm-2 control-label">Product</label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="id_product" style="display: none">
									<input type="text" class="form-control" id="product_name" placeholder="Name of the product">
								</div>
								<label class="col-sm-2 control-label">Department</label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="id_dept" placeholder="Name of the product">
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-2 control-label">Cost</label>
								<div class="col-sm-10">
									<div class="input-group">
										<input type="text" class="form-control" id="age_teen_to">
										<span class="input-group-addon">Adult</span>
										<input type="text" class="form-control" id="age_teen_to">
										<span class="input-group-addon">Teen</span>
										<input type="text" class="form-control" id="age_child_to">
										<span class="input-group-addon">Child</span>
										<input type="text" class="form-control" id="age_inf_to">
										<span class="input-group-addon">Infant</span>
									</div>
									<br>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Currency</label>
								<div class="col-sm-4">
									<select type="text" class="form-control" id="id_dept">
										<!-- To modify - select from db -->
										<option value="5">MUR</option>
									</select>
								</div>
							</div>
							<div class="pager">
								<button type="button" class="btn btn-primary" id="btn-saveProductServicesCost">Save</button>
								<button type="button" class="btn btn-primary" id="btn-deleteProductServicesCost" data-dismiss="modal">Delete</button>
								<button type="button" class="btn btn-primary" id="btn-clearProductServicesCost">Clear</button>
							</div>
							
							<div class="col-md-12">	
								<div class="col-md-12">
									<div class="box box-info">
										<div class="box-header with-border">
											<h3 class="box-title">Extra Cost</h3>
										</div>
										<!-- /.box-header -->
										<!-- form start -->
										<form class="form-horizontal">
											<div class="box-body">
												<div class="form-group"> 
														<label class="col-sm-2 control-label">Extra</label>
														<div class="col-sm-4">
															<select type="text" class="form-control" id="id_service_type">
																<!-- To modify - select from db -->
																<option value="2">Activities</option>
																<option value="4">Others</option>
															</select>
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
													<label class="col-sm-2 control-label">Product</label>
													<div class="col-sm-4">
														<input type="text" class="form-control" id="id_product" style="display: none">
														<input type="text" class="form-control" id="product_name" placeholder="Name of the product">
													</div>
													<label class="col-sm-2 control-label">Department</label>
													<div class="col-sm-4">
														<input type="text" class="form-control" id="id_dept" placeholder="Name of the product">
													</div>
												</div>

												<div class="form-group">
													<label class="col-sm-2 control-label">Cost</label>
													<div class="col-sm-10">
														<div class="input-group">
															<input type="text" class="form-control" id="age_teen_to">
															<span class="input-group-addon">Adult</span>
															<input type="text" class="form-control" id="age_teen_to">
															<span class="input-group-addon">Teen</span>
															<input type="text" class="form-control" id="age_child_to">
															<span class="input-group-addon">Child</span>
															<input type="text" class="form-control" id="age_inf_to">
															<span class="input-group-addon">Infant</span>
														</div>
														<br>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-2 control-label">Currency</label>
													<div class="col-sm-4">
														<select type="text" class="form-control" id="id_dept">
															<!-- To modify - select from db -->
															<option value="5">MUR</option>
														</select>
													</div>
												</div>
												<div class="pager">
													<button type="button" class="btn btn-primary" id="btn-saveProductServicesCost">Save</button>
													<button type="button" class="btn btn-primary" id="btn-deleteProductServicesCost" data-dismiss="modal">Delete</button>
													<button type="button" class="btn btn-primary" id="btn-clearProductServicesCost">Clear</button>
												</div>
											</div>
											<!-- /.box-body -->
										</form>
									</div>
								</div>
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
								<!-- /.box-header -->
								<div class="box-body" style="height:800px;">
									<table id="tbl-productServices" class="table table-bordered table-hover">
										<thead>
											<tr>
												<th class="col-sm-1">Code</th>
												<th class="col-sm-2">Product</th>
												<th class="col-sm-1">Supplier</th>
												<th class="col-sm-1">Dept</th>
												<th class="col-sm-1">Charges</th>
												<th class="col-sm-2">Date</th>
												<th class="col-sm-4"></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>100989</td>
												<td>DEEP INTO THE BLUE - INDIA</td>
												<td>VERANDA LTD</td>
												<td>FIT</td>
												<td>PAX</td>
												<td>01/10/2019 - 10/10/2020</td>
												<td>
													<div class="btn-group">
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-files-o"></i>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
													</div>
												</td>
											</tr>
											<tr>
												<td>100990</td>
												<td>DEEP INTO THE BLUE - INDIA</td>
												<td>VERANDA LTD</td>
												<td>FIT</td>
												<td>PAX</td>
												<td>01/10/2019 - 10/10/2020</td>
												<td>
													<div class="btn-group">
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-files-o"></i>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
													</div>
												</td>
											</tr>
											<tr>
												<td>23467</td>
												<td>DEEP INTO THE BLUE - INDIA</td>
												<td>VERANDA LTD</td>
												<td>FIT</td>
												<td>PAX</td>
												<td>01/10/2019 - 10/10/2020</td>
												<td>
													<div class="btn-group">
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-files-o"></i>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
													</div>
												</td>
											</tr>
										</tbody>
										<tfoot>
											<tr>
												<th>Code</th>
												<th>Product</th>
												<th>Supplier</th>
												<th>Dept</th>
												<th>Charges</th>
												<th>Date</th>
												<th></th>
											</tr>
										</tfoot>
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
<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>

<script>
  $(function () {
    $('#tbl-product, #tbl-productServices').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    })
	
    //Date picker
    $('#valid_from,#valid_to').datepicker({
      autoclose: true
    })  
    $('.select2').select2()
  })	
	
</script>