window.onload = () => {
  $(".summernote").summernote({
    height: 150, // set editor height
    minHeight: null, // set minimum height of editor
    maxHeight: null, // set maximum height of editor
  });
};

const formData = document.getElementById("formData");
const buttons = {
  back: document.getElementById("btnBack"),
  save: document.getElementById("btnSave"),
  cancel: document.getElementById("btnCancel"),
};

const dataForm = {
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
  repair: document.getElementById("data_repair"),
  image: document.getElementById("data_image"),
  img_preview: document.getElementById("img_preview"),
  keterangan: document.getElementById("data_keterangan"),
  label_defect: document.getElementById("label_defect"),
  label_sub_defect: document.getElementById("label_sub_defect"),
};

buttons.back.addEventListener("click", (e) => {
  loading();
  window.location.replace(baseurl + "/spk");
});

function getMaterialList() {
  return fetchData(
    baseurl + "/material/generate_material",
    "POST",
    JSON.stringify({ kategori: dataForm.doc_type.value })
  )
    .then((result) => {
      if (result.data.length > 0) {
        let mold_no = "";
        result.data.forEach((item) => {
          if (dataForm.doc_type.value !== "1") {
            mold_no = " - " + item.nomor_mesin;
          }

          const option = document.createElement("option");
          option.value = item.id;
          option.textContent = item.code + " - " + item.name + mold_no;
          dataForm.material.appendChild(option);
        });
      }
    })
    .catch((err) => {
      dataForm.material.innerHTML = '<option value="">-- Choose --</option>';
      throw err;
    });
}

function getDefectList() {
  return fetchData(
    baseurl + "/defect/generate_defect",
    "POST",
    JSON.stringify({ kategori: dataForm.doc_type.value })
  )
    .then((result) => {
      if (result.data.length > 0) {
        result.data.forEach((item) => {
          const option = document.createElement("option");
          option.value = item.id;
          option.textContent = item.name;
          dataForm.defect.appendChild(option);
        });
      }
    })
    .catch((err) => {
      dataForm.defect.innerHTML = '<option value="">-- Choose --</option>';
      throw err;
    });
}

dataForm.doc_type.onchange = () => {
  try {
    dataForm.model.value = "";
    dataForm.mold.value = "";
    dataForm.tipe_equipment.value = "";
    $(dataForm.tipe_equipment).trigger("change");
    dataForm.material.innerHTML = '<option value="">-- Choose --</option>';
    dataForm.defect.innerHTML = '<option value="">-- Choose --</option>';
    dataForm.sub_defect.innerHTML = '<option value="">-- Choose --</option>';

    if (dataForm.doc_type.value === "") {
      dataForm.material.innerHTML = '<option value="">-- Choose --</option>';
      dataForm.defect.innerHTML = '<option value="">-- Choose --</option>';
      dataForm.sub_defect.innerHTML = '<option value="">-- Choose --</option>';
      dataForm.label_defect.innerHTML = `Defect&ensp;<strong class="text-danger">*</strong>`;
      dataForm.label_sub_defect.innerHTML = `Sub Defect&ensp;<strong class="text-danger">*</strong>`;
    } else {
      if (dataForm.doc_type.value == "1") {
        dataForm.tipe_equipment.setAttribute("disabled", true);
        dataForm.label_defect.innerHTML = `Defect&ensp;<strong class="text-danger">*</strong>`;
        dataForm.label_sub_defect.innerHTML = `Sub Defect&ensp;<strong class="text-danger">*</strong>`;
      } else {
        dataForm.tipe_equipment.removeAttribute("disabled");
        dataForm.label_defect.innerHTML = `Problem&ensp;<strong class="text-danger">*</strong>`;
        dataForm.label_sub_defect.innerHTML = `Sub Problem&ensp;<strong class="text-danger">*</strong>`;
      }

      getMaterialList();
      getDefectList();
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
            dataForm.mold.value = result.data.nomor_mesin;
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
      if (element.value == "") {
        element.classList.add("is-invalid");
        feedback.textContent = "This field is required";
        isValid = false;
      } else {
        element.classList.remove("is-invalid");
        if (feedback) {
          feedback.textContent = "";
        }
      }
    });
  }

  if (dataForm.doc_type.value !== "1") {
    if (dataForm.tipe_equipment.value == "") {
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

function resetForm() {
  formData.reset();
  $(dataForm.doc_type).trigger("change");
  $(dataForm.lokasi).trigger("change");
  $(dataForm.dept).trigger("change");
  $(dataForm.pelapor).trigger("change");
  dataForm.material.innerHTML = '<option value="">-- Choose --</option>';
  $(dataForm.tipe_equipment).trigger("change");
  $(dataForm.leader).trigger("change");
  $(dataForm.defect).trigger("change");
  dataForm.sub_defect.innerHTML = '<option value="">-- Choose --</option>';
  $(dataForm.berulang).trigger("change");
  $(dataForm.repair).trigger("change");
  dataForm.image.value = "";
  $(".summernote").summernote("code", "");

  const requiredElement = document.querySelectorAll(".is-invalid");
  if (requiredElement.length > 0) {
    requiredElement.forEach((element) => {
      element.classList.remove("is-invalid");
    });
  }

  const invalidFeedBack = document.querySelectorAll(".invalid-feedback");
  invalidFeedBack.textContent = "";
}

buttons.cancel.addEventListener("click", (e) => {
  resetForm();
});

buttons.save.addEventListener("click", (e) => {
  if (validasi()) {
    try {
      loading();
      fetchData(baseurl + "/spk/save", "POST", new FormData(formData))
        .then((result) => {
          pesanSukses(result.message);
          hideLoading();
          window.location.replace(baseurl + "/spk/show/" + result.data.token);
        })
        .catch((err) => {
          pesanError(err.message);
          hideLoading();
        });
    } catch (e) {
      pesanError(e.message);
    }
  } else {
    pesanError("Data belum lengkap");
  }
});
