loadLanguge().then((data) => {
  let myDropzone = new Dropzone("#my-dropzone", {
    maxFiles: 10,
    maxFilesize: document.querySelector("#max_file_size").value,
    addRemoveLinks: true,
    dictDefaultMessage: data["drop_files"],
    dictRemoveFile: data["remove_file"],
    dictCancelUpload: data["cancel_upload"],
  });

  myDropzone.on("success", function (files, response) {
    let thumbnail = files.previewElement.querySelector(".dz-filename");
    thumbnail.innerHTML = `<span data-dz-name>
    <a href="${response.downloadlink}">${data["download_file"]}</a>
    </span>`;

    let deleteButton = files.previewElement.querySelector(".dz-remove");
    deleteButton.addEventListener("click", function (e) {
      $.ajax({
        url: "actions/delete_file.php",
        type: "POST",
        data: {
          file_id: response.file_id,
          user_id: response.user_id,
        },
        success: function (response) {
          if (response.status == "success") {
            files.previewElement.remove();
          }
        },
      });
    });
  });
});

async function loadLanguge() {
  let lang = document.querySelector("html").getAttribute("lang");
  const response = await fetch(`languages/${lang}.json`);
  const language = await response.json();
  return language["general"];
}
