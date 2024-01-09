Caman.DEBUG = true;

var caman = Caman("#canvas");

document.addEventListener("click", (e) => {
  if (e.target.classList.contains("filter-btn")) {
    if (e.target.classList.contains("brightness-add")) {
      Caman("#canvas", function () {
        caman.brightness(5).render();
      });
    } else if (e.target.classList.contains("brightness-remove")) {
      Caman("#canvas", function () {
        caman.brightness(-5).render();
      });
    } else if (e.target.classList.contains("contrast-add")) {
      Caman("#canvas", function () {
        caman.contrast(5).render();
      });
    } else if (e.target.classList.contains("contrast-remove")) {
      Caman("#canvas", function () {
        caman.contrast(-5).render();
      });
    } else if (e.target.classList.contains("saturation-add")) {
      Caman("#canvas", function () {
        caman.saturation(5).render();
      });
    } else if (e.target.classList.contains("saturation-remove")) {
      Caman("#canvas", function () {
        caman.saturation(-5).render();
      });
    } else if (e.target.classList.contains("vibrance-add")) {
      Caman("#canvas", function () {
        caman.vibrance(5).render();
      });
    } else if (e.target.classList.contains("vibrance-remove")) {
      Caman("#canvas", function () {
        caman.vibrance(-5).render();
      });
    } else if (e.target.classList.contains("sharpen-add")) {
      Caman("#canvas", function () {
        caman.sharpen(5).render();
      });
    } else if (e.target.classList.contains("sharpen-remove")) {
      Caman("#canvas", function () {
        caman.sharpen(-5).render();
      });
    } else if (e.target.classList.contains("blur-add")) {
      Caman("#canvas", function () {
        caman.stackBlur(5).render();
      });
    } else if (e.target.classList.contains("blur-remove")) {
      Caman("#canvas", function () {
        caman.stackBlur(-5).render();
      });
    } else if (e.target.classList.contains("hue-add")) {
      Caman("#canvas", function () {
        caman.hue(5).render();
      });
    } else if (e.target.classList.contains("hue-remove")) {
      Caman("#canvas", function () {
        caman.hue(-5).render();
      });
    } else if (e.target.classList.contains("sepia-add")) {
      Caman("#canvas", function () {
        caman.sepia(5).render();
      });
    } else if (e.target.classList.contains("sepia-remove")) {
      Caman("#canvas", function () {
        caman.sepia(-5).render();
      });
    }
  }
});

var effects = document.getElementById("effects");

document.addEventListener("change", (e) => {
  switch (effects.value) {
    case "vintage":
      Caman("#canvas", function () {
        caman.revert();
        caman.vintage().render();
      });
      break;

    case "lomo":
      Caman("#canvas", function () {
        caman.revert();
        caman.lomo().render();
      });
      break;

    case "clarity":
      Caman("#canvas", function () {
        caman.revert();
        caman.clarity().render();
      });
      break;

    case "sinCity":
      Caman("#canvas", function () {
        caman.revert();
        caman.sinCity().render();
      });
      break;

    case "sunrise":
      Caman("#canvas", function () {
        caman.revert();
        caman.sunrise().render();
      });
      break;

    case "crossProcess":
      Caman("#canvas", function () {
        caman.revert();
        caman.crossProcess().render();
      });
      break;

    case "orangePeel":
      Caman("#canvas", function () {
        caman.revert();
        caman.orangePeel().render();
      });
      break;

    case "love":
      Caman("#canvas", function () {
        caman.revert();
        caman.love().render();
      });
      break;

    case "grungy":
      Caman("#canvas", function () {
        caman.revert();
        caman.grungy().render();
      });
      break;

    case "jarques":
      Caman("#canvas", function () {
        caman.revert();
        caman.jarques().render();
      });
      break;

    case "oldBoot":
      Caman("#canvas", function () {
        caman.revert();
        caman.oldBoot().render();
      });
      break;

    case "glowingSun":
      Caman("#canvas", function () {
        caman.revert();
        caman.glowingSun().render();
      });
      break;

    case "pinhole":
      Caman("#canvas", function () {
        caman.revert();
        caman.pinhole().render();
      });
      break;

    case "nostalgia":
      Caman("#canvas", function () {
        caman.revert();
        caman.nostalgia().render();
      });
      break;

    case "herMajesty":
      Caman("#canvas", function () {
        caman.revert();
        caman.herMajesty().render();
      });
      break;

    case "hazyDays":
      Caman("#canvas", function () {
        caman.revert();
        caman.hazyDays().render();
      });
      break;

    case "hemingway":
      Caman("#canvas", function () {
        caman.revert();
        caman.hemingway().render();
      });
      break;

    case "concentrate":
      Caman("#canvas", function () {
        caman.revert();
        caman.concentrate().render();
      });
      break;

    default:
      Caman("#canvas", function () {
        caman.revert();
      });
      break;
  }
});

document.getElementById("saveImageToUploads").addEventListener("click", (e) => {
  caman.render(function () {
    $("#canvas")
      .get(0)
      .toBlob(function (blob) {
        var formData = new FormData();

        formData.append(
          "file",
          blob,
          document.getElementById("file_name").value
        );

        var request = new XMLHttpRequest();
        request.open("POST", "actions/update_file.php?action=edit_image");
        request.send(formData);

        request.onreadystatechange = function () {
          if (this.readyState == 4 && this.status == 200) {
            document.getElementById(
              "alert"
            ).innerHTML = `<div class="alert alert-success" id="alert">
              ${JSON.parse(this.responseText).success}</div>`;
          }
        };
      });
  });
});

document.getElementById("clearFilters").addEventListener("click", (e) => {
  Caman("#canvas", function () {
    caman.revert();
  });
});
