<link href="template/css/pages/pricing-tables.css" rel="stylesheet" type="text/css"/>

<div class="row margin-bottom-40">
    <!-- Pricing -->
    <div class="col-md-6">
        <div class="pricing hover-effect">
            <div class="pricing-head">
                <h3><?php
                    echo TEXT_HEADING_EXTENSION ?>
                    <span>
                        <?php
                        echo TEXT_NEW_FEATURES_FOR_YOUR_BUSINESS ?>
                    </span>
                </h3>
            </div>
            <ul class="pricing-content list-unstyled">
                <li>
                    <i class="fa fa-thumbs-o-up"></i> <?php
                    echo TEXT_ONE_OFF_CHARGE ?>
                </li>
                <li>
                    <i class="fa fa-heart"></i> <?php
                    echo TEXT_UPDATES_FOR_FREE ?>
                </li>
                <li>
                    <i class="fa fa-smile-o"></i> <?php
                    echo TEXT_FREE_SUPPORT ?>
                </li>
            </ul>
            <div class="pricing-footer">
                <p style="padding: 7px 0;">
                    <?php
                    echo sprintf(TEXT_EXTENSION_LICENSE_KEY_INFO, str_replace('www.', '', $_SERVER['HTTP_HOST'])) ?>
                </p>

                <?php
                echo '<a target="_balnk" href="https://keruy.com.ua" class="btn btn-success">' . TEXT_BUY_EXTENSION . '</a>' ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">

        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    <b><?php
                        echo TEXT_EXTENSION_FEATURES ?></b>
                </div>

            </div>
            <div class="portlet-body" style="display: block; min-height: 440px;">
                <p><?php
                    echo TEXT_EXTENSION_FEATURES_INFO ?></p>

                <h4><?php
                    echo TEXT_MAIN_FEATURES ?></h4>

                <ul style="list-style:none; padding-left: 0;">
                    <?php
                    $html = '';

                    foreach (explode(',', TEXT_EXT_FEATURES_LIST) as $k => $v) {
                        $p = [
                            '0' => 45,
                            '1' => 36,
                            '2' => 40,
                            '3' => 41,
                            '4' => 106,
                            '5' => 51,
                            '6' => 32,
                            '7' => 62,
                            '8' => 58,
                            '9' => 52,
                        ];

                        $url = 'https://keruy.com.ua/index.php?p=' . (isset($p[$k]) ? $p[$k] : 4);

                        $html .= '
											<li style="padding: 5px 0;"><a href="' . $url . '" target="_blank"><i class="fa fa-check" aria-hidden="true"></i> ' . $v . '</a></li>
										';
                    }

                    echo $html;
                    ?>
                </ul>
                <center><a href="https://keruy.com.ua/extension" target="_blank"
                           class="btn btn-primary"><?php
                        echo TEXT_FULL_LIST_OF_FEATURES ?></a></center>
            </div>
        </div>

    </div>

    <div class="col-md-4">

    </div>

</div>

<?php
$features = [];
$features[] = ['id' => 40, 'img' => 'funnelchart.jpg'];
$features[] = ['id' => 106, 'img' => 'pivot_tables.jpg'];
$features[] = ['id' => 45, 'img' => 'calendar.jpg'];
$features[] = ['id' => 37, 'img' => 'gaphic_report.jpg'];
$features[] = ['id' => 43, 'img' => 'map.jpg'];
$features[] = ['id' => 41, 'img' => 'kanban.jpg'];
$features[] = ['id' => 44, 'img' => 'mind_map.jpg'];
$features[] = ['id' => 36, 'img' => 'gantt.jpg'];

$html = '<ul class="list-inline">';

foreach ($features as $value) {
    $html .= '
            <li style="padding: 10px;">
                <a href="https://keruy.com.ua/index.php?p=' . $value['id'] . '" target="_blank">
                    <img style="border: 1px solid #adadad" src="images/sliders' .  '/' . $value['img'] . '" alt="" />
                </a>
            </li>
            ';
}

$html .= '</ul>';

echo $html;

?>