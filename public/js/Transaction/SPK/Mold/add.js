const formData = document.getElementById('formData');
const buttons = {
    back: document.getElementById('btnBack'),
    save: document.getElementById('btnSave'),
    cancel: document.getElementById('btnCancel'),
}

window.onload = () => {
    $('.summernote').summernote({
        height: 300, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
    });
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
    description: document.getElementById('data_keterangan'),
    defect: document.getElementById('data_defect'),
    sub_defect: document.getElementById('data_sub_defect'),
    leader: document.getElementById('data_leader')
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
    $(dataForm.leader).trigger('change');
    $('.summernote').summernote('code', '');

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

dataForm.defect.onchange = (e) => {
    if (dataForm.defect.value == '') {
        dataForm.sub_defect.innerHTML = '<option value="">-- Choose --</option>';
        dataForm.sub_defect.setAttribute('disabled', true);
    } else {
        try {
            fetchData(baseurl + '/sub_defect/get_list', 'POST', JSON.stringify({ token: dataForm.defect.value }))
                .then(result => {
                    dataForm.sub_defect.innerHTML = '<option value="">-- Choose --</option>';
                    if (result.data.length > 0) {
                        dataForm.sub_defect.removeAttribute('disabled');
                        result.data.forEach(item => {
                            const dataList = `
                                <option value="${item.token}">${item.name}</option>
                            `;

                            dataForm.sub_defect.insertAdjacentHTML('beforeend', dataList);
                        })
                    }
                })
                .catch(err => {
                    dataForm.sub_defect.innerHTML = '<option value="">-- Choose --</option>';
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