<?php
/** @var string $title */
/** @var \Del\Form\FormInterface $form */
/** @var string $url */
/** @var string $message */
?>
<div class="container">
    <h1><?= $title ?></h1>
    <?= $message ?>
    <div class="breadcrumbs">
        <a href="<?= \preg_replace('#\/create$#', '', $url) ?>">Back</a>
    </div>
    <?= $form->render() ?>
</div>
