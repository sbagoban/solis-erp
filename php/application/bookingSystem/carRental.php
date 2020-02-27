<!-- Accommodation Tab -->
<div class="tab-pane active in fade" id="carRental">
<!-- left column -->	
	<div class="col-md-6">
		<!-- form start -->
		<form class="form-horizontal">
			<!-- .box-body -->
			<div class="box-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">Paid By</label>
					<div class="col-sm-3">
						<select class="form-control" id="carRental_paidBy">
							<option value="TO">Tour Operator</option>
							<option value="Client">Client</option>
						</select>
					</div>
					<div class="col-sm-3">
						<select class="form-control" id="carRental_tourOperator">
							<option value="TO">Tour Operator</option>
							<option value="Client">Client</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label">Pickup</label>
					<div class="col-sm-3">
						<select class="form-control" id="carRental_deliveryPlace">
						</select>
					</div>
					<div class="col-sm-5">
						<input type="text" class="form-control" id="carRental_deliveryDate" placeholder="00/00/0000">
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="carRental_deliveryTime" placeholder="00:00">
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Drop off</label>
					<div class="col-sm-3">
						<select class="form-control" id="carRental_collectionPlace">
						</select>
					</div>
					<div class="col-sm-5">
						<input type="text" class="form-control" id="carRental_collectionDate" placeholder="00/00/0000">
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="carRental_collectionTime" placeholder="00:00">
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Vehicle</label>
					<div class="col-sm-3">
						<select class="form-control" id="carRental_category">
						</select>
					</div>
					<div class="col-sm-7">
						<select class="form-control" id="carRental_type">
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Rebate Type</label>
					<div class="col-sm-2">
						<select class="form-control" id="carRental_rebate">
							<option value="None">None</option>
							<option value="Percentage">Percentage</option>
							<option value="Fixed Tariff">Fixed Tariff</option>
							<option value="FOC">FOC</option>
						</select>
					</div>
					<label class="col-sm-2 control-label">Approved by</label>
					<div class="col-sm-2">
						<select class="form-control" id="carRental_approvedBy">
							<option value="None">None</option>
							<option value="Percentage">Percentage</option>
							<option value="Fixed Tariff">Fixed Tariff</option>
							<option value="FOC">FOC</option>
						</select>
					</div>
					<label class="col-sm-2 control-label">Rabate</label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="carRental_rebateTariff" placeholder="00:00">
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Driver</label>
					<div class="col-sm-5">
						<select class="form-control" id="carRental_driver">
							<option value="TO">Tour Operator</option>
							<option value="Client">Client</option>
						</select>
					</div>
					<label class="col-sm-3 control-label">Additional Driver</label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="carRental_additionalDriver" placeholder="00:00">
					</div>
				</div>
				
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Service Remark</label>
					<div class="col-sm-4">
						<textarea class="form-control" id="carRental_serviceRemark" rows="3" style="resize: none"></textarea>
					</div>
					<label class="col-sm-2 control-label">Internal Remark</label>
					<div class="col-sm-4">
						<textarea class="form-control" id="carRental_internalRemark" rows="3" style="resize: none"></textarea>
					</div>
				</div>

				<div class="pager">
					<button type="button" class="btn btn-primary" id="btn-saveCarRental">Save</button>
					<button type="button" class="btn btn-primary" id="btn-deleteAccom">Delete</button>
				</div>

			</div>
			<!-- /.box-body -->
		</form>
	</div>
<!-- /.box -->

<!-- right column -->
	<div class="col-md-6">
		<table id="tbl-bookingAccom" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>RATES</th>
					<th>2-3 DAYS</th>
					<th>4-7 DAYS</th>
					<th>8+ DAYS</th>
					<th>14+ DAYS</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>TO</td>
					<td>459 USD</td>
					<td>459 USD</td>
					<td>459 USD</td>
					<td>354 USD</td>
				</tr>
				<tr>
					<td>TA</td>
					<td>459 USD</td>
					<td>459 USD</td>
					<td>459 USD</td>
					<td>354 USD</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-md-6">
		<table id="tbl-bookingAccom" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th class="col-sm-3">EXTRA</th>
					<th class="col-sm-5">CLAIM</th>
					<th class="col-sm-4">COST</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>BABY SEAT</td>
					<td>40</td>
					<td>40</td>
				</tr>
				<tr>
					<td>CHILD SEAT</td>
					<td>40</td>
					<td>40</td>
				</tr>
				<tr>
					<td>BOSTER SEAT</td>
					<td>40</td>
					<td>40</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-md-6">
		<table id="tbl-bookingAccom" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th class="col-sm-6">Vehicle</th>
					<th class="col-sm-2">Date</th>
					<th class="col-sm-2">Claim</th>
					<th class="col-sm-2"></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>6 SEAT - HYUNDAI</td>
					<td>26.07.2020</td>
					<td>3456 USD</td>
					<td>
						<div class="btn-group">
						  <i class="fa fa-fw fa-edit"></i>
						  <i class="fa fa-fw fa-trash"></i>
						</div>
					</td>
				</tr>
				<tr>
					<td>6 SEAT - HYUNDAI</td>
					<td>26.07.2020</td>
					<td>3456 USD</td>
					<td>
						<div class="btn-group">
						  <i class="fa fa-fw fa-edit"></i>
						  <i class="fa fa-fw fa-trash"></i>
						</div>
					</td>
				</tr>
				<tr>
					<td>6 SEAT - HYUNDAI</td>
					<td>26.07.2020</td>
					<td>3456 USD</td>
					<td>
						<div class="btn-group">
						  <i class="fa fa-fw fa-edit"></i>
						  <i class="fa fa-fw fa-trash"></i>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
<!-- /.box -->
</div>	
<!-- /Accomodation Tab -->
<!--<script src="bower_components/jquery/dist/jquery.min.js"></script>
<script>

$(function () {
	
	
    $('#accom_stay').daterangepicker();
});-->