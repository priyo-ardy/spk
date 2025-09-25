window.onload = () => {
  loadTable();
};

const buttons = {
  add: document.getElementById("btnAdd"),
  filter: document.getElementById("btnFilter"),
  refresh: document.getElementById("btnRefresh"),
  export: document.getElementById("btnExport"),
};

buttons.add.addEventListener("click", (e) => {
  loading();
  window.location.replace(baseurl + "/spk/add");
});

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
      url: baseurl + "/spk/table",
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
