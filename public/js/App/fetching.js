async function fetchData(url, method, body = null) {
  let options = {
    method: method,
    headers: {
      "x-requested-with": "XMLHttpRequest",
    },
  };

  if (body) {
    if (body instanceof FormData) {
      options.body = body;
      options.contentType = false;
      options.processData = false;
    } else {
      options.headers["Content-Type"] = "application/json";
      options.body = body;
    }
  }

  try {
    const response = await fetch(url, options);
    const contentType = response.headers.get("Content-Type");

    if (!response.ok) {
      const responseText = await response.text();
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

    if (contentType && contentType.includes("application/json")) {
      return await response.json(); // Mengembalikan data JSON
    } else {
      return await response.text(); // Mengembalikan teks jika bukan JSON
    }
  } catch (error) {
    throw error;
  }
}
