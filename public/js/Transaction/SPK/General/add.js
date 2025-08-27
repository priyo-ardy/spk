const formData = document.getElementById('formData');
const buttons = {
    back: document.getElementById('btnBack'),
    save: document.getElementById('btnSave'),
    cancel: document.getElementById('btnCancel'),
};
const dataForm = {
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

buttons.back.addEventListener('click', (e) => {
    loading();
    window.location.replace(baseurl + '/spk_general');
})

window.onload = () => {
    $('.summernote').summernote({
        height: 300, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
    });
}

function resetForm() {
    formData.reset();
    $(dataForm.workshop).trigger('change');
    $(dataForm.staff).trigger('change');
    $(dataForm.material).trigger('change');
    $(dataForm.tipe).trigger('change');
    $(dataForm.spv).trigger('change');

    const invalidElement = document.querySelectorAll(".is-invalid");
    if (invalidElement.length > 0) {
        invalidElement.forEach(element => {
            element.classList.remove('is-invalid');
        })
    }
}

buttons.cancel.addEventListener('click', (e) => {
    resetForm();
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

buttons.save.addEventListener('click', (e) => {
    if (validasi()) {
        try {
            loading();
            fetchData(baseurl + '/spk_general/save', 'POST', new FormData(formData))
                .then(result => {
                    pesanSukses(result.message);
                    resetForm();
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
});