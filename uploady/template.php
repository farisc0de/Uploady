<?php include_once 'session.php'; ?>

<?php include_once 'components/header.php'; ?>
<title>
    <?= $st['website_name'] ?> - Simple Template
</title>
<?php include_once 'components/css.php'; ?>
</head>

<body class="d-flex flex-column h-100">

    <?php include_once 'components/navbar.php'; ?>

    <div id="wrapper">
        <div id="content-wrapper">
            <div class="container pb-5 pt-5">
                <div class="row justify-content-center text-center">
                    <div class="col-sm-12 col-md-8 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <b>Website Template</b>
                            </div>
                            <div class="card-body">
                                <p>Template Content</p>
                            </div>
                            <div class="card-footer mb-0">
                                <button class="btn btn-primary">
                                    CTA Button
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once 'components/footer.php'; ?>

    <?php include_once 'components/js.php'; ?>
</body>

</html>