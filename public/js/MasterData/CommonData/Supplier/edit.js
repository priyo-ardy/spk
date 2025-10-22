window.onload = () => {
  $(".summernote").summernote({
    height: 300,
    minHeight: null,
    maxHeight: null,
  });

  $(".summernote").summernote("disable");
};

const formData = document.getElementById("formData");
const dataForm = {
  token: document.getElementById("data_token"),
  code: document.getElementById("data_code"),
  name: document.getElementById("data_name"),
  alamat: document.getElementById("data_alamat"),
  phone: document.getElementById("data_phone"),
  email: document.getElementById("data_email"),
  contact: document.getElementById("data_contact"),
  remark: document.getElementById("data_remark"),
};

const buttons = {
  back: document.getElementById("btnBack"),
  update: document.getElementById("btnSave"),
  edit: document.getElementById("btnEdit"),
  cancel: document.getElementById("btnCancel"),
  prev: document.getElementById("btnPrev"),
  next: document.getElementById("btnNext"),
};

buttons.back.addEventListener("click", (e) => {
  loading();
  window.location.replace(baseurl + "/supplier");
});

function bukaForm() {
  dataForm.name.removeAttribute("readonly");
  dataForm.alamat.removeAttribute("readonly");
  dataForm.phone.removeAttribute("readonly");
  dataForm.email.removeAttribute("readonly");
  dataForm.contact.removeAttribute("readonly");
  dataForm.name.classList.remove("bg-secondary-subtle");
  dataForm.alamat.classList.remove("bg-secondary-subtle");
  dataForm.phone.classList.remove("bg-secondary-subtle");
  dataForm.email.classList.remove("bg-secondary-subtle");
  dataForm.contact.classList.remove("bg-secondary-subtle");

  $(".summernote").summernote("enable");

  dataForm.name.focus();

  buttons.update.removeAttribute("hidden");
  buttons.cancel.removeAttribute("hidden");
  buttons.edit.setAttribute("hidden", true);
  buttons.back.setAttribute("hidden", true);
  buttons.prev.setAttribute("hidden", true);
  buttons.next.setAttribute("hidden", true);
}

buttons.edit.addEventListener("click", (e) => {
  bukaForm();
});

buttons.cancel.addEventListener("click", (e) => {
  loading();
  window.location.reload();
});

function validasi() {
  let isValid = true;

  if (dataForm.token.value === "") {
    isValid = false;
    pesanWarning("Supplier token is required");
  }

  if (dataForm.code.value === "") {
    isValid = false;
    dataForm.code.classList.add("is-invalid");
  }

  if (dataForm.name.value === "") {
    isValid = false;
    dataForm.name.classList.add("is-invalid");
  }

  return isValid;
}

buttons.update.addEventListener("click", (e) => {
  if (validasi()) {
    try {
      loading();
      fetchData(baseurl + "/supplier/update", "POST", new FormData(formData))
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
      baseurl + "/supplier/prev",
      "POST",
      JSON.stringify({ code: dataForm.code.value })
    )
      .then((result) => {
        window.location.replace(
          baseurl + "/supplier/show/" + result.data.token
        );
      })
      .catch((err) => {
        pesanError(err.message);
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
      baseurl + "/supplier/next",
      "POST",
      JSON.stringify({ code: dataForm.code.value })
    )
      .then((result) => {
        window.location.replace(
          baseurl + "/supplier/show/" + result.data.token
        );
      })
      .catch((err) => {
        pesanError(err.message);
        hideLoading();
      });
  } catch (e) {
    pesanError(e.message);
    hideLoading();
  }
});
