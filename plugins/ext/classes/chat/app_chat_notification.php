<?php

if (!app_session_is_registered('chat_all_unread_msg')) {
    $chat_all_unread_msg = 0;
    app_session_register('chat_all_unread_msg');
}

class app_chat_notification
{
    public $sound_file, $has_sound_notification, $has_instant_notification;

    function __construct()
    {
        global $app_users_cfg;

        $this->sound_file = (strlen($app_users_cfg->get('chat_sound_notification')) ? $app_users_cfg->get(
            'chat_sound_notification'
        ) : CFG_CHAT_SOUND_NOTIFICATION);

        $this->has_sound_notification = (strlen($this->sound_file) ? true : false);

        if ($app_users_cfg->get(
                'chat_instant_notification'
            ) == 1 or (CFG_CHAT_INSTANT_NOTIFICATION == 1 and $app_users_cfg->get('chat_instant_notification') != 0)) {
            $this->has_instant_notification = true;
        } else {
            $this->has_instant_notification = false;
        }
    }

    function send($current_unread_all_msg)
    {
        global $chat_all_unread_msg, $app_user, $app_users_cache;

        $html = '';

        //sound notification
        if ($current_unread_all_msg > $chat_all_unread_msg) {
            if ($this->has_instant_notification) {
                $body = '';
                $message_query = db_query(
                    "select * from app_ext_chat_messages where assigned_to='" . $app_user['id'] . "' order by id desc limit 1"
                );
                if ($message = db_fetch_array($message_query)) {
                    $body .= addslashes(strip_tags($app_users_cache[$message['users_id']]['name'])) . '\n' . addslashes(
                            strip_tags($message['message'])
                        );
                }


                $html .= '
                    <script>                      
                        if (("Notification" in window) && (!is_app_caht_timer || document.hidden)) 
                        {
                            Notification.requestPermission(function(permission){ });
                            
                            if (Notification.permission === "granted") 
                            {
                                var notification = new Notification("' . sprintf(
                        TEXT_EXT_NEW_MSG_IN_CHAT,
                        $current_unread_all_msg
                    ) . '",{
                                    dir: app_language_text_direction,
                                    body: "' . $body . '",
                                    sound: "' . (strlen(
                        $this->sound_file
                    ) ? 'js/ion.sound-master/sounds/' . $this->sound_file . '.mp3' : '') . '",
                                    });
                                notification.onclick = function(event){                                
                                  event.preventDefault(); // prevent the browser from focusing the Notification\'s tab
                                  window.open("' . url_for('ext/app_chat/chat_window') . '", "_new");  
                                }  
                            }
                        }
                    </script>';
            }

            if ($this->has_sound_notification) {
                $html .= '
                <script>
                    if(!is_app_caht_timer || document.hidden)
                    {
                        ion.sound.play("' . $this->sound_file . '");
                    }
                </script>';
            }
        }

        //set count unread
        $chat_all_unread_msg = $current_unread_all_msg;

        return $html;
    }

    static function get_sound_choices()
    {
        $choices = [];
        $choices[''] = '';

        foreach (scandir('js/ion.sound-master/sounds/') as $file) {
            if (substr($file, -4) == '.mp3') {
                $name = substr($file, 0, -4);
                $choices[$name] = $name;
            }
        }

        return $choices;
    }

}