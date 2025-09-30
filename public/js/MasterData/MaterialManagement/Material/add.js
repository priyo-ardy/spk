const formData = document.getElementById("formData");
const buttons = {
  back: document.getElementById("btnBack"),
  save: document.getElementById("btnSave"),
  cancel: document.getElementById("btnCancel"),
};

window.onload = () => {
  $(".summernote").summernote({
    height: 300,
    minHeight: null,
    maxHeight: null,
  });
};

const dataForm = {
  code: document.getElementById("id_code"),
  workshop: document.getElementById("id_workshop"),
  kategori: document.getElementById("id_kategori"),
  name: document.getElementById("id_name"),
  materialCustName: document.getElementById("id_cust_name"),
  id_color: document.getElementById("id_color"),
  unit: document.getElementById("id_uom"),
  keterangan: document.getElementById("id_keterangan"),
};

dataForm.kategori.onchange = function () {
  dataForm.code.value = "";
  if (dataForm.kategori.value !== "d40102ed-f432-4ce6-af65-6814b4cbc974") {
    dataForm.code.setAttribute("readonly", true);
    dataForm.code.classList.add("bg-body-secondary");
    dataForm.code.removeAttribute("required");
  } else {
    dataForm.code.removeAttribute("readonly");
    dataForm.code.classList.remove("bg-body-secondary");
    dataForm.code.setAttribute("required", true);
  }
};

buttons.back.addEventListener("click", (e) => {
  loading();
  window.location.replace(baseurl + "/material");
});

function resetForm() {
  formData.reset();
  $(dataForm.workshop).trigger("change");
  $(dataForm.kategori).trigger("change");
  $(dataForm.unit).trigger("change");
  $(".summernote").summernote("code", "");

  const invalidElement = document.querySelectorAll(".is-valid");
  if (invalidElement.length > 0) {
    invalidElement.forEach((element) => {
      element.classList.remove("is-invalid");
    });
  }
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
        isValid = false;
        element.classList.add("is-invalid");
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
      fetchData(baseurl + "/material/save", "POST", new FormData(formData))
        .then((result) => {
          pesanSukses(result.message);
          resetForm();
          hideLoading();
        })
        .catch((err) => {
          pesanError(err.message);
          hideLoading();
        });
    } catch (e) {
      pesanError(e.message);
    }
  }
});
