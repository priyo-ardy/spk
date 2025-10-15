const protocol = window.location.protocol;
const hostname = window.location.hostname;
const port = window.location.port;
const baseurl = `${protocol}//${hostname}${port ? ":" + port : ""}`;

const formReset = document.getElementById("formReset");
const dataForm = {
  email: document.getElementById("user_email"),
  pesan: document.getElementById("pesanLogin"),
};
const buttons = {
  reset: document.getElementById("btnReset"),
};

function resetPassword(e) {
  if (validasi()) {
    try {
      dataForm.email.setAttribute("readonly", true);
      buttons.reset.setAttribute("disabled", true);
      buttons.reset.innerHTML =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&ensp;Sending...';

      fetchData(baseurl + "/reset-password", "POST", new FormData(formReset))
        .then((result) => {
          dataForm.pesan.textContent = result.message;
          dataForm.email.removeAttribute("readonly");
          buttons.reset.removeAttribute("disabled");
          buttons.reset.innerHTML = `<i class="bi bi-box-arrow-in-right"></i>&ensp;Reset Password`;
        })
        .catch((err) => {
          dataForm.pesan.textContent = err.message;
          dataForm.email.removeAttribute("readonly");
          buttons.reset.removeAttribute("disabled");
          buttons.reset.innerHTML = `<i class="bi bi-box-arrow-in-right"></i>&ensp;Reset Password`;
        });
    } catch (e) {
      dataForm.pesan.textContent = e.message;
      dataForm.email.removeAttribute("readonly");
      buttons.reset.removeAttribute("disabled");
      buttons.reset.innerHTML = `<i class="bi bi-box-arrow-in-right"></i>&ensp;Reset Password`;
    }
  } else {
    e.preventDefault();
    e.stopPropagation();
  }
}

function validasi() {
  let isValid = true;

  if (dataForm.email.value === "") {
    dataForm.email.classList.add("is-invalid");
    dataForm.email.parentElement.querySelector(
      ".invalid-feedback"
    ).textContent = "This field is required";
    isValid = false;
  } else {
    if (!validEmailFormat()) {
      dataForm.email.classList.add("is-invalid");
      dataForm.email.parentElement.querySelector(
        ".invalid-feedback"
      ).textContent = "Invalid email format";
      isValid = false;
    } else {
      dataForm.email.classList.remove("is-invalid");
      dataForm.email.parentElement.querySelector(
        ".invalid-feedback"
      ).textContent = "";
    }
  }

  return isValid;
}

function validEmailFormat() {
  if (dataForm.email.value !== "") {
    //Valid email format using regex
    const regex =
      /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
    return regex.test(dataForm.email.value);
  }
}

dataForm.email.addEventListener("keypress", (e) => {
  if (e.key === "Enter") {
    resetPassword(e);
  }
});

buttons.reset.addEventListener("click", (e) => {
  resetPassword(e);
});
