let add_button = document.getElementById("add_more");
let count = 4;

if (add_button != null) {
  add_button.addEventListener("click", () => {
    if (count <= 9) {
      var txt =
        '<div class="pt-2"><input type="file" class="form-control" id="file[]" name="file[]"></div>';
      $("#dvFile").append(txt);
      count++;
    } else {
      $("#upload_alert").html(
        '<div class="alert alert-danger">Sorry you can\'t upload more then 10 files</div>'
      );
      $("#add_more").addClass("disabled");
    }
  });
}

$("#tos").change(function () {
  if ($(this).prop("checked")) {
    $("#submit").prop("disabled", false);
  } else {
    $("#submit").prop("disabled", true);
  }
});

// Call the dataTables jQuery plugin
$(document).ready(function () {
  $("#dataTable").DataTable({
    ordering: true,

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
  });
});

function deleteAccount(token) {
  var conf = confirm("Are you sure ?");
  if (conf == true) {
    window.location.href = `actions/delete_me.php?token=${token}`;
  }
}

let myDropzone = new Dropzone("#my-dropzone", {
  maxFilesize: 2,
  maxFiles: 10,
});

myDropzone.on("success", function (files, response) {
  let thumbnail = files.previewElement.querySelector(".dz-filename");
  thumbnail.innerHTML = `<span data-dz-name>
    <a href="${response.downloadlink}" target="_blank">${files.name}</a>
    </span>`;
});
