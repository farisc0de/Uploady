Caman("#canvas", function () {
  this.render();
});

document.addEventListener("click", (e) => {
  if (e.target.classList.contains("filter-btn")) {
    if (e.target.classList.contains("brightness-add")) {
      Caman("#canvas", function () {
        this.brightness(5).render();
      });
    } else if (e.target.classList.contains("brightness-remove")) {
      Caman("#canvas", function () {
        this.brightness(-5).render();
      });
    } else if (e.target.classList.contains("contrast-add")) {
      Caman("#canvas", function () {
        this.contrast(5).render();
      });
    } else if (e.target.classList.contains("contrast-remove")) {
      Caman("#canvas", function () {
        this.contrast(-5).render();
      });
    } else if (e.target.classList.contains("saturation-add")) {
      Caman("#canvas", function () {
        this.saturation(5).render();
      });
    } else if (e.target.classList.contains("saturation-remove")) {
      Caman("#canvas", function () {
        this.saturation(-5).render();
      });
    } else if (e.target.classList.contains("vibrance-add")) {
      Caman("#canvas", function () {
        this.vibrance(5).render();
      });
    } else if (e.target.classList.contains("vibrance-remove")) {
      Caman("#canvas", function () {
        this.vibrance(-5).render();
      });
    } else if (e.target.classList.contains("sharpen-add")) {
      Caman("#canvas", function () {
        this.sharpen(5).render();
      });
    } else if (e.target.classList.contains("sharpen-remove")) {
      Caman("#canvas", function () {
        this.sharpen(-5).render();
      });
    } else if (e.target.classList.contains("blur-add")) {
      Caman("#canvas", function () {
        this.stackBlur(5).render();
      });
    } else if (e.target.classList.contains("blur-remove")) {
      Caman("#canvas", function () {
        this.stackBlur(-5).render();
      });
    }
  }
});

var effects = document.getElementById("effects");

document.addEventListener("change", (e) => {
  switch (effects.value) {
    case "vintage":
      Caman("#canvas", function () {
        this.revert();
        this.vintage().render();
      });
      break;

    case "lomo":
      Caman("#canvas", function () {
        this.revert();
        this.lomo().render();
      });
      break;

    case "clarity":
      Caman("#canvas", function () {
        this.revert();
        this.clarity().render();
      });
      break;

    case "sincity":
      Caman("#canvas", function () {
        this.revert();
        this.sinCity().render();
      });
      break;

    case "sunrise":
      Caman("#canvas", function () {
        this.revert();
        this.sunrise().render();
      });
      break;

    case "crossprocess":
      Caman("#canvas", function () {
        this.revert();
        this.crossProcess().render();
      });
      break;

    case "orangePeel":
      Caman("#canvas", function () {
        this.revert();
        this.orangePeel().render();
      });
      break;

    case "love":
      Caman("#canvas", function () {
        this.revert();
        this.love().render();
      });
      break;

    case "grungy":
      Caman("#canvas", function () {
        this.revert();
        this.grungy().render();
      });
      break;

    case "jarques":
      Caman("#canvas", function () {
        this.revert();
        this.jarques().render();
      });
      break;

    case "oldBoot":
      Caman("#canvas", function () {
        this.revert();
        this.oldBoot().render();
      });
      break;

    case "glowingSun":
      Caman("#canvas", function () {
        this.revert();
        this.glowingSun().render();
      });
      break;

    case "pinhole":
      Caman("#canvas", function () {
        this.revert();
        this.pinhole().render();
      });
      break;

    case "nostalgia":
      Caman("#canvas", function () {
        this.revert();
        this.nostalgia().render();
      });
      break;

    case "herMajesty":
      Caman("#canvas", function () {
        this.revert();
        this.herMajesty().render();
      });
      break;

    case "hazyDays":
      Caman("#canvas", function () {
        this.revert();
        this.hazyDays().render();
      });
      break;

    case "hemingway":
      Caman("#canvas", function () {
        this.revert();
        this.hemingway().render();
      });
      break;

    case "concentrate":
      Caman("#canvas", function () {
        this.revert();
        this.concentrate().render();
      });
      break;

    default:
      Caman("#canvas", function () {
        this.revert();
      });
      break;
  }
});

document.getElementById("saveImageToUploads").addEventListener("click", (e) => {
  Caman("#canvas", function () {
    this.render(function () {
      $("#canvas")
        .get(0)
        .toBlob(
          function (blob) {
            var formData = new FormData();

            formData.append(
              "file",
              blob,
              document.getElementById("file_name").value
            );

            var request = new XMLHttpRequest();
            request.open("POST", "actions/update_file.php");
            request.send(formData);

            request.onreadystatechange = function () {
              if (this.readyState == 4 && this.status == 200) {
                document.getElementById("alert").innerHTML = JSON.parse(
                  this.responseText
                ).success;
              }
            };
          },
          "image/jpeg",
          0.5
        );
    });
  });
});

document.getElementById("clearFilters").addEventListener("click", (e) => {
  Caman("#canvas", function () {
    this.revert();
  });
});
