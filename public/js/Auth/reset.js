const protocol = window.location.protocol;
const hostname = window.location.hostname;
const port = window.location.port;
const baseurl = `${protocol}//${hostname}${port ? ":" + port : ""}`;

const formReset = document.getElementById("formReset");
const dataForm = {
  password: document.getElementById("new_password"),
  pswd_length: document.getElementById("pswd_length"),
  pswd_upper: document.getElementById("pswd_upper"),
  pswd_lower: document.getElementById("pswd_lower"),
  pswd_number: document.getElementById("pswd_number"),
  pswd_special: document.getElementById("pswd_special"),
  pesan: document.getElementById("pesanLogin"),
};
const buttons = {
  ganti_password: document.getElementById("btnReset"),
};
function validatePasswordFormat() {
  let isValid = true;

  if (dataForm.password.value !== "") {
    if (dataForm.password.value.length < 8) {
      dataForm.pswd_length.classList.add("text-danger");
      isValid = false;
    } else {
      dataForm.pswd_length.classList.remove("text-danger");
      dataForm.pswd_length.classList.add("text-success");
    }

    if (!/[A-Z]/.test(dataForm.password.value)) {
      dataForm.pswd_upper.classList.add("text-danger");
      isValid = false;
    } else {
      dataForm.pswd_upper.classList.remove("text-danger");
      dataForm.pswd_upper.classList.add("text-success");
    }

    if (!/[a-z]/.test(dataForm.password.value)) {
      dataForm.pswd_lower.classList.add("text-danger");
      isValid = false;
    } else {
      dataForm.pswd_lower.classList.remove("text-danger");
      dataForm.pswd_lower.classList.add("text-success");
    }

    if (!/[0-9]/.test(dataForm.password.value)) {
      dataForm.pswd_number.classList.add("text-danger");
      isValid = false;
    } else {
      dataForm.pswd_number.classList.remove("text-danger");
      dataForm.pswd_number.classList.add("text-success");
    }

    if (!/[!@#$%^&*(),.?":{}|<>]/.test(dataForm.password.value)) {
      dataForm.pswd_special.classList.add("text-danger");
      isValid = false;
    } else {
      dataForm.pswd_special.classList.remove("text-danger");
      dataForm.pswd_special.classList.add("text-success");
    }
  } else {
    dataForm.pswd_length.classList.remove("text-danger");
    dataForm.pswd_length.classList.remove("text-success");
    isValid = false;
  }

  return isValid;
}

dataForm.password.addEventListener("keyup", (e) => {
  validatePasswordFormat();
});

dataForm.password.addEventListener("keypress", (e) => {
  if (e.key === "Enter") {
    if (dataForm.password.value === "") {
      dataForm.password.classList.add("is-invalid");
      dataForm.password.parentElement.querySelector(".").textContent =
        "New Password cannot be empty";
    } else {
      if (validatePasswordFormat()) {
        prosesGanti();
      }
    }
  }
});

buttons.ganti_password.addEventListener("click", (e) => {
  if (validatePasswordFormat()) {
    prosesGanti();
  }
});

function prosesGanti() {
  try {
    dataForm.password.setAttribute("readonly", true);
    buttons.ganti_password.setAttribute("disabled", true);
    buttons.ganti_password.innerHTML =
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&ensp;Sending...';

    fetchData(baseurl + "/change", "POST", new FormData(formReset))
      .then((result) => {
        dataForm.pesan.textContent = result.message;
        dataForm.password.value = "";
        buttons.ganti_password.removeAttribute("disabled");
        buttons.ganti_password.innerHTML = `<i class="bi bi-box-arrow-in-right"></i>&ensp;Reset Password`;
      })
      .catch((err) => {
        dataForm.password.removeAttribute("readonly");
        buttons.ganti_password.removeAttribute("disabled");
        buttons.ganti_password.innerHTML = `<i class="bi bi-box-arrow-in-right"></i>&ensp;Reset Password`;
        dataForm.pesan.textContent = err.message;
      });
  } catch (e) {
    dataForm.password.removeAttribute("readonly");
    buttons.ganti_password.removeAttribute("disabled");
    buttons.ganti_password.innerHTML = `<i class="bi bi-box-arrow-in-right"></i>&ensp;Reset Password`;
    dataForm.pesan.textContent = e.message;
  }
}
