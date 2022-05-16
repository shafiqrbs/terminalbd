/**
 Core script to handle the entire theme and core functions
 **/
var AppScript = function () {

    $('.dataTable').DataTable( {
        iDisplayLength: 25,
        scrollY:        '50vh',
        scrollX: false,
        scrollCollapse: true,
        paging:         false,
        bInfo : false,
        orderable: true,
        bSort: true,
        aoColumnDefs: [
            {
                bSortable: false,
                aTargets: [ -1 ]
            }
        ]
    });
};