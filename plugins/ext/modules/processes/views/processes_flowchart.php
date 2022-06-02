<h3 class="page-title"><?php
    echo TEXT_EXT_PROCESSES . ': ' . TEXT_FLOWCHART ?></h3>

<?php
$flowchart = new processes_flowchart;
$flowchart->prepare_data();
?>


<div class="row">
    <div class="col-md-3">
        <?php
        echo '<a class="btn btn-default" href="' . url_for(
                'ext/processes/processes'
            ) . '">' . TEXT_BUTTON_BACK . '</a>&nbsp;&nbsp;' ?>
    </div>
    <div class="col-md-8" style="text-align: right;">
        <span class="label" style="background-color: #999999"><?php
            echo TEXT_EXT_PROCESS ?></span>
        <span class="label" style="background-color: #eaac44"><?php
            echo TEXT_FILTERS ?></span>
        <span class="label" style="background-color: #79b2ff"><?php
            echo TEXT_EXT_PROCESSES_ACTIONS ?></span>
        <span class="label" style="background-color: #68b857;"><?php
            echo TEXT_FIELDS ?></span>
    </div>
</div>
<br>
<div id="flowchart" class="flowchart" style="height: <?php
echo($flowchart->height) ?>px;"></div>

<script>
    $(function () {

        var cy = window.cy = cytoscape({
            container: document.getElementById('flowchart'),

            boxSelectionEnabled: false,
            autounselectify: true,
            wheelSensitivity: 0.1,

            style: [
                {
                    selector: 'node.process',
                    css: {
                        'shape': 'octagon',
                        'content': 'data(name)',
                        'text-valign': 'top',
                        'text-halign': 'left',
                        'font-size': '4px',
                        'text-wrap': 'wrap',
                        'height': '20',
                        'width': '20',
                    }
                },
                {
                    selector: 'node.process_filter',
                    css: {
                        'shape': 'diamond',
                        'content': 'data(name)',
                        'text-valign': 'bottom',
                        'text-halign': 'left',
                        'background-color': '#f0ad4e',
                        'font-size': '4px',
                        'text-wrap': 'wrap',
                        'height': '20',
                        'width': '20',

                    }
                },
                {
                    selector: 'node.actions',
                    css: {
                        'shape': 'ellipse',
                        'content': 'data(name)',
                        'text-valign': 'top',
                        'text-halign': 'right',
                        'background-color': '#6FB1FC',
                        'font-size': '4px',
                        'text-wrap': 'wrap',
                        'height': '20',
                        'width': '20',

                    }
                },
                {
                    selector: 'node.field',
                    css: {
                        'shape': 'rectangle',
                        'content': 'data(name)',
                        'text-valign': 'center',
                        'text-halign': 'right',
                        'background-color': '#5cb85c',
                        'font-size': '4px',
                        'text-wrap': 'wrap',
                        'height': '8',
                        'width': '8',

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
                        'width': 0.5,
                        'curve-style': 'bezier',
                        'content': 'data(label)',
                        'font-size': '5',
                        'line-color': '#c3c3c3',
                        'target-arrow-color': '#c3c3c3',
                        'arrow-scale': 0.4,
                        "overlay-padding": "3px"

                    }
                },
            ],

            elements: {
                nodes: [
                    <?php echo str_replace('<br>', '\n', implode(",\n", $flowchart->nodes)) ?>
                ],
                edges: [
                    <?php echo implode(",\n", $flowchart->edges) ?>
                ]
            },

            layout: {
                name: 'preset',
                padding: 20,
            }
        });


        cy.$('node').on('click', function (e) {
            var node = e.target;

            if (node.id().indexOf('process_filter_') != -1) {
                window.open('<?php echo url_for(
                    'ext/processes/filters'
                ) ?>&process_id=' + node.id().replace('process_filter_', ''), '_blank');
                return true;
            }

            if (node.id().indexOf('process_') != -1) {
                window.open('<?php echo url_for(
                    'ext/processes/actions'
                ) ?>&process_id=' + node.id().replace('process_', ''), '_blank');
                return true;
            }

            if (node.id().indexOf('action_filter_') != -1) {
                window.open('<?php echo url_for(
                    'ext/processes/actions_filters'
                ) ?>&process_id=' + node.data().process_id + '&actions_id=' + node.id().replace('action_filter_', ''), '_blank');
                return true;
            }

            if (node.id().indexOf('action_') != -1) {
                window.open('<?php echo url_for(
                    'ext/processes/fields'
                ) ?>&process_id=' + node.data().process_id + '&actions_id=' + node.id().replace('action_', ''), '_blank');
                return true;
            }

        });
    })

</script>

<script src="js/cytoscape.js-master/dist/cytoscape.min.js"></script>