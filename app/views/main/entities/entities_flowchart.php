<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<h3 class="page-title"><?= \K::$fw->TEXT_ENTITIES_HEADING . ' - ' . \K::$fw->TEXT_FLOWCHART ?></h3>

<div class="row">
    <div class="col-md-3">
        <?= '<a class="btn btn-default" href="' . \Helpers\Urls::url_for(
                'main/entities/entities'
            ) . '">' . \K::$fw->TEXT_BUTTON_BACK . '</a>&nbsp;&nbsp;' ?>
    </div>
    <div class="col-md-8" style="text-align: right;">
        <?= \Helpers\App::tooltip_icon(\K::$fw->TEXT_ENTITIES_FLOWCHART_INFO) ?>
        <span class="label label-info"><?= \K::$fw->TEXT_FIELDTYPE_USERS_TITLE ?></span>
        <span class="label label-warning"><?= \K::$fw->TEXT_FIELDTYPE_ENTITY_TITLE ?></span>
        <span class="label label-success"><?= \K::$fw->TEXT_FIELDTYPE_RELATED_RECORDS_TITLE ?></span>
        <span class="label" style="background-color: #17a2b8;"><?= \K::$fw->TEXT_FIELDTYPE_FORMULA_TITLE ?></span>
    </div>
</div>

<?php
$flowchart = new \Tools\Flowchart\Entities_flowchart();
$flowchart->prepare_data();
?>

<br>
<div id="flowchart" class="flowchart" style="height: <?php
echo $flowchart->height + 500 ?>px;"></div>

<script>
    $(function () {

        var cy = window.cy = cytoscape({
            container: document.getElementById('flowchart'),

            boxSelectionEnabled: false,
            autounselectify: true,
            wheelSensitivity: 0.1,

            style: [
                {
                    selector: 'node',
                    css: {
                        'shape': 'data(faveShape)',
                        'content': 'data(name)',
                        'text-valign': 'data(textValign)',
                        'text-halign': 'data(textHalign)',
                        'background-color': 'data(faveColor)',
                        'font-size': 'data(fontSize)',
                        'text-wrap': 'wrap',
                        'text-max-width': 85,
                        'height': 'data(nodeSize)',
                        'width': 'data(nodeSize)',
                        'border-width': 'data(borderWidth)',
                        'border-color': '#cccccc',
                        "overlay-padding": "3px"
                    }
                },
                {
                    selector: 'edge',
                    css: {
                        'target-arrow-shape': 'data(arrowShape)',
                        'source-arrow-shape': 'data(sourceShape)',
                        'width': 'data(width)',
                        'font-size': '5',
                        'line-color': 'data(lineColor)',
                        'target-arrow-color': 'data(lineColor)',
                        'source-arrow-color': 'data(lineColor)',
                        "overlay-padding": "2px",
                        'arrow-scale': 0.5,

                    }
                },
                {
                    selector: '$node > node',
                    css: {
                        'padding-top': '5px',
                        'padding-left': '5px',
                        'padding-bottom': '5px',
                        'padding-right': '5px',
                        "overlay-padding": "5px"
                    }
                },
                {
                    selector: 'edge.relation',
                    css: {
                        'curve-style': 'unbundled-bezier',
                        'arrow-scale': 0.3,
                        'width': 0.5,
                    }
                },
                {
                    selector: 'edge.function',
                    css: {
                        'line-style': 'dotted',
                        'curve-style': 'unbundled-bezier',
                        'arrow-scale': 0.3,
                        'width': 0.5,
                    }
                },

            ],

            elements: {
                nodes: [
                    <?= str_replace('<br>', '\n', implode(",\n", $flowchart->nodes)) ?>
                ],
                edges: [
                    <?= implode(",\n", $flowchart->edges) ?>
                ]
            },

            layout: {
                name: 'preset',
                padding: 25
            }
        });

        //cy.$('#entity_25').qtip({content: 'Hello!', style: {classes: 'qtip-bootstrap'}})

        <?php
        foreach ($flowchart->tips as $tip) {
            echo "cy.$('#" . $tip['id'] . "').qtip({content: '" . addslashes(
                    str_replace(["\n", "\r", "\n\r", "<br><br>"], '<br>', $tip['content'])
                ) . "', style: {classes: 'qtip-bootstrap'}}); \n";
        }
        ?>

        cy.$('node').on('click', function (e) {
            var node = e.target;
            if (node.id().indexOf('entity_') != -1) {
                window.open('<?= \Helpers\Urls::url_for(
                    'main/entities/fields',
                    'entities_id='
                ) ?>' + node.id().replace('entity_', ''), '_blank');
                return true;
            }
        });
    })
</script>

<script src="<?= \K::$fw->DOMAIN ?>js/cytoscape.js-master/dist/cytoscape.min.js"></script>
<script src="<?= \K::$fw->DOMAIN ?>js/cytoscape.js-master/qtip/jquery.qtip.min.js"></script>
<link href="<?= \K::$fw->DOMAIN ?>js/cytoscape.js-master/qtip/jquery.qtip.min.css" rel="stylesheet" type="text/css"/>
<script src="<?= \K::$fw->DOMAIN ?>js/cytoscape.js-master/qtip/cytoscape-qtip.js"></script>