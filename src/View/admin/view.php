<?php
/** @var string $title */
/** @var string[] $tableColumns */
/** @var object $record */
/** @var array $transformers */
/** @var array $prefixes */
/** @var array $suffixes */
/** @var string $url */
?>
<div class="container">
    <h1><?= $title ?></h1>
    <div class="breadcrumbs">
        <a href="<?= \preg_replace('#\/\d+$#', '', $url) ?>">Back</a>
    </div>
    <table class="table table-condensed table-striped table-hover table-bordered">
        <thead></thead>
        <tbody>
        <?php
            foreach ($tableColumns as $column) {
                echo '<tr>';
                echo '<td>' . $column . '</td>';
                $getter = 'get' . ucfirst($column);
                $value = \property_exists($record, $column) ? $record->$getter() : '';

                if ($transformers[$column] !== null) {
                    $transformer = $transformers[$column];
                    $value = $transformer->input($value);
                }

                echo '<td>' . $prefixes[$column] .  $value . $suffixes[$column] . '</td>';
                echo '</tr>';
            }
        ?>
        </tbody>
    </table>
</div>
