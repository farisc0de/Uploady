$(".owl-carousel").owlCarousel({
  loop: true,
  margin: 10,
  responsiveClass: true,
  autoWidth: true,
  responsive: {
    0: {
      items: 1,
      nav: true,
    },
    600: {
      items: 3,
      nav: true,
    },
    1000: {
      items: 5,
      nav: true,
      loop: false,
    },
  },
});

let add_button = document.getElementById("add_more");
let count = 4;

add_button.addEventListener("click", () => {
  if (count <= 9) {
    var txt =
      '<input type="file" class="form-control-file pt-2" id="file[]" name="file[]">';
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
