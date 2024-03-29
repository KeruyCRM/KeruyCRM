<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_years_difference
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_YEARS_DIFFERENCE_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_FIELDTYPE_DAYS_DIFFERENCE_DYNAMIC_INFO
                ) . \K::$fw->TEXT_FIELDTYPE_MYSQL_QUERY_DYNAMIC_QUERY,
            'name' => 'dynamic_query',
            'type' => 'dropdown',
            'choices' => ['0' => \K::$fw->TEXT_NO, '1' => \K::$fw->TEXT_YES],
            'params' => ['class' => 'form-control input-small']
        ];

        $choices = [];
        $choices['today'] = '[' . \K::$fw->TEXT_CURRENT_DATE . ']';
        $choices['date_added'] = '[' . \K::$fw->TEXT_DATE_ADDED . ']';
        /*$fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_input_date','fieldtype_input_datetime','fieldtype_dynamic_date') and entities_id='" . db_input(
                $_POST['entities_id']
            ) . "'"
        );*/

        $typeIn = \K::model()->quoteToString(
            ['fieldtype_input_date', 'fieldtype_input_datetime', 'fieldtype_dynamic_date']
        );

        $fields_query = \K::model()->db_fetch('app_fields', [
            'type in (' . $typeIn . ') and entities_id = ?',
            \K::$fw->POST['entities_id']
        ], [], 'id,name');

        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $fields = $fields->cast();

            $choices[$fields['id']] = $fields['name'];
        }

        $cfg[] = [
            'title' => \K::$fw->TEXT_START_DATE,
            'name' => 'start_date',
            'default' => '',
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => ['class' => 'form-control input-large chosen-select required']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_END_DATE,
            'name' => 'end_date',
            'default' => '',
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => ['class' => 'form-control input-large chosen-select required']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_CALCULATE_DIFFERENCE_DAYS,
            'name' => 'calculate_diff_days',
            'type' => 'checkbox'
        ];

        $cfg[] = [
            'title' => \Helpers\App::tooltip_icon(\K::$fw->TEXT_CALCULATE_TOTALS_INFO) . \K::$fw->TEXT_CALCULATE_TOTALS,
            'name' => 'calculate_totals',
            'type' => 'checkbox'
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_CALCULATE_AVERAGE_VALUE,
            'name' => 'calculate_average',
            'type' => 'checkbox'
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_PREFIX,
            'name' => 'prefix',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_SUFFIX,
            'name' => 'suffix',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        self::prepare_procedure();

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        return false;
    }

    public function process($options)
    {
        return false;
    }

    public function output($options)
    {
        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        $value = $options['value'];

        //add prefix and suffix
        return (strlen($value) ? $cfg->get('prefix') . $value . $cfg->get('suffix') : '');
    }

    public function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $sql = \Models\Main\Reports\Reports::prepare_numeric_sql_filters($filters, '');

        if (count($sql) > 0) {
            \K::$fw->sql_query_having[$options['entities_id']][] = implode(' and ', $sql);
        }

        return $sql_query;
    }

    public static function prepare_procedure()
    {
        $sql = "
CREATE FUNCTION `keruycrm_years_diff`(`start_date` INT, `end_date` INT, `inc_days` TINYINT(1)) RETURNS int(11)
BEGIN  				
  DECLARE years_diff INT;  
  SET years_diff=0;
	
	IF inc_days=1 THEN			
		SET years_diff = TIMESTAMPDIFF(YEAR, DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(0),INTERVAL start_date SECOND),'%Y-%m-%d') , DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(0),INTERVAL end_date SECOND),'%Y-%m-%d') );
	ELSE
		SET years_diff = YEAR(DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(0),INTERVAL end_date SECOND),'%Y-%m-%d')) -  DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(0),INTERVAL start_date SECOND),'%Y-%m-%d');		
	END IF;
				  
  RETURN years_diff;
END;";

        $is_function = false;
        $check_query = \K::model()->db_query_exec(
            'SHOW FUNCTION STATUS WHERE Db = ' . \K::model()->quote(\K::$fw->DB_name)
        );
        //while ($check = db_fetch_array($check_query)) {
        foreach ($check_query as $check) {
            if ($check['Name'] == 'keruycrm_years_diff') {
                $is_function = true;
            }
        }

        if (!$is_function) {
            \K::model()->db_query_exec($sql);
        }
    }

    public static function prepare_query_select($entities_id, $listing_sql_query_select, $prefix = 'e')
    {
        if (isset(\K::$fw->app_fields_cache[$entities_id])) {
            foreach (\K::$fw->app_fields_cache[$entities_id] as $fields) {
                if ($fields['type'] == 'fieldtype_years_difference') {
                    $cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);

                    //skip dynamic query
                    if (isset($cfg->cfg['dynamic_query']) and $cfg->get('dynamic_query') != 1) {
                        continue;
                    }

                    //array to calculate totals in listing
                    if (is_array($listing_sql_query_select)) {
                        $listing_sql_query_select[] = self::prepare_query($fields, $prefix);
                    } else {
                        $listing_sql_query_select .= ',' . self::prepare_query($fields, $prefix);
                    }
                }
            }
        }

        return $listing_sql_query_select;
    }

    public static function prepare_query($fields, $prefix = 'e', $single_select = false, $force_query = false)
    {
        $cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);

        //skip dynamic query
        if (isset($cfg->cfg['dynamic_query']) and $cfg->get('dynamic_query') != 1 and !$force_query) {
            return $prefix . '.field_' . $fields['id'];
        }

        $start_date_field = ($cfg->get('start_date') == 'today' ? time() : ($cfg->get(
            'start_date'
        ) == 'date_added' ? $prefix . '.date_added' : $prefix . '.field_' . (int)$cfg->get('start_date')));
        $end_date_field = ($cfg->get('end_date') == 'today' ? time() : ($cfg->get(
            'end_date'
        ) == 'date_added' ? $prefix . '.date_added' : $prefix . '.field_' . (int)$cfg->get('end_date')));

        if ($single_select) {
            $mysql_query = "(keruycrm_years_diff(" . $start_date_field . "," . $end_date_field . "," . $cfg->get(
                    'calculate_diff_days',
                    0
                ) . "))";
        } else {
            $mysql_query = "keruycrm_years_diff(" . $start_date_field . "," . $end_date_field . "," . $cfg->get(
                    'calculate_diff_days',
                    0
                ) . ") as field_" . (int)$fields['id'];
        }

        return $mysql_query;
    }

    public static function update_items_fields($entities_id, $items_id)
    {
        if (isset(\K::$fw->app_fields_cache[$entities_id])) {
            $forceCommit = \K::model()->forceCommit();

            foreach (\K::$fw->app_fields_cache[$entities_id] as $fields) {
                if ($fields['type'] == 'fieldtype_years_difference') {
                    $cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);

                    //skip dynamic query
                    if (isset($cfg->cfg['dynamic_query']) and $cfg->get('dynamic_query') != 1) {
                        $item_info = \K::model()->db_query_exec_one(
                            "select " . self::prepare_query(
                                $fields,
                                'e',
                                false,
                                true
                            ) . " from app_entity_" . (int)$entities_id . " e where e.id = ?",
                            $items_id
                        );
                        //$item_info = $item_info_query[0] ?? '';

                        $fields_id = $fields['id'];

                        /*db_query(
                            "update app_entity_{$entities_id} set field_{$fields_id}='" . $item_info['field_' . $fields_id] . "' where id='" . db_input(
                                $items_id
                            ) . "'"
                        );*/
                        \K::model()->db_update('app_entity_' . (int)$entities_id, [
                            'field_' . (int)$fields_id => $item_info['field_' . $fields_id]
                        ], ['id = ?', $items_id]);
                    }
                }
            }

            if ($forceCommit) {
                \K::model()->commit();
            }
        }
    }
}