window.onload = () => {
    loadTable();
}

const buttons = {
    add: document.getElementById('btnAdd'),
    filter: document.getElementById('btnFilter'),
    refresh: document.getElementById('btnRefresh'),
    export: document.getElementById('btnExport')
}

buttons.add.addEventListener('click', (e) => {
    loading();
    window.location.replace(baseurl + '/spk/add');
})

function loadTable() {
    $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        bDestroy: true,
        autoWidth: false,
        scrollX: true,
        search: {
            return: true,
        },
        order: [],
        ajax: {
            url: baseurl + "/spk/table",
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

function refreshTable() {
    $('#dataTable').DataTable().ajax.reload(null, false);
}