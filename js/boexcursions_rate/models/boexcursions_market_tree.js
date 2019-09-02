// $('#jstreeMarket').jstree({
//     'plugins': ['search', 'checkbox', 'wholerow'],
//     'core': {
//         'json_data': {
//             "ajax" : {
//                 "type": 'GET',
//                 "url": function (node) {
//                     var nodeId = "";
//                     var url = ""
//                     if (node == -1)
//                     {
//                         url = "php/api/combos/market_combo.php?t=" + encodeURIComponent(global_token);
//                     }
//                     else
//                     {
//                         nodeId = node.attr('id');
//                         url = "php/api/combos/market_combo.php?t=" + encodeURIComponent(global_token);
//                     }
    
//                     return url;
//                 },
//                 "success": function (new_data) {
//                     return new_data;
//                 }
//             }
//         },
//         'animation': true,
//         'expand_selected_onload': true,
//         'themes': {
//             'icons': false,
//         }
//         },
//         'search': {
//             'show_only_matches': true,
//             'show_only_matches_children': true
//         }
//     });
    
//     $('#search').on("keyup change", function () {
//         $('#jstreeMarket').jstree(true).search($(this).val())
//     })
  
//     $('#clear').click(function (e) {
//         $('#search').val('').change().focus()
//     })
  
//     $('#jstreeMarket').on('changed.jstreeMarket', function (e, data) {
//         var objects = data.instance.get_selected(true)
//         var leaves = $.grep(objects, function (o) { return data.instance.is_leaf(o) })
//         var list = $('#output')
//         list.empty()
//         $.each(leaves, function (i, o) {
//         $('<li/>').text(o.text).appendTo(list)
//     })
// })