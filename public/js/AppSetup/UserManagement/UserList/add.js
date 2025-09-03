const formData = document.getElementById('formData');
const buttons = {
    back: document.getElementById('btnBack'),
    save: document.getElementById('btnSave'),
    cancel: document.getElementById('btnCancel')
}

const dataForm = {
    user_name: document.getElementById('user_name'),
    full_name: document.getElementById('full_name'),
    phone_number: document.getElementById('phone_number'),
    email_address: document.getElementById('email_address'),
    user_password: document.getElementById('user_password'),
    user_level: document.getElementById('user_level'),
    user_image: document.getElementById('user_image')
}

const formFeedback = {
    user_name: document.getElementById('feedback_username'),
    full_name: document.getElementById('feedback_fullname'),
    phone_number: document.getElementById('feedback_phone'),
    email_address: document.getElementById('feedback_email'),
    user_password: document.getElementById('feedback_password'),
    user_image: document.getElementById('feedback_image')
}

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

function checkUsername() {
    if (dataForm.user_name.value === '') {
        dataForm.user_name.classList.add('is-invalid');
        formFeedback.user_name.textContent = "This field is required";
    } else {
        try {
            fetchData(baseurl + '/users/check_user', 'POST', JSON.stringify({ username: dataForm.user_name.value }))
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
        fetchData(baseurl + '/users/check_phone', 'POST', JSON.stringify({ user_phone: dataForm.phone_number.value }))
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
            fetchData(baseurl + '/users/check_email', 'POST', JSON.stringify({ user_email: dataForm.email_address.value }))
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

dataForm.user_name.addEventListener('change', (e) => {
    checkUsername();
})

dataForm.user_name.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        if (dataForm.user_name.value === '') {
            dataForm.user_name.classList.add('is-invalid');
            formFeedback.user_name.textContent = 'This field is required';
        } else {
            checkUsername();
        }
    }
})

dataForm.phone_number.addEventListener('change', (e) => {
    checkUserPhone();
})

dataForm.phone_number.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        if (dataForm.phone_number.value === '') {
            dataForm.phone_number.classList.add('is-invalid');
            formFeedback.phone_number.textContent = "This filed is required";
        } else {
            checkUserPhone();
        }
    }
})

dataForm.email_address.addEventListener('change', (e) => {
    if (isValidEmail(dataForm.email_address.value)) {
        dataForm.email_address.classList.remove('is-invalid');
        formFeedback.email_address.textContent = "";
        checkUserEmail();
    } else {
        dataForm.email_address.classList.add('is-invalid');
        formFeedback.email_address.textContent = "Invalid email address format";
        dataForm.email_address.focus()
    }
})

dataForm.email_address.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        if (dataForm.email_address.value === '') {
            dataForm.email_address.classList.add('is-invalid');
            formFeedback.email_address.textContent = 'This field is required';
        } else {
            if (isValidEmail(dataForm.email_address.value)) {
                dataForm.email_address.classList.remove('is-invalid');
                formFeedback.email_address.textContent = "";
                checkUserEmail();
            } else {
                dataForm.email_address.classList.add('is-invalid');
                formFeedback.email_address.textContent = "Invalid email address format";
                dataForm.email_address.focus()
            }
        }
    }
})

buttons.back.addEventListener('click', (e) => {
    loading();
    window.location.replace(baseurl + '/users');
});

buttons.save.addEventListener('click', (e) => {
    if (validasi()) {
        try {
            loading();
            fetchData(baseurl + '/users/save', 'POST', new FormData(formData))
                .then(result => { })
                .catch(err => {
                    pesanError(err.message);
                    hideLoading()
                })
        } catch (e) {
            pesanError(e.message);
            hideLoading();
        }
    }
})