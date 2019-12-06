<!-- Accommodation Tab -->
<div class="tab-pane active in fade" id="transfer">
<!-- left column -->	
	<div class="col-md-6">
		<!-- form start -->
		<form class="form-horizontal">
			<!-- .box-body -->
			<div class="box-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">Paid By</label>
					<div class="col-sm-3">
						<select class="form-control" id="transfer_paidBy">
							<option value="TO">Tour Operator</option>
							<option value="Client">Client</option>
						</select>
					</div>
					<div class="col-sm-3">
						<select class="form-control" id="transfer_tourOperator">
							<option value="TO">Tour Operator</option>
							<option value="Client">Client</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Type</label>
					<div class="col-sm-3">
						<select class="form-control" id="transfer_type">
							<option value="BOTH">BOTH</option>
							<option value="ARR">ARRIVAL</option>
							<option value="DEP">DEPARTURE</option>
							<option value="INTER">INTER HOTEL</option>
						</select>
					</div>
					<div class="col-sm-2">
						<select class="form-control" id="transfer_transferPort">
							<option value="Airport">Airport</option>
							<option value="Port">Port</option>
						</select>
					</div>
					<label class="col-sm-1 control-label">Vehicle</label>
					<div class="col-sm-4">
						<select class="form-control" id="transfer_vehicle">
							<option value="BOTH">BOTH</option>
							<option value="ARR">ARRIVAL</option>
							<option value="DEP">DEPARTURE</option>
							<option value="INTER">INTER HOTEL</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label">Arrival</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" id="transfer_arrivalDate" placeholder="00/00/0000">
					</div>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="transfer_arrivalFlight" placeholder="ZZZ000">
					</div>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="transfer_arrivalTime" placeholder="00:00">
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label">Departure</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" id="transfer_departureDate" placeholder="00/00/0000">
					</div>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="transfer_departureFlight" placeholder="ZZZ000">
					</div>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="transfer_departureTime" placeholder="00:00">
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Destination</label>
					<div class="col-sm-5">
						<select class="form-control" id="transfer_destinationFrom">
							<option value="TO">Tour Operator</option>
							<option value="Client">Client</option>
						</select>
					</div>
					<div class="col-sm-5">
						<select class="form-control" id="transfer_destinationTo">
							<option value="TO">Tour Operator</option>
							<option value="Client">Client</option>
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Client</label>
					<div class="col-sm-10">
						<select class="form-control" id="transfer_hotel">
							<option value="TO">Tour Operator</option>
							<option value="Client">Client</option>
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label"></label>
					<div class="col-sm-10">
						<div class="input-group">
							<input type="number" class="form-control" id="transfer_adultAmt">
							<span class="input-group-addon">Adult</span>
							<input type="number" class="form-control" id="transfer_childAmt">
							<span class="input-group-addon">Child</span>
							<input type="number" class="form-control" id="transfer_InfantAmt">
							<span class="input-group-addon">Infant</span>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Rebate</label>
					<div class="col-sm-4">
						<select class="form-control" id="transfer_rebate">
							<option value="Percentage">Percentage</option>
							<option value="Fixed Tariff">Fixed Tariff</option>
							<option value="FOC">FOC</option>
						</select>
					</div>
					<label class="col-sm-2 control-label">Approved by</label>
					<div class="col-sm-4">
						<select class="form-control" id="transfer_approvedBy">
							<option value="None">None</option>
							<option value="Percentage">Percentage</option>
							<option value="Fixed Tariff">Fixed Tariff</option>
							<option value="FOC">FOC</option>
						</select>
					</div>
				</div>
				<div id="rebateSection">
					<div class="form-group">
						<label class="col-sm-2 control-label">Rebate</label>
						<div class="col-sm-10">
							<div class="input-group">
								<input type="number" class="form-control" id="transfer_adultRebate">
								<span class="input-group-addon">Adult</span>
								<input type="number" class="form-control" id="transfer_childRebate">
								<span class="input-group-addon">Child</span>
								<input type="number" class="form-control" id="transfer_InfantRebate">
								<span class="input-group-addon">Infant</span>
							</div>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Service Remark</label>
					<div class="col-sm-4">
						<textarea class="form-control" id="transfer_serviceRemark" rows="3" style="resize: none"></textarea>
					</div>
					<label class="col-sm-2 control-label">Internal Remark</label>
					<div class="col-sm-4">
						<textarea class="form-control" id="transfer_internalRemark" rows="3" style="resize: none"></textarea>
					</div>
				</div>

				<div class="pager">
					<button type="button" class="btn btn-primary" id="btn-saveAccom">Save</button>
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
					<th class="col-sm-1">TYPE</th>
					<th class="col-sm-2">MAX OCCUPANCY</th>
					<th class="col-sm-2">CLAIM</th>
					<th class="col-sm-2">COST</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>ADULT</td>
					<td>2</td>
					<td>459 USD</td>
					<td>354 USD</td>
				</tr>
				<tr>
					<td>CHILD</td>
					<td>2</td>
					<td>459 USD</td>
					<td>354 USD</td>
				</tr>
				<tr>
					<td>INFANT</td>
					<td>2</td>
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
					<th class="col-sm-5">UNIT CLAIM</th>
					<th class="col-sm-4">UNIT CLAIM</th>
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
					<th class="col-sm-2">Vehicle</th>
					<th class="col-sm-1">Date</th>
					<th class="col-sm-2">Destination</th>
					<th class="col-sm-1">Type</th>
					<th class="col-sm-2">Extra</th>
					<th class="col-sm-2">Claim</th>
					<th class="col-sm-2"></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>SIT IN COACH SOUTH EAST</td>
					<td>26.07.2020</td>
					<td>LUX LE MORNE</td>
					<td>ARRIVAL</td>
					<td>BOSTER SEAT</td>
					<td>3456 USD</td>
					<td>
						<div class="btn-group">
						  <i class="fa fa-fw fa-edit"></i>
						  <i class="fa fa-fw fa-trash"></i>
						</div>
					</td>
				</tr>
				<tr>
					<td>SIT IN COACH SOUTH EAST</td>
					<td>26.07.2020</td>
					<td>LUX LE MORNE</td>
					<td>ARRIVAL</td>
					<td>BOSTER SEAT</td>
					<td>3456 USD</td>
					<td>
						<div class="btn-group">
						  <i class="fa fa-fw fa-edit"></i>
						  <i class="fa fa-fw fa-trash"></i>
						</div>
					</td>
				</tr>
				<tr>
					<td>SIT IN COACH SOUTH EAST</td>
					<td>26.07.2020</td>
					<td>LUX LE MORNE</td>
					<td>ARRIVAL</td>
					<td>BOSTER SEAT</td>
					<td>3456 USD</td>
					<td>
						<div class="btn-group">
						  <i class="fa fa-fw fa-edit"></i>
						  <i class="fa fa-fw fa-trash"></i>
						</div>
					</td>
				</tr>
				<tr>
					<td>SIT IN COACH SOUTH EAST</td>
					<td>26.07.2020</td>
					<td>LUX LE MORNE</td>
					<td>ARRIVAL</td>
					<td>BOSTER SEAT</td>
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