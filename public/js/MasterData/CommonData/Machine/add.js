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
  mesin: document.getElementById("id_mesin"),
  nama: document.getElementById("id_nama"),
  spesifikasi: document.getElementById("id_spesifikasi"),
  brand: document.getElementById("id_brand"),
  serial: document.getElementById("id_serial"),
  tonnage: document.getElementById("id_tonnage"),
  rate: document.getElementById("id_rate"),
  mfgDate: document.getElementById("id_tanggal"),
  purchaseDate: document.getElementById("id_beli"),
  remark: document.getElementById("id_keterangan"),
};

buttons.back.addEventListener("click", (e) => {
  loading();
  window.location.replace(baseurl + "/machine");
});

function resetForm() {
  formData.reset();
  $(dataForm.workshop).trigger("change");
  $(dataForm.tonnage).trigger("change");
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
      fetchData(baseurl + "/machine/save", "POST", new FormData(formData))
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
