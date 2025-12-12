window.onload = () => {
  loadTable();

  $("#select-all").on("click", function () {
    var isChecked = this.checked;
    $(".row-checkbox").prop("checked", isChecked);
  });

  $("#dataTable tbody").on("click", ".row-checkbox", function () {
    var totalCheckbox = $(".row-checkbox").length;
    var totalChecked = $(".row-checkbox:checked").length;

    if (totalCheckbox == totalChecked) {
      $("#select-all").prop("checked", true);
    } else {
      $("#select-all").prop("checked", false);
    }
  });
};

const formGenerate = document.getElementById("form-generate");

const buttons = {
  add: document.getElementById("btnAdd"),
  filter: document.getElementById("btnFilter"),
  refresh: document.getElementById("btnRefresh"),
  export: document.getElementById("btnExport"),
  generate: document.getElementById("btnGenerate"),
  modal_generate: document.getElementById("btnModalGenerate"),
};

buttons.generate.addEventListener("click", (e) => {
  e.preventDefault();

  // Ambil semua elemen checkbox yang dicentang
  var checkedBoxes = $(".row-checkbox:checked");
  var isAnyCheckboxChecked = checkedBoxes.length > 0;

  // Cek Kondisi
  if (!isAnyCheckboxChecked) {
    pesanWarning("No Data Selected");
    return;
  } else {
    var selectedData = checkedBoxes
      .map(function () {
        return $(this).val();
      })
      .get(); // Mengubah object jQuery menjadi Array standar

    // const selectedDataJSON = JSON.stringify(selectedData);
    // const selectedDataArray = Array.from(JSON.parse(selectedDataJSON));
    selectedData.forEach((item) => {
      const inputForm = document.createElement("input");
      const inputGroup = document.createElement("div");
      inputForm.type = "hidden";
      inputForm.name = "spk[]";
      inputForm.classList.add("form-control");
      inputForm.classList.add("rounded-0");
      inputForm.setAttribute("readonly", true);
      inputForm.value = item;

      inputGroup.classList.add("input-group", "mb-3");
      inputGroup.appendChild(inputForm);

      formGenerate.appendChild(inputGroup);
    });
    $("#modal-generate").modal("show");
  }
});

buttons.modal_generate.addEventListener("click", (e) => {
  try {
    loading();
    fetchData(
      baseurl + "/identification/generate-from-spk",
      "POST",
      new FormData(formGenerate)
    )
      .then((result) => {
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
});

buttons.add.addEventListener("click", (e) => {
  loading();
  window.location.replace(baseurl + "/spk/add");
});

function loadTable() {
  table = $("#dataTable").DataTable({
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
      url: baseurl + "/spk/table",
      type: "POST",
      data: function (d) {},
    },
    error: function (xhr, error, thrown) {
      if (typeof pesanError === "function") {
        pesanError(xhr.responseJSON ? xhr.responseJSON.message : error);
      } else {
        console.error("Error:", error);
      }
    },
    deferRender: true,
    columnDefs: [
      {
        targets: 0,
        orderable: false,
        className: "text-center align-middle",
        render: function (data, type, row) {
          return (
            '<input type="checkbox" class="row-checkbox form-check-input border-1 rounded-0 border-primary" name="token[]" value="' +
            data +
            '">'
          );
        },
      },
    ],
    drawCallback: function (settings) {
      $("#select-all").prop("checked", false);
    },
  });
}

function refreshTable() {
  $("#dataTable").DataTable().ajax.reload(null, false);
}

buttons.refresh.addEventListener("click", (e) => {
  refreshTable();
});

buttons.export.addEventListener("click", async (e) => {
  try {
    loading();
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 300000);

    const response = await fetch(baseurl + "/spk/export", {
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
    a.download = "spk_list.xlsx";
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

function showImage(token) {
  try {
    loading();
    fetchData(baseurl + "/spk/image/" + token, "GET")
      .then((result) => {
        if (result.data.details.length > 0) {
          document.getElementById("modalTitle").textContent =
            "SPK No. : " + result.data.header;
          result.data.details.forEach((item) => {
            let htmlData = `
                <div class="col-4 clearfix mb-3">
                    <a href="${item.file_name}?#" class="link-underline-opacity-0" data-lightbox="preview" data-title="${item.file_name}">
                        <img src="${item.file_name}" class="img-fluid img-thumbnail rounded-0">
                    </a>
                </div>
            `;

            document
              .getElementById("imageData")
              .insertAdjacentHTML("beforeend", htmlData);
          });

          $("#modalImage").modal("show");
        }
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
  $("#modalImage").modal("hide");
  document.getElementById("imageData").textContent = "";
  document.getElementById("modalTitle").textContent = "";
}

function clearModalGenerate() {
  formGenerate.innerHTML = "";
}
