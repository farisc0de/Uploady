<?php include_once 'session.php'; ?>

<?php include_once 'logic/editFileLogic.php'; ?>

<?php include_once 'components/header.php'; ?>

<div class="container pb-5 pt-5">
    <div class="row justify-content-center text-center">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <div class="row">
                <div class="col-md-8 m-auto">

                    <input type="hidden" id="file_name" value="<?= $file['filename'] ?>">

                    <div class="alert alert success" id="alert"></div>

                    <img class="img-fluid" name="canvas" id="canvas" src="<?= $picture ?>"></img>

                    <h4 class="text-center my-3">Filters</h4>

                    <div class="row my-4 text-center">
                        <div class="col-md-3 mb-3">
                            <div class="btn-group btn-group-sm">
                                <button class="filter-btn brightness-remove btn btn-primary">-</button>
                                <button class="btn btn-secondary btn-disabled" disabled>Brightness</button>
                                <button class="filter-btn brightness-add btn btn-primary">+</button>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="btn-group btn-group-sm">
                                <button class="filter-btn contrast-remove btn btn-primary">-</button>
                                <button class="btn btn-secondary btn-disabled" disabled>Contrast</button>
                                <button class="filter-btn contrast-add btn btn-primary">+</button>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="btn-group btn-group-sm">
                                <button class="filter-btn saturation-remove btn btn-primary">-</button>
                                <button class="btn btn-secondary btn-disabled" disabled>Saturation</button>
                                <button class="filter-btn saturation-add btn btn-primary">+</button>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="btn-group btn-group-sm">
                                <button class="filter-btn vibrance-remove btn btn-primary">-</button>
                                <button class="btn btn-secondary btn-disabled" disabled>Vibrance</button>
                                <button class="filter-btn vibrance-add btn btn-primary">+</button>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="btn-group btn-group-sm">
                                <button class="filter-btn sharpen-remove btn btn-primary">-</button>
                                <button class="btn btn-secondary btn-disabled" disabled>Sharpen</button>
                                <button class="filter-btn sharpen-add btn btn-primary">+</button>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="btn-group btn-group-sm">
                                <button class="filter-btn blur-remove btn btn-primary">-</button>
                                <button class="btn btn-secondary btn-disabled" disabled>Blur</button>
                                <button class="filter-btn blur-add btn btn-primary">+</button>
                            </div>
                        </div>
                    </div>
                    <!-- ./row -->

                    <h4 class="text-center my-3">Effects</h4>

                    <select class="form-select" id="effects">
                        <option value="none">Select Filter</option>
                        <option value="vintage">Vintage</option>
                        <option value="lomo">Lomo</option>
                        <option value="clarity">Clarity</option>
                        <option value="sinCity">Sin City</option>
                        <option value="crossProcess">Cross Process</option>
                        <option value="sunrise">Sunrise</option>
                        <option value="orangePeel">Orange Peel</option>
                        <option value="love">Love</option>
                        <option value="grungy">Grungy</option>
                        <option value="jarques">Jarques</option>
                        <option value="pinhole">Pinhole</option>
                        <option value="oldBoot">Old Boot</option>
                        <option value="glowingSun">Glowing Sun</option>
                        <option value="hazyDays">Hazy Days</option>
                        <option value="nostalgia">Nostalgia</option>
                        <option value="herMajesty">Her Majesty</option>
                        <option value="hemingway">Hemingway</option>
                        <option value="concentrate">Concentrate</option>
                    </select>

                    <div class="row mt-5">
                        <div class="col-md-6">
                            <button id="saveImageToUploads" class="btn btn-primary">Save Image</button>
                        </div>
                        <div class="col-md-6">
                            <button id="clearFilters" class="btn btn-danger">Remove Filters</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'components/footer.php'; ?>