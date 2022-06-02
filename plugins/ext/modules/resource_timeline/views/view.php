<h3 class="page-title"><?php
    echo $reports['name'] ?></h3>

<?php
require(component_path('ext/resource_timeline/report')); ?>

<script>
    function get_resource_timeline_height() {
        if ($(window).height() > 400) {
            return $(window).height() - 170;
        } else {
            return $(window).height();
        }
    }

    $(function () {
        $(window).resize(function () {
            $('#resource_timeline<?php echo $reports['id'] ?>').fullCalendar('option', 'height', get_resource_timeline_height());
        });
    })
</script>
