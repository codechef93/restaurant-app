"use strict";

$.extend( $.fn.dataTable.defaults, {
    'pagingType': 'numbers',
    'processing': true,
    'serverSide': true,
    'responsive': true,
    'pageLength': 25,
    'dom'       : '<"d-flex mb-3"<"mr-auto"i> <"mr-4"l><f>><"clearfix">r<t><"mt-3"p><"clearfix">',
    'scrollX'   : true,
    drawCallback: function( settings ) {
        if(typeof settings.jqXHR.responseJSON.access != 'undefined' && settings.jqXHR.responseJSON.access == false){
            $(".dataTables_empty").html("<i class='fas fa-info-circle text-warning'></i> You Don't Currently Have Permission to Access Listing. Please Request for Access.");
        }
        if(typeof settings.jqXHR.responseJSON != 'undefined' && settings.jqXHR.responseJSON.data.length == 1){
            $('div.dataTables_scrollHead').css('overflow', 'visible');
            $('div.dataTables_scrollBody').css('overflow', 'visible');
        }
    }
} );

$('#menu-toggle').on('click', function () {
    "use strict";
    setTimeout(function(){
        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
    }, 300);
});
