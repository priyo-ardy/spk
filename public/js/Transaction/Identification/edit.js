const inputForm = {
  data_analysis: document.getElementById("data_analysis"),
};

window.onload = () => {
  $(".summernote").summernote({
    height: 150, // set editor height
    minHeight: null, // set minimum height of editor
    maxHeight: null, // set maximum height of editor
  });

  $(".summernote").summernote("disable");
};
