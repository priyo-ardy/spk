window.onload = () => {
  $(".summernote").summernote({
    height: 30,
    minHeight: null,
    maxHeight: null,
  });

  $(".summernote").summernote("disable");
};

const formData = document.getElementById("formData");

const buttons = {
  back: document.getElementById("btnBack"),
  update: document.getElementById("btnUpdate"),
  add: document.getElementById("btnAdd"),
  edit: document.getElementById("btnEdit"),
  cancel: document.getElementById("btnCancel"),
  prev: document.getElementById("btnPrev"),
  next: document.getElementById("btnNext"),
};

const dataForm = {
  token: document.getElementById("id_token"),
  code: document.getElementById("id_code"),
  workshop: document.getElementById("id_workshop"),
  kategori: document.getElementById("id_kategori"),
  name: document.getElementById("id_name"),
  materialCustName: document.getElementById("id_cust_name"),
  id_color: document.getElementById("id_color"),
  unit: document.getElementById("id_uom"),
  keterangan: document.getElementById("id_keterangan"),
};

buttons.back.addEventListener("click", (e) => {
  loading();
  window.location.replace(baseurl + "/material");
});

function bukaForm() {
  buttons.back.setAttribute("hidden", true);
  buttons.edit.setAttribute("hidden", true);
  buttons.add.setAttribute("hidden", true);
  buttons.prev.setAttribute("hidden", true);
  buttons.next.setAttribute("hidden", true);

  buttons.update.removeAttribute("hidden");
  buttons.cancel.removeAttribute("hidden");

  dataForm.workshop.removeAttribute("disabled");
  dataForm.kategori.removeAttribute("disabled");
  dataForm.unit.removeAttribute("disabled");
  dataForm.name.removeAttribute("readonly");
  dataForm.materialCustName.removeAttribute("readonly");
  dataForm.id_color.removeAttribute("readonly");
  dataForm.name.classList.remove("bg-body-secondary");
  dataForm.materialCustName.classList.remove("bg-body-secondary");
  dataForm.id_color.classList.remove("bg-body-secondary");

  $(".summernote").summernote("enable");

  if (dataForm.kategori.value == "d40102ed-f432-4ce6-af65-6814b4cbc974") {
    dataForm.code.removeAttribute("readonly");
    dataForm.code.classList.remove("bg-body-secondary");
  }
}

buttons.add.addEventListener("click", (e) => {
  loading();
  window.location.replace(baseurl + "/material/add");
});

buttons.edit.addEventListener("click", (e) => {
  bukaForm();
});

buttons.cancel.addEventListener("click", (e) => {
  loading();
  window.location.reload();
});

function validasi() {
  let isValid = true;

  let requiredElement = document.querySelectorAll("required");

  if (dataForm.token.value.trim() === "") {
    isValid = false;
    pesanWarning("Material code is required");
  }

  if (requiredElement.length > 0) {
    requiredElement.forEach((element) => {
      if (element.value.trim() === "") {
        element.classList.add("is-invalid");
        isValid = false;
      } else {
        element.classList.remove("is-invalid");
      }
    });
  }

  return isValid;
}

buttons.update.addEventListener("click", (e) => {
  if (validasi()) {
    try {
      loading();
      fetchData(baseurl + "/material/update", "POST", new FormData(formData))
        .then((result) => {
          pesanSukses(result.message);
          setTimeout(() => {
            window.location.reload();
          }, 1500);
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

buttons.prev.addEventListener("click", (e) => {
  try {
    loading();
    fetchData(
      baseurl + "/material/prev",
      "POST",
      JSON.stringify({ code: dataForm.code.value })
    )
      .then((result) => {
        window.location.replace(
          baseurl + "/material/details/" + result.data.token
        );
      })
      .catch((err) => {
        pesanWarning(err.message);
        hideLoading();
      });
  } catch (e) {
    pesanError(e.message);
    hideLoading();
  }
});

buttons.next.addEventListener("click", (e) => {
  try {
    loading();
    fetchData(
      baseurl + "/material/next",
      "POST",
      JSON.stringify({ code: dataForm.code.value })
    )
      .then((result) => {
        window.location.replace(
          baseurl + "/material/details/" + result.data.token
        );
      })
      .catch((err) => {
        pesanWarning(err.message);
        hideLoading();
      });
  } catch (e) {
    pesanError(e.message);
    hideLoading();
  }
});
