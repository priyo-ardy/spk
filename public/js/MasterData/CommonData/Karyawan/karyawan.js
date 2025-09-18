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
  name: document.getElementById("data_name"),
  NIK: document.getElementById("data_nik"),
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
      url: baseurl + "/karyawan/table",
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
  if (dataForm.NIK.value === "") {
    dataForm.NIK.classList.add("is-invalid");
  } else if (dataForm.name.value === "") {
    dataForm.name.classList.add("is-invalid");
    dataForm.NIK.classList.remove("is-invalid");
  } else {
    dataForm.NIK.classList.remove("is-invalid");
    dataForm.name.classList.remove("is-invalid");

    try {
      loading();
      fetchData(baseurl + "/karyawan/save", "POST", new FormData(formData))
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
    fetchData(
      baseurl + "/karyawan/get",
      "POST",
      JSON.stringify({ token: token })
    )
      .then((result) => {
        dataForm.token.value = result.data.token;
        dataForm.NIK.value = result.data.NIK;
        dataForm.name.value = result.data.name;
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
    pesanWarning("Employee token is required");
    isValid = false;
  }

  if (dataForm.NIK.value === "") {
    dataForm.NIK.classList.add("is-invalid");
    isValid = false;
  } else {
    dataForm.NIK.classList.remove("is-invalid");
  }

  if (dataForm.name.value === "") {
    dataForm.name.classList.add("is-invalid");
    isValid = false;
  } else {
    dataForm.name.classList.remove("is-invalid");
  }

  return isValid;
}

buttons.update.addEventListener("click", (e) => {
  if (validasiUpdate()) {
    try {
      loading();
      fetchData(baseurl + "/karyawan/update", "POST", new FormData(formData))
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
    hapusData("/karyawan/delete", token);
  } catch (e) {
    pesanError(e.message);
  }
}
