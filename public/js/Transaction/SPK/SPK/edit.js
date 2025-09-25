window.onload = () => {
  $(".summernote").summernote({
    height: 150, // set editor height
    minHeight: null, // set minimum height of editor
    maxHeight: null, // set maximum height of editor
  });
  if (document.getElementById("status") !== "0") {
    $(".summernote").summernote("disable");
  } else {
    $(".summernote").summernote();
  }
};

const formData = document.getElementById("formData");
const buttons = {
  back: document.getElementById("btnBack"),
  save: document.getElementById("btnSave"),
  submit: document.getElementById("btnSubmit"),
  undo: document.getElementById("btnUndo"),
  approve: document.getElementById("btnApprove"),
  un_approve: document.getElementById("btnUnApprove"),
  cancel: document.getElementById("btnCancel"),
  add: document.getElementById("btnAdd"),
  prev: document.getElementById("btnPrev"),
  next: document.getElementById("btnNext"),
};

const dataForm = {
  token: document.getElementById("data_token"),
  code: document.getElementById("data_code"),
  doc_type: document.getElementById("doc_type"),
  lokasi: document.getElementById("data_lokasi"),
  dept: document.getElementById("data_dept"),
  pelapor: document.getElementById("data_pelapor"),
  tanggal: document.getElementById("data_tanggal"),
  material: document.getElementById("data_material"),
  model: document.getElementById("data_model"),
  mold: document.getElementById("data_mold"),
  tipe_equipment: document.getElementById("tipe_equipment"),
  leader: document.getElementById("data_leader"),
  defect: document.getElementById("data_defect"),
  sub_defect: document.getElementById("data_sub_defect"),
  berulang: document.getElementById("data_berulang"),
  posisi: document.getElementById("data_posisi"),
  repair: document.getElementById("data_repair"),
  image: document.getElementById("data_image"),
  img_preview: document.getElementById("img_preview"),
  keterangan: document.getElementById("data_keterangan"),
};

buttons.back.addEventListener("click", (e) => {
  loading();
  window.location.replace(baseurl + "/spk");
});

buttons.add.addEventListener("click", (e) => {
  loading();
  window.location.replace(baseurl + "/spk/add");
});

buttons.cancel.addEventListener("click", (e) => {
  loading();
  window.location.reload();
});

dataForm.doc_type.onchange = () => {
  try {
    dataForm.model.value = "";
    dataForm.mold.value = "";
    dataForm.tipe_equipment.value = "";
    $(dataForm.tipe_equipment).trigger("change");
    dataForm.material.innerHTML = '<option value="">-- Choose --</option>';

    if (dataForm.doc_type.value === "") {
      dataForm.material.innerHTML = '<option value="">-- Choose --</option>';
    } else {
      if (dataForm.doc_type.value == "1") {
        dataForm.tipe_equipment.setAttribute("disabled", true);
      } else {
        dataForm.tipe_equipment.removeAttribute("disabled");
      }

      fetchData(
        baseurl + "/material/generate_material",
        "POST",
        JSON.stringify({ kategori: dataForm.doc_type.value })
      )
        .then((result) => {
          if (result.data.length > 0) {
            result.data.forEach((item) => {
              const option = document.createElement("option");
              option.value = item.id;
              option.textContent = item.code + " - " + item.name;
              dataForm.material.appendChild(option);
            });
          }
        })
        .catch((err) => {
          dataForm.material.innerHTML =
            '<option value="">-- Choose --</option>';
        });
    }
  } catch (e) {
    pesanError(e.message);
  }
};

dataForm.defect.onchange = () => {
  try {
    if (dataForm.defect.value === "") {
      dataForm.sub_defect.innerHTML = '<option value="">-- Choose --</option>';
    } else {
      fetchData(
        baseurl + "/sub_defect/get_list",
        "POST",
        JSON.stringify({ token: dataForm.defect.value })
      )
        .then((result) => {
          dataForm.sub_defect.innerHTML =
            '<option value="">-- Choose --</option>';
          if (result.data.length > 0) {
            result.data.forEach((item) => {
              const option = document.createElement("option");
              option.value = item.token;
              option.textContent = item.name;

              dataForm.sub_defect.appendChild(option);
            });
          }
        })
        .catch((err) => {
          dataForm.sub_defect.innerHTML =
            '<option value="">-- Choose --</option>';
        });
    }
  } catch (e) {
    pesanError(e.message);
  }
};

dataForm.material.onchange = () => {
  try {
    if (dataForm.material.value === "") {
      dataForm.model.value = "";
      dataForm.mold.value = "";
      dataForm.tipe_equipment.value = "";
      $(dataForm.tipe_equipment).trigger("change");
    } else {
      fetchData(
        baseurl + "/material/get_material",
        "POST",
        JSON.stringify({
          kategori: dataForm.doc_type.value,
          token: dataForm.material.value,
        })
      ).then((result) => {
        dataForm.model.value = result.data.model;
        switch (dataForm.doc_type.value) {
          case "1":
            dataForm.mold.value = result.data.code;
            break;
          case "2":
            dataForm.mold.value = result.data.no_mesin;
            break;
        }
      });
    }
  } catch (e) {
    pesanError(e.message);
  }
};

function validasi() {
  let isValid = true;
  const requiredElement = document.querySelectorAll("[required]");
  if (requiredElement.length > 0) {
    requiredElement.forEach((element) => {
      const feedback = element.parentNode.querySelector(".invalid-feedback");
      if (element.value.trim() === "") {
        element.classList.add("is-invalid");
        feedback.textContent = "This field is required";
        isValid = false;
      } else {
        element.classList.remove("is-invalid");
        feedback.textContent = "";
      }
    });
  }

  if (dataForm.doc_type.value !== "1") {
    if (dataForm.tipe_equipment.value === "") {
      dataForm.tipe_equipment.classList.add("is-invalid");
      dataForm.tipe_equipment.parentNode.querySelector(
        ".invalid-feedback"
      ).textContent = "This field is required";
      isValid = false;
    } else {
      dataForm.tipe_equipment.classList.remove("is-invalid");
    }
  }

  return isValid;
}

buttons.save.addEventListener("click", (e) => {
  if (validasi()) {
    try {
      loading();
      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: "btn btn-primary rounded-0",
          cancelButton: "btn btn-secondary rounded-0",
        },
      });

      swalWithBootstrapButtons
        .fire({
          title: "Confirmation !",
          text: "Apakah anda yakin akan mengupdate data ini ?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: '<i class="bi bi-check"></i>&ensp;Yes',
          cancelButtonText: '<i class="bi bi-x"></i>&ensp;Cancel',
          reverseButtons: true,
        })
        .then((result) => {
          if (result.isConfirmed) {
            Swal.fire({
              title: "Please wait ...",
              timerProgressBar: true,
              allowEscapeKey: false,
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              },
            }).then(
              fetchData(baseurl + "/spk/update", "POST", new FormData(formData))
                .then((result) => {
                  pesanSukses(result.message);
                  setTimeout(() => {
                    window.location.reload();
                  }, 1500);
                })
                .catch((err) => {
                  pesanError(err.message);
                })
            );
          }
        });
    } catch (e) {
      pesanError(e.message);
    }
  }
});

function deleteImage(token) {
  try {
    loading();
    hapusData("/spk/delete_image", token);
  } catch (e) {
    pesanError(e.message);
    hideLoading();
  }
}

function kunciForm() {
  const inputElement = document.querySelectorAll("input");
  const selectElement = document.querySelectorAll("select");
}

buttons.submit.addEventListener("click", (e) => {
  try {
    loading();
    fetchData(
      baseurl + "/spk/submit",
      "POST",
      JSON.stringify({ token: dataForm.token.value })
    )
      .then((result) => {
        pesanSukses(result.message);
        window.location.reload();
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

buttons.undo.addEventListener("click", (e) => {
  try {
    loading();
    fetchData(
      baseurl + "/spk/undo",
      "POST",
      JSON.stringify({ token: dataForm.token.value })
    )
      .then((result) => {
        pesanSukses(result.message);
        window.location.reload();
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

buttons.approve.addEventListener("click", (e) => {
  try {
    loading();
    fetchData(
      baseurl + "/spk/approve",
      "POST",
      JSON.stringify({ token: dataForm.token.value })
    )
      .then((result) => {
        pesanSukses(result.message);
        window.location.reload();
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

buttons.un_approve.addEventListener("click", (e) => {
  try {
    loading();
    fetchData(
      baseurl + "/spk/un_approve",
      "POST",
      JSON.stringify({ token: dataForm.token.value })
    )
      .then((result) => {
        pesanSukses(result.message);
        window.location.reload();
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

buttons.prev.addEventListener("click", (e) => {
  try {
    loading();
    fetchData(
      baseurl + "/spk/prev",
      "POST",
      JSON.stringify({ code: dataForm.code.value })
    )
      .then((result) => {
        window.location.replace(baseurl + "/spk/show/" + result.data.token);
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
      baseurl + "/spk/next",
      "POST",
      JSON.stringify({ code: dataForm.code.value })
    )
      .then((result) => {
        window.location.replace(baseurl + "/spk/show/" + result.data.token);
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
