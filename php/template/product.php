<div class="container-fluid">
    <div class="row">
        <!-- Column 1 -->
        <div class="col-md-8">
            <div class="col-md-12">
                <!-- row 1 -->
                <div class="panel panel-default">
                    <div class="panel-heading clearfix">
                        <i class="icon-calendar"></i>
                        <h3 class="panel-title">Product <span id="productId" style="display: block">0</span></h3>
                    </div>

                    <div class="panel-body">
                        <div class="form-group col-md-12">
                            <!-- Row 1 -->
                            <label class="col-md-2 control-label">Type</label>
                            <div class="col-md-4">
                                <select type="text" class="form-control" id="ddlType">
                                    <!-- To modify - select from db -->
                                    <option value="2">Activities</option>
                                    <option value="3">Transfer</option>
                                    <option value="4">Others</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <!-- Row 1 -->
                            <label class="col-md-2 control-label">Product Name</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control text-uppercase" id="productName">
                            </div>

                            <label class="col-md-2 control-label">Product Type</label>
                            <div class="col-md-4">
                                <select type="text" class="form-control" id="ddlProductType">
                                    <!-- To modify - select from db -->
                                    <option value="1">Land</option>
                                    <option value="2">Sea</option>
                                    <option value="3">Air</option>
                                    <option value="4">Other</option>
                                </select>

                                <div class="pager pull-right">
                                    <button type="button" class="btn btn-default" id="btnResetProduct" onclick="resetFormAddProduct()"><i class="fa fa-refresh" aria-hidden="true"></i>&nbsp;&nbsp;Reset</button>
                                    <button type="button" class="btn btn-success" id="btnSaveProduct">Save</button>
                                </div>
                            </div>
                        <!-- Row 1 -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <!-- row 2 -->
                <div class="panel panel-default">
                    <div class="panel-heading clearfix">
                        <i class="icon-calendar"></i>
                        <h3 class="panel-title">Product Services Claim</h3>
                    </div>

                    <div class="panel-body" id="productService">
                        <table class="table responsive" id="productServiceSort">
                            <thead>
                                <tr>
                                    <th>Product Id</th>
                                    <th>Service Type</th>
                                    <th>Product</th>
                                    <th></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column 2 -->
        <div class="col-md-4">
            
            <div class="col-md-12">
                <div  id="parent_latest" style="text-align: center;">
                    <!-- Latest Product Dynamic from JS -->
                    <div id='loadingmessage' style='display:none; margin-top: 195px;'>
                        <img src='https://www.makealltrip.com/template/img/loader.gif'/>
                    </div>
                </div>
            </div>
        </div> 
    </div>
</div>



<div class="toast jam toast_added" aria-hidden="true" style="display:none;">
    <span class="close" aria-role="button" tabindex="0">&times;</span> Product Added.
</div>
