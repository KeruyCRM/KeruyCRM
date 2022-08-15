<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Users;

class Photo extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'photo.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }

    public function upload()
    {
        if (\K::$fw->VERB == 'POST') {
            if (strlen(\K::$fw->FILES['Filedata']['tmp_name']) and \Helpers\App::is_image(
                    \K::$fw->FILES['Filedata']['tmp_name']
                )) {
                //$file = attachments::prepare_filename($_FILES['Filedata']['name']);

                $filename = \Tools\FieldsTypes\Fieldtype_user_photo::tmp_filename(
                    \K::$fw->FILES['Filedata']['tmp_name']
                );

                if (move_uploaded_file(\K::$fw->FILES['Filedata']['tmp_name'], \K::$fw->DIR_FS_USERS . $filename)) {
                    echo json_encode([
                        'name' => $filename,
                        'file' => $filename,
                    ]);

                    return;
                }
            }

            echo 'error';
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $filename = strlen(
                \K::$fw->POST['filename']
            ) ? \K::$fw->POST['filename'] : \Tools\FieldsTypes\Fieldtype_user_photo::tmp_filename();

            $img = str_replace(['data:image/png;base64,', 'data:image/jpeg;base64', 'data:image/gif;base64', ' '],
                ['', '', '', '+'],
                $_POST['img']);

            file_put_contents(\K::$fw->DIR_WS_USERS . $filename, base64_decode($img));

            if (!\Helpers\App::is_image(\K::$fw->DIR_WS_USERS . $filename)) {
                unlink(\K::$fw->DIR_WS_USERS . $filename);
            } else {
                \Helpers\App::image_resize(\K::$fw->DIR_FS_USERS . $filename, \K::$fw->DIR_FS_USERS . $filename, 250);
            }

            echo json_encode([
                'name' => $filename,
                'file' => \K::$fw->DIR_WS_USERS . $filename,
            ]);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}