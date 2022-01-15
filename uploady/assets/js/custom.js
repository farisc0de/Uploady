let add_button = document.getElementById("add_more");
let count = 4;

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

$("#tos").change(function () {
  if ($(this).prop("checked")) {
    $("#submit").prop("disabled", false);
  } else {
    $("#submit").prop("disabled", true);
  }
});
