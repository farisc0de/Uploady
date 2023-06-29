var lang = document.querySelector("html").getAttribute("lang");

if (lang == "en") {
  lang = "en-GB";
}

$(document).ready(function () {
  $("#dataTable").DataTable({
    ordering: true,
    language: {
      url: `//cdn.datatables.net/plug-ins/1.13.4/i18n/${lang}.json`,
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

  $("#supported").DataTable({
    ordering: true,
    language: {
      url: `//cdn.datatables.net/plug-ins/1.13.4/i18n/${lang}.json`,
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
});

function deleteAccount(token) {
  var conf = confirm("Are you sure ?");
  if (conf == true) {
    window.location.href = `actions/delete_me.php?token=${token}`;
  }
}
