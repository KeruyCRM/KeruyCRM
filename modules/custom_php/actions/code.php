<?php

class warningException extends Exception
{
    public function errorMessage()
    {
        $errorMsg = 'Warning: ' . $this->getMessage();
        return $errorMsg;
    }
}

switch ($app_module_action) {
    case 'validate':

        $php_code = $_POST['code'];

        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            if ($errno === E_WARNING) {
                throw new warningException($errstr . ' on line  ' . $errline);
            }
        });

        if (strlen($php_code)) {
            try {
                eval($php_code);
            } catch (Error $e) {
                echo json_encode('Error: ' . $e->getMessage() . ' on line ' . $e->getLine());
                exit();
            } catch (warningException $e) {
                echo json_encode($e->errorMessage());
                exit();
            } finally {
                restore_error_handler();
            }
        }

        echo json_encode(true);

        exit();

        break;
    case 'save':

        $is_folder = $_POST['is_folder'] ?? 0;
        $name = $_POST['name'] ?? '';
        $code_id = _POST('code_id');

        $sql_data = [
            'parent_id' => (isset($_POST['parent_id']) ? $_POST['parent_id'] : 0),
            'is_active' => $_POST['is_active'] ?? 0,
            'is_folder' => $is_folder,
            'name' => $name,
            'code' => $_POST['code'] ?? '',
            'notes' => $_POST['notes'] ?? '',
            'sort_order' => $_POST['sort_order'],

        ];

        if ($code_id > 0) {
            db_perform('app_custom_php', $sql_data, 'update', "id='" . db_input($code_id) . "'");
        } else {
            db_perform('app_custom_php', $sql_data);
            $code_id = db_insert_id();
        }

        if ($_POST['is_crtl_s'] == 1) {
            echo $code_id;
            exit();
        } else {
            redirect_to('custom_php/code');
        }

        break;
    case 'delete':
        $obj = db_find('app_custom_php', $_GET['id']);

        db_delete_row('app_custom_php', $_GET['id']);

        db_query("update app_custom_php set parent_id=0 where parent_id='" . _get::int('id') . "'");

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('custom_php/code');
        break;
}
    