<?php

class currencies
{

    public $path;

    function __construct()
    {
        $this->path = DIR_FS_CATALOG . 'plugins/ext/currencies_modules/';
    }

    function get_modules()
    {
        $choices = [];

        if ($dir = @dir($this->path)) {
            while ($file = $dir->read()) {
                if (is_file($this->path . $file) and $file != '.' and $file != '..') {
                    require($this->path . $file);

                    $class = substr($file, 0, strrpos($file, '.'));

                    $module = new $class;

                    $choices[$class] = $module->title;
                }
            }

            $dir->close();
        }

        return $choices;
    }

    function get_default_code()
    {
        $currencies_query = db_query("select * from app_ext_currencies where is_default=1");
        $currencies = db_fetch_array($currencies_query);

        return $currencies['code'];
    }

    function update($module)
    {
        global $alerts;

        $default_code = $this->get_default_code();

        require($this->path . $module . '.php');

        $module = new $module;

        $update_error = [];

        $currencies_query = db_query("select * from app_ext_currencies where code!='" . $default_code . "'");
        while ($currencies = db_fetch_array($currencies_query)) {
            if ($value = $module->rate($default_code, $currencies['code'])) {
                db_query("update app_ext_currencies set value='" . $value . "' where id='" . $currencies['id'] . "'");
            } elseif (!is_cron()) {
                $update_error[] = $currencies['code'];
            }
        }

        if (count($update_error)) {
            $alerts->add(sprintf(TEXT_CAN_NOT_UPDATE_CURRENCY, implode(',', $update_error)), 'error');
        }
    }

    static function get_cache()
    {
        $cache = [];
        $currencies_query = db_query("select * from app_ext_currencies order by sort_order");
        while ($currencies = db_fetch_array($currencies_query)) {
            $cache[$currencies['code']] = $currencies;
        }

        return $cache;
    }

    static function get_choices()
    {
        global $app_currencies_cache;
        $choices = [];

        foreach ($app_currencies_cache as $currency) {
            $choices[$currency['code']] = $currency['code'];
        }

        return $choices;
    }

    static function get_exchange_rate()
    {
        global $app_currencies_cache;

        $html = [];

        foreach ($app_currencies_cache as $currency) {
            if ($currency['is_default'] == 1) {
                continue;
            }

            if ($currency['value'] == 0) {
                continue;
            }

            $rate = number_format((1 / $currency['value']), 2);

            $html[] = '<span class="label label-info">' . $currency['symbol'] . ' ' . $rate . '</span>';
        }

        return implode(' ', $html);
    }

    static function exchange_rate_widget()
    {
        global $app_user, $app_currencies_cache;

        if (!in_array($app_user['group_id'], explode(',', CFG_CURRENCIES_WIDGET_USERS_GROUPS)) or !strlen(
                CFG_CURRENCIES_WIDGET_USERS_GROUPS
            )) {
            return '';
        }

        $html = '
			<table class="table">
				<tr>
					<td colspan="3" style="text-align:right; padding: 10px 10px; line-height: 2">' . self::get_exchange_rate(
            ) . '</td>
				</tr>';

        foreach ($app_currencies_cache as $currency) {
            $rate = number_format($currency['value'], 3);

            $html .= '<tr><td>' . $currency['code'] . '</td><td>' . $currency['symbol'] . '</td><td>' . input_tag(
                    'currency_' . $currency['code'],
                    $rate,
                    [
                        'class' => 'form-control input-small currency-field',
                        'data-currency-value' => $currency['value'],
                        'data-currency-default' => $currency['is_default'],
                        'autocomplete' => 'off'
                    ]
                ) . '</td><tr>';
        }

        $html .= '</table>
					<script>
						$(function(){
							app_currency_converter("#header_exchange_rates")
						})
					</script>
				';

        $html = '
			<li class="dropdown" id="header_exchange_rates">
				<a href="#" class="dropdown-toggle currencies-dropdown" data-hover="dropdown" data-close-others="true">
				<i class="fa fa-money"></i>				
				</a>
				<ul class="dropdown-menu extended tasks">
					<li>
						<p>' . TEXT_EXT_EXCHANGE_RATES . '</p>
					</li>
					<li>
						<ul class="dropdown-menu-list scroller" style="height: ' . (count(
                    $app_currencies_cache
                ) * 46 + 65) . 'px;">
							<li>
								' . $html . '
							</li>
						</ul>
					</li>
				</ul>
			</li>	

<script>
$("#header_exchange_rates a").on("click", function (event) {	  
    $(this).parent().toggleClass("open");
});		
										
$("body").on("click", function (e) {
    if (!$("#header_exchange_rates").is(e.target) && $("#header_exchange_rates").has(e.target).length === 0 && $(".open").has(e.target).length === 0){
        $("#header_exchange_rates").removeClass("open");
    }
});										
</script>
										
		';


        return $html;
    }

    static function prepare_input_attributes($attributes, $currency, $class = '')
    {
        global $app_currencies_cache;

        $attributes['class'] = $attributes['class'] . ' currency-field' . $class;
        $attributes['data-currency-value'] = $app_currencies_cache[$currency]['value'];
        $attributes['data-currency-default'] = $app_currencies_cache[$currency]['is_default'];

        return $attributes;
    }

}
