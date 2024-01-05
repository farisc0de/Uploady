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
                            <h4>Custom CSS Code</h4>
                            <div id="csseditor" class="editor"><?php echo file_get_contents(APP_PATH . "/assets/css/custom.css"); ?></div>
                            <div class="pt-3"></div>
                            <button class="btn btn-primary" id="save">Save</button>
                        </div>
                    </div>
                </div>
            </main>
            <?php include_once '../components/footer.php'; ?>
        </div>
    </div>
    <?php include_once '../components/js.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.18.0/ace.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.18.0/theme-monokai.min.js" integrity="sha512-8rSB/wU3KoN3rr1VHwPP4dfpbU063BvH6qzwt0oe9E2ThEchzd8MmJylVPxZ5kLvxsShgtWvc9AtIlS7rmPunA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.18.0/mode-javascript.min.js" integrity="sha512-4pSr5XJEo9PorZpo7xa7DrbEWKN5Pu4atm/tmMXTRJtepTeKo+sNzlfqkDwPlPx+hpSM4yz4fJEacTiaPazeoA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.18.0/mode-css.min.js" integrity="sha512-q2Qu7dOhudjAQ8wvsLOsZ1NyUhOPAeGL/jzO1f45NMFGSv9F6sgDyzWa00LCVBWg/p84nGM/NHOX4bO1ctbkKg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        var editor = ace.edit("jseditor");
        editor.setTheme("ace/theme/monokai");
        editor.session.setMode("ace/mode/javascript");

        var editor = ace.edit("csseditor");
        editor.setTheme("ace/theme/monokai");
        editor.session.setMode("ace/mode/css");

        $("#save").click(function() {
            var js = ace.edit("jseditor").getValue();
            var css = ace.edit("csseditor").getValue();

            $.ajax({
                url: "actions/save.php",
                type: "POST",
                data: {
                    js: js,
                    css: css
                },
                success: function(data) {
                    if (data == "success") {
                        $("#alert").html('<div class="alert alert-success">Custom code has been updated!</div>');
                    } else {
                        $("#alert").html('<div class="alert alert-danger">Error in updating cusotm code!</div>');
                    }
                }
            });
        });
    </script>
</body>

</html>