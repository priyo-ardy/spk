window.onload = () => {
  $(".summernote").summernote({
    height: 150, // set editor height
    minHeight: null, // set minimum height of editor
    maxHeight: null, // set maximum height of editor
  });
  $(".summernote").summernote("disable");
};

const formData = document.getElementById("formData");
const buttons = {
  back: document.getElementById("btnBack"),
  save: document.getElementById("btnSave"),
  submit: document.getElementById("btnSubmit"),
  approve: document.getElementById("btnApprove"),
  cancel: document.getElementById("btnCancel"),
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

  if (dataForm.doc_type.value !== "1" || dataForm.tipe_equipment.value !== "") {
    dataForm.tipe_equipment.classList.add("is-invalid");
    dataForm.tipe_equipment.parentNode.querySelector(
      ".invalid-feedback"
    ).textContent = "This field is required";
    isValid = false;
  } else {
    dataForm.tipe_equipment.classList.remove("is-invalid");
  }

  return isValid;
}

buttons.save.addEventListener("click", (e) => {});

buttons.submit.addEventListener("click", (e) => {});

buttons.approve.addEventListener("click", (e) => {});
