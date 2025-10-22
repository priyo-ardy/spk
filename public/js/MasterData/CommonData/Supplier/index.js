window.onload = () => {
  loadTable();
};

function loadTable() {
  $("#dataTable").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    bDestroy: true,
    search: {
      return: true,
    },
    order: [],
    ajax: {
      url: baseurl + "/supplier/table",
      type: "POST",
      data: "raw",
      action: "calls",
    },
    deferRender: true,
    columnDefs: [
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
