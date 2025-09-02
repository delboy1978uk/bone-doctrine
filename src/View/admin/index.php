<?php
 /** @var string $title */
 /** @var string[] $tableColumns */
 /** @var array $records */
 /** @var array $transformers */
 /** @var array $prefixes */
 /** @var array $suffixes */
 /** @var string $url */
 /** @var string $messages */
?>
<div class="container">
    <h1>
        <a class="btn btn-primary pull-right mt10" href="<?= $url . '/create' ?>">Create new</a>
        <?= $title ?>
    </h1>
    <?= $messages ?>
    <?= $paginator ?>
    <?= $records->getTotalRecords() ?> records found.
    <table class="table table-condensed table-striped table-hover table-bordered">
        <thead>
            <tr>
                <?php
                foreach ($tableColumns as $column) {
                    echo '<th>' . $column . '</th>';
                }
                ?>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($records as $record) {
            echo '<tr>';

            foreach ($tableColumns as $column) {
                $getter = 'get' . ucfirst($column);
                $value = \property_exists($record, $column) ? $record->$getter() : '';

                if ($transformers[$column] !== null) {
                    $transformer = $transformers[$column];
                    $value = $transformer->input($value);
                }

                echo '<td>' . $prefixes[$column] .  $value . $suffixes[$column] . '</td>';
            }

            echo '<td><a href="' . $url . '/'. $record->getId() . '">view</a></td>';
            echo '<td><a href="' . $url . '/'. $record->getId() . '/edit">edit</a></td>';
            echo '<td><a href="' . $url . '/'. $record->getId() . '/delete">delete</a></td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</div>
