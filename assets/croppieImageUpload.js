(function ($) {
    'use strict';

    $.fn.croppieImageUpload = function (options) {
        var defaults = {
            aspectRatio: 1,
            modalSel: '',
            containerSel: '',
            cropInputSel: '',
            resultImageSel: '',
            croppieOptions: [],

            imageTag: 'img',
            imageAttrs: {
                class: 'img-responsive'
            },
            imageCSS: {}
        };

        var settings = $.extend(true, {}, defaults, options);

        return this.each(function () {
            var $input = $(this),
                $container = $input.closest(settings.containerSel),
                $cropInput = $container.find(settings.cropInputSel),
                $modal = $(settings.modalSel),
                $resultImage = $container.find(settings.resultImageSel);

            // Fire when new image select in input
            var changeImage = function (src) {
                var $image, $cropper,
                    $body = $modal.find('.modal-body');

                var $footer = $('<p>' +
                    '<button type="button" class="btn btn-primary btn-save" data-dismiss="modal">Сохранить</button>' +
                    '<button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">Отмена</button>' +
                    '</p>');

                $footer.find('.btn-save').on('click', function (e) {
                    $cropper.croppie('result', {
                        type: 'base64',
                        size: 'viewport',
                        format: 'jpeg'
                    }).then(function (resp) {
                        $cropInput.val(resp);
                        $resultImage.empty();
                        $('<img/>').attr('src', resp).appendTo($resultImage);
                    });
                    e.preventDefault();
                });

                $footer.find('.btn-cancel').on('click', function () {
                    $input.val('');
                });

                $body.empty();
                $modal.modal('show');

                $modal.one('shown.bs.modal', function () {
                    $image = $('<' + settings.imageTag + '/>')
                        .attr('src', src)
                        .one('load', function () {
                            $image.attr(settings.imageAttrs).css(settings.imageCSS);

                            var $imageWrapper = $('<div class="croppie-image-upload__container"></div>').append($image);

                            $body.append($imageWrapper).append($footer);

                            var w = $imageWrapper.innerWidth();
                            var vw = w - 80;
                            var wh = Math.round(vw / settings.aspectRatio);
                            $imageWrapper.height(wh + 80);

                            var croppieOptions = {
                                showZoomer: false,
                                viewport: {
                                    width: vw,
                                    height: wh,
                                    type: 'square'
                                }
                            };
                            croppieOptions = $.extend(true, {}, croppieOptions, settings.croppieOptions);

                            $cropper = $image.croppie(croppieOptions);
                            $cropper.croppie('bind', {url: src});
                        });
                });
            };

            $input.on('change', function () {
                var file = this.files[0];
                var reader = new FileReader();

                reader.onloadend = function () {
                    changeImage(reader.result);
                };

                if (file) {
                    reader.readAsDataURL(file);
                }
            });
        });
    };

}(jQuery));
