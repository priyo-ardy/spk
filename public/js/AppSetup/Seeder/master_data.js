const dataForm = {
  table_name: document.getElementById("table_name"),
  submit: document.getElementById("btnSubmit"),
  reset: document.getElementById("btnReset"),
  data_result: document.getElementById("dataResult"),
};

dataForm.table_name.onchange = () => {
  dataForm.data_result.textContent = "";
};

dataForm.reset.addEventListener("click", (e) => {
  dataForm.table_name.value = "";
  $(dataForm.table_name).trigger("change");
  dataForm.data_result.textContent = "";
  dataForm.table_name.classList.remove("is-invalid");
});

dataForm.submit.addEventListener("click", (e) => {
  if (dataForm.table_name.value === "") {
    dataForm.table_name.classList.add("is-invalid");
    dataForm.data_result.textContent = "";
  } else {
    try {
      dataForm.table_name.classList.remove("is-invalid");
      loading();
      dataForm.data_result.textContent = "";
      fetchData(
        baseurl + "/seeder/generate",
        "POST",
        JSON.stringify({ table_name: dataForm.table_name.value })
      )
        .then((result) => {
          const serverResult = result;
          console.log(result.data);
          dataForm.data_result.innerHTML = `<pre>${JSON.stringify(result.data, null, 2)}</pre>`;
          console.log(result);
          hideLoading();
        })
        .catch((err) => {
          pesanError(err.message);
          hideLoading();
        });
    } catch (e) {
      pesanError(e.message);
    }
  }
});
