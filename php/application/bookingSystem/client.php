<script src="php/application/bookingSystem/js/bookingClient.js"></script>
<script src="php/application/bookingSystem/js/newClient.js"></script>
<script src="php/application/bookingSystem/js/tableBookingClient.js"></script>
<script src="php/application/bookingSystem/js/saveClient.js"></script>
<script src="php/application/bookingSystem/js/deleteClient.js"></script>
<!-- Client Tab -->
<div class="tab-pane active in fade" id="client">
<!-- left column -->	
	<div class="col-md-6">
		<!-- form start -->
		<form class="form-horizontal">
			<!-- .box-body -->
			<div class="box-body">
				<div class="form-group">
					<label class="col-sm-1 control-label">Type</label>
					<div class="col-sm-3">
						<select class="form-control bookingClient" id="client_type">
							<option value="ADULT">Adult</option>
							<option value="TEEN">Teen</option>
							<option value="CHILD">Child</option>
							<option value="INFANT">Infant</option>
						</select>
					</div>
					<label class="col-sm-3">
						<input type="checkbox" id="client_vip" class="bookingClient"> VIP
					</label>
					<div class="col-sm-2" style="display: none">
						<input type="text" class="form-control bookingClient" id="id_client" placeholder="id_client">
					</div>
					<div class="col-sm-2" style="display: none">
						<input type="text" class="form-control bookingClient" id="id_booking_client" placeholder="id_booking_client">
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-1 control-label">Client</label>
					<div class="col-sm-2">
						<select class="form-control bookingClient" id="client_title">
							<option value="MR">Mr</option>
							<option value="MRS">Mrs</option>
							<option value="MS">Ms</option>
							<option value="MISTER">Mister</option>
							<option value="MISS">Miss</option>
							<option value="SIR">Sir</option>
							<option value="LADY">Lady</option>
						</select>
					</div>
					<div class="col-sm-4">
						<input type="text" class="form-control bookingClient text-uppercase" id="client_surname" placeholder="Surname">
					</div>
					<div class="col-sm-5">
						<input type="text" class="form-control bookingClient text-uppercase" id="client_forename" placeholder="Forename">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-1 control-label">DOB</label>
					<div class="col-sm-3">
						<input type="text" class="form-control bookingClient" id="client_dob" placeholder="00/00/0000">
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control bookingClient" id="client_years" placeholder="00">
					</div>
					<div class="col-sm-2">
						<select class="form-control bookingClient" id="client_yearMonth">
							<option value="YEAR">Year</option>
							<option value="MONTH">Month</option>
						</select>
					</div>
					<label class="col-sm-2 control-label">Passport</label>
					<div class="col-sm-2">
						<input type="text" class="form-control bookingClient" id="client_passport" placeholder="0000A000">
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-1 control-label">Remarks</label>
					<div class="col-sm-11">
						<textarea class="form-control bookingClient" id="client_remarks" rows="3" style="resize: none"></textarea>
					</div>
				</div>

				<div class="pager">
						<button type="button" class="btn btn-primary" text-align="center" id="btn-saveClient">
							<span class="glyphicon glyphicon-ok"></span> Save
						</button>
						<button type="button" class="btn btn-primary" id="btn-newClient">
							<span class="glyphicon glyphicon-refresh"></span> New Client
						</button>
						<button type="button" class="btn btn-primary" id="btn-deleteClient">
							<span class="glyphicon glyphicon-remove"></span> Delete
						</button>
				</div>

			</div>
			<!-- /.box-body -->
		</form>
	</div>
<!-- /.box -->

<!-- right column -->
	<div class="col-md-6">
		<table id="tbl-bookingClient" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th class="col-sm-1">Type</th>
					<th class="col-sm-3">Name</th>
					<th class="col-sm-1">Age</th>
					<th class="col-sm-1"></th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
<!-- /.box -->
</div>	
<!-- /Client Tab -->