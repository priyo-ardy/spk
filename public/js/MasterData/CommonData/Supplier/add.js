const formData = document.getElementById("formData");
const buttons = {
  back: document.getElementById("btnBack"),
  save: document.getElementById("btnSave"),
  cancel: document.getElementById("btnCancel"),
};

const dataForm = {
  code: document.getElementById("data_code"),
  name: document.getElementById("data_name"),
  alamat: document.getElementById("data_alamat"),
  phone: document.getElementById("data_phone"),
  email: document.getElementById("data_email"),
  contact: document.getElementById("data_contact"),
  remark: document.getElementById("data_remark"),
};

window.onload = () => {
  $(".summernote").summernote({
    height: 300,
    minHeight: null,
    maxHeight: null,
  });
};

buttons.back.addEventListener("click", (e) => {
  loading();
  window.location.replace(baseurl + "/supplier");
});

function resetForm() {
  formData.reset();
  $(".summernote").summernote("code", "");
}
buttons.cancel.addEventListener("click", (e) => {
  resetForm();
});

function validasi() {
  let isValid = true;

  const requiredElement = document.querySelectorAll("[required]");
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

buttons.save.addEventListener("click", (e) => {
  if (validasi()) {
    try {
      loading();
      fetchData(baseurl + "/supplier/save", "POST", new FormData(formData))
        .then((result) => {
          pesanSukses(result.message);
          setTimeout(() => {
            window.location.replace(
              baseurl + "/supplier/show/" + result.data.token
            );
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
