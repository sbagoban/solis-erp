<script src="js/utils/numberValidation.js"></script>
<script src="php/application/bookingSystem/js/bookingTransfer.js"></script>
<script src="php/application/bookingSystem/js/newTransfer.js"></script>
<script src="php/application/bookingSystem/js/tableBookingTransfer.js"></script>
<script src="php/application/bookingSystem/js/saveTransfer.js"></script>
<script src="php/application/bookingSystem/js/deleteTransfer.js"></script>
<!-- Transfer Tab -->
<div class="tab-pane active in fade" id="transfer">
<!-- left column -->	
	<div class="col-md-6">
		<!-- form start -->
		<form class="form-horizontal">
			<!-- .box-body -->
			<div class="box-body">
                <div class="form-group" style="display: none">
					<label class="col-sm-2 control-label">ID BOOKING TRANSFER</label>
					<div class="col-sm-2">
						<input type="text" class="form-control bookingTransfer" id="id_booking_transfer_claim" placeholder="000" readonly>
					</div>
				</div>
                
				<div class="form-group">
					<label class="col-sm-2 control-label">Booking Date</label>
					<div class="col-sm-3">
						<input type="text" class="form-control bookingTransfer" id="transfer_bookingDate" placeholder="00/00/0000">
					</div>
					<label class="col-sm-1 control-label">Status</label>
					<div class="col-sm-6">
						<select class="form-control bookingTransfer select2" id="transfer_status">
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
						<select class="form-control bookingTransfer" id="transfer_paidBy">
							<option value="TO">Tour Operator</option>
							<option value="Client">Client</option>
						</select>
					</div>
					<div class="col-sm-7">
						<select class="form-control bookingTransfer" id="transfer_payer">
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label">Type</label>
					<div class="col-sm-2">
						<select class="form-control" id="transfer_type">
							<option value="NONE" selected disabled>NONE</option>
							<option value="BOTH">BOTH</option>
							<option value="ARR">ARRIVAL</option>
							<option value="DEP">DEPARTURE</option>
							<option value="INTER HOTEL">INTER HOTEL</option>
							<option value="ACTIVITY">ACTIVITY</option>
						</select>
					</div>
					<div class="col-sm-3">
						<select class="form-control" id="transfer_port">
							<option value="0" disabled selected>NONE</option>
						</select>
					</div>
					<label class="col-sm-1 control-label">Vehicle</label>
					<div class="col-sm-4">
						<select class="form-control" id="transfer_vehicle">
							<option value="0" disabled selected>NONE</option>
						</select>
					</div>
				</div>
                
				<div class="form-group destinationFrom">
					<label class="col-sm-2 control-label">Destination From</label>
					<div class="col-sm-10">
						<select class="form-control" id="transfer_destination_from">
						</select>
					</div>
				</div>
                
				<div class="form-group destinationTo">
					<label class="col-sm-2 control-label">Destination To</label>
					<div class="col-sm-10">
						<select class="form-control" id="transfer_destination_to">
						</select>
					</div>
				</div>

				<div class="form-group arrivalLine">
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

				<div class="form-group departureLine">
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

				<div class="form-group pickupLine" style="display: none">
					<label class="col-sm-2 control-label" id="pickupLabel">Departure</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" id="transfer_pickupDate" placeholder="00/00/0000">
					</div>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="transfer_pickupFlight" placeholder="ZZZ000">
					</div>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="transfer_pickupTime" placeholder="00:00">
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Client</label>
					<div class="col-sm-10">
						<select class="form-control bookingTransfer selectpicker" multiple data-live-search="true" data-actions-box="true" id="transfer_client" multiple="multiple">
						</select>
					</div>
				</div>
                
				<div class="form-group">
					<label class="col-sm-2 control-label"></label>
					<div class="col-sm-10">
						<div class="input-group">
							<input readonly class="form-control bookingTransfer" id="transfer_adultAmt">
							<span class="input-group-addon">Adult</span>
							<input readonly class="form-control bookingTransfer" id="transfer_childAmt">
							<span class="input-group-addon">Child</span>
							<input readonly class="form-control bookingTransfer" id="transfer_infantAmt">
							<span class="input-group-addon">Infant</span>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Claim Rebate</label>
					<div class="col-sm-4">
						<select class="form-control bookingTransfer select2" id="transfer_rebateClaim">
							<option value="None">None</option>
							<option value="Percentage">Percentage</option>
							<option value="Fixed Tariff">Fixed Tariff</option>
							<option value="FOC">FOC</option>
						</select>
					</div>
					<label class="col-sm-2 control-label">Approved by</label>
					<div class="col-sm-4">
						<select class="form-control bookingTransfer select2" id="transfer_rebateClaimApproveBy">
							<option value="None">None</option>
							<option value="Percentage">Percentage</option>
							<option value="Fixed Tariff">Fixed Tariff</option>
							<option value="FOC">FOC</option>
						</select>
					</div>
				</div>
				
				<div id="claimRebateSection">
					<div class="form-group">
						<label class="col-sm-2 control-label">Claim Rebate</label>
						<div class="col-sm-4">
							<input type="text" class="form-control numberWithDecimal bookingTransfer" id="transfer_claimPercentageRebate" placeholder="00000" style="display: none">
						</div>
						<div class="col-sm-10" id="claimRebateFix">
							<div class="input-group">
								<input type="text" class="form-control numberWithDecimal bookingTransfer" id="transfer_adultClaimRebate">
								<span class="input-group-addon rebateAdult">Adult</span>
								<input type="text" class="form-control numberWithDecimal bookingTransfer" id="transfer_childClaimRebate">
								<span class="input-group-addon rebateChild">Child</span>
								<input type="text" class="form-control numberWithDecimal bookingTransfer" id="transfer_InfantClaimRebate">
								<span class="input-group-addon rebateInfant">Infant</span>
							</div>
						</div>
					</div>
				</div>
				
				<!--<div class="form-group">
					<label class="col-sm-2 control-label">Cost Rebate</label>
					<div class="col-sm-4">
						<select class="form-control bookingTransfer select2" id="transfer_rebateCost">
							<option value="None">None</option>
							<option value="Percentage">Percentage</option>
							<option value="Fixed Tariff">Fixed Tariff</option>
							<option value="FOC">FOC</option>
						</select>
					</div>
					<label class="col-sm-2 control-label">Approved by</label>
					<div class="col-sm-4">
						<select class="form-control bookingTransfer select2" id="transfer_costApprovedBy">
							<option value="None">None</option>
							<option value="Percentage">Percentage</option>
							<option value="Fixed Tariff">Fixed Tariff</option>
							<option value="FOC">FOC</option>
						</select>
					</div>
				</div>
				
				<div id="costRebateSection">
					<div class="form-group">
						<label class="col-sm-2 control-label">Cost Rebate</label>
						<div class="col-sm-4">
							<input type="text" class="form-control numberWithDecimal bookingTransfer" id="transfer_costPercentageRebate" placeholder="00000" style="display: none">
						</div>
						<div class="col-sm-10" id="costRebateFix">
							<div class="input-group">
								<input type="text" class="form-control numberWithDecimal bookingTransfer" id="transfer_adultCostRebate">
								<span class="input-group-addon rebateAdult">Adult</span>
								<input type="text" class="form-control numberWithDecimal bookingTransfer" id="transfer_childCostRebate">
								<span class="input-group-addon rebateChild">Child</span>
								<input type="text" class="form-control numberWithDecimal bookingTransfer" id="transfer_InfantCostRebate">
								<span class="input-group-addon rebateInfant">Infant</span>
							</div>
						</div>
					</div>
				</div>-->
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Service Remark</label>
					<div class="col-sm-4">
						<textarea class="form-control bookingTransfer" id="transfer_serviceRemark" rows="3" style="resize: none"></textarea>
					</div>
					<label class="col-sm-2 control-label">Internal Remark</label>
					<div class="col-sm-4">
						<textarea class="form-control bookingTransfer" id="transfer_internalRemark" rows="3" style="resize: none"></textarea>
					</div>
				</div>


				<div class="pager">
						<button type="button" class="btn btn-primary" text-align="center" id="btn-saveTransfer">
							<span class="glyphicon glyphicon-ok"></span> Save
						</button>
						<button type="button" class="btn btn-primary" id="btn-newTransfer">
							<span class="glyphicon glyphicon-refresh"></span> New Transfer
						</button>
						<button type="button" class="btn btn-primary" id="btn-deleteTransfer">
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
                        <a class="panel-title" data-toggle="collapse" data-parent="#servicePanel" panel="serviceDetails"> Transfer Details</a>
                </div>
                <div id="serviceDetails" class="panel-collapse collapse">
                    <div class="panel-body">  
		                <table id="tbl-transferDetails" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">TYPE</th>
                                    <th class="col-sm-2 unit_charge">UNIT</th>
                                    <th class="col-sm-2 pax_charge">ADULT</th>
                                    <th class="col-sm-2 pax_charge">CHILD</th>
                                    <!--<th class="col-sm-2">MAX OCCUPANCY</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="display: none">
                                <!--<tr style="display: none">-->
                                    <td colspan="2">
						                <input type="text" id="id_product_service_arr_claim">
                                    </td>
                                    <td colspan="2">
						                <input type="text" id="id_product_service_dep_claim">
                                    </td>
                                </tr>
                                <tr id="transfer_in">
                                    <td>ARRIVAL</td>
                                    <td class="unit_charge" colspan="3"><span id="ps_unit_arr_claim"></span><span class="ps_claim_arr_cur"></span></td>
                                    <td class="pax_charge"><span id="ps_adult_arr_claim"></span><span class="ps_claim_arr_cur"></span></td>
                                    <td class="pax_charge"><span id="ps_child_arr_claim"></span><span class="ps_claim_arr_cur"></span></td>
                                </tr>
                                <tr id="transfer_out">
                                    <td>DEPARTURE</td>
                                    <td class="unit_charge" colspan="3"><span id="ps_unit_dep_claim"></span><span class="ps_claim_dep_cur"></span></td>
                                    <td class="pax_charge"><span id="ps_adult_dep_claim"></span> <span class="ps_claim_dep_cur"></span></td>
                                    <td class="pax_charge"><span id="ps_child_dep_claim"></span> <span class="ps_claim_dep_cur"></span></td>
                                </tr>
                                <tr id="transfer_interHotel">
                                    <td>INTER HOTEL</td>
                                    <td class="unit_charge" colspan="3"><span id="ps_unit_int_claim"></span><span class="ps_claim_int_cur"></span></td>
                                    <td class="pax_charge"><span id="ps_adult_int_claim"></span> <span class="ps_claim_int_cur"></span></td>
                                    <td class="pax_charge"><span id="ps_child_int_claim"></span> <span class="ps_claim_int_cur"></span></td>
                                </tr>
                                <tr id="transfer_activity">
                                    <td>ACTIVITY</td>
                                    <td class="unit_charge" colspan="3"><span id="ps_unit_act_claim"></span><span class="ps_claim_act_cur"></span></td>
                                    <td class="pax_charge"><span id="ps_adult_act_claim"></span> <span class="ps_claim_act_cur"></span></td>
                                    <td class="pax_charge"><span id="ps_child_act_claim"></span> <span class="ps_claim_act_cur"></span></td>
                                </tr>
                                <tr>
                                    <th>DESCRIPTION</th>
                                    <td colspan="3" id="transfer_description"></td>
                                </tr>
                            </tbody>
                        </table>
                        
		               <!-- <table id="tbl-transferExtra" class="table table-bordered table-hover">
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
                        </table>-->
	                </div>
                </div>
            </div>
            
            <div class="panel panel-default">
                <div class="panel-heading">
                        <a class="panel-title" data-toggle="collapse" data-parent="#servicePanel" panel="dossierService"> Dossier Transfer</a>
                </div>
                <div id="dossierService" class="panel-collapse collapse">
                    <div class="panel-body">
		                <table id="tbl-bookingTransfer" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="col-sm-5">Transfer</th>
                                    <th class="col-sm-2">Date</th>
                                    <th class="col-sm-1">Rebate</th>
                                    <th class="col-sm-2">Claim</th>
                                    <th class="col-sm-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
	                </div>
                </div>
            </div>
        </div>
    </div>
<!-- /.box -->
</div>	
<!-- Transfer Tab -->