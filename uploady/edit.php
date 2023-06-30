<?php include_once 'session.php'; ?>

<?php include_once 'logic/editFileLogic.php'; ?>

<?php include_once 'components/header.php'; ?>

<div class="container pb-5 pt-5">
    <div class="row justify-content-center text-center">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <div class="row">
                <div class="col-md-8 m-auto">

                    <input type="hidden" id="file_name" value="<?= $file['filename'] ?>">

                    <div id="alert">
                    </div>

                    <img name="canvas" id="canvas" src="<?= $picture ?>"></img>

                    <h4 class="text-center my-3"><?= $lang['filters_title'] ?></h4>

                    <div class="row my-4 text-center">
                        <?php foreach ($filters as $filter) : ?>
                            <div class="col-md-3 mb-3">
                                <div class="btn-group btn-group-sm">
                                    <button class="filter-btn <?= strtolower($filter)  ?>-remove btn btn-primary">-</button>
                                    <button class="btn btn-secondary btn-disabled" disabled><?= $filter ?></button>
                                    <button class="filter-btn <?= strtolower($filter)  ?>-add btn btn-primary">+</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- ./row -->

                    <h4 class="text-center my-3"><?= $lang['effects_title'] ?></h4>

                    <select class="form-select" id="effects">
                        <option value="none"><?= $lang['select_effect'] ?></option>
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
                        <div class="col-md-6 mb-3">
                            <button id="saveImageToUploads" class="btn btn-primary"><?= $lang['save_image_btn'] ?></button>
                        </div>
                        <div class="col-md-6">
                            <button id="clearFilters" class="btn btn-danger"><?= $lang['remove_filter_btn'] ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'components/footer.php'; ?>