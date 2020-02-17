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
					<div class="col-sm-7">
						<select class="form-control" id="accom_room">
						</select>
					</div>
					<div class="col-sm-4">
						<select class="form-control" id="accom_occupancy" placeholder="Occupancy">
							<option value="TO">OCCUPANCY</option>
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
		<table id="tbl-bookingAccom" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th rowspan="2">OCCUPANCY</th>
					<th class="col-sm-3" colspan="3" style="text-align: center">ADULT</th>
					<th class="col-sm-3" colspan="3" style="text-align: center">TEEN</th>
					<th class="col-sm-3" colspan="3" style="text-align: center">CHILD</th>
					<th class="col-sm-3" colspan="3" style="text-align: center">INFANT</th>
				</tr>
				<tr>
					<th>CLAIM</th>
					<th>COST</th>
					<th></th>
					<th>CLAIM</th>
					<th>COST</th>
					<th></th>
					<th>CLAIM</th>
					<th>COST</th>
					<th></th>
					<th>CLAIM</th>
					<th>COST</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>SINGLE</td>
                    <td><strike>365 USD</strike> <br><font color="red"> 345 USD </font> </td>
					<td>300 USD </td>
					<td>Max 1</td>
                    <td><strike>265 USD</strike> <br><font color="red"> 245 USD </font> </td>
					<td>200 USD </td>
					<td>Max 1</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>SINGLE - SHARING</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
                    <td><strike>165 USD</strike> <br><font color="red"> 145 USD </font> </td>
					<td>100 USD </td>
					<td>Max 1</td>
                    <td><strike>65 USD</strike> <br><font color="red"> 45 USD </font> </td>
					<td>10 USD </td>
					<td>Max 1</td>
				</tr>
				<tr>
					<td>DOUBLE</td>
                    <td><strike>355 USD</strike> <br><font color="red"> 345 USD </font> </td>
					<td>295 USD </td>
					<td>Max 2</td>
                    <td><strike>255 USD</strike> <br><font color="red"> 245 USD </font> </td>
					<td>195 USD </td>
					<td>Max 1</td>
                    <td><strike>165 USD</strike> <br><font color="red"> 145 USD </font> </td>
					<td>95 USD </td>
					<td>Max 2</td>
                    <td><strike>465 USD</strike> <br><font color="red"> 35 USD </font> </td>
					<td>7 USD </td>
					<td>Max 1</td>
				</tr>
				<tr>
					<td>DOUBLE - Sharing</td>
                    <td><strike>345 USD</strike> <br><font color="red"> 315 USD </font> </td>
					<td>280 USD </td>
					<td>Max 2</td>
                    <td><strike>245 USD</strike> <br><font color="red"> 215 USD </font> </td>
					<td>180 USD </td>
					<td>Max 1</td>
                    <td><strike>145 USD</strike> <br><font color="red"> 115 USD </font> </td>
					<td>80 USD </td>
					<td>Max 2</td>
                    <td><strike>45 USD</strike> <br><font color="red"> 25 USD </font> </td>
					<td>5 USD </td>
					<td>Max 1</td>
				</tr>
				<tr>
					<td>TRIPLE</td>
                    <td><strike>365 USD</strike> <br><font color="red"> 345 USD </font> </td>
					<td>300 USD </td>
					<td>Max 2</td>
                    <td><strike>265 USD</strike> <br><font color="red"> 245 USD </font> </td>
					<td>200 USD </td>
					<td>Max 1</td>
                    <td><strike>165 USD</strike> <br><font color="red"> 145 USD </font> </td>
					<td>100 USD </td>
					<td>Max 2</td>
                    <td><strike>65 USD</strike> <br><font color="red"> 45 USD </font> </td>
					<td>10 USD </td>
					<td>Max 1</td>
				</tr>
				<tr>
					<td>TRIPLE - Sharing</td>
                    <td><strike>365 USD</strike> <br><font color="red"> 345 USD </font> </td>
					<td>300 USD </td>
					<td>Max 2</td>
                    <td><strike>265 USD</strike> <br><font color="red"> 245 USD </font> </td>
					<td>200 USD </td>
					<td>Max 1</td>
                    <td><strike>165 USD</strike> <br><font color="red"> 145 USD </font> </td>
					<td>100 USD </td>
					<td>Max 2</td>
                    <td><strike>65 USD</strike> <br><font color="red"> 45 USD </font> </td>
					<td>10 USD </td>
					<td>Max 1</td>
				</tr>
			</tbody>
		</table>
	</div>
<div class="col-md-6">
		<table id="tbl-bookingAccom" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th class="col-sm-1" rowspan="2" style="text-align: center">TYPE</th>
					<th class="col-sm-1" colspan="2" style="text-align: center">OCCUPANCY</th>
					<th class="col-sm-2" colspan="2" style="text-align: center">OWN ROOM</th>
					<th class="col-sm-2" colspan="2" style="text-align: center">SHARING </th>
				</tr>
				<tr>
					<th>IN ROOM</th>
					<th>SHARING</th>
					<th>CLAIM</th>
					<th>COST</th>
					<th>CLAIM</th>
					<th>COST</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>ADULT</td>
					<td>2</td>
					<td>1</td>
					<td><strike>459 USD</strike> <br><font color="red"> 445 USD </font> </td>
					<td>434 USD</td>
					<td><strike>449 USD</strike> <br><font color="red"> 435 USD </font> </td>
					<td>424 USD</td>
				</tr>
				<tr>
					<td>TEEN</td>
					<td>2</td>
					<td>1</td>
					<td><strike>359 USD</strike> <br><font color="red"> 345 USD </font> </td>
					<td>334 USD</td>
					<td><strike>349 USD</strike> <br><font color="red"> 335 USD </font> </td>
					<td>324 USD</td>
				</tr>
				<tr>
					<td>CHILD</td>
					<td>2</td>
					<td>1</td>
					<td><strike>259 USD</strike> <br><font color="red"> 245 USD </font> </td>
					<td>234 USD</td>
					<td><strike>249 USD</strike> <br><font color="red"> 235 USD </font> </td>
					<td>224 USD</td>
				</tr>
				<tr>
					<td>INFANT</td>
					<td>2</td>
					<td>1</td>
					<td><strike>159 USD</strike> <br><font color="red"> 145 USD </font> </td>
					<td>134 USD</td>
					<td><strike>149 USD</strike> <br><font color="red"> 135 USD </font> </td>
					<td>124 USD</td>
				</tr>
				<tr>
					<th rowspan="2">POLICY</th>
					<th>Age</th>
					<td colspan="2">Infant : 0 - 2 Years </td>
					<td colspan="2">Child : 3 - 5 Years </td>
					<td colspan="1">Teen : 6 - 17 Years </td>
				</tr>
				<tr>
					<th>Baby Cot</th>
					<td colspan="6">
                        CLAIM 259 USD - COST 240 USD
                    </td>
				</tr>
				<tr>
					<th>Sharing</th>
					<td colspan="6">
                        1st Adult : CLAIM <strike>259 USD</strike><font color="red"> 245 USD </font> -  COST 240 USD<br>
                        2nd Adult : CLAIM <strike>259 USD</strike><font color="red"> 245 USD </font> -  COST 240 USD
                    </td>
				</tr>
			</tbody>
		</table>
	</div>-->
	<div class="col-md-6">
		<table id="tbl-bookingAccom" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th class="col-sm-3">OFFER</th>
					<th class="col-sm-5">DESCRIPTION</th>
					<th class="col-sm-4">COMBINABLE</th>
				</tr>
			</thead>
			<tbody>
				<tr >
					<td><font color="red">EARLY BIRD</font></td>
					<td><font color="red">40% discount on Half Board for all bookings made 180 days prior to guest arrival</font></td>
                    <td><font color="red">FAMILY OFFER, WEDDING OFFER</font></td>
				</tr>
				<tr>
					<td>LONG STAY OFFER</td>
					<td>40% discount on Half Board for all bookings made 180 days prior to guest arrival</td>
					<td>FAMILY OFFER, WEDDING OFFER</td>
				</tr>
				<tr>
					<td>FAMILY OFFER</td>
					<td>40% discount on Half Board for all bookings made 180 days prior to guest arrival</td>
					<td>FAMILY OFFER, WEDDING OFFER</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-md-6">
		<table id="tbl-bookingAccom" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th class="col-sm-4">Room</th>
					<th class="col-sm-2">Date</th>
					<th class="col-sm-2">No Night</th>
					<th class="col-sm-2">Claim</th>
					<th class="col-sm-2"></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>JUNIOR SUITE - DOUBLE - HALF BOARD</td>
					<td>26.07.2020 - 30.07.2020</td>
					<td>5</td>
					<td>3456 USD</td>
					<td>
						<div class="btn-group">
						  <i class="fa fa-fw fa-edit"></i>
						  <i class="fa fa-fw fa-trash"></i>
						</div>
					</td>
				</tr>
				<tr>
					<td>JUNIOR SUITE - DOUBLE - HALF BOARD</td>
					<td>26.07.2020 - 30.07.2020</td>
					<td>5</td>
					<td>3456 USD</td>
					<td>
						<div class="btn-group">
						  <i class="fa fa-fw fa-edit"></i>
						  <i class="fa fa-fw fa-trash"></i>
						</div>
					</td>
				</tr>
				<tr>
					<td>JUNIOR SUITE - DOUBLE - HALF BOARD</td>
					<td>26.07.2020 - 30.07.2020</td>
					<td>5</td>
					<td>3456 USD</td>
					<td>
						<div class="btn-group">
						  <i class="fa fa-fw fa-edit"></i>
						  <i class="fa fa-fw fa-trash"></i>
						</div>
					</td>
				</tr>
				<tr>
					<td>JUNIOR SUITE - DOUBLE - HALF BOARD</td>
					<td>26.07.2020 - 30.07.2020</td>
					<td>5</td>
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