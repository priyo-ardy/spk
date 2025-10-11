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
      url: baseurl + "/planer/table",
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
        targets: "_all",
        orderable: false,
        className: "dt-nowrap",
      },
    ],
  });
}

function refreshTable() {
  $("#dataTable").DataTable().ajax.reload(null, false);
}
