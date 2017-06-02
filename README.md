# yii2-croppieimageupload

Crop image upload for Yii framework.

This widget depend on:
- https://github.com/mohorev/yii2-upload-behavior
- https://foliotek.github.io/Croppie/

## Install

``composer require ereminmdev/yii-croppieimageupload``

## Use

```
public function behaviors()
{
    return [
        ...
        'avatar' => [
            'class' => CroppieImageUploadBehavior::className(),
            'attribute' => 'avatar',
            'scenarios' => ['create', 'update'],
            'placeholder' => '@app/modules/user/assets/images/avatar.jpg',
            'path' => '@webroot/upload/avatar/{id}',
            'url' => '@web/upload/avatar/{id}',
            'thumbs' => [
                'thumb' => ['width' => 42, 'height' => 42, 'mode' => ManipulatorInterface::THUMBNAIL_OUTBOUND],
                'preview' => ['width' => 200, 'height' => 200, 'mode' => ManipulatorInterface::THUMBNAIL_OUTBOUND],
            ],
            'ratio' => 1,
        ],
    ];
}
```

View file:

```php
<?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'photo')->widget(CroppieImageUploadWidget::className()) ?>
    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end(); ?>
```
