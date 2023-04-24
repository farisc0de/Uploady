$(document).ready(function () {
  $("#dataTable").DataTable({
    ordering: true,
    language: {
      url: `//cdn.datatables.net/plug-ins/1.13.4/i18n/${document
        .querySelector("html")
        .getAttribute("lang")}.json`,
    },
    select: {
      style: "multi",
    },
    order: [[1, null]],
    columnDefs: [
      {
        targets: 0,
        orderable: false,
      },
    ],
    fixedColumns: true,
  });
});

$("#select-all").click(function (event) {
  if (this.checked) {
    $(":checkbox").each(function () {
      this.checked = true;
    });
  } else {
    $(":checkbox").each(function () {
      this.checked = false;
    });
  }
});

$(document).ready(function () {
  var table = $("#supported").DataTable({
    ordering: true,
    language: {
      url: `//cdn.datatables.net/plug-ins/1.13.4/i18n/${document
        .querySelector("html")
        .getAttribute("lang")}.json`,
    },
    select: {
      style: "multi",
    },
    order: [[0, "asc"]],
    columnDefs: [
      {
        targets: 0,
        orderable: false,
      },
    ],
    fixedColumns: true,
  });
});

function deleteAccount(token) {
  var conf = confirm("Are you sure ?");
  if (conf == true) {
    window.location.href = `actions/delete_me.php?token=${token}`;
  }
}

loadLanguge().then((data) => {
  let myDropzone = new Dropzone("#my-dropzone", {
    maxFiles: 10,
    addRemoveLinks: true,
    dictDefaultMessage: data["drop_files"],
    dictRemoveFile: data["remove_file"],
    dictCancelUpload: data["cancel_upload"],
  });

  myDropzone.on("success", function (files, response) {
    let thumbnail = files.previewElement.querySelector(".dz-filename");
    thumbnail.innerHTML = `
    ${data["download_file"]}
    `;

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
  return language;
}
