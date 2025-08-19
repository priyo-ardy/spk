const formAuth = document.getElementById('formAuth');
const buttons = {
    auth: document.getElementById('btnAuth'),
    cancel: document.getElementById('btnCancel'),
    reset: document.getElementById('btnReset')
}
const dataForm = {
    user_name: document.getElementById('username'),
    user_password: document.getElementById('password'),
    pesan: document.getElementById('pesanLogin')
}

dataForm.user_name.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        if (dataForm.user_name.value == '') {
            dataForm.user_name.classList.add('is-invalid');
        } else {
            dataForm.user_name.classList.remove('is-invalid');
            dataForm.user_password.focus();
        }
    }
});

dataForm.user_password.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        if (dataForm.user_name.value.trim() === '') {
            dataForm.user_name.classList.add('is-invalid');
            dataForm.user_name.focus();
        } else {
            dataForm.user_name.classList.remove('is-invalid');
        }

        if (dataForm.user_password.value.trim() === '') {
            dataForm.user_password.classList.add('is-invalid');
        } else {
            dataForm.user_password.classList.remove('is-invalid');
            prosesLogin();
        }
    }
})

function validasi() {
    let isValid = true;

    if (dataForm.user_name.value.trim === '') {
        dataForm.user_name.classList.add('is-invalid');
        isValid = false;
    } else {
        dataForm.user_name.classList.remove('is-invalid');
    }

    if (dataForm.user_password.value.trim === '') {
        dataForm.user_password.classList.add('is-invalid');
        isValid = false;
    } else {
        dataForm.user_password.classList.remove('is-invalid');
    }

    return isValid;
}

function resetForm() {
    formAuth.reset();
    dataForm.user_name.removeAttribute('readonly');
    dataForm.user_password.removeAttribute('readonly');
    buttons.auth.removeAttribute('disabled');
    buttons.auth.innerHTML = '<i class="bi bi-box-arrow-in-right"></i>&ensp;Log in';
    dataForm.user_name.focus();
}

async function prosesLogin() {
    if (validasi()) {
        try {
            dataForm.user_name.setAttribute('readonly', true);
            dataForm.user_password.setAttribute('readonly', true);
            buttons.auth.setAttribute('disabled', true);
            buttons.auth.innerHTML = `
                    <div div class="spinner-border text-light" role="status" >
                        <span class="sr-only">Loading...</span>
                    </div>
                `;
            const response = fetch(baseurl + '/proses', {
                method: 'POST',
                body: new FormData(formAuth)
            })
        } catch (e) {
            dataForm.pesan.textContent = e.message;
            resetForm();
        }
    }
}