<!-- Start Container -->
<div class="container-fluid"> 
    <!-- First Row -->
    <div class="row">
        <!-- Display All service - Cost details -->
        <div class="row" id="searchProductServiceRate">
            <div class="col-md-12">
                <div class="col-md-12">
                    <table class="table responsive" id="productServiceRateSort">
                        <thead>
                            <tr>
                                <th scope="col">Product Service</th>
                                <th scope="col">Supplier</th>
                                <th scope="col">Coast</th>
                                <th scope="col">Package</th>
                                <th scope="col">Date From</th>
                                <th scope="col">Date To</th>
                                <th scope="col">Add</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <!-- Display All service - Cost details -->
    </div>
    <!-- First Row End -->

    <!-- Second Row -->
    <div class="row">
        <div class="col-md-12">
            <!-- Form Product Service Claim -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading clearfix">
                            <i class="icon-calendar"></i>
                            <h3 class="panel-title">Product Services Claim</h3>
                        </div>

                        <div class="panel-body">
                            <form class="form row-border" action="#" onsubmit="return false">

                                <!-- Row 1 -->
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <label class="col-md-2 control-label">Product Services</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="col-md-2 control-label">Supplier</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <!-- Row 1 -->

                                <!-- Row 2 -->
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <label class="col-md-2 control-label">Coast</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="col-md-2 control-label">Package</label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <!-- Row 2 -->

                                <!-- Row 3 -->
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <label class="col-md-2 control-label">Date From</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="col-md-2 control-label">Date To</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <!-- Row 3 -->

                                <!-- Row 4 -->
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <label class="col-md-2 control-label">Charges</label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control">
                                        </div>
                                        <div class="col-md-4" id="perUnit">
                                            <input type="number" class="form-control" >
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <label class="col-md-2 control-label">Pax Charges</label>
                                        <div class="col-md-2" id="perUnit">
                                            <div class="input-group">
                                                <input type="text" class="form-control" aria-label="">
                                                <span class="input-group-addon">Adult</span>
                                            </div>  
                                        </div>
                                        <div class="col-md-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control" aria-label="">
                                                <span class="input-group-addon">Teen</span>
                                            </div>                                       
                                        </div>
                                        <div class="col-md-2" id="perUnit">
                                            <div class="input-group">
                                                <input type="text" class="form-control" aria-label="">
                                                <span class="input-group-addon">Child</span>
                                            </div>  
                                        </div>
                                        <div class="col-md-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control" aria-label="">
                                                <span class="input-group-addon">Infant</span>
                                            </div>                                       
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-md-12">
                                        <button id="addClaims" class="btn btn-success pull-right addClaims">
                                            <span class="glyphicon glyphicon-search"></span> Add Claims
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="col-md-12">
                                <hr>
                            </div>
                    
                                <!-- Display All service - Cost details -->
                                <div class="row" id="searchProductServiceClaim">
                                    <div class="col-md-12">
                                        <div class="col-md-12">
                                            <table class="table responsive" id="productServiceClaimSort">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Product Service</th>
                                                        <th scope="col">Supplier</th>
                                                        <th scope="col">Specific For</th>
                                                        <th scope="col">Country/Agency</th>
                                                        <th scope="col">Date From</th>
                                                        <th scope="col">Date To</th>
                                                        <th scope="col">Charges</th>
                                                        <th scope="col">Adult</th>                                        
                                                        <th scope="col">Teen</th>
                                                        <th scope="col">Child</th>
                                                        <th scope="col">Infant</th>
                                                        <th scope="col">Edit</th>
                                                        <th scope="col">Delete</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- Display All service - Cost details -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Second Row End -->

    <!-- Modal Block -->
    <!-- Alert Modal Edit Add Tarif -->
    <div class="modal fade" id="productServicesClaimModal" role="dialog">
        <div class="modal-dialog modal-xl">

            <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Rates Detail</h4>
                    </div>
                    <div class="modal-body">

                    <form class="form row-border" action="#" onsubmit="return false">

                        <!-- Row 1 -->
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label class="col-md-2 control-label">Date From</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="col-md-2 control-label">Date To</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <!-- Row 1 -->

                        <!-- Row 2 -->
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label class="col-md-2 control-label">Specific For</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="col-md-2 control-label">Market</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <!-- Row 2 -->

                        <!-- Row 3 -->
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label class="col-md-2 control-label">Currency</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <!-- Row 3 -->

                        <!-- Row 4 -->
                        <!-- Row 4 -->
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label class="col-md-2 control-label">Charges</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-md-4" id="perUnit">
                                    <input type="number" class="form-control" >
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label class="col-md-2 control-label">Pax Charges</label>
                                <div class="col-md-2" id="perUnit">
                                    <div class="input-group">
                                        <input type="text" class="form-control" aria-label="">
                                        <span class="input-group-addon">Adult</span>
                                    </div>  
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control" aria-label="">
                                        <span class="input-group-addon">Teen</span>
                                    </div>                                       
                                </div>
                                <div class="col-md-2" id="perUnit">
                                    <div class="input-group">
                                        <input type="text" class="form-control" aria-label="">
                                        <span class="input-group-addon">Child</span>
                                    </div>  
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control" aria-label="">
                                        <span class="input-group-addon">Infant</span>
                                    </div>                                       
                                </div>
                            </div>
                        </div>
                        
                        <!-- Row 5 -->
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label class="col-md-2 control-label">Excluding</label>
                                <div class="col-md-9">
                                    <li class="checkBoxMain">
                                        <label class='with-square-checkbox'>
                                            <input type='checkbox' checked/>
                                            <span>Monday</span>
                                        </label>
                                    </li>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="button" class="btn btn-default pull-right" id="btnQuit" data-dismiss="modal">Clear</button>
                            <button type="button" class="btn btn-primary pull-right" id="btnSaveClaim">Save</button>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <!-- Row Dsiplay Table add cost -->


                        <div id="extraServiceForm">
                            <!-- Row 1 - Add Extra Services -->
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <label class="col-md-2">Extra Service</label>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-md-3" id="perUnit">
                                        <div class="input-group">
                                            <input type="text" class="form-control" aria-label="">
                                            <span class="input-group-addon">Adult</span>
                                        </div>  
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control" aria-label="">
                                            <span class="input-group-addon">Teen</span>
                                        </div>                                       
                                    </div>
                                    <div class="col-md-3" id="perUnit">
                                        <div class="input-group">
                                            <input type="text" class="form-control" aria-label="">
                                            <span class="input-group-addon">Child</span>
                                        </div>  
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control" aria-label="">
                                            <span class="input-group-addon">Infant</span>
                                        </div>                                       
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr>
                            </div>
                            <!-- Display All service - Cost details -->
                            <div class="row" id="productExtraServiceClaimS">
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <table class="table responsive" id="productExtraServiceClaimSort">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Extra</th>
                                                    <th scope="col">Charges</th>
                                                    <th scope="col">Adult</th>
                                                    <th scope="col">Teen</th>
                                                    <th scope="col">Child</th>
                                                    <th scope="col">Infant</th>
                                                    <th scope="col">Delete</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Display All service - Cost details -->
                        </div>
                    </form>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Modal Block -->
</div>
<!-- End Container -->
