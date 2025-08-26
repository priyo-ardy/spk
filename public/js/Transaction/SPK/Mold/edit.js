const formData = document.getElementById('formData');
const buttons = {
    back: document.getElementById('btnBack'),
    update: document.getElementById('btnUpdate'),
    add: document.getElementById('btnAdd'),
    edit: document.getElementById('btnEdit'),
    cancel: document.getElementById('btnCancel'),
    prev: document.getElementById('btnPrev'),
    next: document.getElementById('btnNext')
}
const dataForm = {
    token: document.getElementById('data_token'),
    code: document.getElementById('data_code'),
    workshop: document.getElementById('data_workshop'),
    staff: document.getElementById('data_staff'),
    tanggal: document.getElementById('data_tanggal'),
    part_no: document.getElementById('data_material'),
    part_name: document.getElementById('data_name'),
    part_model: document.getElementById('data_model'),
    mold_no: document.getElementById('data_mold'),
    reason: document.getElementById('data_repair'),
    fupload: document.getElementById('fupload'),
    description: document.getElementById('data_keterangan')
}

buttons.back.addEventListener('click', (e) => {
    loading();
    window.location.replace(baseurl + '/spk_mold');
})

// Inisialisasi Lightbox
lightbox.option({
    'resizeDuration': 200,
    'wrapAround': true,
    'showImageNumberLabel': true,
    'alwaysShowNavOnTouchDevices': true
});

function bukaForm() {
    buttons.back.setAttribute('hidden', true);
    buttons.edit.setAttribute('hidden', true);
    buttons.prev.setAttribute('hidden', true);
    buttons.next.setAttribute('hidden', true);
    buttons.add.setAttribute('hidden', true);

    buttons.update.removeAttribute('hidden');
    buttons.cancel.removeAttribute('hidden');

    dataForm.workshop.removeAttribute('disabled');
    dataForm.staff.removeAttribute('disabled');
    dataForm.tanggal.removeAttribute('readonly');
    dataForm.tanggal.classList.remove('bg-body-secondary');
    dataForm.part_no.removeAttribute('disabled');
    dataForm.reason.removeAttribute('disabled');
    dataForm.fupload.removeAttribute('disabled');
    dataForm.description.removeAttribute('disabled');
}

buttons.add.addEventListener('click', (e) => {
    loading();
    window.location.replace(baseurl + '/spk_mold/add');
})

buttons.edit.addEventListener('click', (e) => {
    bukaForm();
})

buttons.cancel.addEventListener('click', (e) => {
    loading();
    window.location.reload();
})

function validasi() {
    let isValid = true;

    let requiredElement = document.querySelectorAll('[required]');

    if (dataForm.token.value.trim() === '') {
        isValid = false;
        pesanWarning('SPK token is required');
    }

    if (requiredElement.length > 0) {
        requiredElement.forEach(element => {
            if (element.value.trim() === '') {
                element.classList.add('is-invalid');
                isValid = false;
            } else {
                element.classList.remove('is-invalid');
            }
        })
    }

    return isValid;
}

buttons.update.addEventListener('click', (e) => {
    if (validasi()) {
        try {
            loading();
            fetchData(baseurl + '/spk_mold/update', 'POST', new FormData(formData))
                .then(result => {
                    pesanSukses(result.message);
                    setTimeout(() => {
                        window.location.reload()
                    }, 1500)
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
});

buttons.prev.addEventListener('click', (e) => {
    try {
        loading();
        fetchData(baseurl + '/spk_mold/prev', 'POST', JSON.stringify({ code: dataForm.code.value }))
            .then(result => {
                window.location.replace(baseurl + '/spk_mold/show' + result.data.token);
            })
            .catch(err => {
                pesanWarning(err.message);
                hideLoading();
            })
    } catch (e) {
        pesanError(e.message);
        hideLoading();
    }
})

buttons.next.addEventListener('click', (e) => {
    try {
        loading();
        fetchData(baseurl + '/spk_mold/next', 'POST', JSON.stringify({ code: dataForm.code.value }))
            .then(result => {
                window.location.replace(baseurl + '/spk_mold/show' + result.data.token);
            })
            .catch(err => {
                pesanWarning(err.message);
                hideLoading();
            })
    } catch (e) {
        pesanError(e.message);
        hideLoading();
    }
})

function deleteImage(token) {
    try {
        hapusData('/spk_mold/delete_image', token);
    } catch (e) {
        pesanError(e.message);
        hideLoading();
    }
}