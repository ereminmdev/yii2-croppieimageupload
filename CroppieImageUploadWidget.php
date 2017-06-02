<?php

namespace ereminmdev\yii2\croppieimageupload;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Class croppie_image_upload
 * @package common\widgets\croppie_image_upload
 */
class CroppieImageUploadWidget extends InputWidget
{
    /**
     * @var string crop ratio
     * format is width:height where width and height are both floats
     * if empty and has model, will be got from CropImageUploadBehavior
     */
    public $ratio = 1;
    /**
     * @var string bs modal window selector
     */
    public $modalSel;
    /**
     * @var string attribute name storing crop value or crop value itself if no model
     * if empty and has model, will be got from CropImageUploadBehavior
     */
    public $cropField;
    /**
     * @var string imageU url where uploaded file is stored
     * if empty and has model, will be got from CropImageUploadBehavior
     */
    public $imageUrl;
    /**
     * @var array the options for the Croppie plugin.
     * Please refer to the Croppie documentation page for possible options.
     * @see https://foliotek.github.io/Croppie/#documentation
     */
    public $croppieOptions = [];
    /**
     * @var array the options for the croppieImageUpload plugin.
     */
    public $clientOptions = [];
    /**
     * @var string
     */
    public $template = "{crop_input}\n{input}\n{image}";
    /**
     * @var array different parts of the input. This will be used together with
     * [[template]] to generate the final field HTML code. The keys are the token names in [[template]],
     * while the values are the corresponding HTML code. Valid tokens include `{input}`, `{crop_input}` and `{image}`.
     * Note that you normally don't need to access this property directly as
     * it is maintained by various methods of this class.
     */
    public $parts = [];

    protected $crop_id;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Html::addCssClass($this->field->options, 'field-croppie-image-upload');

        if ($this->cropField === null) {
            $this->cropField = $this->attribute;
        }

        if ($this->hasModel()) {
            $model = $this->model;
            $behavior = $model->hasMethod('findImageBehavior') ? $model->findImageBehavior($this->attribute) : null;
            if ($behavior !== null) {
                $this->ratio = $behavior->ratio;
                $this->imageUrl = $this->imageUrl === null ? $model->getImageUrl($this->attribute) : $this->imageUrl;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo $this->renderTemplate();

        $options = ArrayHelper::merge([
            'aspectRatio' => $this->ratio,
            'modalSel' => $this->modalSel,
            'containerSel' => '.field-croppie-image-upload',
            'cropInputSel' => '#' . $this->options['id'] . '_crop',
            'resultImageSel' => '.croppie-image-upload__image',
            'croppieOptions' => $this->croppieOptions,
        ], $this->clientOptions);

        $this->registerPlugin($options);
    }

    /**
     * @param string|callable $content the content within the field container.
     * @return string the rendering result.
     */
    public function renderTemplate($content = null)
    {
        if ($content === null) {
            if (!isset($this->parts['{input}'])) {
                $this->parts['{input}'] = $this->renderInput();
            }
            if (!isset($this->parts['{crop_input}'])) {
                $this->parts['{crop_input}'] = $this->renderCropInput();
            }
            if (!isset($this->parts['{image}'])) {
                $this->parts['{image}'] = Html::tag('div', $this->renderImage(), ['class' => 'croppie-image-upload__image']);
            }
            $content = strtr($this->template, $this->parts);

        } elseif (!is_string($content)) {
            $content = call_user_func($content, $this);
        }

        return $content;
    }

    /**
     * @return string
     */
    public function renderInput()
    {
        if ($this->hasModel()) {
            return Html::activeInput('file', $this->model, $this->attribute, $this->options);
        } else {
            return Html::fileInput($this->name, $this->value, $this->options);
        }
    }

    /**
     * @return string
     */
    public function renderCropInput()
    {
        $options = [
            'id' => $this->options['id'] . '_crop',
        ];

        if ($this->cropField) {
            if ($this->hasModel()) {
                return Html::activeHiddenInput($this->model, $this->cropField, $options);
            } else {
                return Html::hiddenInput($this->cropField, $options);
            }
        }

        return '';
    }

    /**
     * @return string
     */
    public function renderImage()
    {
        $options = [
            'class' => 'img-responsive',
        ];

        return $this->imageUrl ? Html::img($this->imageUrl, $options) : '';
    }

    /**
     * @param array $options
     */
    protected function registerPlugin($options)
    {
        $view = $this->getView();

        CroppieImageUploadAsset::register($view);

        $view->registerJs('jQuery("#' . $this->options['id'] . '").croppieImageUpload(' . json_encode($options) . ');');
    }
}
