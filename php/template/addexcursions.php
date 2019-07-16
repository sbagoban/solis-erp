<div class="container">
    <div class="col-lg-12">
    <!-- Row start -->
    <div class="row">
        <div class="col-md-12 col-sm-6 col-xs-12">
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
        <div class="col-md-12 col-sm-6 col-xs-12">
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
                            <li class="active">
                                <a href="#costDetails" data-toggle="tab" title="Accomodations" aria-expanded="true">
                                    <span class="round-tabs one">
                                        <i class="fa fa-money fa-lg"></i><br>Cost Details                                    
                                    </span>
                                </a>
                            </li>

                            <!-- Second Tab -->
                            <li class="">
                                <a href="#quoteDetails" data-toggle="tab" title="Accomodations" aria-expanded="true">
                                    <span class="round-tabs one">
                                        <i class="fa fa-quote-left fa-lg"></i><br>Quote Details                                    
                                    </span>
                                </a>
                            </li>
                            <!-- Third Tab -->
                            <li class="">
                                <a href="#addNotes" data-toggle="tab" title="Accomodations" aria-expanded="true">
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
                                <a href="#voucherDetails" data-toggle="tab" title="Accomodations" aria-expanded="true">
                                    <span class="round-tabs one">
                                        <i class="fa fa-gift fa-lg"></i><br>Voucher Details                                   
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Start First Tab -->
                    <div class="tab-content">
                        <div class="tab-pane active in fade" id="costDetails">
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
                                                <div class="col-md-6">
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
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Comments</h5>
                                            <textarea class="form-control" rows="5" id="comment"></textarea>                    
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-12">
                                        &nbsp;
                                    </div>
                                </div>
                            </form>
                            <div class="alert alert-danger">
                                Please complete required fields marked in red.
                            </div>

                            <!-- Search Button -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <div class="pull-right">
                                            <!-- <button type="button" class="btn btn-default reset">Reset</button> -->
                                            <button type="button" class="btn btn-primary submit">
                                                <span class="glyphicon glyphicon-search"></span> Search
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End First Tab -->

                    </div>
                </div>
            </div>
        </div>

        <!-- End Tab HERE !!! -->
    </div>
</div>