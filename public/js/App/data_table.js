function loadTable(element, url) {
  table = $(element).DataTable({
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
      url: baseurl + url,
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

function refreshTable(table) {
  table.ajax.reload(null, false);
}

function checkAll(table) {
  $("#select-all").on("click", function () {
    var isChecked = this.checked;
    $(".row-checkbox").prop("checked", isChecked);
  });

  $(table + " tbody").on("click", ".row-checkbox", function () {
    var totalCheckbox = $(".row-checkbox").length;
    var totalChecked = $(".row-checkbox:checked").length;

    if (totalCheckbox == totalChecked) {
      $("#select-all").prop("checked", true);
    } else {
      $("#select-all").prop("checked", false);
    }
  });
}
