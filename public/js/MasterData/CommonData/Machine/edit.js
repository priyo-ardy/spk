window.onload = () => {
  $(".summernote").summernote({
    height: 300,
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

function bukaForm() {
  buttons.back.setAttribute("hidden", true);
  buttons.edit.setAttribute("hidden", true);
  buttons.prev.setAttribute("hidden", true);
  buttons.next.setAttribute("hidden", true);
  buttons.add.setAttribute("hidden", true);

  buttons.update.removeAttribute("hidden");
  buttons.cancel.removeAttribute("hidden");

  dataForm.workshop.removeAttribute("disabled");
  dataForm.tonnage.removeAttribute("disabled");
  dataForm.mfgDate.classList.remove("bg-body-secondary");
  dataForm.purchaseDate.classList.remove("bg-body-secondary");
  dataForm.mesin.classList.remove("bg-body-secondary");
  dataForm.nama.classList.remove("bg-body-secondary");
  dataForm.spesifikasi.classList.remove("bg-body-secondary");
  dataForm.brand.classList.remove("bg-body-secondary");
  dataForm.serial.classList.remove("bg-body-secondary");
  dataForm.rate.classList.remove("bg-body-secondary");
  dataForm.mesin.removeAttribute("readonly");
  dataForm.nama.removeAttribute("readonly");
  dataForm.spesifikasi.removeAttribute("readonly");
  dataForm.brand.removeAttribute("readonly");
  dataForm.serial.removeAttribute("readonly");
  dataForm.rate.removeAttribute("readonly");
  $(".summernote").summernote("enable");
}

buttons.add.addEventListener("click", (e) => {
  loading();
  window.location.replace(baseurl + "/machine/add");
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

  let requiredElement = document.querySelectorAll("[required]");

  if (dataForm.token.value.trim() === "") {
    isValid = false;
    pesanWarning("Machine token is required");
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
      fetchData(baseurl + "/machine/update", "POST", new FormData(formData))
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
      baseurl + "/machine/prev",
      "POST",
      JSON.stringify({ code: dataForm.code.value })
    )
      .then((result) => {
        window.location.replace(
          baseurl + "/machine/details/" + result.data.token
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
      baseurl + "/machine/next",
      "POST",
      JSON.stringify({ code: dataForm.code.value })
    )
      .then((result) => {
        window.location.replace(
          baseurl + "/machine/details/" + result.data.token
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
