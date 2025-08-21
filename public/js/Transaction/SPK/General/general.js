const buttons = {
    add: document.getElementById('btnAdd'),
    filter: document.getElementById('btnFilter'),
    refresh: document.getElementById('btnRefresh'),
    export: document.getElementById('btnExport')
}

loadTable();

buttons.add.addEventListener('click', (e) => {
    loading();
    window.location.replace(baseurl + '/spk_general/add');
})

function loadTable() {
    $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        bDestroy: true,
        search: {
            return: true,
        },
        order: [],
        ajax: {
            url: baseurl + "/spk_general/table",
            type: "POST",
            data: "raw",
            action: "calls",
        },
        error: function (xhr, error, thrown) {
            pesanError(error.message);
        },
        deferRender: true,
        columnsDefs: [
            {
                targets: 0,
                orderable: false,
            },
        ],
    });
}

function showImage(token) {
    alert(token);
}