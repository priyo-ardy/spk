const formData = document.getElementById("formData");
const buttons = {
  cancel: document.getElementById("btnCancel"),
  update: document.getElementById("btnUpdate"),
  save: document.getElementById("btnSave"),
  download: document.getElementById("btnDownload"),
  refresh: document.getElementById("btnRefresh"),
};

const dataForm = {
  token: document.getElementById("data_token"),
  code: document.getElementById("data_code"),
  name: document.getElementById("data_name"),
  remark: document.getElementById("data_remark"),
  symbol: document.getElementById("data_simbol"),
};

loadTable();

function loadTable() {
  $("#dataTable").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    bDestroy: true,
    search: {
      return: true,
    },
    order: [],
    ajax: {
      url: baseurl + "/satuan/table",
      type: "POST",
      data: "raw",
      action: "calls",
    },
    deferRender: true,
    columnDefs: [
      {
        targets: 0,
        orderable: false,
      },
    ],
  });
}

function refreshTable() {
  $("#dataTable").DataTable().ajax.reload(null, false);
}

buttons.refresh.addEventListener("click", (e) => {
  refreshTable(e);
});

function resetForm() {
  formData.reset();
  const invalidElement = document.querySelectorAll(".is-invalid");
  if (invalidElement.length > 0) {
    invalidElement.forEach((element) => {
      element.classList.remove("is-invalid");
    });
  }

  buttons.save.removeAttribute("hidden");
  buttons.update.setAttribute("hidden", true);
}

buttons.cancel.addEventListener("click", (e) => {
  resetForm();
});

buttons.save.addEventListener("click", (e) => {
  if (dataForm.name.value === "") {
    dataForm.name.classList.add("is-invalid");
  } else if (dataForm.symbol.value === "") {
    dataForm.name.classList.remove("is-invalid");
    dataForm.symbol.classList.add("is-invalid");
  } else {
    dataForm.name.classList.remove("is-invalid");
    dataForm.symbol.classList.remove("is-invalid");

    try {
      loading();
      fetchData(baseurl + "/satuan/save", "POST", new FormData(formData))
        .then((result) => {
          pesanSukses(result.message);
          resetForm();
          refreshTable();
          hideLoading();
        })
        .catch((err) => {
          pesanError(err.message);
          hideLoading();
        });
    } catch (e) {
      pesanError(e.message);
      hideLoading();
    }
  }
});

function getData(token) {
  try {
    loading();
    fetchData(baseurl + "/satuan/get", "POST", JSON.stringify({ token: token }))
      .then((result) => {
        dataForm.token.value = result.data.token;
        dataForm.code.value = result.data.code;
        dataForm.name.value = result.data.name;
        dataForm.symbol.value = result.data.simbol;
        dataForm.remark.value = result.data.remark;
        buttons.save.setAttribute("hidden", true);
        buttons.update.removeAttribute("hidden");

        hideLoading();
      })
      .catch((err) => {
        pesanError(err.message);
        hideLoading();
      });
  } catch (e) {
    pesanError(e.message);
    hideLoading();
  }
}

function validasiUpdate() {
  let isValid = true;

  if (dataForm.token.value.trim() === "") {
    pesanWarning("Defect token is required");
    isValid = false;
  }

  if (dataForm.code.value === "") {
    dataForm.code.classList.add("is-invalid");
    isValid = false;
  } else {
    dataForm.code.classList.remove("is-invalid");
  }

  if (dataForm.name.value.trim() === "") {
    dataForm.name.classList.add("is-invalid");
    isValid = false;
  } else {
    dataForm.name.classList.remove("is-invalid");
  }

  if (dataForm.symbol.value.trim() === "") {
    dataForm.symbol.classList.add("is-invalid");
  } else {
    dataForm.symbol.classList.remove("is-invalid");
  }

  return isValid;
}

buttons.update.addEventListener("click", (e) => {
  if (validasiUpdate()) {
    try {
      loading();
      fetchData(baseurl + "/satuan/update", "POST", new FormData(formData))
        .then((result) => {
          pesanSukses(result.message);
          resetForm();
          refreshTable();
          hideLoading();
        })
        .catch((err) => {
          pesanError(err.message);
          hideLoading();
        });
    } catch (e) {
      pesanError(e.message);
      hideLoading();
    }
  }
});

function deleteData(token) {
  try {
    hapusData("/satuan/delete", token);
  } catch (e) {
    pesanError(e.message);
  }
}
