const formData = document.getElementById('formData');
const buttons = {
    back: document.getElementById('btnBack'),
    save: document.getElementById('btnSave'),
    cancel: document.getElementById('btnCancel'),
}
const dataForm = {
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
});

function resetForm() {
    formData.reset();
    $(dataForm.workshop).trigger('change');
    $(dataForm.staff).trigger('change');
    $(dataForm.part_no).trigger('change');
    $(dataForm.reason).trigger('change');

    const invalidElement = document.querySelectorAll(".is-valid");
    if (invalidElement.length > 0) {
        invalidElement.forEach(element => {
            element.classList.remove('is-invalid');
        })
    }
}

buttons.cancel.addEventListener('click', (e) => {
    resetForm();
})

dataForm.part_no.onchange = () => {
    try {
        if (dataForm.part_no.value === '') {
            dataForm.part_name.value = '';
            dataForm.part_model.value = '';
        }
        else {
            loading();
            fetchData(baseurl + '/material/get_material', 'POST', JSON.stringify({ token: dataForm.part_no.value }))
                .then(result => {
                    dataForm.part_name.value = result.data.name;
                    dataForm.part_model.value = result.data.model;
                    dataForm.mold_no.value = result.data.code;
                    hideLoading();
                })
                .catch(err => {
                    pesanError(err.message);
                    hideLoading();
                })
        }
    } catch (e) {
        pesanError(e.message);
        hideloading();
    }
}

function validasi() {
    let isValid = true;
    const requiredElement = document.querySelectorAll("[required]");
    if (requiredElement.length > 0) {
        requiredElement.forEach(element => {
            if (element.value.trim() === '') {
                isValid = false;
                element.classList.add('is-invalid');
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
            fetchData(baseurl + '/spk_mold/save', 'POST', new FormData(formData))
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
        }
    }
});