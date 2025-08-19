const protocol = window.location.protocol;
const hostname = window.location.hostname;
const port = window.location.port;
const baseurl = `${protocol}//${hostname}${port ? ":" + port : ""}`;
const formAuth = document.getElementById("formAuth");
const buttons = {
  auth: document.getElementById("btnAuth"),
  cancel: document.getElementById("btnCancel"),
  reset: document.getElementById("btnReset"),
};
const dataForm = {
  user_name: document.getElementById("username"),
  user_password: document.getElementById("password"),
  pesan: document.getElementById("pesanLogin"),
};

dataForm.user_name.addEventListener("keypress", (e) => {
  if (e.key === "Enter") {
    if (dataForm.user_name.value == "") {
      dataForm.user_name.classList.add("is-invalid");
    } else {
      dataForm.user_name.classList.remove("is-invalid");
      dataForm.user_password.focus();
    }
  }
});

dataForm.user_password.addEventListener("keypress", (e) => {
  if (e.key === "Enter") {
    if (dataForm.user_name.value.trim() === "") {
      dataForm.user_name.classList.add("is-invalid");
      dataForm.user_name.focus();
    } else {
      dataForm.user_name.classList.remove("is-invalid");
    }

    if (dataForm.user_password.value.trim() === "") {
      dataForm.user_password.classList.add("is-invalid");
    } else {
      dataForm.user_password.classList.remove("is-invalid");
      prosesLogin();
    }
  }
});

function validasi() {
  let isValid = true;

  if (dataForm.user_name.value.trim === "") {
    dataForm.user_name.classList.add("is-invalid");
    isValid = false;
  } else {
    dataForm.user_name.classList.remove("is-invalid");
  }

  if (dataForm.user_password.value.trim === "") {
    dataForm.user_password.classList.add("is-invalid");
    isValid = false;
  } else {
    dataForm.user_password.classList.remove("is-invalid");
  }

  return isValid;
}

function resetForm() {
  formAuth.reset();
  dataForm.user_name.removeAttribute("readonly");
  dataForm.user_password.removeAttribute("readonly");
  buttons.auth.removeAttribute("disabled");
  buttons.auth.innerHTML =
    '<i class="bi bi-box-arrow-in-right"></i>&ensp;Log in';
  dataForm.user_name.focus();
}

async function prosesLogin() {
  if (validasi()) {
    try {
      dataForm.user_name.setAttribute("readonly", true);
      dataForm.user_password.setAttribute("readonly", true);
      buttons.auth.setAttribute("disabled", true);
      buttons.auth.innerHTML = `
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>`;
      const response = await fetch(baseurl + "/proses", {
        method: "POST",
        body: new FormData(formAuth),
        contentType: false,
        processData: false,
      });

      const contentType = response.headers.get("Content-Type");

      // Cek apakah respons tidak ok
      if (!response.ok) {
        const responseText = await response.text(); // Ambil teks respons
        if (contentType && contentType.includes("application/json")) {
          const errorData = JSON.parse(responseText);
          throw errorData;
        } else if (contentType && contentType.includes("text/html")) {
          const parser = new DOMParser();
          const doc = parser.parseFromString(responseText, "text/html");
          const errorMessage =
            doc.querySelector("p")?.textContent || "Error occurred";
          throw { message: errorMessage };
        } else {
          throw new Error("Unexpected content type");
        }
      }

      let data;
      if (contentType && contentType.includes("application/json")) {
        data = await response.json(); // Mengembalikan data JSON
      } else {
        await response.text(); // Mengembalikan teks jika bukan JSON
        data = {};
      }

      // Redirect jika status code 200 (response.ok)
      if (data.redirect) {
        window.location.replace(data.redirect);
      }
    } catch (e) {
      dataForm.pesan.textContent = e.message;
      resetForm();
    }
  }
}
