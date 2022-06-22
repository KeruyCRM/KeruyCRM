<?php

class social_login
{
    function __construct()
    {
        $this->user = null;
    }
    
    function set_user($options=[])
    {
        $email = explode('@',$options['email']);
        
        $this->user = [
            'username' => $options['username'] ?? $email[0], 
            'first_name' => $options['first_name'] ?? '',
            'last_name' => $options['last_name'] ?? '',
            'photo' => $options['photo'] ?? '',
            'email' => $options['email'],
            'social' => $options['social']??'',
        ];
    }
    
    function login()
    {
        global $alerts;
        
        if(!$this->user)
        {
            $alerts -> add(TEXT_USER_NOT_FOUND, 'error');
            redirect_to('users/login');            
        }
        
        //print_rr($this->user);
             
        //check if user exist
        switch($this->user['social'])
        {
            case 'steam':
                $user_query = db_query("select * from app_entity_1 where field_12='" . db_input($this->user['username']) . "'");
                break;
            default:
                $user_query = db_query("select * from app_entity_1 where field_9='" . db_input($this->user['email']) . "'");
                break;
        }
                
        if($user = db_fetch_array($user_query))
        {      
            //check if user is active
            if($user['field_5']==1)
            {
                app_session_register('app_logged_users_id', $user['id']);
                
                users_login_log::success($user['field_12'], $user['id']);
                
                redirect_to('dashboard/'); 
            }
            else
            {
                users_login_log::fail($user['field_12']);
                
                $alerts -> add(TEXT_USER_NOT_FOUND, 'error');
                redirect_to('users/login'); 
            }
        }
        else
        {
            switch(CFG_SOCAL_LOGIN_CREATE_USER)
            {
                case 'autocreate':
                    
                    $photo = '';
                    
                    if(strlen($this->user['photo']))
                    {
                        $curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $this->user['photo']);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$data = curl_exec($curl);
			curl_close($curl);
                        
                        $file = attachments::prepare_filename(pathinfo($this->user['username'], PATHINFO_BASENAME));
			
			$filename = DIR_FS_USERS  . $file['file'];
			
                        $photo = $file['name'];
						
			file_put_contents($filename, $data);
                    }
                    
                    $sql_data = [
                        'date_added'=>0,
                        'created_by'=>0,
                        'parent_item_id'=>0,
                        'field_6' => (int)CFG_SOCAL_LOGIN_USER_GROUP,
                        'multiple_access_groups'=> (count(explode(',',CFG_SOCAL_LOGIN_USER_GROUP))>1 ? CFG_SOCAL_LOGIN_USER_GROUP:''),
                        'field_5'=>1,
                        'is_email_verified'=>1, 
                        'field_13' =>CFG_APP_LANGUAGE,
                        'field_7' => db_prepare_input($this->user['first_name']),
                        'field_8' => db_prepare_input($this->user['last_name']),
                        'field_9' => db_prepare_input($this->user['email']),
                        'field_12' => db_prepare_input($this->user['username']),
                        'field_10' => $photo,
                    ];
                    
                    db_perform('app_entity_' . 1,$sql_data);
		    $item_id = db_insert_id();
                    
                    app_session_register('app_logged_users_id', $item_id);
                    
                    users_login_log::success($sql_data['field_12'], $item_id);
                    
                    redirect_to('users/account'); 
                    
                    break;
                case 'public_registration':
                    redirect_to('users/registration','fields[7]=' . $this->user['first_name'] . '&fields[8]=' . $this->user['last_name'] . '&fields[9]=' . $this->user['email'] . '&fields[12]=' . $this->user['username']);
                    break;
                default:
                    $alerts -> add(TEXT_USER_NOT_FOUND, 'error');
                    redirect_to('users/login');
                    break;
            }
            
        }
    }
}
