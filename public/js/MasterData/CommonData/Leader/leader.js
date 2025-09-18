const formData = document.getElementById("formData");
const buttons = {
  cancel: document.getElementById("btnCancel"),
  update: document.getElementById("btnUpdate"),
  save: document.getElementById("btnSave"),
  refresh: document.getElementById("btnRefresh"),
};

const dataForm = {
  token: document.getElementById("id_token"),
  NIK: document.getElementById("id_NIK"),
  nama: document.getElementById("id_nama"),
  remark: document.getElementById("id_remark"),
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
      url: baseurl + "/leader/table",
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

dataForm.NIK.onchange = async (e) => {
  if (dataForm.NIK.value == "") {
    dataForm.nama.value == "";
  } else {
    try {
      await fetchData(
        baseurl + "/leader/namaKaryawan",
        "POST",
        JSON.stringify({ NIK: dataForm.NIK.value })
      )
        .then((result) => {
          dataForm.nama.value = result.data.nama;
        })
        .catch((err) => {
          pesanError(err.message);
        });
    } catch (e) {
      pesanError(e.message);
    }
  }
};

buttons.save.addEventListener("click", (e) => {
  if (dataForm.NIK.value === "") {
    dataForm.NIK.classList.add("is-invalid");
  } else if (dataForm.nama.value === "") {
    dataForm.nama.classList.add("is-invalid");
    dataForm.NIK.classList.remove("is-invalid");
  } else {
    dataForm.nama.classList.remove("is-invalid");
    dataForm.NIK.classList.remove("is-invalid");

    try {
      loading();
      fetchData(baseurl + "/leader/save", "POST", new FormData(formData))
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
    fetchData(baseurl + "/leader/get", "POST", JSON.stringify({ token: token }))
      .then((result) => {
        dataForm.token.value = result.data.token;
        dataForm.NIK.value = result.data.NIK;
        dataForm.nama.value = result.data.nama;
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
    pesanWarning("Sub defect token is required");
    isValid = false;
  }

  if (dataForm.NIK.value.trim() === "") {
    pesanWarning("NIK is required");
    isValid = false;
  }

  if (dataForm.nama.value === "") {
    dataForm.nama.classList.add("is-invalid");
    isValid = false;
  } else {
    dataForm.nama.classList.remove("is-invalid");
  }

  return isValid;
}

buttons.update.addEventListener("click", (e) => {
  if (validasiUpdate()) {
    try {
      loading();
      fetchData(baseurl + "/leader/update", "POST", new FormData(formData))
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
    hapusData("/leader/delete", token);
  } catch (e) {
    pesanError(e.message);
  }
}
