<link href="<?php echo getCDN(); ?>node_modules/croppie/croppie.css" rel="stylesheet" type="text/css" />
<script src="<?php echo getCDN(); ?>node_modules/croppie/croppie.min.js" type="text/javascript"></script>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo __('Title and Logo'); ?> </div>
    <div class="panel-body">
        <?php
        include $global['systemRootPath'] . 'view/ImageMagick.check.php';
        ?>
        <form id="updateConfigForm">
            <div class="row">
                <div class="col-md-6">
                    <label class="col-md-4 control-label"><?php echo __("Web site title"); ?></label>
                    <div class="col-md-8 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                            <input id="inputWebSiteTitle" placeholder="<?php echo __("Web site title"); ?>" class="form-control" type="text" value="<?php echo $config->getWebSiteTitle(); ?>" maxlength="45">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="col-md-4 control-label">
                        <?php echo __("Your Logo"); ?> (250x70)
                    </label>
                    <div class="col-md-8 ">
                        <div id="croppieLogo"></div>
                        <a id="logo-btn" class="btn btn-default btn-xs btn-block"><?php echo __("Upload a logo"); ?></a>
                    </div>
                    <input type="file" id="logo" value="Choose a Logo" accept="image/*" style="display: none;" />
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-block btn-primary btn-lg"><?php echo __("Save"); ?> <span class="fa fa-save"></span></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    var logoCrop;
    var logoSmallCrop;
    var theme;

    function readFile(input, c) {
        console.log("read file");
        if ($(input)[0].files && $(input)[0].files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                c.croppie('bind', {
                    url: e.target.result
                }).then(function() {
                    console.log('jQuery bind complete');
                });

            }

            reader.readAsDataURL($(input)[0].files[0]);
        } else {
            avideoAlert("Sorry - you're browser doesn't support the FileReader API");
        }
    }

    var logoImgBase64;

    $(document).ready(function() {
        // start croppie logo
        $('#logo').on('change', function() {
            readFile(this, logoCrop);
        });
        $('#logo-btn').on('click', function(ev) {
            $('#logo').trigger("click");
        });
        $('#logo-result-btn').on('click', function(ev) {
            logoCrop.croppie('result', {
                type: 'canvas',
                size: 'viewport'
            }).then(function(resp) {

            });
        });

        logoCrop = $('#croppieLogo').croppie({
            url: '<?php echo $global['webSiteRootURL'], $config->getLogo(); ?>?' + Math.random(),
            enableExif: true,
            enforceBoundary: false,
            mouseWheelZoom: false,
            viewport: {
                width: 250,
                height: 70
            },
            boundary: {
                width: 250,
                height: 70
            }
        });
        setTimeout(function() {
            logoCrop.croppie('setZoom', 1);
        }, 1000);


        $('#updateConfigForm').submit(function(evt) {
            evt.preventDefault();
            modal.showPleaseWait();
            $('#tabRegularLink').tab('show');
            setTimeout(function() {
                logoCrop.croppie('result', {
                    type: 'canvas',
                    size: 'viewport'
                }).then(function(resp) {
                    logoImgBase64 = resp;
                    $.ajax({
                        url: webSiteRootURL + 'admin/customize_settings_nativeUpdate.json.php',
                        data: {
                            "logoImgBase64": logoImgBase64,
                            "webSiteTitle": $('#inputWebSiteTitle').val(),
                        },
                        type: 'post',
                        success: function(response) {
                            if (response.status === "1") {
                                avideoAlertSuccess(__("Your configurations has been updated!"));
                            } else {
                                avideoAlertError(__("Your configurations has NOT been updated!"));
                            }
                            modal.hidePleaseWait();
                        }
                    });
                });
            }, 500);

        });
    });
</script>
