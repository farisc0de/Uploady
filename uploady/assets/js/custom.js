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

let text = loadLangugeValue("drop_files").then((data) => {
  $(".dz-button").text(data);
});

let myDropzone = new Dropzone("#my-dropzone", {
  maxFiles: 10,
});

myDropzone.on("success", function (files, response) {
  let thumbnail = files.previewElement.querySelector(".dz-filename");
  loadLangugeValue("download_file").then((data) => {
    thumbnail.innerHTML = `<span data-dz-name>
    <a href="${response.downloadlink}" target="_blank">${data}</a>
    </span>`;
  });
});

async function loadLangugeValue(key) {
  let lang = document.querySelector("html").getAttribute("lang");
  const response = await fetch(`languages/${lang}.json`);
  const movies = await response.json();
  return movies[key];
}
