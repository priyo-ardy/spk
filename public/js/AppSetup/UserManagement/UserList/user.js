const buttons = {
    add: document.getElementById('btnAdd'),
    filter: document.getElementById('btnFilter'),
    refresh: document.getElementById('btnRefresh'),
    export: document.getElementById('btnExport')
}

buttons.add.addEventListener('click', (e) => {
    loading();
    window.location.replace(baseurl + '/users/add');
})