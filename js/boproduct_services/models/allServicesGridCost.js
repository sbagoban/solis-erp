function allServicesGridCost() {
    var id_product_services = window.location.href.split('psid=').pop();
    $('#tbl-productServicesCost').DataTable({
        "processing": true,
        "ajax": {
            "url": "php/api/backofficeproduct/gridservicecost.php?t=" + encodeURIComponent(global_token) + "&id_product_services=" + id_product_services,
            dataSrc: ''
        },
        "destroy": true,
        "bProcessing": true,
        "bAutoWidth": false,
        "responsive": true,
        "pageLength": 4,
        "dom": "<'row'<'form-inline' <'col-sm-5'B>>>"
            + "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>"
            + "<'row'<'col-sm-12'tr>>"
            + "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "columnDefs": [
            { width: 200, targets: -1 }
        ],
        "buttons": [
            {
                "extend": "csvHtml5",
                "text": "<i class='fa fa-file-text-o'> Excel</i>",
                "titleAttr": "Download in Excel Format",
            }
        ],
        "columnDefs": [],
        "columns": [{
            "data": "id_product_services_cost"
        }, {
            "data": "allDate"
        }, {
            "data": "charges"
        },
        {
            "targets": -1,
            "data": null,
            "class": 'btnCol',
            "defaultContent": '<div class="btn-group">' +
                '<button type="button" id="btnAddExtraServices" class="btn btn-primary"><i class="fa fa-fw fa-plus-circle"></i></button>' +
                '<button type="button" id="btnEditServiceCost" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i>' +
                '<button type="button" id="btnDeleteServiceCost" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button></div>'
        }
        ]
    });
    $('#tbl-productServicesCost tbody').on('click', '#btnAddExtraServices', function () {
        var table = $('#tbl-productServicesCost').DataTable();
        var data = table.row($(this).parents('tr')).data();
        serviceCostExtra(data);
    });
    $('#tbl-productServicesCost tbody').on('click', '#btnDeleteServiceCost', function () {
        var table = $('#tbl-productServicesCost').DataTable();
        var data = table.row($(this).parents('tr')).data();
        serviceCostDelete(data);
    });
    $('#tbl-productServicesCost tbody').on('click', '#btnEditServiceCost', function () {
        var table = $('#tbl-productServicesCost').DataTable();
        var data = table.row($(this).parents('tr')).data();
        serviceCostEdit(data);
        allExtraServicesCostGrid(data.id_product_services_cost);
    });
}
