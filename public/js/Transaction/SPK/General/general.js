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

function refreshTable() {
    $('#dataTable').DataTable().ajax.reload(null, false);
}

function showImage(token) {
    try {
        loading();
        fetchData(baseurl + '/spk_general/image', 'POST', JSON.stringify({ token: token }))
            .then((result) => {
                document.getElementById('modalTitle').textContent = result.data.title;
                const bodyImage = document.getElementById('imageData');
                const img = document.createElement('img');
                img.classList.add("img-fluid", "img-thumbnail");
                img.src = result.data.image;

                bodyImage.appendChild(img);
                console.log(result.data);
                $('#modalImage').modal('show');
                hideLoading();
            }).catch((err) => {
                pesanError(err.message);
                hideLoading();
            });
    } catch (e) {
        pesanError(e.message);
        hideLoading();
    }
}

function clearModal() {
    const bodyImage = document.getElementById('imageData');
    bodyImage.textContent = '';
    document.getElementById('modalTitle').textContent = '';
    $('#modalImage').modal('hide');
}

buttons.refresh.addEventListener('click', (e) => {
    refreshTable();
})