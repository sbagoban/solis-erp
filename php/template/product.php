<div class="container-fluid">
    <div class="row">
        <!-- Column 1 -->
        <div class="col-md-8">
            <div class="col-md-12">
                <!-- row 1 -->
                <div class="panel panel-default">
                    <div class="panel-heading clearfix">
                        <i class="icon-calendar"></i>
                        <h3 class="panel-title">Product </h3>
                    </div>

                    <div class="panel-body">
                        <!-- Row 1 -->
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label class="col-md-2 control-label">Type</label>
                                <div class="col-md-9">
                                    <select type="text" class="form-control" id="ddlType">
                                        <!-- To modify - select from db -->
                                        <option value="2">Activities</option>
                                        <option value="4">Others</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label class="col-md-2 control-label">Product</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="productName">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="col-md-2 control-label">Product Type</label>
                                <div class="col-md-9">
                                    <select type="text" class="form-control" id="ddlProductType">
                                        <!-- To modify - select from db -->
                                        <option value="1">Land</option>
                                        <option value="2">Sea</option>
                                        <option value="3">Air</option>
                                        <option value="4">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary pull-right" id="btnSaveProduct">Save</button>
                            <button type="button" class="btn btn-default pull-right" id="btnDeleteProduct" data-dismiss="modal">Delete</button>
                        </div>
                        <!-- Row 1 -->
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
                                    <th scope="col">Product Id</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Product</th>
                                    <th scope="col">Product Type</th>
                                    <th scope="col">Add</th>
                                    <th scope="col">Edit</th>
                                    <th scope="col">Delete</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Column 2 -->
        <div class="col-md-4">
            <!-- Panel -->
            <h3 class="panel-title">Latest Product Services</h3>
            <br>
            <div class="panel panel-theme">
                <div class="panel-heading">Product Name</div>
                <div class="panel-body">
                    Product : <br>
                    Product Type :  <br>
                    Services :  <br>
                    Package :  <br>
                </div>
            </div>
            <div class="panel panel-theme">
                <div class="panel-heading">Product Name</div>
                <div class="panel-body">
                    Product : <br>
                    Product Type :  <br>
                    Services :  <br>
                    Package :  <br>
                </div>
            </div>
            <div class="panel panel-theme">
                <div class="panel-heading">Product Name</div>
                <div class="panel-body">
                    Product : <br>
                    Product Type :  <br>
                    Services :  <br>
                    Package :  <br>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="toast jam toast_added" aria-hidden="true" style="display:none;">
    <span class="close" aria-role="button" tabindex="0">&times;</span> Product Added.
</div>