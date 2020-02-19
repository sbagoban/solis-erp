<?php
require_once("../../api/ratescalculator/_rates_get_contract.php");
require_once("../../api/ratescalculator/_rates_calculator.php");
require_once("../../api//hotelspecialoffers/_spo_taxcommi.php");
require_once("../../api/hotelcontracts/_contract_capacityarr.php");
require_once("../../api/hotelcontracts/_contract_exchangerates.php");
require_once("../../api/hotelcontracts/_contract_calculatesp.php");
require_once("../../api/hotelcontracts/_contract_taxcommi.php");
require_once("../../api/hotelcontracts/_contract_combinations_rooms.php");
require_once("../../globalvars/globalvars.php");
require_once("../../utils/utilities.php");
?>
<script src="php/application/bookingSystem/js/bookingAccom.js"></script>
<script src="php/application/bookingSystem/js/newAccom.js"></script>
<script src="php/application/bookingSystem/js/phpMailer.js"></script>
<script src="php/application/bookingSystem/js/gridAccomDetails.js"></script>
<!-- Accommodation Tab -->
<div class="tab-pane active in fade" id="accom">
<!-- left column -->	
	<div class="col-md-6">
		<!-- form start -->
		<form class="form-horizontal">
			<!-- .box-body -->
			<div class="box-body">
                <div class="form-group" style="">
					<label class="col-sm-2 control-label">ID BOOKING ACCOM</label>
					<div class="col-sm-2">
						<input type="text" class="form-control bookingAccom" id="id_booking_accom_claim" placeholder="000" readonly>
					</div>
				</div>
                
				<div class="form-group">
					<label class="col-sm-1 control-label">Booking Date</label>
					<div class="col-sm-3">
						<input type="text" class="form-control bookingAccom" id="accom_bookingDate" placeholder="00/00/0000">
					</div>
					<label class="col-sm-1 control-label">Status</label>
					<div class="col-sm-7">
						<select class="form-control bookingAccom select2" id="accom_status">
							<option value="QUOTE" selected>QUOTE</option>
							<option value="CONFIRM">CONFIRM</option>
							<option value="CANCEL">CANCEL</option>
							<option value="CANCEL WITH FEE">CANCEL WITH FEE</option>
						</select>
					</div>
				</div>
                
				<div class="form-group">
					<label class="col-sm-1 control-label">Paid By</label>
					<div class="col-sm-3">
						<select class="form-control bookingAccom" id="accom_paidBy">
							<option value="TO">Tour Operator</option>
							<!--<option value="Client">Client</option>-->
						</select>
					</div>
					<div class="col-sm-8">
						<select class="form-control bookingAccom" id="accom_payer">
						</select>
					</div>
				</div>
                
				<div class="form-group">
					<label class="col-sm-1 control-label">Stay</label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="accom_stay" placeholder="00/00/0000">
					</div>
					<label class="col-sm-1 control-label">Night</label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="accom_night" placeholder="0" readonly>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-1 control-label">Hotel</label>
					<div class="col-sm-7">
						<select class="form-control" id="accom_hotel">
						</select>
					</div>
					<div class="col-sm-4">
						<select class="form-control" id="accom_mealPlan">
						</select>
					</div>
                </div>
                
				<div class="form-group">
					<label class="col-sm-1 control-label">Room</label>
					<div class="col-sm-11">
						<select class="form-control" id="accom_room">
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-1 control-label">Client</label>
					<div class="col-sm-11">
						<select class="form-control bookingAccom selectpicker" multiple data-live-search="true" data-actions-box="true" id="accom_client" multiple="multiple">
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-1 control-label"></label>
					<div class="col-sm-11">
						<div class="input-group">
							<input type="number" class="form-control" id="accom_adultAmt">
							<span class="input-group-addon">Adult</span>
							<input type="number" class="form-control" id="accom_TeentAmt">
							<span class="input-group-addon">Teen</span>
							<input type="number" class="form-control" id="accom_childAmt">
							<span class="input-group-addon">Child</span>
							<input type="number" class="form-control" id="accom_InfantAmt">
							<span class="input-group-addon">Infant</span>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-1 control-label">Rebate</label>
					<div class="col-sm-4">
						<select class="form-control" id="accom_rebate">
							<option value="Percentage">Percentage</option>
							<option value="Fixed Tariff">Fixed Tariff</option>
							<option value="FOC">FOC</option>
						</select>
					</div>
					<label class="col-sm-3 control-label">Approved by</label>
					<div class="col-sm-4">
						<select class="form-control" id="accom_approvedBy">
							<option value="None">None</option>
							<option value="Percentage">Percentage</option>
							<option value="Fixed Tariff">Fixed Tariff</option>
							<option value="FOC">FOC</option>
						</select>
					</div>
				</div>
				<div id="rebateSection">
					<div class="form-group">
						<label class="col-sm-1 control-label">Room Rebate</label>
						<div class="col-sm-11">
							<div class="input-group">
								<input type="number" class="form-control" id="accom_adultRebate">
								<span class="input-group-addon">Adult</span>
								<input type="number" class="form-control" id="accom_TeentRebate">
								<span class="input-group-addon">Teen</span>
								<input type="number" class="form-control" id="accom_childRebate">
								<span class="input-group-addon">Child</span>
								<input type="number" class="form-control" id="accom_InfantRebate">
								<span class="input-group-addon">Infant</span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-1 control-label">Sharing Rebate</label>
						<div class="col-sm-11">
							<div class="input-group">
								<input type="number" class="form-control" id="accom_adultSharingRebate">
								<span class="input-group-addon">Adult</span>
								<input type="number" class="form-control" id="accom_TeentSharingRebate">
								<span class="input-group-addon">Teen</span>
								<input type="number" class="form-control" id="accom_childSharingRebate">
								<span class="input-group-addon">Child</span>
								<input type="number" class="form-control" id="accom_InfantSharingRebate">
								<span class="input-group-addon">Infant</span>
							</div>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-1 control-label">Special Offer</label>
					<div class="col-sm-11">
						<select class="form-control" id="accom_hotel">
							<option value="TO">Tour Operator</option>
							<option value="Client">Client</option>
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-1 control-label">Baby Cot</label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="accom_babyCot">
					</div>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="accom_babyCotPrice">
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-1 control-label">Service Remark</label>
					<div class="col-sm-5">
						<textarea class="form-control" id="accom_serviceRemark" rows="3" style="resize: none"></textarea>
					</div>
					<label class="col-sm-1 control-label">Internal Remark</label>
					<div class="col-sm-5">
						<textarea class="form-control" id="accom_internalRemark" rows="3" style="resize: none"></textarea>
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
        <div class="panel-group box-body" id="servicePanel">
            <div class="panel panel-default">
                <div class="panel-heading">
                        <a class="panel-title" data-toggle="collapse" data-parent="#servicePanel" panel="serviceDetails"> Accom Details</a>
                </div>
                <!-- <div id="serviceDetails" class="panel-collapse collapse in"> -->
                <div id="serviceDetails">
                    <div class="panel-body" id="grid_accom_details">
                        <!-- <table id="tbl-accomDetails" class="table table-bordered table-hover">
                            <tr>
                                <th>Room Charge</th>
                                <td colspan="3">PAX/UNIT</td>
                            </tr>
                            <tr>
                                <th>Room Status</th>
                                <td colspan="3">On Request</td>
                            </tr>
                            <tr>
                                <th>Room Policy</th>
                                <td>Infant : 0-2</td>
                                <td>Child : 3-11</td>
                                <td>Team : 12-17 </td>
                            </tr>
                            <tr>
                                <th class="text-center"  colspan="4">Room Claim Description</th>
                            </tr>
                            <tr>
                                <th>Pax Type</th>
                                <th>Claim </th>
                                <th>Special Claim</th>
                                <th>Cost</th>
                            </tr>
                            <tr>
                                <td>Adult Bride</td>
                                <td>$ 48 </td>
                                <td>$ 48</td>
                                <td>$ 45</td>
                            </tr>
                            <tr>
                                <td>Adult Groom</td>
                                <td>$ 48 </td>
                                <td>$ 48</td>
                                <td>$ 45</td>
                            </tr>
                            <tr>
                                <td>Adult </td>
                                <td>$ 48 </td>
                                <td>$ 48</td>
                                <td>$ 45</td>
                            </tr>
                            <tr>
                                <td>Child </td>
                                <td>$ 37 </td>
                                <td>$ 37</td>
                                <td>$ 35</td>
                            </tr>
                            <tr>
                                <td>Child </td>
                                <td>$ 37 </td>
                                <td>$ 37</td>
                                <td>$ 35</td>
                            </tr>
                            <tr>
                                <th class="text-center"  colspan="4">Special Offer apply</th>
                            </tr>
                            <tr>
                                <td  colspan="4">
                                    SEASONAL OFFER - ALL INCLUSIVE
                                    Breakfast Included
                                    Seasonal Offer - All Inclusive
                                    Rates include All Inclusive Package, 15 percent VAT and 15-30 percent discount. Free Green Fee at both our Golf Courses. Rate requires 50 per cent deposit of the total stay.
                                </td>
                            </tr>
                        </table> -->
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                            <a class="panel-title" data-toggle="collapse" data-parent="#servicePanel" panel="dossierService"> Dossier Accomodation</a>
                    </div>
                    <div id="dossierService" class="panel-collapse collapse">
                        <div class="panel-body">
                            <table id="tbl-bookingAccom" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="col-sm-5">Accomodation</th>
                                        <th class="col-sm-2">Stay Date</th>
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
	</div>
<!-- .right column -->
<!-- /Accomodation Tab -->
<!--<script src="bower_components/jquery/dist/jquery.min.js"></script>
<script>

$(function () {
	
	
    $('#accom_stay').daterangepicker();
});-->