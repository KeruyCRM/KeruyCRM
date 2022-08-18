<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/entities/navigation')); ?>

<h3 class="page-title"><?= \K::$fw->field_info['name'] . ': ' . \K::$fw->TEXT_FLOWCHART ?></h3>

<div class="row">
    <div class="col-md-3">
        <?= '<a class="btn btn-default" href="' . \Helpers\Urls::url_for(
            'main/entities/fields_choices',
            'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
        ) . '">' . \K::$fw->TEXT_BUTTON_BACK . '</a>' ?>
    </div>
    <div class="col-md-8" style="text-align: right;">
        <span class="label" style="background-color: #eaac44"><?= \K::$fw->TEXT_FILTERS ?></span>
        <span class="label" style="background-color: #68b857;"><?= \K::$fw->TEXT_FIELDS ?></span>
    </div>
</div>
<br>
<div id="flowchart" class="flowchart" style="height: <?= \K::$fw->flowchart->height ?>px;"></div>

<script>
    $(function () {
        var cy = window.cy = cytoscape({
            container: document.getElementById('flowchart'),
            boxSelectionEnabled: false,
            autounselectify: true,
            wheelSensitivity: 0.1,
            style: [
                {
                    selector: 'node.choice_filter',
                    css: {
                        'shape': 'diamond',
                        'content': 'data(name)',
                        'text-valign': 'top',
                        'text-halign': 'left',
                        'background-color': '#f0ad4e',
                        'font-size': '5px',
                        'text-wrap': 'wrap',
                        'height': '30',
                        'width': '30',

                    }
                },
                {
                    selector: 'node.choice',
                    css: {
                        'shape': 'rectangle',
                        'content': 'data(name)',
                        'text-valign': 'center',
                        'text-halign': 'right',
                        'background-color': '#5cb85c',
                        'font-size': '7px',
                        'text-wrap': 'wrap',
                        'height': '15',
                        'width': '15',

                    }
                },
                {
                    selector: 'node',
                    css: {
                        "overlay-padding": "3px"
                    }
                },
                {
                    selector: 'edge',
                    css: {
                        'target-arrow-shape': 'triangle',
                        'width': 1,
                        'curve-style': 'bezier',
                        'content': 'data(label)',
                        'font-size': '5',
                        'line-color': '#c3c3c3',
                        'target-arrow-color': '#c3c3c3',
                        'arrow-scale': 0.7,
                        "overlay-padding": "3px"

                    }
                },
            ],
            elements: {
                nodes: [
                    <?= str_replace('<br>', '\n', implode(",\n", \K::$fw->flowchart->nodes)) ?>
                ],
                edges: [
                    <?= implode(",\n", \K::$fw->flowchart->edges) ?>
                ]
            },
            layout: {
                name: 'preset',
                padding: 25
            }
        });

        cy.$('node').on('click', function (e) {
            var node = e.target;
            if (node.id().indexOf('choice_filter_') != -1) {
                window.open('<?= \Helpers\Urls::url_for(
                    'main/entities/fields_choices_filters',
                    'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
                ) ?>&choices_id=' + node.id().replace('choice_filter_', ''), '_blank');
                return true;
            }
        });
    })
</script>

<script src="<?= \K::$fw->DOMAIN ?>js/cytoscape.js-master/dist/cytoscape.min.js"></script>