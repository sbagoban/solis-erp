var data = [
    {
        location: 531718,
        service: 'Investment Form',
        supplierCode: 'Test User',
        supplier: '13 March, 2017',
        option: '1:30PM',
        class: 'Test',
        locality: 'Port louis',
        edit: '<a href="" href="" onclick="editservice(param)"><i data-toggle="tooltip" title="Edit Service Details" class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i></a>'
    }
]

var columns = {
    'location': 'Location',
    'service': 'Service',
    'supplierCode': 'Supplier Code',
    'supplier': 'Supplier',
    'option': 'Option',
    'class': 'Class',
    'locality': 'Location', 
    'edit': 'Edit'
}

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
})
