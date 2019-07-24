// Request call everything from database
function callDataNewServiceGrid() {
    const url_excursiongrid = "php/api/bckoffservices/servicesgrid.php?t=" + encodeURIComponent(global_token); 
    $.ajax({
        url : url_excursiongrid,
        type : "GET",
        dataType: "json",
        success : function(data) {
            populateServiceGrid(data);
        }, 
        error:function(error) {
            console.log('Error ${error}');
        }
    });
}

function populateServiceGrid(objNewService) {
    
    columns = {
        'location': 'Location',
        'service': 'Service',
        'supplierCode': 'Supplier Code',
        'supplier': 'Supplier',
        'option': 'Option',
        'class': 'Class',
        'locality': 'Location', 
        'edit': 'Edit'
    }
    objNewService.forEach(element => {
        var chk = element;
        data = [
            {
                location: chk.id,
                service: chk.countryfk,
                supplierCode: chk.countryfk,
                supplier: chk.countryfk,
                option: chk.countryfk,
                class: chk.countryfk,
                locality: 'Port louis',
                edit: '<a href="" href="" onclick="editservice(param)"><i data-toggle="tooltip" title="Edit Service Details" class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i></a>'
            }
        ]
        
    console.log('data -->', data);
    });

    TestData = {
        data: data,
        columns: columns
    }
    var table = $('#root').tableSortable({
        data: TestData.data,
        columns: TestData.columns,
        dateParsing: true,
        columnsHtml: function(value, key) {
            return value;
        },
        pagination: 10,
        showPaginationLabel: true,
        prevText: 'Prev',
        nextText: 'Next',
        searchField: $('#search'),
        responsive: [
            {
                maxWidth: 992,
                minWidth: 769,
                columns: TestData.col,
                pagination: true,
                paginationLength: 3
            },
            {
                maxWidth: 768,
                minWidth: 0,
                columns: TestData.colXS,
                pagination: true,
                paginationLength: 2
            }
        ]
    });

}

