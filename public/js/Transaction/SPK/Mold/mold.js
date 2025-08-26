lightbox.option({
    'resizeDuration': 200,
    'wrapAround': true,
    'showImageNumberLabel': true,
    'alwaysShowNavOnTouchDevices': true
});

const buttons = {
    add: document.getElementById('btnAdd'),
    filter: document.getElementById('btnFilter'),
    refresh: document.getElementById('btnRefresh'),
    export: document.getElementById('btnExport')
}

const modalData = {
    title: document.getElementById('modalTitle'),
    body: document.getElementById('imageData')
}

buttons.add.addEventListener('click', (e) => {
    loading();
    window.location.replace(baseurl + '/spk_mold/add');
})

loadTable();

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
            url: baseurl + "/spk_mold/table",
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
        fetchData(baseurl + '/spk_mold/image', 'POST', JSON.stringify({ token: token }))
            .then(result => {
                modalData.title.textContent = result.header.title;
                if (result.data.length > 0) {
                    result.data.forEach(item => {
                        let imageList = `
                            <div class="col-4 clearfix mb-3">
                                <a href="${item.file_name}?#" class="link-underline-opacity-0" data-lightbox="preview" data-title="${item.image_file}">
                                    <img src="${item.file_name}" class="img-fluid img-thumbnail rounded-0">
                                </a>
                            </div>
                        `;

                        modalData.body.insertAdjacentHTML('beforeend', imageList);
                    });
                }

                $('#modalImage').modal('show');
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

function clearModal() {
    modalData.body.textContent = '';
    document.getElementById('modalTitle').textContent = '';
    $('#modalImage').modal('hide');
}

buttons.export.addEventListener('click', async (e) => {
    try {
        loading();
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 300000);

        const response = await fetch(baseurl + '/spk_mold/export', {
            method: 'GET',
            signal: controller.signal

        });

        clearTimeout(timeoutId);

        if (!response.ok) {
            const errorData = await response.json().catch(() => null);
            throw new Error(errorData?.error || `HTTP error! status: ${response.status}`);
        }

        const blob = await response.blob();

        if (blob.size === 0) {
            throw new Error("Failed to creating exported file");
        }

        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'mold_spk_list.xlsx';
        document.body.appendChild(a);
        a.click();

        window.URL.revokeObjectURL(url);
        a.remove();
        hideLoading();
    } catch (e) {
        if (e.name === 'AbortError') {
            pesanError('Proses ekspor terlalu lama. Silakan coba lagi atau ekspor data lebih kecil.');
        } else {
            pesanError(e.message || 'Gagal mengekspor data');
        }

        hideLoading();
    }
})