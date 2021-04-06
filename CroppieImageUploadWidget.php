<?php

namespace ereminmdev\yii2\croppieimageupload;

use Yii;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

/**
 * Class croppie_image_upload
 * @package common\widgets\croppie_image_upload
 */
class CroppieImageUploadWidget extends InputWidget
{
    /**
     * @var bool use croppie
     */
    public $crop = true;
    /**
     * @var string crop ratio
     * format is width:height where width and height are both floats
     * If not set and has model, will be got from CropImageUploadBehavior
     */
    public $ratio;
    /**
     * @var string bs modal window selector
     * If not set, will be created Modal::widget().
     */
    public $modalSel;
    /**
     * @var string attribute name storing crop value or crop value itself if no model
     * if not set, will be the same as $attribute
     */
    public $cropField;
    /**
     * @var string resulting image selector
     */
    public $resultImageSel = '.img-result';
    /**
     * @var array the options for the Croppie plugin.
     * Please refer to the Croppie documentation page for possible options.
     * @see https://foliotek.github.io/Croppie/#documentation
     */
    public $croppieOptions = [];
    /**
     * @var array of options for croppie result method
     */
    public $croppieResultOpts = [];
    /**
     * @var array the options for the $.fn.croppieImageUpload plugin.
     */
    public $clientOptions = [];
    /**
     * @var string to render $parts into html string
     */
    public $template = "{crop_input}\n{input}";
    /**
     * @var array different parts of the input. This will be used together with
     * [[template]] to generate the final field HTML code. The keys are the token names in [[template]],
     * while the values are the corresponding HTML code. Valid tokens include `{input}` and `{crop_input}`.
     * Note that you normally don't need to access this property directly as
     * it is maintained by various methods of this class.
     */
    public $parts = [];
    /**
     * @var string class name added to field block
     */
    public $containerClass = 'field-croppie-image-upload';

    protected $crop_id;


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        Html::addCssClass($this->field->options, $this->containerClass);

        if ($this->cropField === null) {
            $this->cropField = $this->attribute;
        }

        if ($this->hasModel()) {
            $model = $this->model;
            $behavior = $model->hasMethod('findCroppieBehavior') ? $model->findCroppieBehavior($this->attribute) : null;
            if ($behavior !== null) {
                $this->crop = $behavior->crop;
                $this->ratio = $behavior->ratio;
                $this->croppieOptions = ArrayHelper::merge($this->croppieOptions, $behavior->croppieOptions);
                $this->croppieResultOpts = ArrayHelper::merge($this->croppieResultOpts, $behavior->croppieResultOpts);
            }
        }

        $form = $this->field->form;
        if (!isset($form->options['enctype'])) {
            $form->options['enctype'] = 'multipart/form-data';
        }

        $this->options['accept'] = 'image/*';
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo $this->renderTemplate();

        if ($this->modalSel === null) {
            $this->modalSel = '#' . $this->options['id'] . '_modal';
            echo Modal::widget([
                'options' => [
                    'id' => $this->options['id'] . '_modal',
                ],
            ]);
        }

        $options = ArrayHelper::merge([
            'aspectRatio' => $this->ratio ? $this->ratio : 1,
            'modalSel' => $this->modalSel,
            'containerSel' => '.' . $this->containerClass,
            'cropInputSel' => '#' . $this->options['id'] . '_crop',
            'resultImageSel' => $this->resultImageSel,
            'btnSaveText' => Yii::t('app', 'Save'),
            'btnCancelText' => Yii::t('app', 'Cancel'),
            'btnRotateLeft' => '<i class="fa fa-rotate-left"></i>',
            'btnRotateRight' => '<i class="fa fa-rotate-right"></i>',
            'croppieOptions' => $this->croppieOptions,
            'croppieResultOpts' => $this->croppieResultOpts,
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
     * @param array $options
     */
    protected function registerPlugin($options)
    {
        $view = $this->getView();

        if ($this->crop) {
            CroppieImageUploadAsset::register($view);
            $view->registerJs('jQuery("#' . $this->options['id'] . '").croppieImageUpload(' . Json::encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT) . ');');
        }
    }
}
