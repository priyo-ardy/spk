window.onload = () => {
    $('.summernote').summernote({
        height: 300, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
    });

    $('.summernote').summernote('disable');
}

const formData = document.getElementById('formData');
const buttons = {
    back: document.getElementById('btnBack'),
    edit: document.getElementById('btnEdit'),
    update: document.getElementById('btnUpdate'),
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
    material: document.getElementById('data_material'),
    nomor: document.getElementById('data_nomor'),
    model: document.getElementById('data_model'),
    tipe: document.getElementById('data_tipe'),
    spv: document.getElementById('data_spv'),
    fupload: document.getElementById('fupload'),
    keterangan: document.getElementById('data_keterangan')
}

lightbox.option({
    'resizeDuration': 200,
    'wrapAround': true,
    'showImageNumberLabel': true,
    'alwaysShowNavOnTouchDevices': true
});

function bukaForm() {
    buttons.back.setAttribute('hidden', true);
    buttons.edit.setAttribute('hidden', true);
    buttons.update.removeAttribute('hidden');
    buttons.cancel.removeAttribute('hidden');

    dataForm.workshop.removeAttribute('disabled');
    dataForm.staff.removeAttribute('disabled');
    dataForm.tanggal.removeAttribute('readonly');
    dataForm.tanggal.classList.remove('bg-body-secondary');
    dataForm.material.removeAttribute('disabled');
    dataForm.tipe.removeAttribute('disabled');
    dataForm.spv.removeAttribute('disabled');
    dataForm.fupload.removeAttribute('disabled');
    dataForm.keterangan.removeAttribute('readonly');
    dataForm.keterangan.classList.remove('bg-body-secondary');
    $('.summernote').summernote('enable');
    dataForm.workshop.focus();
}

buttons.back.addEventListener('click', (e) => {
    loading();
    window.location.replace(baseurl + '/spk_general');
})

buttons.edit.addEventListener('click', (e) => {
    bukaForm();
});

buttons.cancel.addEventListener('click', (e) => {
    loading();
    window.location.reload();
})

dataForm.material.onchange = () => {
    if (dataForm.material.value === '') {
        dataForm.nomor.value = '';
        dataForm.model.value = '';
    } else {
        try {
            fetchData(baseurl + '/machine/machine_data', 'POST', JSON.stringify({ token: dataForm.material.value }))
                .then(result => {
                    console.table(result.data);
                    dataForm.nomor.value = result.data.nomor_mesin;
                    dataForm.model.value = result.data.specification;
                })
                .catch(err => {
                    pesanError(err.message);
                    dataForm.nomor.value = '';
                    dataForm.model.value = '';
                })
        } catch (e) {
            pesanError(e.message);
        }
    }
}

function validasi() {
    let isValid = true;

    const requiredElement = document.querySelectorAll("[required]");
    if (dataForm.token.value === '') {
        isValid = false;
        pesanWarning("SPK token is required");
    }

    if (dataForm.code.value === '') {
        dataForm.code.classList.add('is-invalid');
        isValid = false;
    } else {
        dataForm.code.classList.remove('is-invalid');
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
            fetchData(baseurl + '/spk_general/update', 'POST', new FormData(formData))
                .then(result => {
                    pesanSukses(result.message);
                    setTimeout(() => {
                        window.location.reload();
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
})

function deleteImage(token) {
    try {
        hapusData('/spk_general/delete_image', token);
    } catch (e) {
        pesanError(e.message);
    }
}

buttons.prev.addEventListener('click', (e) => {
    try {
        loading();
        fetchData(baseurl + '/spk_general/prev', 'POST', JSON.stringify({ code: dataForm.code.value }))
            .then(result => {
                window.location.replace(baseurl + '/spk_general/show/' + result.data.token);
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
        fetchData(baseurl + '/spk_general/next', 'POST', JSON.stringify({ code: dataForm.code.value }))
            .then(result => {
                window.location.replace(baseurl + '/spk_general/show/' + result.data.token);
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