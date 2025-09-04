loadTable();

const buttons = {
    add: document.getElementById('btnAdd'),
    filter: document.getElementById('btnFilter'),
    refresh: document.getElementById('btnRefresh'),
    export: document.getElementById('btnExport'),
    change: document.getElementById('btnChangePassword')
}

buttons.add.addEventListener('click', (e) => {
    loading();
    window.location.replace(baseurl + '/users/add');
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
            url: baseurl + "/users/table",
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

function changePassword(token) {
    document.getElementById('user_token').value = token;
    $('#changePassword').modal('show');
}

function closeModal() {
    document.getElementById('formGantiPassword').reset();
    $('#changePassword').modal('hide');
}

buttons.change.onclick = () => {
    if (document.getElementById('new_password').value === '') {
        document.getElementById('new_password').classList.add('is-invalid');
    } else {
        loading();
        try {
            loading();
            fetchData(baseurl + '/users/change_password', 'POST', new FormData(document.getElementById('formGantiPassword')))
                .then(result => {
                    pesanSukses(result.message);
                    closeModal();
                    refreshTable();
                    hideLoading();
                })
                .catch(err => {
                    pesanError(err.message);
                    hideLoading();
                })
        } catch (e) {
            pesanError(e.message);
            hideLoading();
        }
    }
}

function disableUser(token) {
    try {
        hapusData('/users/disable', token);
    } catch (e) {
        pesanError(e.message);
    }
}