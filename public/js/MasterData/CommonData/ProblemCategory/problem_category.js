const formData = document.getElementById("formData");

const inputForm = {
  token: document.getElementById("data_token"),
  category: document.getElementById("data_kategori"),
  name: document.getElementById("data_name"),
  remark: document.getElementById("data_remark"),
};

const buttons = {
  cancel: document.getElementById("btnCancel"),
  update: document.getElementById("btnUpdate"),
  save: document.getElementById("btnSave"),
  refresh: document.getElementById("btnRefresh"),
  export: document.getElementById("btnDownload"),
};

buttons.refresh.addEventListener("click", (e) => {
  refreshTable();
});

function resetForm() {
  formData.reset();
  inputForm.category.value = "";
  inputForm.category.dispatchEvent(new Event("change"));

  buttons.update.setAttribute("hidden", true);
  buttons.save.removeAttribute("hidden");
}

window.onload = () => {
  loadTable();
};
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
      url: baseurl + "/problem_category/table",
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

function validasi() {
  let isValid = true;

  if (inputForm.category.value === "") {
    isValid = false;
    inputForm.category.classList.add("is-invalid");
    inputForm.category.parentNode.querySelector(
      ".invalid-feedback"
    ).textContent = "This field is required";
  } else {
    inputForm.category.classList.remove("is-invalid");
  }

  if (inputForm.name.value === "") {
    isValid = false;
    inputForm.name.classList.add("is-invalid");
    inputForm.name.parentNode.querySelector(".invalid-feedback").textContent =
      "This field is required";
  } else {
    inputForm.name.classList.remove("is-invalid");
  }

  return isValid;
}

buttons.save.addEventListener("click", (e) => {
  if (validasi()) {
    try {
      loading();
      fetchData(
        baseurl + "/problem_category/save",
        "POST",
        new FormData(formData)
      )
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

buttons.cancel.addEventListener("click", (e) => {
  resetForm();
});

function getData(token) {
  try {
    loading();
    fetchData(baseurl + "/problem_category/get/" + token, "get")
      .then((result) => {
        inputForm.token.value = result.data.token;
        inputForm.category.value = result.data.category;
        inputForm.category.dispatchEvent(new Event("change"));
        inputForm.name.value = result.data.name;
        inputForm.remark.value = result.data.remark;
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

  if (inputForm.token.value == "") {
    isValid = false;
  }

  if (!validasi()) {
    isValid = false;
  }

  return isValid;
}

buttons.update.addEventListener("click", (e) => {
  if (validasiUpdate()) {
    try {
      loading();
      fetchData(
        baseurl + "/problem_category/update",
        "POST",
        new FormData(formData)
      )
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
    hapusData("/problem_category/delete", token);
  } catch (e) {
    pesanError(e.message);
  }
}
