<script src="php/application/bookingSystem/js/bookingSystem.js"></script>
<script src="php/application/bookingSystem/js/newBooking.js"></script>
<script src="php/application/bookingSystem/js/searchBooking.js"></script>
<script src="php/application/bookingSystem/js/saveBooking.js"></script>
<script src="php/application/bookingSystem/js/deleteBooking.js"></script>
<script src="php/application/bookingSystem/js/bookingTab.js"></script>
<link rel="stylesheet" href="php/application/bookingSystem/css/bookingSystem.css">
<!-- Main content -->
<section class="content">
	<!-- 1 row -->
	<div class="row">
		<div class="col-md-12">
			<!-- Booking Form -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Reservation Dossier</h3>
				</div>
				<!-- /.box-header -->
				<!-- form start -->
				<form id="bookingDossier" class="form-horizontal" onsubmit="return false">					
              		<!-- .box-body -->
              		<div class="box-body">
						<div class="form-group">

							<label class="col-sm-2 control-label">Dossier</label>
							<div class="col-sm-2">
								
								<div class="input-group add-on">
									<input class="form-control numberNoDecimal" placeholder="000000" name="id_booking" id="id_booking" type="text" onkeypress="return runScript(event)">
									<div class="input-group-btn">
										<button class="btn btn-default" id="btn-searchBooking"><i class="glyphicon glyphicon-search"></i></button>
									</div>
								</div>
							</div>
							<label class="col-sm-1 control-label">TO REF</label>
							<div class="col-sm-2">
								<input type="text" class="form-control bookingDossier" id="booking_toRef" placeholder="000A000A">
							</div>
							<div class="col-sm-5">
								<div class="btn-group">
								<button type="button" class="btn btn-default">Voucher</button>
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul class="dropdown-menu" role="menu">
									<li><a href="#">Client Voucher</a></li>
									<li><a href="#">Supplier Voucher</a></li>
									<li class="divider"></li>
									<li><a href="#">Invoice</a></li>
								</ul>
								</div>
								<div class="btn-group">
									<button type="button" class="btn btn-default">Search</button>
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
										<span class="caret"></span>
										<span class="sr-only">Toggle Dropdown</span>
									</button>
									<ul class="dropdown-menu" role="menu">
										<li><a href="#">Dossier</a></li>
										<li class="divider"></li>
										<li><a href="#">Invoice</a></li>
									</ul>
								</div>
								<div class="btn-group">
									<button type="button" class="btn btn-default">Log</button>
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
										<span class="caret"></span>
										<span class="sr-only">Toggle Dropdown</span>
									</button>
									<ul class="dropdown-menu" role="menu">
										<li><a href="#">Dossier</a></li>
										<li class="divider"></li>
										<li><a href="#">Client</a></li>
										<li><a href="#">Accomodation</a></li>
										<li><a href="#">Transfer</a></li>
										<li><a href="#">Activities</a></li>
										<li><a href="#">Service</a></li>
										<li><a href="#">Car Rental</a></li>
									</ul>
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">TO Name</label>
							<div class="col-sm-4">
								<select class="form-control bookingDossier select2" id="booking_toName">
									<option></option>
								</select>
							</div>
							<label class="col-sm-1 control-label">Origin</label>
							<div class="col-sm-2">
								<select class="form-control bookingDossier select2" id="booking_paxOrigin">
									<option></option>
								</select>
							</div>
							<label class="col-sm-1 control-label">Department</label>
							<div class="col-sm-2">
								<select class="form-control bookingDossier select2" id="booking_dept">
									<option></option>
								</select>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">Client</label>
							<div class="col-sm-4">
								<input type="text" class="form-control bookingDossier text-uppercase" id="booking_dossierName" placeholder="Dossier Name">
							</div>
							<label class="col-sm-2 control-label">Pax type</label>
							<div class="col-sm-4">
								<select class="form-control bookingDossier select2" id="booking_clientType">
									<option value="NORMAL">Normal</option>
									<option value="HONEYMOON">Honeymoon</option>
									<option value="WEDDING ANNIVERSARY">Wedding Anniversary</option>
								</select>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">Travel</label>
							<div class="col-sm-3">
								<input type="text" class="form-control bookingDossier" id="booking_travelDate" placeholder="31/12/9999 - 31/12/9999">
							</div>
							<label class="col-sm-1 control-label">Pax</label>
							<div class="col-sm-6">
								<div class="input-group">
									<input type="text" class="form-control  numberNoDecimal bookingDossier" id="booking_adultAmt">
									<span class="input-group-addon">Adult</span>
									<input type="text" class="form-control  numberNoDecimal bookingDossier" id="booking_teenAmt">
									<span class="input-group-addon">Teen</span>
									<input type="text" class="form-control numberNoDecimal bookingDossier" id="booking_childAmt">
									<span class="input-group-addon">Child</span>
									<input type="text" class="form-control numberNoDecimal bookingDossier" id="booking_infantAmt">
									<span class="input-group-addon">Infant</span>
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">Status</label>
							<div class="col-sm-3">
								<select class="form-control bookingDossier select2" id="booking_status">
									<option value="QUOTE" selected>QUOTE</option>
									<option value="CONFIRM">CONFIRM</option>
									<option value="CANCEL">CANCEL</option>
									<option value="CANCEL WITH FEE">CANCEL WITH FEE</option>
								</select>
							</div>
							<label class="col-sm-1 control-label">Closure</label>
							<div class="col-sm-2">
								<input type="text" class="form-control bookingDossier" id="booking_closureDate" placeholder="31/12/9999" readonly>
							</div>
							<label class="col-sm-1 control-label">Created</label>
							<div class="col-sm-3">
								<input type="text" class="form-control bookingDossier" id="booking_createdBy" placeholder="000A000A" readonly>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">Remarks</label>
							<div class="col-sm-10">
								<textarea class="form-control bookingDossier" id="booking_remarks" rows="3" style="resize: none"></textarea>
							</div>
						</div>
						
						<div class="pager">
                                <button type="button" class="btn btn-primary" text-align="center" id="btn-saveBooking">
									<span class="glyphicon glyphicon-ok"></span> Save
								</button>
                                <button type="button" class="btn btn-primary" id="btn-newBooking">
                                    <span class="glyphicon glyphicon-refresh"></span> New Booking
                                </button>
                                <button type="button" class="btn btn-primary" id="btn-deleteBooking">
                                    <span class="glyphicon glyphicon-remove"></span> Delete
                                </button>
                        </div>
					</div>
              		<!-- /.box-body -->
				</form>
			</div>
			<!-- /.box -->
		</div>
		<!-- right column -->
		<!--<div class="col-md-3">
			<div class="box box-primary" style="height:520px">
				<div class="box-header with-border">
				<h3 class="box-title">Summary</h3>
				</div>
				<form class="form-horizontal">
					
              		<div class="box-body">
						
					</div>
				</form>
			</div>
		</div>-->
  	</div>
	<!-- /.row -->
	  <!-- End Seven Tab -->	  
	<div class="toast jam toast_found" aria-hidden="true" style="display:none;">
		<span class="close" aria-role="button" tabindex="0">&times;</span> Dossier found.
	</div>

	<div class="toast-danger jam toast_notfound" aria-hidden="true" style="display:none;">
		<span class="close" aria-role="button" tabindex="0">&times;</span> Dossier not found.
	</div>
	<!-- 2 row -->
	<div class="row">
		<!-- main column -->
		<div class="col-md-12">
			<div class="box box-primary">
              	<!-- .box-body -->
              	<div class="box-body">
					<div class="board-inner">
						<div class="liner">
							<ul class="nav nav-tabs" id="myTab" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" id="client-tab" data-toggle="tab" href="#client" role="tab" aria-controls="client" aria-selected="true">
										<span class="round-tabs one">
											<i class="fa fa-users fa-lg"></i><br>Client                                    
										</span>
									</a>
								</li>
								
								<li class="nav-item">
									<a class="nav-link" id="accom-tab" data-toggle="tab" href="#accom" role="tab" aria-controls="accom" aria-selected="true">
										<span class="round-tabs two">
											<i class="fa fa-hotel fa-lg"></i><br>Accomodation                                  
										</span>
									</a>
								</li>

								<li class="nav-item">
									<a class="nav-link" id="transfer-tab" data-toggle="tab" href="#transfer" role="tab" aria-controls="transfer" aria-selected="true">
										<span class="round-tabs three">
											<i class="fa fa-bus fa-lg"></i><br>Transfer
										</span>
									</a>
								</li>

								<li class="nav-item">
									<a class="nav-link" id="activity-tab" data-toggle="tab" href="#activity" role="tab" aria-controls="activity" aria-selected="true">
										<span class="round-tabs four">
											<i class="fa fa-binoculars fa-lg"></i><br>Activity
										</span>
									</a>
								</li>

								<!--<li class="nav-item">
									<a class="nav-link" id="service-tab" data-toggle="tab" href="#service" role="tab" aria-controls="service" aria-selected="true">
										<span class="round-tabs five">
											<i class="fa fa-tags fa-lg"></i><br>Service
										</span>
									</a>
								</li>-->

								<li class="nav-item">
									<a class="nav-link" id="carRental-tab" data-toggle="tab" href="#carRental" role="tab" aria-controls="carRental" aria-selected="true">
										<span class="round-tabs six">
											<i class="fa fa-car fa-lg"></i><br>Car Rental
										</span>
									</a>
								</li>
							</ul>
						</div>
					</div>
				
					<div class="tab-content" id="myTabContent">
					</div>
			
				</div>
              	<!-- /.box-body -->
			</div>
		</div>
		<!-- /main column -->
	</div>
	<!-- /.row -->

</section>
<!-- /.content -->

