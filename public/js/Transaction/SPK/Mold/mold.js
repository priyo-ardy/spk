lightbox.option({
  resizeDuration: 200,
  wrapAround: true,
  showImageNumberLabel: true,
  alwaysShowNavOnTouchDevices: true,
});

const buttons = {
  add: document.getElementById("btnAdd"),
  filter: document.getElementById("btnFilter"),
  refresh: document.getElementById("btnRefresh"),
  export: document.getElementById("btnExport"),
};

const modalData = {
  title: document.getElementById("imageTitle"),
  body: document.getElementById("imageData"),
};

buttons.add.addEventListener("click", (e) => {
  loading();
  window.location.replace(baseurl + "/spk_mold/add");
});

loadTable();

function loadTable() {
  $("#dataTable").DataTable({
    processing: true,
    serverSide: true,
    responsive: false,
    bDestroy: true,
    autoWidth: false,
    scrollX: true,
    search: {
      return: true,
    },
    order: [],
    ajax: {
      url: baseurl + "/mold_spk/table",
      type: "POST",
      data: "raw",
      action: "calls",
    },
    error: function (xhr, error, thrown) {
      pesanError(error.message);
    },
    deferRender: true,
    columnsDefs: [
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

function lihatGambar(token) {
  try {
    loading();
    fetchData(
      baseurl + "/mold_spk/image",
      "POST",
      JSON.stringify({ token: token })
    )
      .then((result) => {
        modalData.title.textContent = result.data.code;
        if (result.data.image.length > 0) {
          result.data.image.forEach((item) => {
            let imageList = `
                <div class="col-4 clearfix mb-3">
                    <a href="${item.file_name}?#" class="link-underline-opacity-0" data-lightbox="preview" data-title="${item.title}">
                        <img src="${item.file_name}" class="img-fluid img-thumbnail rounded-0">
                    </a>
                </div>
            `;

            modalData.body.insertAdjacentHTML("beforeend", imageList);
          });
        }

        $("#modalImage").modal("show");
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

function clearModal() {
  modalData.body.textContent = "";
  document.getElementById("modalTitle").textContent = "";
  $("#modalImage").modal("hide");
}

buttons.export.addEventListener("click", async (e) => {
  try {
    loading();
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 300000);

    const response = await fetch(baseurl + "/spk_mold/export", {
      method: "GET",
      signal: controller.signal,
    });

    clearTimeout(timeoutId);

    if (!response.ok) {
      const errorData = await response.json().catch(() => null);
      throw new Error(
        errorData?.error || `HTTP error! status: ${response.status}`
      );
    }

    const blob = await response.blob();

    if (blob.size === 0) {
      throw new Error("Failed to creating exported file");
    }

    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.style.display = "none";
    a.href = url;
    a.download = "mold_spk_list.xlsx";
    document.body.appendChild(a);
    a.click();

    window.URL.revokeObjectURL(url);
    a.remove();
    hideLoading();
  } catch (e) {
    if (e.name === "AbortError") {
      pesanError(
        "Proses ekspor terlalu lama. Silakan coba lagi atau ekspor data lebih kecil."
      );
    } else {
      pesanError(e.message || "Gagal mengekspor data");
    }

    hideLoading();
  }
});

const formKonfirmasi = document.getElementById("formKonfirmasi");
const dataKonfirmasi = {
  token: document.getElementById("konfirmasi_token"),
  tgl_selesai: document.getElementById("plan_finish_date"),
  keterangan: document.getElementById("reason"),
  submit: document.getElementById("btnConfirm"),
  cancel: document.getElementById("btnCancel"),
  tgl_lapor: document.getElementById("tgl_lapor"),
};

function clearModal() {
  formKonfirmasi.reset();
  $("#modalConfirm").modal("hide");
}

function confirmSelesai(token) {
  try {
    loading();
    fetchData(
      baseurl + "/mold_spk/get_data",
      "POST",
      JSON.stringify({ token: token })
    )
      .then((result) => {
        dataKonfirmasi.token.value = token;
        dataKonfirmasi.tgl_lapor.value = result.data.tgl_lapor;
        dataKonfirmasi.tgl_selesai.focus();
        $("#modalConfirm").modal("show");
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

function validasi() {
  let isValid = true;

  if (dataKonfirmasi.tgl_selesai.value === "") {
    dataKonfirmasi.tgl_selesai.classList.add("is-invalid");
    dataKonfirmasi.tgl_selesai.parentElement.querySelector(
      ".invalid-feedback"
    ).textContent = "This field is required";
    isValid = false;
  } else {
    dataKonfirmasi.tgl_selesai.classList.remove("is-invalid");
  }

  if (dataKonfirmasi.tgl_selesai.value !== "") {
    if (dataKonfirmasi.tgl_selesai.value < dataKonfirmasi.tgl_lapor.value) {
      dataKonfirmasi.tgl_selesai.classList.add("is-invalid");
      dataKonfirmasi.tgl_selesai.parentElement.querySelector(
        ".invalid-feedback"
      ).textContent = "Tanggal selesai harus lebih besar dari tanggal lapor";
      isValid = false;
    } else {
      dataKonfirmasi.tgl_selesai.classList.remove("is-invalid");
    }
  }

  return isValid;
}

dataKonfirmasi.submit.addEventListener("click", (e) => {
  if (validasi()) {
    try {
      loading();
      fetchData(
        baseurl + "/mold_spk/confirm",
        "POST",
        JSON.stringify({
          token: dataKonfirmasi.token.value,
          tgl_selesai: dataKonfirmasi.tgl_selesai.value,
          keterangan: dataKonfirmasi.keterangan.value,
        })
      )
        .then((result) => {
          pesanSukses(result.message);
          //   window.location.reload();
          refreshTable();
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
