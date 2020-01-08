<script src="php/application/bookingSystem/js/bookingTransfer.js"></script>
<script src="php/application/bookingSystem/js/newTransfer.js"></script>
<!-- Transfer Tab -->
<div class="tab-pane active in fade" id="transfer">
<!-- left column -->	
	<div class="col-md-6">
		<!-- form start -->
		<form class="form-horizontal">
			<!-- .box-body -->
			<div class="box-body">
                <div class="form-group" style="">
					<label class="col-sm-2 control-label">ID BOOKING TRANSFER</label>
					<div class="col-sm-2">
						<input type="text" class="form-control bookingTransfer" id="id_booking_transfer" placeholder="000" readonly>
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
							<option value="AIRPORT">Airport</option>
							<option value="PORT">Port</option>
						</select>
					</div>
					<label class="col-sm-1 control-label">Vehicle</label>
					<div class="col-sm-4">
						<select class="form-control" id="transfer_vehicle">
						</select>
					</div>
				</div>
                
				<div class="form-group">
					<label class="col-sm-2 control-label">Destination</label>
					<div class="col-sm-10">
						<select class="form-control" id="transfer_destination">
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
							<input readonly class="form-control bookingTransfer" id="transfer_teenAmt">
							<span class="input-group-addon">Teen</span>
							<input readonly class="form-control bookingTransfer" id="transfer_childAmt">
							<span class="input-group-addon">Child</span>
							<input readonly class="form-control bookingTransfer" id="transfer_infantAmt">
							<span class="input-group-addon">Infant</span>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Rebate</label>
					<div class="col-sm-4">
						<select class="form-control bookingTransfer select2" id="transfer_rebate">
							<option value="None">None</option>
							<option value="Percentage">Percentage</option>
							<option value="Fixed Tariff">Fixed Tariff</option>
							<option value="FOC">FOC</option>
						</select>
					</div>
					<label class="col-sm-2 control-label">Approved by</label>
					<div class="col-sm-4">
						<select class="form-control bookingTransfer select2" id="transfer_approvedBy">
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
							<input type="text" class="form-control bookingTransfer" id="transfer_percentageRebate" placeholder="00000" style="display: none">
						</div>
						<div class="col-sm-10" id="rebate_fix">
							<div class="input-group">
								<input type="number" class="form-control bookingTransfer" id="transfer_adultRebate">
								<span class="input-group-addon rebateAdult">Adult</span>
								<input type="number" class="form-control bookingTransfer" id="transfer_teenRebate">
								<span class="input-group-addon rebateTeen">Teen</span>
								<input type="number" class="form-control bookingTransfer" id="transfer_childRebate">
								<span class="input-group-addon rebateChild">Child</span>
								<input type="number" class="form-control bookingTransfer" id="transfer_InfantRebate">
								<span class="input-group-addon rebateInfant">Infant</span>
							</div>
						</div>
					</div>
				</div>
				
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
						<button type="button" class="btn btn-primary" id="btn-deleteTransfer" disabled>
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
                        <a data-toggle="collapse" data-parent="#servicePanel" href="#serviceDetails"> Transfer Details</a>
                    </h4>
                </div>
                <div id="serviceDetails" class="panel-collapse collapse">
                    <div class="panel-body">  
		                <table id="tbl-bookingTransfer" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">TYPE<input type="text" class="form-control bookingTransfer" id="id_product_service_claim" style=""></th>
                                    <th class="col-sm-2 unit_charge">UNIT</th>
                                    <th class="col-sm-2 pax_charge">ADULT</th>
                                    <th class="col-sm-2 pax_charge">CHILD</th>
                                    <!--<th class="col-sm-2">MAX OCCUPANCY</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="transfer_in">
                                    <td>ARRIVAL</td>
                                    <td class="unit_charge"><span id="ps_unit_arr_claim"></span><span class="ps_claim_arr_cur"></span></td>
                                    <td><span id="ps_adult_arr_claim"></span><span class="ps_claim_arr_cur"></span></td>
                                    <td><span id="ps_child_arr_claim"></span><span class="ps_claim_arr_cur"></span></td>
                                </tr>
                                <tr id="transfer_out">
                                    <td>DEPARTURE</td>
                                    <td class="unit_charge"><span id="ps_unit_dep_claim"></span><span class="ps_claim_dep_cur"></span></td>
                                    <td><span id="ps_adult_dep_claim"></span> <span class="ps_claim_dep_cur"></span></td>
                                    <td><span id="ps_child_dep_claim"></span> <span class="ps_claim_dep_cur"></span></td>
                                </tr>
                                <tr id="transfer_interHotel">
                                    <td>INTER HOTEL</td>
                                    <td class="unit_charge"><span id="ps_unit_int_claim"></span><span class="ps_claim_int_cur"></span></td>
                                    <td><span id="ps_adult_int_claim"></span> <span class="ps_claim_int_cur"></span></td>
                                    <td><span id="ps_child_int_claim"></span> <span class="ps_claim_int_cur"></span></td>
                                </tr>
                                <tr>
                                    <th>DESCRIPTION</th>
                                    <td colspan="3" id="transfer_description">459 USD</td>
                                </tr>
                            </tbody>
                        </table>
                        
		                <table id="tbl-bookingTransfer" class="table table-bordered table-hover">
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
                </div>
            </div>
            
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#servicePanel" href="#dossierService"> Dossier Transfer</a>
                    </h4>
                </div>
                <div id="dossierService" class="panel-collapse collapse">
                    <div class="panel-body">
		                <table id="tbl-bookingTransfer" class="table table-bordered table-hover">
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
                </div>
            </div>
        </div>
    </div>
<!-- /.box -->
</div>	
<!-- Transfer Tab -->