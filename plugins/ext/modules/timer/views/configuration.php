<h3 class="page-title"><?php
    echo TEXT_EXT_TIMER_CONFIGURATION ?></h3>

<p><?php
    echo TEXT_EXT_TIMER_CONFIGURATION_INFO ?></p>

<?php
$entities_list = entities::get_tree();
$choices = access_groups::get_choices();
?>

<table class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th>#</th>
        <th width="100%"><?php
            echo TEXT_NAME ?></th>
        <th><?php
            echo TEXT_ACCESS ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if (count($entities_list) == 0) {
        echo '<tr><td colspan="4">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
    } ?>
    <?php
    foreach ($entities_list as $v): ?>
        <tr>
            <td><?php
                echo $v['id'] ?></td>
            <td><?php
                echo str_repeat('&nbsp;-&nbsp;', $v['level']) . $v['name'] ?></td>
            <td><?php

                $attributes = [
                    'class' => 'form-control chosen-select input-xlarge timer-users-groups',
                    'multiple' => 'multiple',
                    'data-entities-id' => $v['id'],
                    'data-placeholder' => TEXT_SELECT_SOME_VALUES
                ];

                echo select_tag('users_groups[]', $choices, timer::get_configuration($v['id']), $attributes);

                ?></td>
        </tr>
    <?php
    endforeach ?>
    </tbody>
</table>


<script>
    $(function () {
        $(".timer-users-groups").change(function () {

            $.ajax({
                type: "POST", url: "<?php echo url_for('ext/timer/configuration') ?>", data: {
                    action: "save",
                    users_groups: $(this).val(),
                    entities_id: $(this).attr("data-entities-id"),
                }
            });
        })
    })
</script>
