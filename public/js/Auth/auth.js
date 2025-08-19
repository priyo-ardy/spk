const formAuth = document.getElementById('formAuth');
const buttons = {
    auth: document.getElementById('btnAuth'),
    cancel: document.getElementById('btnCancel'),
    reset: document.getElementById('btnReset')
}
const dataForm = {
    user_name: document.getElementById('username'),
    user_password: document.getElementById('password')
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

function prosesLogin() {

}