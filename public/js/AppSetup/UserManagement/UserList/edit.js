const formData = document.getElementById('formData');
const buttons = {
    back: document.getElementById('btnBack'),
    add: document.getElementById('btnAdd'),
    edit: document.getElementById('btnEdit'),
    update: document.getElementById('btnUpdate'),
    cancel: document.getElementById('btnCancel'),
    prev: document.getElementById('btnPrev'),
    next: document.getElementById('btnNext')
}

const dataForm = {
    token: document.getElementById('data_token'),
    user_name: document.getElementById('user_name'),
    full_name: document.getElementById('full_name'),
    phone_number: document.getElementById('phone_number'),
    email_address: document.getElementById('email_address'),
    user_password: document.getElementById('user_password'),
    user_level: document.getElementById('user_level'),
    user_image: document.getElementById('user_image'),
    img_preview: document.getElementById('img_preview')
}

const formFeedback = {
    user_name: document.getElementById('feedback_username'),
    full_name: document.getElementById('feedback_fullname'),
    phone_number: document.getElementById('feedback_phone'),
    email_address: document.getElementById('feedback_email'),
    user_password: document.getElementById('feedback_password'),
    user_image: document.getElementById('feedback_image')
}

function bukaForm() {
    dataForm.user_name.removeAttribute('disabled');
    dataForm.full_name.removeAttribute('disabled');
    dataForm.phone_number.removeAttribute('disabled');
    dataForm.email_address.removeAttribute('disabled');
    dataForm.user_level.removeAttribute('disabled');
    dataForm.user_image.removeAttribute('disabled');

    buttons.back.setAttribute('disabled', true);
    buttons.add.setAttribute('disabled', true);
    buttons.prev.setAttribute('disabled', true);
    buttons.next.setAttribute('disabled', true);
    buttons.edit.setAttribute('disabled', true);

    buttons.update.removeAttribute('disabled');
    buttons.cancel.removeAttribute('disabled');
}

dataForm.user_image.addEventListener('change', (e) => {
    const file = e.target.files[0];

    if (!file) {
        dataForm.img_preview.display = 'none';
        return;
    }

    if (!file.type.startsWith('image/')) {
        const feedback = dataForm.parentElement.querySelector('.invalid-feedback');
        e.target.value = '';
        // dataForm.img_preview.style.display = 'none';
        feedback.textContent = "Only image file accepted";
        dataForm.img_preview.src = baseurl + '/image/no-foto.jpg';
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        dataForm.img_preview.src = e.target.result;
        // dataForm.img_preview.style.display = 'block';
    }

    reader.readAsDataURL(file);
});

function checkUsername() {
    if (dataForm.user_name.value === '') {
        dataForm.user_name.classList.add('is-invalid');
        formFeedback.user_name.textContent = "This field is required";
    } else {
        try {
            fetchData(baseurl + '/users/check_user', 'POST', JSON.stringify({
                token: dataForm.token.value,
                username: dataForm.user_name.value,
            }))
                .then(result => {
                    dataForm.user_name.classList.remove('is-invalid');
                    dataForm.user_name.classList.add('is-valid');
                    dataForm.full_name.focus;
                })
                .catch(err => {
                    dataForm.user_name.classList.add('is-invalid');
                    formFeedback.user_name.textContent = err.message;
                    dataForm.user_name.focus();
                })
        } catch (e) {
            pesanError(e.message);
            dataForm.user_name.focus;
        }
    }
}

function checkUserPhone() {
    try {
        fetchData(baseurl + '/users/check_phone', 'POST', JSON.stringify({
            token: dataForm.token.value,
            user_phone: dataForm.phone_number.value
        }))
            .then(result => {
                dataForm.phone_number.classList.remove('is-invalid');
                dataForm.phone_number.classList.add('is-valid');
                dataForm.email_address.focus();
            })
            .catch(err => {
                dataForm.phone_number.classList.add('is-invalid');
                formFeedback.phone_number.textContent = err.message;
                dataForm.phone_number.focus();
            })
    } catch (e) {
        pesanError(e.message);
    }
}

function isValidEmail(email) {
    // Regex standar sederhana untuk validasi email
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function checkUserEmail() {
    try {
        if (isValidEmail(dataForm.email_address.value)) {
            fetchData(baseurl + '/users/check_email', 'POST', JSON.stringify({
                token: dataForm.token.value,
                user_email: dataForm.email_address.value
            }))
                .then(result => {
                    dataForm.email_address.classList.remove('is-invalid');
                    dataForm.email_address.classList.add('is-valid');
                    dataForm.user_password.focus();
                })
                .catch(err => {
                    dataForm.email_address.classList.add('is-invalid');
                    formFeedback.email_address.textContent = err.message;
                    dataForm.email_address.focus();
                })
        }
    } catch (e) {
        pesanError(e.message);
    }
}

buttons.back.addEventListener('click', (e) => {
    loading();
    window.location.replace(baseurl + '/users');
})

buttons.add.addEventListener('click', (e) => {
    loading();
    window.location.replace(baseurl + '/users/add');
})

buttons.cancel.addEventListener('click', (e) => {
    loading();
    window.location.reload();
})

buttons.edit.addEventListener('click', (e) => {
    bukaForm();
});

buttons.prev.addEventListener('click', (e) => {
    if (dataForm.token.value === '') {
        pesanError("User token is required");
    } else {
        try {
            loading();
            fetchData(baseurl + '/users/prev', 'POST', JSON.stringify({ token: dataForm.token.value }))
                .then(result => {
                    hideLoading();
                    window.location.replace(baseurl + '/users/show/' + result.data.token);
                })
                .catch(err => {
                    pesanWarning(err.message);
                    hideLoading();
                })
        } catch (e) {
            pesanError(e.message);
            hideLoading();
        }
    }
})

buttons.next.addEventListener('click', (e) => {
    if (dataForm.token.value === '') {
        pesanError("User token is required");
    } else {
        try {
            loading();
            fetchData(baseurl + '/users/next', 'POST', JSON.stringify({ token: dataForm.token.value }))
                .then(result => {
                    hideLoading();
                    window.location.replace(baseurl + '/users/show/' + result.data.token);
                })
                .catch(err => {
                    pesanWarning(err.message);
                    hideLoading();
                })
        } catch (e) {
            pesanError(e.message);
            hideLoading();
        }
    }
})

buttons.update.addEventListener('click', (e) => {
    if (validasi()) {
        try {
            loading();
            fetchData(baseurl + '/users/update', 'POST', new FormData(formData))
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

dataForm.user_name.addEventListener('change', (e) => {
    if (dataForm.user_name.value === '') {
        dataForm.user_name.classList.add('is-invalid');
        formFeedback.user_name.textContent = "This field is required"
        dataForm.user_name.focus();
    } else {
        checkUsername();
    }
})

dataForm.user_name.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        if (dataForm.user_name.value === '') {
            dataForm.user_name.focus();
            dataForm.user_name.classList.add('is-invalid');
            formFeedback.user_name.textContent = 'This field is required';
        } else {
            checkUsername();
        }
    }
})

dataForm.phone_number.addEventListener('change', (e) => {
    if (dataForm.phone_number.value === '') {
        dataForm.phone_number.focus();
        dataForm.phone_number.classList.add('is-invalid');
        formFeedback.phone_number.textContent = "This field is required";
    } else {
        checkUserPhone();
    }
})

dataForm.phone_number.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        if (dataForm.phone_number.value === '') {
            dataForm.phone_number.focus();
            dataForm.phone_number.classList.add('is-invalid');
            formFeedback.phone_number.textContent = "This field is required";
        }
        else {
            checkUserPhone();
        }
    }
})

dataForm.email_address.addEventListener('change', (e) => {
    if (dataForm.email_address.value === '') {
        dataForm.email_address.focus();
        dataForm.email_address.classList.add('is-invalid');
        formFeedback.email_address.textContent = "This field is required";
    } else {
        checkUserEmail();
    }
})

dataForm.email_address.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        if (dataForm.email_address.value === '') {
            dataForm.email_address.focus();
            dataForm.email_address.classList.add('is-invalid');
            formFeedback.email_address.textContent = "This field is required";
        }
        else {
            checkUserEmail();
        }
    }
})

function validasi() {
    let isValid = true;
    const requiredElement = document.querySelectorAll("[required]");
    if (requiredElement.length > 0) {
        requiredElement.forEach(element => {
            const feedback = element.parentElement.querySelector('.invalid-feedback');
            if (element.value.trim() === '') {
                element.classList.add('is-invalid');
                if (feedback) {
                    feedback.textContent = "This field is required";
                }

                isValid = false
            } else {
                element.classList.remove('is-invalid');
                if (feedback) {
                    feedback.isValid = '';
                }
            }
        })
    }

    // Chek username
    checkUsername();

    // Check nomor telepon
    checkUserPhone()

    // Check alamat email
    checkUserEmail();

    // Check password

    return isValid;
}