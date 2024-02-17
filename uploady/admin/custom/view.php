<?php include_once '../session.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../components/meta.php'; ?>
    <title>Custom Code - <?= $st['website_name'] ?></title>
    <?php include_once '../components/css.php'; ?>
    <style>
        .editor {
            height: 500px;
            width: 100%;
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <?php include_once '../components/navbar.php' ?>
    <div id="layoutSidenav">
        <?php include_once '../components/sidebar.php'; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                    <div class="card h-100 mb-4" height="">
                        <div class="card-header">
                            <i class="fas fa-code mr-1"></i>
                            Custom Code
                        </div>
                        <div class="card-body">
                            <div id="alert"></div>
                            <h4>Custom Javascript Code</h4>
                            <div id="jseditor" class="editor"><?php echo file_get_contents(APP_PATH . "/assets/js/custom.js"); ?></div>
                            <div class="pt-3"></div>
                            <button class="btn btn-primary" id="saveJS">Update JS</button>
                            <hr />
                            <h4>Custom CSS Code</h4>
                            <div id="csseditor" class="editor"><?php echo file_get_contents(APP_PATH . "/assets/css/custom.css"); ?></div>
                            <div class="pt-3"></div>
                            <button class="btn btn-primary" id="saveCSS">Update CSS</button>
                        </div>
                    </div>
                </div>
            </main>
            <?php include_once '../components/footer.php'; ?>
        </div>
    </div>
    <?php include_once '../components/js.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/ace.min.js" integrity="sha512-kiECX53fzPhY5cnGzxTUZUOefsjR7gY3SD2OOgcsxZ0nAMZ3e+lkqxhXzGAFm05KjIaQ49/OyNryGTcbLb2V9w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/theme-monokai.min.js" integrity="sha512-g9yptARGYXbHR9r3kTKIAzF+vvmgEieTxuuUUcHC5tKYFpLR3DR+lsisH2KZJG2Nwaou8jjYVRdbbbBQI3Bo5w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/mode-javascript.min.js" integrity="sha512-Vxd7YZ0TLTa/GFCZ9UxBW9fipF4lxQXad6T2/VaIntzS77vh30JjpxAEpvrBUwtoUItupwvGAHi1TdXTddUxhQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/mode-css.min.js" integrity="sha512-q2Qu7dOhudjAQ8wvsLOsZ1NyUhOPAeGL/jzO1f45NMFGSv9F6sgDyzWa00LCVBWg/p84nGM/NHOX4bO1ctbkKg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        var editor = ace.edit("jseditor");
        editor.setTheme("ace/theme/monokai");
        editor.session.setMode("ace/mode/javascript");

        var editor = ace.edit("csseditor");
        editor.setTheme("ace/theme/monokai");
        editor.session.setMode("ace/mode/css");

        $("#saveCSS").click(function() {
            var css = ace.edit("csseditor").getValue();
            update(css, "css");

        });

        $("#saveJS").click(function() {
            var js = ace.edit("jseditor").getValue();
            update(js, "js");

        });

        function update(editor, button) {
            $.ajax({
                url: "actions/save.php",
                type: "POST",
                data: {
                    editor: editor,
                    button: button
                },
                success: function(data) {
                    if (data == "success") {
                        $("#alert").html('<div class="alert alert-success">Custom code has been updated!</div>');
                    } else {
                        $("#alert").html('<div class="alert alert-danger">Error in updating cusotm code!</div>');
                    }
                }
            });
        }
    </script>
</body>

</html>