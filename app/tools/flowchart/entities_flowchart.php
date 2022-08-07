<?php

namespace Tools\Flowchart;

class Entities_flowchart
{
    public $nodes;
    public $edges;
    public $tips;
    public $fields_step;
    public $height;
    public $entities_fields_count;
    public $entities_coords;
    public $shcema;

    public function __construct()
    {
        $this->fields_step = 8;
        $this->height = 0;

        $this->entities_fields_count = [];
        $this->entities_coords = [];
        $this->nodes = [];
        $this->edges = [];
        $this->tips = [];

        $data = $this->get_shcema();
        $this->shcema = $data['tree'];
    }

    public function get_shcema($parent_id = 0, $tree = [], $level = 0, $x = 0, $y = 0)
    {
        /*$entities_query = db_query(
            "select * from app_entities where parent_id='" . $parent_id . "' order by sort_order, name"
        );*/

        $entities_query = \K::model()->db_fetch('app_entities', [
            'parent_id = ?',
            $parent_id
        ], ['order' => 'sort_order, name']);

        //while ($entities = db_fetch_array($entities_query)) {
        foreach ($entities_query as $entities) {
            $entities = $entities->cast();

            $tree['tree'][] = [
                'id' => $entities['id'],
                'parent_id' => $entities['parent_id'],
                'name' => $entities['name'],
                'notes' => $entities['notes'],
                'sort_order' => $entities['sort_order'],
                'level' => $level,
                'x' => $x,
                'y' => $y,
            ];

            $tree['y'] = $y;

            $tree = $this->get_shcema($entities['id'], $tree, $level + 1, $x + 130, $y);

            $y = $tree['y'];

            $count_fields = 0;
            /*$check_fields_query = db_query(
                "select * from app_fields where entities_id = '" . $entities['id'] . "' and type in ('fieldtype_users','fieldtype_users_ajax','fieldtype_grouped_users','fieldtype_entity','fieldtype_entity_ajax','fieldtype_entity_multilevel','fieldtype_related_records','fieldtype_formula')"
            );*/

            $fields = [
                'fieldtype_users',
                'fieldtype_users_ajax',
                'fieldtype_grouped_users',
                'fieldtype_entity',
                'fieldtype_entity_ajax',
                'fieldtype_entity_multilevel',
                'fieldtype_related_records',
                'fieldtype_formula'
            ];

            $check_fields_query = \K::model()->db_fetch('app_fields', [
                'entities_id = ? and type in (' . \K::model()->quoteToString($fields) . ')',
                $entities['id']
            ]);

            //while ($check_fields = db_fetch_array($check_fields_query)) {
            foreach ($check_fields_query as $check_fields) {
                $check_fields = $check_fields->cast();

                if ($check_fields['type'] == 'fieldtype_formula') {
                    $cfg = new \Models\Main\Fields_types_cfg($check_fields['configuration']);

                    if (strstr($cfg->get('formula'), '{')) {
                        $count_fields++;
                    }
                } else {
                    $count_fields++;
                }
            }

            $y += 30 + ($count_fields * 11);
        }

        return $tree;
    }

    public function prepare_data()
    {
        $this->build_entities_nodes();
        $this->build_users_fields_nodes();
        $this->build_entity_fields_nodes();
        $this->build_related_records_nodes();
        $this->build_functions_nodes();
        $this->build_entities_edges();
        $this->build_tips();
    }

    public function build_entities_nodes()
    {
        foreach ($this->shcema as $entities) {
            //users entity have own color
            $faveColor = ($entities['id'] == 1 ? '#5bc0de' : '#e2e3e5');

            //entity node
            $this->nodes[] = "{ data: { id: 'entity_" . $entities['id'] . "',name: '" . addslashes(
                    $entities['name']
                ) . "',faveShape:'rectangle',borderWidth:1, nodeSize: 15, faveColor:'{$faveColor}', fontSize:'7px',textValign:'top',textHalign:'center'}, position: { x: " . $entities['x'] . ", y: " . $entities['y'] . " }}";

            //extra parent node to display arrows edge
            $this->nodes[] = "{ data: { id: 'field_0_" . $entities['id'] . "',name: '',parent:'entity_" . $entities['id'] . "',faveShape:'rectangle',borderWidth:1, nodeSize: 0, faveColor:'{$faveColor}', fontSize:'7px',textValign:'top',textHalign:'center'}, position: { x: " . $entities['x'] . ", y: " . $entities['y'] . " }}";

            //hold coordinates for each entity
            $this->entities_coords[$entities['id']] = [$entities['x'], $entities['y']];

            //get max height
            $this->height = ($entities['y'] > $this->height ? $entities['y'] : $this->height);

            //reset fields count
            $this->entities_fields_count[$entities['id']] = 0;
        }
    }

    public function build_users_fields_nodes()
    {
        /*$fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_users','fieldtype_grouped_users')"
        );*/
        $fields_query = \K::model()->db_fetch('app_fields', [
            'type in (' . \K::model()->quoteToString(['fieldtype_users', 'fieldtype_grouped_users']) . ')'
        ]);

        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $fields = $fields->cast();

            //set coordinates for field entity
            $x = $this->entities_coords[$fields['entities_id']][0];
            $y = $this->entities_coords[$fields['entities_id']][1] + ($this->entities_fields_count[$fields['entities_id']] * $this->fields_step);

            $this->nodes[] = "{ data: { id: 'field_" . $fields['id'] . "',name: '" . addslashes(
                    $fields['name']
                ) . "',parent: 'entity_{$fields['entities_id']}', faveShape:'rectangle',borderWidth:0, nodeSize: 4, faveColor:'#5bc0de', fontSize:'4px',textValign:'center',textHalign:'right'}, position: { x: " . $x . ", y: " . $y . " }}";

            $this->entities_fields_count[$fields['entities_id']]++;
        }
    }

    public function build_entity_fields_nodes()
    {
        /*$fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_entity','fieldtype_entity_ajax','fieldtype_entity_multilevel')"
        );*/

        $fields_query = \K::model()->db_fetch('app_fields', [
            'type in (' . \K::model()->quoteToString(
                ['fieldtype_entity', 'fieldtype_entity_ajax', 'fieldtype_entity_multilevel']
            ) . ')'
        ]);

        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $fields = $fields->cast();

            //set coordinates for field entity
            $x = $this->entities_coords[$fields['entities_id']][0];
            $y = $this->entities_coords[$fields['entities_id']][1] + ($this->entities_fields_count[$fields['entities_id']] * $this->fields_step);

            $this->nodes[] = "{ data: { id: 'field_" . $fields['id'] . "',name: '" . addslashes(
                    $fields['name']
                ) . "',parent: 'entity_{$fields['entities_id']}', faveShape:'rectangle',borderWidth:0, nodeSize: 4, faveColor:'#ffc107', fontSize:'4px',textValign:'center',textHalign:'right'}, position: { x: " . $x . ", y: " . $y . " }}";

            $this->entities_fields_count[$fields['entities_id']]++;

            $cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);

            $this->edges[] = "{ data: { id: 'edge_field_{$fields['id']}', source: 'entity_{$cfg->get('entity_id')}', target: 'field_{$fields['id']}',lineColor: '#ffc107',arrowShape:'triangle',sourceShape:'none',width:1},classes:'relation' }";
        }
    }

    public function build_related_records_nodes()
    {
        $skip_related_records_fields = [];
        //$fields_query = db_query("select * from app_fields where type in ('fieldtype_related_records')");

        $fields_query = \K::model()->db_fetch('app_fields', [
            'type in (' . \K::model()->quoteToString(['fieldtype_related_records']) . ')'
        ]);

        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $fields = $fields->cast();

            //set coordinates for field entity
            $x = $this->entities_coords[$fields['entities_id']][0];
            $y = $this->entities_coords[$fields['entities_id']][1] + ($this->entities_fields_count[$fields['entities_id']] * $this->fields_step);

            $this->nodes[] = "{ data: { id: 'field_" . $fields['id'] . "',name: '" . addslashes(
                    $fields['name']
                ) . "',parent: 'entity_{$fields['entities_id']}', faveShape:'rectangle',borderWidth:0, nodeSize: 4, faveColor:'#28a745', fontSize:'4px',textValign:'center',textHalign:'right'}, position: { x: " . $x . ", y: " . $y . " }}";

            $this->entities_fields_count[$fields['entities_id']]++;

            $cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);

            $source = 'entity_' . $cfg->get('entity_id');
            $sourceShape = 'none';

            //check if exist related field
            /*$check_query = db_query(
                "select * from app_fields where type in ('fieldtype_related_records') and entities_id='" . $cfg->get(
                    'entity_id'
                ) . "'"
            );*/

            $check_query = \K::model()->db_fetch('app_fields', [
                'type in (' . \K::model()->quoteToString(['fieldtype_related_records']) . ') and entities_id = ?',
                $cfg->get('entity_id')
            ]);

            //while ($check = db_fetch_array($check_query)) {
            foreach ($check_query as $check) {
                $check = $check->cast();

                $check_cfg = new \Models\Main\Fields_types_cfg($check['configuration']);
                if ($check_cfg->get('entity_id') == $fields['entities_id']) {
                    $source = 'field_' . $check['id'];
                    $sourceShape = 'triangle';
                    $skip_related_records_fields[] = $check['id'];
                }
            }

            if (!in_array($fields['id'], $skip_related_records_fields)) {
                $this->edges[] = "{ data: { id: 'edge_field_{$fields['id']}', source: '{$source}', target: 'field_{$fields['id']}',lineColor: '#28a745',arrowShape:'triangle',sourceShape:'{$sourceShape}',width:1},classes:'relation' }";
            }
        }
    }

    public function build_functions_nodes()
    {
        $skip_functions = [];
        //$fields_query = db_query("select * from app_fields where type in ('fieldtype_formula')");

        $fields_query = \K::model()->db_fetch('app_fields', [
            'type in (' . \K::model()->quoteToString(['fieldtype_formula']) . ')'
        ]);

        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $fields = $fields->cast();

            $cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);

            if (strstr($cfg->get('formula'), '{') and class_exists('functions')) {
                if (!isset($entities_fields_count[$fields['entities_id']])) {
                    $entities_fields_count[$fields['entities_id']] = 0;
                }

                //set coordinates for field entity
                $x = $this->entities_coords[$fields['entities_id']][0];
                $y = $this->entities_coords[$fields['entities_id']][1] + ($this->entities_fields_count[$fields['entities_id']] * $this->fields_step);

                $this->nodes[] = "{ data: { id: 'field_" . $fields['id'] . "',name: '" . addslashes(
                        $fields['name']
                    ) . "',parent: 'entity_{$fields['entities_id']}', faveShape:'rectangle',borderWidth:0, nodeSize: 4, faveColor:'#17a2b8', fontSize:'4px',textValign:'center',textHalign:'right'}, position: { x: " . $x . ", y: " . $y . " }}";

                $this->entities_fields_count[$fields['entities_id']]++;

                $skip_functions_edges = [];

                foreach (\K::$fw->app_functions_cache as $functions) {
                    //simple formula string
                    if (strstr($cfg->get('formula'), '{' . $functions['id'] . '}')) {
                        //set coordinates for field entity
                        $x = $this->entities_coords[$functions['entities_id']][0];
                        $y = $this->entities_coords[$functions['entities_id']][1] + ($this->entities_fields_count[$functions['entities_id']] * $this->fields_step);

                        if (!in_array($functions['id'], $skip_functions)) {
                            $this->nodes[] = "{ data: { id: 'function_" . $functions['id'] . "',name: '" . addslashes(
                                    $functions['name']
                                ) . "',parent: 'entity_{$functions['entities_id']}', faveShape:'diamond',borderWidth:0, nodeSize: 5, faveColor:'#959393', fontSize:'4px',textValign:'center',textHalign:'right'}, position: { x: " . $x . ", y: " . $y . " }}";

                            $skip_functions[] = $functions['id'];

                            $this->entities_fields_count[$functions['entities_id']]++;

                            //function tip
                            $this->tips[] = [
                                'id' => 'function_' . $functions['id'],
                                'content' => \K::$fw->TEXT_EXT_FUNCTION . ': ' . $functions['functions_name'] . '<br>' . \K::$fw->TEXT_FORMULA . ': ' . $functions['functions_formula'] . '<br>' . $functions['notes'],
                            ];
                        }

                        $this->edges[] = "{ data: { id: 'edge_field_{$fields['id']}', source: 'function_{$functions['id']}', target: 'field_{$fields['id']}',lineColor: '#17a2b8',arrowShape:'triangle',sourceShape:'none',width:1},classes:'function' }";
                    }

                    //formula with related items
                    if (preg_match_all('/{(\d+):(\d+)}/', $cfg->get('formula'), $matches)) {
                        foreach ($matches[1] as $matches_key => $functions_id) {
                            if (!isset(\K::$fw->app_functions_cache[$functions_id])) {
                                continue;
                            }

                            $function_info = \K::$fw->app_functions_cache[$functions_id];

                            //set coordinates for field entity
                            $x = ($this->entities_coords[$function_info['entities_id']][0] ?? 0);
                            $y = $this->entities_coords[$function_info['entities_id']][1] + ($this->entities_fields_count[$function_info['entities_id']] * $this->fields_step);

                            if (!in_array($function_info['id'], $skip_functions)) {
                                $this->nodes[] = "{ data: { id: 'function_" . $function_info['id'] . "',name: '" . addslashes(
                                        $function_info['name']
                                    ) . "',parent: 'entity_{$function_info['entities_id']}', faveShape:'diamond',borderWidth:0, nodeSize: 5, faveColor:'#959393', fontSize:'4px',textValign:'center',textHalign:'right'}, position: { x: " . $x . ", y: " . $y . " }}";

                                $skip_functions[] = $function_info['id'];

                                $this->entities_fields_count[$function_info['entities_id']]++;

                                //function tip
                                $this->tips[] = [
                                    'id' => 'function_' . $function_info['id'],
                                    'content' => \K::$fw->TEXT_EXT_FUNCTION . ': ' . $function_info['functions_name'] . '<br>' . \K::$fw->TEXT_FORMULA . ': ' . $function_info['functions_formula'] . '<br>' . $function_info['notes'],
                                ];
                            }

                            if (!in_array($function_info['id'], $skip_functions_edges)) {
                                $this->edges[] = "{ data: { id: 'edge_field_{$fields['id']}', source: 'function_{$function_info['id']}', target: 'field_{$fields['id']}',lineColor: '#17a2b8',arrowShape:'triangle',sourceShape:'none',width:1},classes:'function' }";
                                $skip_functions_edges[] = $function_info['id'];
                            }
                        }
                    }
                }
            }
        }
    }

    public function build_entities_edges()
    {
        foreach ($this->shcema as $entities) {
            //build entity node for parents entities tree
            if (\K::model()->db_count('app_entities', $entities['id'], 'parent_id') > 0) {
                $y = $entities['y'];

                switch (true) {
                    case $this->entities_fields_count[$entities['id']] > 1:
                        $y = $entities['y'] + (($this->entities_fields_count[$entities['id']] - 1) * 4);
                        break;
                }

                $this->nodes[] = "{ data: { id: 'entity_node_" . $entities['id'] . "',name: '',faveShape:'rectangle',borderWidth:0, nodeSize: 2, faveColor:'#cccccc', fontSize:'7px',textValign:'top',textHalign:'center'}, position: { x: " . ($entities['x'] + 100) . ", y: " . $y . " }}";
                $this->edges[] = "{ data: { id: 'edge_node_{$entities['id']}_{$entities['id']}', source: 'entity_{$entities['id']}', target: 'entity_node_{$entities['id']}',lineColor: '#cccccc',arrowShape:'none',sourceShape:'none',width:2} }";
            }

            if ($entities['parent_id'] > 0) {
                $y = $entities['y'];

                switch (true) {
                    case $this->entities_fields_count[$entities['id']] > 1:
                        $y = $entities['y'] + (($this->entities_fields_count[$entities['id']] - 1) * 4);
                        break;
                }
                //entity short nodes for tree
                $this->nodes[] = "{ data: { id: 'entity_shortnode_" . $entities['id'] . "',name: '',faveShape:'rectangle',borderWidth:0, nodeSize: 2, faveColor:'#cccccc', fontSize:'7px',textValign:'top',textHalign:'center'}, position: { x: " . ($entities['x'] - 30) . ", y: " . $y . " }}";
                $this->edges[] = "{ data: { id: 'edge_node_{$entities['id']}_{$entities['parent_id']}', source: 'entity_shortnode_{$entities['id']}', target: 'entity_{$entities['id']}',lineColor: '#cccccc',arrowShape:'triangle',sourceShape:'none',width:2} }";

                $this->edges[] = "{ data: { id: 'edge_{$entities['id']}_{$entities['parent_id']}', source: 'entity_node_{$entities['parent_id']}', target: 'entity_shortnode_{$entities['id']}',lineColor: '#cccccc',arrowShape:'none',sourceShape:'none',width:2} }";
            }
        }
    }

    public function build_tips()
    {
        //$fields_query = db_query("select * from app_fields");

        $fields_query = \K::model()->db_fetch_all('app_fields');

        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $fields = $fields->cast();

            $cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);

            $content = '';

            switch ($fields['type']) {
                case 'fieldtype_formula':
                    $content .= \K::$fw->TEXT_FORMULA . ': ' . $cfg->get('formula') . '<br>';
                    break;
                case 'fieldtype_entity':
                case 'fieldtype_entity_ajax':
                case 'fieldtype_entity_multilevel':
                case 'fieldtype_related_records':
                    $content .= \K::$fw->TEXT_RELATIONSHIP_HEADING . ': ' . \Models\Main\Entities::get_name_by_id(
                            $cfg->get('entity_id')
                        ) . '<br>';
                    break;
                case 'fieldtype_users':
                    $content .= \K::$fw->TEXT_TYPE . ': ' . \K::$fw->TEXT_FIELDTYPE_USERS_TITLE . '<br>';
                    break;
                case 'fieldtype_grouped_users':
                    $content .= \K::$fw->TEXT_TYPE . ': ' . \K::$fw->TEXT_FIELDTYPE_GROUPEDUSERS_TITLE . '<br>';
                    break;
            }

            $content .= $fields['notes'];

            if (strlen($content)) {
                $this->tips[] = [
                    'id' => 'field_' . $fields['id'],
                    'content' => $content,
                ];
            }
        }
    }
}