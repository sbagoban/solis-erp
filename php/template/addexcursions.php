<div class="container">
    <div class="col-lg-12">
        <!-- Row Search Start -->
        <!-- <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">

                    <div class="panel-heading clearfix">
                        <i class="icon-calendar"></i>
                        <h3 class="panel-title">Search Service</h3>
                    </div>
                    <div class="panel-body">
                        <form class="row-border" action="#" onsubmit="return false">
                                <div class=" selectValidation has-success">
                                    <div class="col-md-4">
                                        <select class="custom-select form-control form-control-sm inputValidation" style="width: 100%;" name="location[location]" id="ddlChooseLocation">
                                            <option value="selectedCountry" selected disabled hidden>Choose Location</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="selectValidation has-success">
                                    <div class="col-md-4">
                                        <select class="custom-select form-control form-control-sm inputValidation" style="width: 100%;" name="serviceType[serviceType]" id="ddlSelectServiceType">
                                        </select>
                                    </div>
                                </div>

                                <div class="selectValidation has-success">
                                    <div class="col-md-4">
                                        <select class="custom-select form-control form-control-sm inputValidation" style="width: 100%;" name="supplier[supplier]" id="ddlChooseSupplier">
                                            <option selected disabled hidden>Select Option</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button id="searchService"  class="btn btn-success pull-right searchServices">
                                        <span class="glyphicon glyphicon-search"></span> Search Services
                                    </button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- Row Search End -->

        <!-- Row start -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading clearfix">
                        <i class="icon-calendar"></i>
                        <h3 class="panel-title">Add New Service</h3>
                    </div>

                    <div class="panel-body">
                        <form class="form-horizontal row-border" action="#" onsubmit="return false">
                            <div class="form-group selectValidation" id="chooseLocation">
                                <label class="col-md-2 control-label">Location</label>
                                <div class="col-md-10">
                                    <select class="custom-select form-control form-control-sm inputValidation" data-toggle="tooltip" title="Compulsory field" style="width: 100%;" name="location[location]" id="ddlChooseLocation">
                                        <option value="selectedCountry" selected disabled hidden>Choose Location</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group selectValidation" id="serviceType">
                                <label class="col-md-2 control-label">Service Type</label>
                                <div class="col-md-10">
                                    <select class="custom-select form-control form-control-sm inputValidation" data-toggle="tooltip" title="Compulsory field" style="width: 100%;" name="serviceType[serviceType]" id="ddlSelectServiceType">
                                    </select>
                                </div>
                            </div>

                            <div class="form-group selectValidation" id="supplier">
                                <label class="col-md-2 control-label">Supplier Name</label>
                                <div class="col-md-10">
                                    <select class="custom-select form-control form-control-sm inputValidation" data-toggle="tooltip" title="Compulsory field" style="width: 100%;" name="supplier[supplier]" id="ddlChooseSupplier">
                                        <option selected disabled hidden>Select Option</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group selectValidation" id="optionCode">
                                <label class="col-md-2 control-label">Option Code</label>
                                <div class="col-md-3">
                                    <select class="custom-select form-control form-control-sm inputValidation opt1" name="regular" data-toggle="tooltip" title="Compulsory field" style="width: 100%;" name="optionCode[optionCode]" id="ddlOptionCode">
                                        <option value="00" selected disabled hidden>Select Option</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <h3><code id="output">0000</code></h3>
                                </div>

                                <div class="col-xs-1">
                                    <a id="generateNone" style="display: block;" data-toggle="tooltip" title="Click to Generate Option Code"><i class="fa fa-refresh fa-lg" aria-hidden="true"></i></a>
                                    <a id="generate" style="display: none;" data-toggle="tooltip" title="Click to Generate Option Code"><i class="fa fa-refresh fa-spin-hover fa-lg" aria-hidden="true"></i></a>
                                </div>
                            </div>
                            <div class="form-group has-warning">
                                <label class="col-md-2 control-label">Desctription</label>
                                <div class="col-md-10">
                                    <textarea class="form-control textAreaDesc" rows="5" id="addedDescription"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Comments</label>
                                <div class="col-md-10">
                                    <textarea class="form-control" rows="5" id="addedComment"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <button id="createNewService" class="btn btn-primary pull-right">Create New Service &raquo;</button>
                                    <button id="searchService" class="btn btn-success pull-right searchServices">
                                        <span class="glyphicon glyphicon-search"></span> Search Services
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="toast jam toast_added" aria-hidden="true" style="display:none;">
            <span class="close" aria-role="button" tabindex="0">&times;</span> Service Added.
        </div>
        <!-- Row end -->

        <!-- Row Search Service Details -->
        <!-- <div class="row" id="searchServiceDetails">
                <div class="col-md-12">
                    <div class="searchFilter">
                        <input type="search" id="search" class="form-control" placeholder="&#xf0e0;  Search Service Details ..." data-toggle="tooltip" title="Search Service Details ...">
                    </div>
                    <div id="root"></div>
                    <div class="pages"></div>
                </div>
            </div> -->
        <!-- <div class="row" id="searchServiceDetails"> -->

        <div class="row" id="searchServiceDetails">
            <div class="col=md-12">
                <div class="col=md-12">
                    <table class="table responsive" id="sort">
                        <thead>
                            <tr>
                                <th scope="col">Location</th>
                                <th scope="col">Service Type</th>
                                <th scope="col">Supplier Name</th>
                                <th scope="col">Option Code</th>
                                <th scope="col">Description</th>
                                <th scope="col">Comments</th>
                                <th scope="col">Edit</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        &nbsp; &nbsp;
        <!-- Row Search Service Details -->
        <!-- Row start - Service Details  -->
        <div class="row" id="serviceDetails" style="display: block;">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading clearfix">
                        <i class="icon-calendar"></i>
                        <h3 class="panel-title">Service Details <strong> Option Code : <div id="OptionCodeDisplay"></div></strong></h3>
                    </div>
                    <ul class="list-group list-group-flush text-justify">
                        <li class="list-group-item">
                            Description : <strong><div id="descriptionDisplay"></div></strong>
                        </li>
                        <li class="list-group-item">
                            Comments : <strong><div id="commentsDisplay"></div></strong>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-12" id="editServiceBlock" style="display: block;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="board">
                            <div class="board-inner">
                                <ul class="nav nav-tabs nav-underline" id="myTab">
                                    <div class="liner"></div>
                                    <!-- First Tab -->
                                    <li class="active">
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
                                    <li class="">
                                        <a href="#addNotes" data-toggle="tab" title="Notes" aria-expanded="true">
                                            <span class="round-tabs one">
                                        <i class="fa fa-address-book   fa-lg"></i><br>Notes                                   
                                    </span>
                                        </a>
                                    </li>

                                    <!-- Fourth Tab -->
                                    <!-- <li class="">
                                <a href="#itinerarySegments" data-toggle="tab" title="Itinerary Segments" aria-expanded="true">
                                    <span class="round-tabs one">
                                        <i class="fa fa-plane fa-lg"></i><br>Itinerary Segments                                   
                                    </span>
                                </a>
                            </li> -->
                                    <!-- Fifth Tab -->
                                    <li class="">
                                        <a href="#policies" data-toggle="tab" title="Policies" aria-expanded="true">
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
                                <div class="tab-pane active in fade" id="costDetails">
                                    <form action="#" onsubmit="return false">
                                        <div class="row">
                                            <div class="col-md-12">
                                                &nbsp;
                                            </div>
                                        </div>

                                        <h3 class="panel-title" style="display: none;"><strong>ID :</strong><div id="idBlock"></div></h3>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-6 has-success">
                                                    <h5>Locality</h5>
                                                    <div class="form-group selectValidation">
                                                        <select class="custom-select form-control form-control-sm" data-toggle="tooltip" title="Compulsory field" style="width: 100%;" name="location[location]" id="ddlChooseLocality">
                                                            <option selected disabled hidden>Select an option</option>
                                                            <option value="North">North</option>
                                                            <option value="East">East</option>
                                                            <option value="South">South</option>
                                                            <option value="West">West</option>
                                                            <option value="South East">South East</option>
                                                            <option value="South West">South West</option>
                                                            <option value="North East">North East</option>
                                                            <option value="North West">North West</option>
                                                            <option value="Centre">Centre</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 has-warning">
                                                    <h5>Department</h5>
                                                    <div class="form-group selectValidation">
                                                        <select class="custom-select form-control form-control-sm inputValidationCostDetails" data-toggle="tooltip" title="Compulsory field" style="width: 100%;" name="dept[dept]" id="ddlChooseDept">
                                                            <option selected disabled hidden>Select an option</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-12 has-success">
                                                            <h5>Description</h5>
                                                            <input type="text" class="form-control" id="textFieldDescriptionCostDetails">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            &nbsp;
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <h5>Cost charged - Adults / Unit</h5>
                                                            <div class="costPerRadio">
                                                                <p>
                                                                    <input type="radio" id="adults" value="adults" name="radioGroupAdult" checked>
                                                                    <label for="adults">Adults</label>
                                                                </p>
                                                                <p>
                                                                    <input type="radio" id="unit" value="unit" name="radioGroupAdult">
                                                                    <label for="unit">Unit</label>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <h5>Min Adults</h5>
                                                            <input type="number" min='1' max='100' class="form-control" id="minAdults">
                                                            <!-- <select class="custom-select form-control form-control-sm" name="regular" style="width: 100%;" name="location[location]" id="chooseLocation">
                                                        <option selected disabled hidden>Minimum Persons</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                    </select>   -->
                                                        </div>
                                                        <div class="col-md-4">
                                                            <h5>Max Adults</h5>
                                                            <input type="number" min='1' max='9999' class="form-control" id="maxAdults">
                                                            <!-- <select class="custom-select form-control form-control-sm" name="regular" style="width: 100%;" name="location[location]" id="chooseLocation">
                                                        <option selected disabled hidden>Maximum Persons</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                    </select>                                                                                                -->
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <h5>Cost charged - Children / Unit</h5>
                                                            <div class="costPerRadio">
                                                                <p>
                                                                    <input type="radio" id="children" value="children" name="radioCostChildren" checked>
                                                                    <label for="children">Children</label>
                                                                </p>
                                                                <p>
                                                                    <input type="radio" id="unitChildren" value="unit" name="radioCostChildren">
                                                                    <label for="unitChildren">Unit</label>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <h5>Min Children</h5>
                                                            <input type="number" min='1' max='100' class="form-control" id="minChildren">
                                                            <!-- <select class="custom-select form-control form-control-sm" name="regular" style="width: 100%;" name="location[location]" id="chooseLocation">
                                                        <option selected disabled hidden>Minimum Persons</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                    </select>   -->
                                                        </div>
                                                        <div class="col-md-4">
                                                            <h5>Max Children</h5>
                                                            <input type="number" min='1' max='9999' class="form-control" id="maxChildren">
                                                            <!-- <select class="custom-select form-control form-control-sm" name="regular" style="width: 100%;" name="location[location]" id="chooseLocation">
                                                        <option selected disabled hidden>Maximum Persons</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                    </select>                                                                                                -->
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="col-md-6 has-success">
                                                    <h5>Comments</h5>
                                                    <textarea class="form-control" rows="5" id="textFieldCommentsCostDetails" class="hhh"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-3">
                                                    <h5>Invoice Text</h5>
                                                    <input type="text" class="form-control" id="textFieldInvoiceCostDetails" disabled>&nbsp;
                                                    <input type="text" class="form-control" id="descriptionInvoiceCostDetails" placeholder="Invoice Text Description">
                                                </div>
                                                <div class="col-md-3">
                                                    <h5>Duration</h5>
                                                    <input type="text" class="form-control" id="duration">
                                                </div>
                                                <div class="col-md-3">
                                                    <h5>Tax Basis</h5>
                                                    <p>
                                                        <input type="radio" id="inclusive" value="inclusive" name="radioGroupTax" checked>
                                                        <label for="inclusive">Inclusive</label>
                                                    </p>
                                                    <p>
                                                        <input type="radio" id="exclusive" value="exclusive" name="radioGroupTax">
                                                        <label for="exclusive">Exclusive</label>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-6">
                                                    <li class="checkBoxMain">
                                                        <label class='with-square-checkbox'>
                                                            <input type='checkbox' id="flagDeleted" />
                                                            <span>Flag Service As Deleted</span>
                                                        </label>
                                                    </li>
                                                </div>
                                                <div class="col-md-6">
                                                    <button id="updateService" class="btn btn-primary pull-right">Update Cost Details &raquo;</button>
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
                                                        <p id="error">Are you sure you want to delete ?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                        <button type="button" class="btn btn-success" data-dismiss="modal">Yes</button>
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
                                            <div class="col-md-12">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Extra Name</th>
                                                            <th>Extra Description</th>
                                                            <th>Charge Per</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr id="addRow">
                                                            <td class="col-xs-3">
                                                                <input class="form-control addMain" type="text" placeholder="Enter Name" />
                                                            </td>

                                                            <td class="col-xs-3">
                                                                <input class="form-control addPrefer" type="text" placeholder="Enter Descrition" />
                                                            </td>
                                                            <td class="col-xs-5">
                                                                <div class="resize4 form-control">
                                                                    <div class="policiesGroup">
                                                                        <p>
                                                                            <input type="radio" id="chargePerPerson" name="radioChargePer" checked>
                                                                            <label for="chargePerPerson">Person</label>
                                                                        </p>
                                                                        <p>
                                                                            <input type="radio" id="chargePerUnit" name="radioChargePer">
                                                                            <label for="chargePerUnit">Unit</label>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="col-xs-1 text-center">
                                                                <span class="addBtn" id="btnCounter">
                                                    <i class="fa fa-plus fa-lg" data-toggle="tooltip" title="Add Extra Field"></i>
                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="col-xs-3">
                                                                <input type="text" value="Enter Name" class="form-control editable" />
                                                            </td>

                                                            <td class="col-xs-3">
                                                                <input type="text" value="Enter Descrition" class="form-control editable" />
                                                            </td>
                                                            <td class="col-xs-5">
                                                                <div class="resize4 form-control">
                                                                    <div class="policiesGroup">
                                                                        <p>
                                                                            <input type="radio" id="chargePerPerson0" name="radioChargePer0" checked>
                                                                            <label for="chargePerPerson0">Person</label>
                                                                        </p>
                                                                        <p>
                                                                            <input type="radio" id="chargePerUnit0" name="radioChargePer0">
                                                                            <label for="chargePerUnit0">Unit</label>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="col-xs-1 text-center"><a class="deleteBtn" data-toggle="tooltip" title="Delete Row"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></a></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
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
                                                            <input type='checkbox' />
                                                            <span>Include <strong>Children</strong> in Pax Break Count</span>
                                                        </label>
                                                    </li>
                                                </div>
                                                <div class="col-md-4">
                                                    <li class="checkBoxMain">
                                                        <label class='with-square-checkbox'>
                                                            <input type='checkbox' />
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
                                <div class="tab-pane fade" id="addNotes">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-12">
                                                <h3>Notes</h3>
                                                <div class="col-lg-12 nopadding">
                                                    <textarea id="txtEditor"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                &nbsp;
                                            </div>
                                            <div class="col-md-12">
                                                <button id="updateNotes" class="btn btn-primary pull-right">Update Notes &raquo;</button>
                                            </div>
                                            <div class="col-md-12">
                                                &nbsp;
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Start Third Tab -->

                                <!-- Start Fifth Tab -->
                                <div class="tab-pane fade" id="policies">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-12">
                                                <h2>Policies</h2>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h5>Setting Apply To</h5>
                                                        <div class="resize3 form-control">
                                                            <div class="policiesGroup">
                                                                <p>
                                                                    <input type="radio" id="supplierRadio" name="radioSettingApply" value="supplier" checked>
                                                                    <label for="supplierRadio">Supplier</label>
                                                                </p>
                                                                <p>
                                                                    <input type="radio" id="produtRadio" name="radioSettingApply" value="produt">
                                                                    <label for="produtRadio">Product</label>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h5>General</h5>
                                                        <li class="checkBoxMain">
                                                            <label class='with-square-checkbox'>
                                                                <input type='checkbox' id="pickOffDropOff">
                                                                <span>Pick Up / DropOff Promp</span>
                                                            </label>
                                                        </li>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h5>Cross Seasons Rates</h5>

                                                        <div class="resize3 form-control">
                                                            <div class="policiesGroup">
                                                                <p>
                                                                    <input type="radio" id="useFirstRate" name="radioCrossSeasonRates" value="use first rate" checked>
                                                                    <label for="useFirstRate">Use First Rate</label>
                                                                </p>
                                                                <p>
                                                                    <input type="radio" id="splitService" name="radioCrossSeasonRates" value="split service">
                                                                    <label for="splitService">Split Service</label>
                                                                </p>
                                                                <p>
                                                                    <input type="radio" id="useAverageRate" name="radioCrossSeasonRates" value="use average rate">
                                                                    <label for="useAverageRate">Use Average Rate</label>
                                                                </p>
                                                                <p>
                                                                    <input type="radio" id="notAllowed" name="radioCrossSeasonRates" value="not allowed">
                                                                    <label for="notAllowed">Not Allowed</label>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h5>Age Policy</h5>
                                                <h5>Infant</h5>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="infantMin">
                                                    <span class="input-group-addon">></span>
                                                    <input type="number" class="form-control" id="infantMax">
                                                </div>
                                                <h5>Child</h5>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="childMin">
                                                    <span class="input-group-addon">></span>
                                                    <input type="number" class="form-control" id="childMax">
                                                </div>
                                                <h5>Teen</h5>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="teenMin">
                                                    <span class="input-group-addon">></span>
                                                    <input type="number" class="form-control" id="teenMax">
                                                </div>
                                                <h5>Adult</h5>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="adultMin">
                                                    <span class="input-group-addon">></span>
                                                    <input type="number" class="form-control" id="adultMax">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <h5>Stay Must Start On</h5>
                                                <div class="blockStay form-control">
                                                    <li class="checkBoxMain">
                                                        <label class='with-square-checkbox'>
                                                            <input type='checkbox' id="chkMonday">
                                                            <span>Monday</span>
                                                        </label>
                                                    </li>
                                                    <li class="checkBoxMain">
                                                        <label class='with-square-checkbox'>
                                                            <input type='checkbox' id="chkTuesday">
                                                            <span>Tuesday</span>
                                                        </label>
                                                    </li>
                                                    <li class="checkBoxMain">
                                                        <label class='with-square-checkbox'>
                                                            <input type='checkbox' id="chkWednesday">
                                                            <span>Wednesday</span>
                                                        </label>
                                                    </li>
                                                    <li class="checkBoxMain">
                                                        <label class='with-square-checkbox'>
                                                            <input type='checkbox' id="chkThursday">
                                                            <span>Thursday</span>
                                                        </label>
                                                    </li>
                                                    <li class="checkBoxMain">
                                                        <label class='with-square-checkbox'>
                                                            <input type='checkbox' id="chkFriday">
                                                            <span>Friday</span>
                                                        </label>
                                                    </li>
                                                    <li class="checkBoxMain">
                                                        <label class='with-square-checkbox'>
                                                            <input type='checkbox' id="chkSaturday">
                                                            <span>Saturday</span>
                                                        </label>
                                                    </li>
                                                    <li class="checkBoxMain">
                                                        <label class='with-square-checkbox'>
                                                            <input type='checkbox' id="chkSunday">
                                                            <span>Sunday</span>
                                                        </label>
                                                    </li>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <h5>Stay Must Include</h5>
                                                <div class="blockStay form-control">
                                                    <li class="checkBoxMain">
                                                        <label class='with-square-checkbox'>
                                                            <input type='checkbox' id="chkMondayInclude">
                                                            <span>Monday</span>
                                                        </label>
                                                    </li>
                                                    <li class="checkBoxMain">
                                                        <label class='with-square-checkbox'>
                                                            <input type='checkbox' id="chkTuesdayInclude">
                                                            <span>Tuesday</span>
                                                        </label>
                                                    </li>
                                                    <li class="checkBoxMain">
                                                        <label class='with-square-checkbox'>
                                                            <input type='checkbox' id="chkWednesdayInclude">
                                                            <span>Wednesday</span>
                                                        </label>
                                                    </li>
                                                    <li class="checkBoxMain">
                                                        <label class='with-square-checkbox'>
                                                            <input type='checkbox' id="chkThursdayInclude">
                                                            <span>Thursday</span>
                                                        </label>
                                                    </li>
                                                    <li class="checkBoxMain">
                                                        <label class='with-square-checkbox'>
                                                            <input type='checkbox' id="chkFridayInclude">
                                                            <span>Friday</span>
                                                        </label>
                                                    </li>
                                                    <li class="checkBoxMain">
                                                        <label class='with-square-checkbox'>
                                                            <input type='checkbox' id="chkSaturdayInclude">
                                                            <span>Saturday</span>
                                                        </label>
                                                    </li>
                                                    <li class="checkBoxMain">
                                                        <label class='with-square-checkbox'>
                                                            <input type='checkbox' id="chkSundayInclude">
                                                            <span>Sunday</span>
                                                        </label>
                                                    </li>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                &nbsp;
                                            </div>
                                            <div class="col-md-12">
                                                <button id="updatePolicies" class="btn btn-primary pull-right">Update Policies &raquo;</button>
                                            </div>
                                            <div class="col-md-12">
                                                &nbsp;
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                                <!-- End FIfth Tab -->
                                <!-- Start Six Tab -->
                                <div class="tab-pane fade" id="voucherDetails">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-12">
                                                <h3>Contact Details</h3>
                                            </div>
                                            <div class="col-md-6">
                                                <h5>Address</h5>
                                                <input type="text" id="addressVoucherDetails" class="form-control" placeholder="Address 1">
                                                <input type="text" id="countryVoucherDetails" class="form-control" placeholder="Country">
                                                <input type="text" id="stateVoucherDetails" class="form-control" placeholder="State">
                                                <h5>Post Code</h5>
                                                <input type="text" id="postCodeVoucherDetails" class="form-control" placeholder="Post Code">
                                            </div>
                                            <div class="col-md-3">
                                                <h5>Voucher creation</h5>
                                                <!-- Filter Two Locations -->
                                                <fieldset class="form-group">
                                                    <div class="filterBlock resize2 form-control">
                                                        <p>
                                                            <input type="radio" id="oneVoucher" value="One Voucher" name="radioCreationVoucher" checked>
                                                            <label for="oneVoucher"><span class="label label-pill label-default">One Voucher</span></label>
                                                        </p>
                                                        <p>
                                                            <input type="radio" id="voucherEachPerson" value="Voucher For Each Person" name="radioCreationVoucher">
                                                            <label for="voucherEachPerson"><span class="label label-pill label-default">Vouchers for each Person</span></label>
                                                        </p>
                                                        <p>
                                                            <input type="radio" id="voucherEachDay" value="Vouchers for each Day" name="radioCreationVoucher">
                                                            <label for="voucherEachDay"><span class="label label-pill label-default">Vouchers for each Day</span></label>
                                                        </p>
                                                        <p>
                                                            <input type="radio" id="voucherEachPersonDay" value="Vouchers for each Person per Day" name="radioCreationVoucher">
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
                                                            <input type="radio" id="printVoucher" name="radioPrintVoucher" value="Print Voucher" checked>
                                                            <label for="printVoucher"><span class="label label-pill label-default">Print Voucher</span></label>
                                                        </p>
                                                        <p>
                                                            <input type="radio" id="noCost" name="radioPrintVoucher" value="No Cost">
                                                            <label for="noCost"><span class="label label-pill label-default">No Cost</span></label>
                                                        </p>
                                                        <p>
                                                            <input type="radio" id="recordLiability" name="radioPrintVoucher" value="Record liability only">
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
                                                    <div class="col-md-11">
                                                        <h3>Voucher Text</h3></div>
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
                                                                <input type='checkbox' />
                                                                <span></span>
                                                            </label>
                                                        </li>
                                                    </div>
                                                </div>
                                                &nbsp;
                                                <div class="form-group">
                                                    <label for="line1" class="col-sm-1 control-label">Line 2</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="line2" placeholder="Voucher Text">
                                                    </div>

                                                    <div class="col-md-1">
                                                        <li class="checkBoxMain">
                                                            <label class='with-square-checkbox'>
                                                                <input type='checkbox' />
                                                                <span></span>
                                                            </label>
                                                        </li>
                                                    </div>
                                                </div>
                                                &nbsp;
                                                <div class="form-group">
                                                    <label for="line1" class="col-sm-1 control-label">Line 3</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="line3" placeholder="Voucher Text">
                                                    </div>

                                                    <div class="col-md-1">
                                                        <li class="checkBoxMain">
                                                            <label class='with-square-checkbox'>
                                                                <input type='checkbox' />
                                                                <span></span>
                                                            </label>
                                                        </li>
                                                    </div>
                                                </div>
                                                &nbsp;
                                                <div class="form-group">
                                                    <label for="line1" class="col-sm-1 control-label">Line 4</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="line4" placeholder="Voucher Text">
                                                    </div>

                                                    <div class="col-md-1">
                                                        <li class="checkBoxMain">
                                                            <label class='with-square-checkbox'>
                                                                <input type='checkbox' />
                                                                <span></span>
                                                            </label>
                                                        </li>
                                                    </div>
                                                </div>
                                                &nbsp;
                                            </div>
                                            <div class="col-md-12">
                                                &nbsp;
                                            </div>
                                            <div class="col-md-12">
                                                <button id="updateVoucherDetails" class="btn btn-primary pull-right">Update Voucher Details &raquo;</button>
                                            </div>
                                            <div class="col-md-12">
                                                &nbsp;
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Start Six Tab -->
                                <div class="toast jam toast_updated" aria-hidden="true" style="display:none;">
                                    <span class="close" aria-role="button" tabindex="0">&times;</span> Service Updated.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- End Tab HERE !!! -->
        </div>
    </div>