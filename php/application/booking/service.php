<!-- Accommodation Tab -->
<div class="tab-pane active in fade" id="otherService">
<!-- left column -->	
	<div class="col-md-6">
		<!-- form start -->
		<form class="form-horizontal">
			<!-- .box-body -->
			<div class="box-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">Paid By</label>
					<div class="col-sm-3">
						<select class="form-control" id="otherService_paidBy">
							<option value="TO">Tour Operator</option>
							<option value="Client">Client</option>
						</select>
					</div>
					<div class="col-sm-3">
						<select class="form-control" id="otherService_tourOperator">
							<option value="TO">Tour Operator</option>
							<option value="Client">Client</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label">Date</label>
					<div class="col-sm-5">
						<input type="text" class="form-control" id="otherService_date" placeholder="00/00/0000">
					</div>
					<div class="col-sm-5">
						<input type="text" class="form-control" id="otherService_time" placeholder="00:00">
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Service</label>
					<div class="col-sm-3">
						<select class="form-control" id="otherService_category">
						</select>
					</div>
					<div class="col-sm-7">
						<select class="form-control" id="otherService_type">
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Client</label>
					<div class="col-sm-10">
						<select class="form-control" id="otherService_hotel">
							<option value="TO">Tour Operator</option>
							<option value="Client">Client</option>
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label"></label>
					<div class="col-sm-10">
						<div class="input-group">
							<input type="number" class="form-control" id="otherService_adultAmt">
							<span class="input-group-addon">Adult</span>
							<input type="number" class="form-control" id="otherService_teenAmt">
							<span class="input-group-addon">Teen</span>
							<input type="number" class="form-control" id="otherService_childAmt">
							<span class="input-group-addon">Child</span>
							<input type="number" class="form-control" id="otherService_InfantAmt">
							<span class="input-group-addon">Infant</span>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Rebate</label>
					<div class="col-sm-4">
						<select class="form-control" id="otherService_rebate">
							<option value="None">None</option>
							<option value="Percentage">Percentage</option>
							<option value="Fixed Tariff">Fixed Tariff</option>
							<option value="FOC">FOC</option>
						</select>
					</div>
					<label class="col-sm-2 control-label">Approved by</label>
					<div class="col-sm-4">
						<select class="form-control" id="otherService_approvedBy">
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
								<input type="number" class="form-control" id="otherService_adultRebate">
								<span class="input-group-addon">Adult</span>
								<input type="number" class="form-control" id="otherService_teenRebate">
								<span class="input-group-addon">Teen</span>
								<input type="number" class="form-control" id="otherService_childRebate">
								<span class="input-group-addon">Child</span>
								<input type="number" class="form-control" id="otherService_InfantRebate">
								<span class="input-group-addon">Infant</span>
							</div>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Service Remark</label>
					<div class="col-sm-4">
						<textarea class="form-control" id="otherService_serviceRemark" rows="3" style="resize: none"></textarea>
					</div>
					<label class="col-sm-2 control-label">Internal Remark</label>
					<div class="col-sm-4">
						<textarea class="form-control" id="otherService_internalRemark" rows="3" style="resize: none"></textarea>
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
					<th>TYPE</th>
					<th>MIN PAX</th>
					<th>MAX PAX</th>
					<th>CLAIM</th>
					<th>COST</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>ADULT</td>
					<td>2</td>
					<td>2</td>
					<td>459 USD</td>
					<td>354 USD</td>
				</tr>
				<tr>
					<td>TEEN</td>
					<td>2</td>
					<td>2</td>
					<td>459 USD</td>
					<td>354 USD</td>
				</tr>
				<tr>
					<td>CHILD</td>
					<td>2</td>
					<td>2</td>
					<td>459 USD</td>
					<td>354 USD</td>
				</tr>
				<tr>
					<td>INFANT</td>
					<td>2</td>
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
					<th class="col-sm-6">Activity</th>
					<th class="col-sm-2">Date</th>
					<th class="col-sm-2">Claim</th>
					<th class="col-sm-2"></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>TOUR - KOULEUR CHAMAREL</td>
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
					<td>TOUR - KOULEUR CHAMAREL</td>
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
					<td>TOUR - KOULEUR CHAMAREL</td>
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
					<td>TOUR - KOULEUR CHAMAREL</td>
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
					<td>TOUR - KOULEUR CHAMAREL</td>
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