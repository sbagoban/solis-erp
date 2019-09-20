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
									<input type="text" class="form-control" id="id_product_services_cost" style="display: none" value="0">
									<input type="text" class="form-control" id="id_product_services" style="display: none" value="0">
									<input type="text" class="form-control" id="id_dept" style="display: none" value="0">
									<input type="text" class="form-control" id="charges" style="display: none" value="0">
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
								<label class="col-sm-2 control-label">Cost</label>
								<div class="col-sm-10">
									<div class="input-group">
										<input type="number" class="form-control" id="ps_adult_cost">
										<span class="input-group-addon">Adult</span>
										<input type="number" class="form-control" id="ps_teen_cost">
										<span class="input-group-addon">Teen</span>
										<input type="number" class="form-control" id="ps_child_cost">
										<span class="input-group-addon">Child</span>
										<input type="number" class="form-control" id="ps_infant_cost">
										<span class="input-group-addon">Infant</span>
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
							
							<div class="pager">
								<button type="button" class="btn btn-primary" id="btn-saveProductServicesCost">Save</button>
								<button type="button" class="btn btn-primary" id="btn-addProductServicesExtra" data-toggle="modal" data-target="#modal-extraServices">Add Extra</button>
								<button type="button" class="btn btn-primary" id="btn-productServices" onclick="history.go(-1);">Back</button>
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
												<table id="tbl-productServices" class="table table-bordered table-hover">
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
																  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit" data-toggle="modal" data-target="#modal-extraServices"></i></button>
																  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
																</div>
															</td>
														</tr>
														<tr>
															<td>100989</td>
															<td>ACCESS FEE</td>
															<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
															<td>
																<div class="btn-group">
																  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
																  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
																</div>
															</td>
														</tr>
														<tr>
															<td>100989</td>
															<td>ACCESS FEE</td>
															<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
															<td>
																<div class="btn-group">
																  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
																  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
																</div>
															</td>
														</tr>
														<tr>
															<td>100989</td>
															<td>ACCESS FEE</td>
															<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
															<td>
																<div class="btn-group">
																  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
																  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
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
										<div class="col-sm-6">
											<input type="text" class="form-control" id="id_product" style="display: none">
											<input type="text" class="form-control" id="product_name" placeholder="Name of the product">
										</div>
									</div>
							
									<table id="tbl-productServices" class="table table-bordered table-hover">
										<thead>
											<tr>
												<th class="col-sm-1">Code</th>
												<th class="col-sm-4">Date</th>
												<th class="col-sm-5">Charges</th>
												<th class="col-sm-2"></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>100989</td>
												<td>01/10/2019 - 10/10/2020</td>
												<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
												<td>
													<div class="btn-group">
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
													</div>
												</td>
											</tr>
											<tr>
												<td>100989</td>
												<td>01/10/2019 - 10/10/2020</td>
												<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
												<td>
													<div class="btn-group">
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
													</div>
												</td>
											</tr>
											<tr>
												<td>100989</td>
												<td>01/10/2019 - 10/10/2020</td>
												<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
												<td>
													<div class="btn-group">
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
													</div>
												</td>
											</tr>
											<tr>
												<td>100989</td>
												<td>01/10/2019 - 10/10/2020</td>
												<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
												<td>
													<div class="btn-group">
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
													</div>
												</td>
											</tr>
											<tr>
												<td>100989</td>
												<td>01/10/2019 - 10/10/2020</td>
												<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
												<td>
													<div class="btn-group">
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
													</div>
												</td>
											</tr>
											<tr>
												<td>100989</td>
												<td>01/10/2019 - 10/10/2020</td>
												<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
												<td>
													<div class="btn-group">
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
													</div>
												</td>
											</tr>
											<tr>
												<td>100989</td>
												<td>01/10/2019 - 10/10/2020</td>
												<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
												<td>
													<div class="btn-group">
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
													</div>
												</td>
											</tr>
											<tr>
												<td>100989</td>
												<td>01/10/2019 - 10/10/2020</td>
												<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
												<td>
													<div class="btn-group">
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
													</div>
												</td>
											</tr>
											<tr>
												<td>100989</td>
												<td>01/10/2019 - 10/10/2020</td>
												<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
												<td>
													<div class="btn-group">
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
													</div>
												</td>
											</tr>
											<tr>
												<td>100989</td>
												<td>01/10/2019 - 10/10/2020</td>
												<td>PAX - 45AD | 35TN | 25CH | 0INF</td>
												<td>
													<div class="btn-group">
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i></button>
													  <button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button>
													</div>
												</td>
											</tr>
										</tbody>
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
									<input type="text" class="form-control" id="id_product_services_cost" style="display: none" value="0">
									<input type="text" class="form-control" id="id_product_services" style="display: none" value="0">
									<input type="text" class="form-control" id="id_service_type" style="display: none" value="0">
									<input type="text" class="form-control" id="id_product_type" style="display: none" value="0">
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
								<label class="col-sm-2 control-label">Charges</label>
								<div class="col-sm-4">
									<select type="text" class="form-control" id="id_dept">
										<option value="UNIT" selected="selected">UNIT</option>
										<option value="PAX" selected="selected">PAX</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Cost</label>
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
								<button type="button" class="btn btn-primary" id="btn-clearProductServicesExtraCost" data-toggle="modal" data-target="#modal-extraServices">Add Extra</button>
							</div>
						</div>
						<!-- /.box-body -->
					</form>
			</div>
		</div>
	</div>
</div>
