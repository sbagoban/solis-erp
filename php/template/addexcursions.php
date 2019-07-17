<div class="container">
    <div class="col-lg-12">
    <!-- Row start -->
    <div class="row">
        <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
            <i class="icon-calendar"></i>
            <h3 class="panel-title">Add New Service</h3>
            </div>
        
            <div class="panel-body">
            <form class="form-horizontal row-border" action="#">
                <div class="form-group has-warning">
                <label class="col-md-2 control-label">Location</label>
                <div class="col-md-10">
                    <select class="custom-select form-control form-control-sm"  id="inputSuccess" placeholder="Default Location" style="width: 100%;" name="location[location]" id="chooseLocation">
                        <option selected disabled hidden>Choose Location</option>
                        <option value="1">CR</option>
                        <option value="2">CT</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                </div>
                <div class="form-group has-warning">
                <label class="col-md-2 control-label">Service Type</label>
                    <div class="col-md-10">
                        <select class="custom-select form-control form-control-sm"  style="width: 100%;" name="location[location]" id="chooseLocation">
                            <option selected disabled hidden>Choose Service Type</option>
                            <option value="1">CR</option>
                            <option value="2">CT</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group has-warning">
                <label class="col-md-2 control-label">Supplier</label>
                    <div class="col-md-10">
                        <select class="custom-select form-control form-control-sm"  style="width: 100%;" name="location[location]" id="chooseLocation">
                            <option selected disabled hidden>Choose Supplier</option>
                            <option value="1">CR</option>
                            <option value="2">CT</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                <label class="col-md-2 control-label">Option Code</label>
                <div class="col-md-3">
                    <select class="custom-select form-control form-control-sm" name="regular" style="width: 100%;" name="location[location]" id="chooseLocation">
                        <option selected disabled hidden>Choose Dept</option>
                        <option value="1">DS</option>
                        <option value="2">CT</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <div class="col-md-7">
                    <input type="text" name="regular" class="form-control" name="placeholder" placeholder="Option Code DS****">
                </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Desctription</label>
                    <div class="col-md-10">
                        <textarea class="form-control" rows="5" id="comment"></textarea>                    
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Comments</label>
                    <div class="col-md-10">
                        <textarea class="form-control" rows="5" id="comment"></textarea>                    
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <button class="btn btn-primary pull-right">Next &raquo;</button>                   
                    </div>
                </div>
            </form>
            </div>
        </div>
        </div>
    </div>
    <!-- Row end -->

    <!-- Row start - Service Details  -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <i class="icon-calendar"></i>
                    <h3 class="panel-title">Service Details <strong> MURCR7SOUTHDS0000</strong></h3>
                </div>
                    <ul class="list-group list-group-flush text-justify">
                        <li class="list-group-item">
                            Location : <strong>Mauritius</strong>
                            <span class="glyphicon glyphicon-ok pull-right"></span>
                        </li>
                        <li class="list-group-item">
                            Service Type <strong>Care Rental</strong>
                            <span class="glyphicon glyphicon-ok pull-right"></span>
                        </li>
                        <li class="list-group-item">
                            Supplier <strong>7 South Ltd</strong>
                            <span class="glyphicon glyphicon-ok pull-right"></span>
                        </li>
                        <li class="list-group-item">
                            Option Code <strong>DS0000 - TEST EXCURSION</strong>
                            <span class="glyphicon glyphicon-ok pull-right"></span>
                        </li>
                    </ul>
            </div>
        </div>
    <!-- Row end -->

        <div class="col-lg-12">
            <div class="row">
                <div class="board">
                    <div class="board-inner">
                        <ul class="nav nav-tabs nav-underline" id="myTab">
                            <div class="liner"></div>
                            <!-- First Tab -->
                            <li class="">
                                <a href="#costDetails" data-toggle="tab" title="Cost Details" aria-expanded="true">
                                    <span class="round-tabs one">
                                        <i class="fa fa-money fa-lg"></i><br>Cost Details                                    
                                    </span>
                                </a>
                            </li>

                            <!-- Second Tab -->
                            <li class="">
                                <a href="#quoteDetails" data-toggle="tab" title="Quote Details" aria-expanded="true">
                                    <span class="round-tabs one">
                                        <i class="fa fa-quote-left fa-lg"></i><br>Quote Details                                    
                                    </span>
                                </a>
                            </li>
                            <!-- Third Tab -->
                            <li class="active">
                                <a href="#addNotes" data-toggle="tab" title="Notes" aria-expanded="true">
                                    <span class="round-tabs one">
                                        <i class="fa fa-address-book   fa-lg"></i><br>Notes                                   
                                    </span>
                                </a>
                            </li>

                            <!-- Fourth Tab -->
                            <li class="">
                                <a href="#itinerarySegments" data-toggle="tab" title="Accomodations" aria-expanded="true">
                                    <span class="round-tabs one">
                                        <i class="fa fa-plane fa-lg"></i><br>Itinerary Segments                                   
                                    </span>
                                </a>
                            </li>
                            <!-- Fifth Tab -->
                            <li class="">
                                <a href="#policies" data-toggle="tab" title="Accomodations" aria-expanded="true">
                                    <span class="round-tabs one">
                                        <i class="fa fa-first-order fa-lg"></i><br>Policies                                   
                                    </span>
                                </a>
                            </li>
                            <!-- Six Tab -->
                            <li class="">
                                <a href="#voucherDetails" data-toggle="tab" title="Voucher Details" aria-expanded="true">
                                    <span class="round-tabs one">
                                        <i class="fa fa-gift fa-lg"></i><br>Voucher Details                                   
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Start First Tab -->
                    <div class="tab-content">
                        <div class="tab-pane fade" id="costDetails">
                            <form>
                                <div class="row">
                                    <div class="col-md-12">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h5>Description</h5>
                                                    <input type="text" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    &nbsp;
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h5>Cost charged - Person / Unit</h5>
                                                    <div class="costPerRadio">
                                                        <p>
                                                            <input type="radio" id="person" name="radio-group" checked>
                                                            <label for="person">Person</label>
                                                        </p>
                                                        <p>
                                                            <input type="radio" id="unit" name="radio-group">
                                                            <label for="unit">Unit</label>
                                                        </p>
                                                    </div>                                                  
                                                </div>
                                                <div class="col-md-4">
                                                    <h5>Min Person</h5>
                                                    <select class="custom-select form-control form-control-sm" name="regular" style="width: 100%;" name="location[location]" id="chooseLocation">
                                                        <option selected disabled hidden>Minimum Persons</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                    </select>  
                                                </div>
                                                <div class="col-md-4">
                                                    <h5>Max Person</h5>
                                                    <select class="custom-select form-control form-control-sm" name="regular" style="width: 100%;" name="location[location]" id="chooseLocation">
                                                        <option selected disabled hidden>Maximum Persons</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                    </select>                                                                                               
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Comments</h5>
                                            <textarea class="form-control" rows="5" id="comment"></textarea>                    
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <h5>Invoice Text</h5>
                                            <input type="text" class="form-control">&nbsp;
                                            <input type="text" class="form-control">
                                        </div>  
                                        <div class="col-md-3">
                                            <h5>Duration</h5>
                                            <input type="text" class="form-control" id="duration">
                                        </div>   
                                        <div class="col-md-3">
                                            <h5>Tax Basis</h5>
                                            <p>
                                                <input type="radio" id="inclusive" name="radio-group" checked>
                                                <label for="inclusive">Inclusive</label>
                                            </p>
                                            <p>
                                                <input type="radio" id="exclusive" name="radio-group">
                                                <label for="exclusive">Exclusive</label>
                                            </p>
                                        </div>                                      
                                    </div>
                                </div>
                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-12">
                                            <h5>Service Class</h5>
                                            <div class="input-group"> 
                                                <select class="custom-select form-control form-control-sm" name="regular" style="width: 100%;" name="location[location]" id="chooseLocation">
                                                    <option selected disabled hidden>Service Class</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                </select> 
                                                <span class="input-group-addon">></span>
                                                <input type="text" class="form-control" id="duration" placeholder="Not Applicable">
                                            </div>
                                        </div>  
                                        <div class="col-md-12">
                                            <h5>Locality</h5>
                                            <div class="input-group"> 
                                                <select class="custom-select form-control form-control-sm" name="regular" style="width: 100%;" name="location[location]" id="chooseLocation">
                                                    <option selected disabled hidden>Locality</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                </select> 
                                                <span class="input-group-addon">></span>
                                                <input type="text" class="form-control" id="duration">
                                            </div>
                                        </div>              
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-12">
                                            <h5>Department</h5>
                                            <div class="input-group"> 
                                                <select class="custom-select form-control form-control-sm" name="regular" style="width: 100%;" name="location[location]" id="chooseLocation">
                                                    <option selected disabled hidden>Add Department</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                </select> 
                                                <span class="input-group-addon">></span>
                                                <input type="text" class="form-control" id="duration">
                                            </div>
                                        </div>  
                                        <div class="col-md-12">
                                            <h5>AE</h5>
                                            <div class="input-group"> 
                                                <select class="custom-select form-control form-control-sm" name="regular" style="width: 100%;" name="location[location]" id="chooseLocation">
                                                    <option selected disabled hidden>Add AE</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                </select> 
                                                <span class="input-group-addon">></span>
                                                <input type="text" class="form-control" id="duration">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <h5>EX</h5>
                                            <div class="input-group"> 
                                                <select class="custom-select form-control form-control-sm" name="regular" style="width: 100%;" name="location[location]" id="chooseLocation">
                                                    <option selected disabled hidden>Add EX</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                </select> 
                                                <span class="input-group-addon">></span>
                                                <input type="text" class="form-control" id="duration">
                                            </div>
                                        </div>               
                                    </div>
                                </div>

                                <hr>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-6">
                                                <li class="checkBoxMain">
                                                    <label class='with-square-checkbox'>
                                                        <input type='checkbox' id="flagDeleted"/>
                                                        <span>Flag Service As Deleted</span>
                                                    </label>
                                                </li>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Alert Modal when Flag -->
                                    <div class="modal fade" id="alertModal" role="dialog">
                                        <div class="modal-dialog">
                                        
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">
                                                <i class="fa fa-info" style="color: #337ab7;"></i> 
                                                Flag Service As Deleted</h4>
                                            </div>
                                            <div class="modal-body">
                                            <p id="error">
                                                Are you sure you want to delete ?</p>
                                            </div>
                                            <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Yes</button>
                                            </div>
                                        </div>
                                        
                                        </div>
                                    </div>
                                <!-- End Alert Modal when Flag -->

                                <div class="row">
                                    <div class="col-md-12">
                                        &nbsp;
                                    </div>
                                </div>
                            </form>
                            <div class="alert alert-danger">
                                Please complete required fields marked in red.
                            </div>
                        </div>
                        <!-- End First Tab -->

                        <!-- Start Second Tab -->
                        <div class="tab-pane fade" id="quoteDetails">
                            <form>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-12">
                                            <h2>Extra Service</h2>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12" id="addRowBody">
                                        <div class="col-md-12">
                                            <div class="input-group">
                                                <input type="text" class="form-control" placeholder="Name">
                                                <span class="input-group-addon">></span>
                                                <input type="text" class="form-control" placeholder="Extra Description">
                                                <span class="input-group-addon">></span>
                                                <input type="number" max="20" min="1" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                &nbsp;
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-12">
                                            <div class="pull-right">
                                                <button id="add-row" type="button" class="btn btn-primary submit">
                                                    <span class="glyphicon glyphicon-plus"></span> Add Field
                                                </button>
                                            </div>    
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-12">
                                            <h5>Pay Breaks</h5>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" max="9999" min="1" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" max="9999" min="1" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" max="9999" min="1" class="form-control">
                                        </div>
                                    </div>
                                </div>   
                                &nbsp;  
                                <!-- Copy Paste -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <input type="number" max="9999" min="1" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" max="9999" min="1" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" max="9999" min="1" class="form-control">
                                        </div>
                                    </div>
                                </div> 
                                &nbsp;  
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <input type="number" max="9999" min="1" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" max="9999" min="1" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" max="9999" min="1" class="form-control">
                                        </div>
                                    </div>
                                </div>   
                                &nbsp;
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <li class="checkBoxMain">
                                                <label class='with-square-checkbox'>
                                                    <input type='checkbox'/>
                                                    <span>Include <strong>Chldren</strong> in Pax Break Count</span>
                                                </label>
                                            </li>
                                        </div>
                                        <div class="col-md-4">
                                            <li class="checkBoxMain">
                                                <label class='with-square-checkbox'>
                                                    <input type='checkbox'/>
                                                    <span>Include <strong>Infant</strong> in Pax Break Count</span>
                                                </label>
                                            </li>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- End Second Tab -->

                        
                        <!-- Start Third Tab -->
                        <div class="tab-pane active in fade" id="addNotes">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <h3>Notes</h3>
                                        <div class="col-lg-12 nopadding">
                                            <textarea id="txtEditor"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Start Third Tab -->

                        <!-- Start Six Tab -->
                        <div class="tab-pane fade" id="voucherDetails">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <h3>Contact Details</h3>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Address</h5>
                                        <input type="text" class="form-control" placeholder="Address 1">
                                        <input type="text" class="form-control" placeholder="Country">
                                        <input type="text" class="form-control" placeholder="State">
                                        <h5>Post Code</h5>
                                        <input type="text" class="form-control" placeholder="Post Code">
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Voucher creation</h5>
                                        <!-- Filter Two Locations -->
                                        <fieldset class="form-group">
                                            <div class="filterBlock resize2 form-control">
                                                <p>
                                                    <input type="radio" id="oneVoucher" name="radioCreationVoucher" checked>
                                                    <label for="oneVoucher"><span class="label label-pill label-default">One Voucher</span></label>
                                                </p>
                                                <p>
                                                    <input type="radio" id="voucherEachPerson" name="radioCreationVoucher">
                                                    <label for="voucherEachPerson"><span class="label label-pill label-default">Vouchers for each Person</span></label>
                                                </p>
                                                <p>
                                                    <input type="radio" id="voucherEachDay" name="radioCreationVoucher">
                                                    <label for="voucherEachDay"><span class="label label-pill label-default">Vouchers for each Day</span></label>
                                                </p>
                                                <p>
                                                    <input type="radio" id="voucherEachPersonDay" name="radioCreationVoucher">
                                                    <label for="voucherEachPersonDay"><span class="label label-pill label-default">Vouchers for each Person per Day</span></label>
                                                </p>
                                            </div>
                                        </fieldset>
                                    <!-- Filter One Locations -->
                                    </div>

                                    <div class="col-md-3">
                                        <h5>Print Voucher</h5>
                                        <!-- Filter Two Locations -->
                                        <fieldset class="form-group">
                                            <div class="filterBlock resize2 form-control">
                                                <p>
                                                    <input type="radio" id="printVoucher" name="radioPrintVoucher" checked>
                                                    <label for="printVoucher"><span class="label label-pill label-default">Print Voucher</span></label>
                                                </p>
                                                <p>
                                                    <input type="radio" id="noCost" name="radioPrintVoucher">
                                                    <label for="noCost"><span class="label label-pill label-default">No Cost</span></label>
                                                </p>
                                                <p>
                                                    <input type="radio" id="recordLiability" name="radioPrintVoucher">
                                                    <label for="recordLiability"><span class="label label-pill label-default">Record liability only</span></label>
                                                </p>
                                            </div>
                                        </fieldset>
                                    <!-- Filter One Locations -->
                                    </div>


                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-11"><h3>Voucher Text</h3></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="line1" class="col-sm-1 control-label">Line 1</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="line1" placeholder="Voucher Text">
                                            </div>
                                            <div class="col-md-1">
                                                <li class="checkBoxMain">
                                                    <label class='with-square-checkbox'>
                                                        <input type='checkbox'/>
                                                        <span></span>
                                                    </label>
                                                </li>
                                            </div>
                                        </div>
                                        &nbsp;
                                        <div class="form-group">
                                            <label for="line1" class="col-sm-1 control-label">Line 2</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="line1" placeholder="Voucher Text">
                                            </div>
                                            
                                            <div class="col-md-1">
                                                <li class="checkBoxMain">
                                                    <label class='with-square-checkbox'>
                                                        <input type='checkbox'/>
                                                        <span></span>
                                                    </label>
                                                </li>
                                            </div>
                                        </div>
                                        &nbsp;
                                        <div class="form-group">
                                            <label for="line1" class="col-sm-1 control-label">Line 3</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="line1" placeholder="Voucher Text">
                                            </div>
                                            
                                            <div class="col-md-1">
                                                <li class="checkBoxMain">
                                                    <label class='with-square-checkbox'>
                                                        <input type='checkbox'/>
                                                        <span></span>
                                                    </label>
                                                </li>
                                            </div>
                                        </div>
                                        &nbsp;
                                        <div class="form-group">
                                            <label for="line1" class="col-sm-1 control-label">Line 4</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="line1" placeholder="Voucher Text">
                                            </div>
                                            
                                            <div class="col-md-1">
                                                <li class="checkBoxMain">
                                                    <label class='with-square-checkbox'>
                                                        <input type='checkbox'/>
                                                        <span></span>
                                                    </label>
                                                </li>
                                            </div>
                                        </div>
                                        &nbsp;
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Start Six Tab -->
                    </div>
                </div>
            </div>
        </div>

        <!-- End Tab HERE !!! -->
    </div>
</div>