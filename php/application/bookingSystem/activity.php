<script src="php/application/bookingSystem/js/bookingActivity.js"></script>
<script src="php/application/bookingSystem/js/newActivity.js"></script>
<script src="php/application/bookingSystem/js/tableBookingActivity.js"></script>
<script src="php/application/bookingSystem/js/saveActivity.js"></script>
<script src="php/application/bookingSystem/js/tableActivityExtra.js"></script>
<script src="php/application/bookingSystem/js/deleteActivity.js"></script>
<!-- Activity Tab -->
<div class="tab-pane active in fade" id="activity">
<!-- left column -->	
	<div class="col-md-6">
		<!-- form start -->
		<form class="form-horizontal">
			<!-- .box-body -->
			<div class="box-body">
				<div class="form-group" style="display: none">
					<label class="col-sm-2 control-label">ID BOOKING ACTIVITY</label>
					<div class="col-sm-2">
						<input type="text" class="form-control bookingActivity" id="id_booking_activity_claim" placeholder="000" readonly>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label">Booking Date</label>
					<div class="col-sm-3">
						<input type="text" class="form-control bookingActivity" id="activity_bookingDate" placeholder="00/00/0000">
					</div>
					<label class="col-sm-1 control-label">Status</label>
					<div class="col-sm-6">
						<select class="form-control bookingActivity select2" id="activity_status">
							<option value="QUOTE" selected>QUOTE</option>
							<option value="CONFIRM">CONFIRM</option>
							<option value="CANCEL">CANCEL</option>
							<option value="CANCEL WITH FEE">CANCEL WITH FEE</option>
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Paid By</label>
					<div class="col-sm-3">
						<select class="form-control bookingActivity" id="activity_paidBy">
							<option value="TO">Tour Operator</option>
							<option value="Client">Client</option>
						</select>
					</div>
					<div class="col-sm-7">
						<select class="form-control bookingActivity" id="activity_payer">
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label">Date</label>
					<div class="col-sm-5">
						<input type="text" class="form-control bookingActivity" id="activity_date" placeholder="00/00/0000">
					</div>
					<label class="col-sm-1 control-label">Time</label>
					<div class="col-sm-4">
						<input type="text" class="form-control bookingActivity timepicker" id="activity_time" placeholder="00:00">
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Activity</label>
					<div class="col-sm-3">
						<select class="form-control bookingActivity select2" id="activity_type">
						</select>
					</div>
					<div class="col-sm-7">
						<select class="form-control bookingActivity select2" id="activity_service">
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Client</label>
					<div class="col-sm-10">
						<select class="form-control bookingActivity selectpicker" multiple data-live-search="true" data-actions-box="true" id="activity_client" multiple="multiple">
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label"></label>
					<div class="col-sm-10">
						<div class="input-group">
							<input readonly class="form-control bookingActivity" id="activity_adultAmt">
							<span class="input-group-addon">Adult</span>
							<input readonly class="form-control bookingActivity" id="activity_teenAmt">
							<span class="input-group-addon">Teen</span>
							<input readonly class="form-control bookingActivity" id="activity_childAmt">
							<span class="input-group-addon">Child</span>
							<input readonly class="form-control bookingActivity" id="activity_infantAmt">
							<span class="input-group-addon">Infant</span>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Rebate</label>
					<div class="col-sm-4">
						<select class="form-control bookingActivity select2" id="activity_rebate">
							<option value="None">None</option>
							<option value="Percentage">Percentage</option>
							<option value="Fixed Tariff">Fixed Tariff</option>
							<option value="FOC">FOC</option>
						</select>
					</div>
					<label class="col-sm-2 control-label">Approved by</label>
					<div class="col-sm-4">
						<select class="form-control bookingActivity select2" id="activity_approvedBy">
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
						<div class="col-sm-4">
							<input type="text" class="form-control bookingActivity" id="activity_percentageRebate" placeholder="00000" style="display: none">
						</div>
						<div class="col-sm-10" id="rebate_fix">
							<div class="input-group">
								<input type="number" class="form-control bookingActivity" id="activity_adultRebate">
								<span class="input-group-addon rebateAdult">Adult</span>
								<input type="number" class="form-control bookingActivity" id="activity_teenRebate">
								<span class="input-group-addon rebateTeen">Teen</span>
								<input type="number" class="form-control bookingActivity" id="activity_childRebate">
								<span class="input-group-addon rebateChild">Child</span>
								<input type="number" class="form-control bookingActivity" id="activity_InfantRebate">
								<span class="input-group-addon rebateInfant">Infant</span>
							</div>
						</div>
					</div>
				</div>
				<!----
				<div class="form-group">
					<label class="col-sm-2 control-label">Representative</label>
					<div class="col-sm-4">
						<select class="form-control bookingActivity select2" id="activity_representative">
							<option value="Airport">Airport</option>
							<option value="Port">Port</option>
						</select>
					</div>
					<label class="col-sm-1 control-label">Voucher</label>
					<div class="col-sm-2">
						<input type="text" class="form-control bookingActivity" id="activity_voucherNo" placeholder="00000">
					</div>
					<label class="col-sm-1 control-label">Language</label>
					<div class="col-sm-2">
						<select class="form-control bookingActivity select2" id="activity_language">
							<option value="Airport">Airport</option>
							<option value="Port">Port</option>
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Pickup</label>
					<div class="col-sm-4">
						<select class="form-control bookingActivity select2" id="activity_pickupHotel">
							<option value="Airport">Airport</option>
							<option value="Port">Port</option>
						</select>
					</div>
					<label class="col-sm-1 control-label">Time</label>
					<div class="col-sm-2">
						<input type="text" class="form-control bookingActivity timepicker" id="activity_pickupTime" placeholder="00:00">
					</div>
					<label class="col-sm-1 control-label">Room</label>
					<div class="col-sm-2">
						<input type="text" class="form-control bookingActivity" id="activity_pickupRoomNo" placeholder="000">
					</div>
				</div>
				---->
				<div class="form-group">
					<label class="col-sm-2 control-label">Service Remark</label>
					<div class="col-sm-4">
						<textarea class="form-control bookingActivity" id="activity_serviceRemark" rows="3" style="resize: none"></textarea>
					</div>
					<label class="col-sm-2 control-label">Internal Remark</label>
					<div class="col-sm-4">
						<textarea class="form-control bookingActivity" id="activity_internalRemark" rows="3" style="resize: none"></textarea>
					</div>
				</div>

				<div class="pager">
						<button type="button" class="btn btn-primary" text-align="center" id="btn-saveActivity">
							<span class="glyphicon glyphicon-ok"></span> Save
						</button>
						<button type="button" class="btn btn-primary" id="btn-newActivity">
							<span class="glyphicon glyphicon-refresh"></span> New Activity
						</button>
						<button type="button" class="btn btn-primary" id="btn-deleteActivity" disabled>
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
        <div class="panel-group box-body" id="servicePanel">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#servicePanel" href="#serviceDetails"> Activity Details</a>
                    </h4>
                </div>
                <div id="serviceDetails" class="panel-collapse collapse">
                    <div class="panel-body">  
                        <table id="tbl-activityDetails" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="col-sm-2"><input type="text" class="form-control bookingActivity" id="id_product_service_claim" style="display: none"></th>
                                    <th>CLAIM</th>
                                    <th>COST</th>
                                    <th>POLICY</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="unit_charge" style="display: none">
                                    <th>UNIT</th>
                                    <td><span id="ps_unit_claim"></span><span class="ps_claim_cur"></span></td>
                                    <td><span id="ps_unit_cost"></span><span class="ps_cost_cur"></span></td>
                                    <td id="unit_policy"></td>
                                </tr>
                                <tr class="pax_charge adult_details">
                                    <th>ADULT</th>
                                    <td><span id="ps_adult_claim"></span><span class="ps_claim_cur"></span></td>
                                    <td><span id="ps_adult_cost"></span><span class="ps_cost_cur"></span></td>
                                    <td id="adult_policy"></td>
                                </tr>
                                <tr class="pax_charge teen_details">
                                    <th>TEEN</th>
                                    <td><span id="ps_teen_claim"></span><span class="ps_claim_cur"></span></td>
                                    <td><span id="ps_teen_cost"></span><span class="ps_cost_cur"></span></td>
                                    <td id="teen_policy"></td>
                                </tr>
                                <tr class="pax_charge child_details">
                                    <th>CHILD</th>
                                    <td><span id="ps_child_claim"></span><span class="ps_claim_cur"></span></td>
                                    <td><span id="ps_child_cost"></span><span class="ps_cost_cur"></span></td>
                                    <td id="child_policy"></td>
                                </tr>
                                <tr class="pax_charge infant_details">
                                    <th>INFANT</th>
                                    <td><span id="ps_infant_claim"></span><span class="ps_claim_cur"></span></td>
                                    <td><span id="ps_infant_cost"></span><span class="ps_cost_cur"></span></td>
                                    <td id="infant_policy"></td>
                                </tr>
                                <tr class="activity_details">
                                    <th>DETAILS</th>
                                    <td colspan="3" id="activty_desc"></td>
                                </tr>
                                <tr class="activity_policyDetails">
                                    <th>POLICY</th>
                                    <td colspan="3" id="activty_policy"></td>
                                </tr>
                            </tbody>
                        </table>
                        <table id="tbl-activityExtra" class="table table-bordered table-hover" style="display: none">
                            <thead>
                                <tr>
                                    <th class="col-sm-3">EXTRA</th>
                                    <th class="col-sm-5">CLAIM</th>
                                    <th class="col-sm-4">COST</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#servicePanel" href="#dossierService"> Dossier Activity</a>
                    </h4>
                </div>
                <div id="dossierService" class="panel-collapse collapse">
                    <div class="panel-body">
                        <table id="tbl-bookingActivity" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="col-sm-4">Activity</th>
                                    <th class="col-sm-2">Date</th>
                                    <th class="col-sm-2">Rebate</th>
                                    <th class="col-sm-2">Claim</th>
                                    <th class="col-sm-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>TOUR - KOULEUR CHAMAREL</td>
                                    <td>26.07.2020</td>
                                    <td>None</td>
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
                </div>
            </div>
        </div>
	</div>
<!-- /.box -->
</div>	