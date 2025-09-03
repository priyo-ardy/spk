const formData = document.getElementById('formData');
const buttons = {
    back: document.getElementById('btnBack'),
    save: document.getElementById('btnSave'),
    cancel: document.getElementById('btnCancel'),
}

buttons.back.addEventListener('click', (e) => {
    loading();
    window.location.replace(baseurl + '/spk');
})