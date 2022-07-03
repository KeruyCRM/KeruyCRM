<?php

namespace Controllers\Set;

class Install
{
    public $f3;
    public $db;

    public function __construct($f3)
    {
        $this->f3 = $f3;
        $this->f3->exists('PARAMS.lang', $this->f3->LANGUAGE);
        $this->f3->app_title = sprintf($this->f3->TEXT_INSTALLATION_HEADING, $this->f3->PROJECT_VERSION);

        //Lock install
        /*if (file_exists('config/database.php')) {
            new \DB\SQL\Session(\Model::instance()->db, 'app_sessions_new', true, null, 'CSRF');
            $this->f3->reroute('/');
            //http://mysql.rjweb.org/doc.php/limits#767_limit_in_innodb_indexes
            //#1071 - Specified key was too long; max key length is 767 bytes
            //This problem exists before the limit was raised in 5.7.7 (MariaDB 10.2.2?).
        }*/
    }

    public function index()
    {
        $this->f3->nextAction = $this->f3->DOMAIN . 'set/install/checking_environment/' . $this->f3->APP_LANGUAGE_SHORT_CODE;
        $this->f3->urlToImg = $this->f3->DOMAIN . $this->f3->UI . 'img/flag/' . $this->f3->TEXT_APP_LANGUAGE_COUNTRY . '.png';

        $this->f3->subTemplate = 'install/index.php';
        echo \View::instance()->render('install.php');
    }

    public function checking_environment()
    {
        $error_list = [];

        if (!version_compare(phpversion(), '7.2', '>=')) {
            $error_list[] = sprintf($this->f3->TEXT_ERROR_PHP_VERSION, phpversion());
        }

        $requried_php_extensions = [
            'gd',
            'mbstring',
            'xmlwriter',
            'curl',
            'zip',
            'xml',
            'fileinfo',
        ];

        foreach ($requried_php_extensions as $ext) {
            if (!extension_loaded($ext)) {
                $error_list[] = sprintf($this->f3->TEXT_ERROR_LIB, strtoupper($ext));
            }
        }

        $check_folders = [
            'backups',
            'log',
            'uploads',
            'uploads/attachments',
            'uploads/users',
            'uploads/images',
            'cache',
            'config'
        ];

        foreach ($check_folders as $v) {
            if (is_dir($v)) {
                if (!is_writable($v)) {
                    $error_list[] = sprintf($this->f3->TEXT_ERROR_FOLDER_NOT_WRITABLE, $v);
                }
            } else {
                $error_list[] = sprintf($this->f3->TEXT_ERROR_FOLDER_NOT_EXIST, $v);
            }
        }

        $this->f3->error_list = $error_list;

        $this->f3->prevAction = $this->f3->DOMAIN . 'set/install/checking_environment/' . $this->f3->APP_LANGUAGE_SHORT_CODE;
        $this->f3->nextAction = $this->f3->DOMAIN . 'set/install/config_database/' . $this->f3->APP_LANGUAGE_SHORT_CODE;

        $this->f3->subTemplate = 'install/checking_environment.php';
        echo \View::instance()->render('install.php');
    }

    public function config_database()
    {
        $params = [
            'db_host' => 'localhost',
            'db_port' => '',
            'db_username' => '',
            'db_password' => '',
            'db_name' => ''
        ];

        $error = false;
        if (\Flash::instance()->hasKey('error')) {
            $error = \Flash::instance()->getKey('error');
        }
        if (\Flash::instance()->hasKey('params')) {
            $params = json_decode(base64_decode(\Flash::instance()->getKey('params')), true);
        }

        $this->f3->error = $error;
        $this->f3->params = $params;

        $this->f3->nextAction = $this->f3->DOMAIN . 'set/install/config_crm/' . $this->f3->APP_LANGUAGE_SHORT_CODE;
        $this->f3->subTemplate = 'install/config_database.php';
        echo \View::instance()->render('install.php');
    }

    public function config_crm()
    {
        $this->f3->params = $this->actions_check_db_settings();

        $timezone_list = [];
        $timezone_identifiers = \DateTimeZone::listIdentifiers();
        for ($i = 0; $i < sizeof($timezone_identifiers); $i++) {
            $timezone_list[$timezone_identifiers[$i]] = $timezone_identifiers[$i];
        }

        if ($this->f3->APP_LANGUAGE_SHORT_CODE == 'uk') {
            $time_zone = 'Europe/Kiev';
        } else {
            $time_zone = 'America/New_York';
        }

        $this->f3->app_time_zone = \Helpers\Html::select_tag(
            'app_time_zone',
            $timezone_list,
            $time_zone,
            ['class' => 'form-control input-large']
        );

        $this->f3->nextAction = $this->f3->DOMAIN . 'set/install/install_crm/' . $this->f3->APP_LANGUAGE_SHORT_CODE;
        $this->f3->subTemplate = 'install/config_crm.php';
        echo \View::instance()->render('install.php');
    }

    public function install_crm()
    {
        $this->f3->DB_host = $this->f3->POST['db_host'];
        $this->f3->DB_port = $this->f3->POST['db_port'];
        $this->f3->DB_username = $this->f3->POST['db_username'];
        $this->f3->DB_password = $this->f3->POST['db_password'];
        $this->f3->DB_name = $this->f3->POST['db_name'];

        //db_connect($server, $username, $password, $database, $port);
        $db = $this->db = \Model::instance()->db;

        $this->_initSqlSchema();
        $this->_setSqlConfig();

        new \DB\SQL\Session($db, 'app_sessions_new', true, null, 'CSRF');

        $db_config = "<?php

// define database connection
\K::fw()->mset([
  'DB_host' => '{$this->f3->DB_host}',
  'DB_port' => '{$this->f3->DB_port}',
  'DB_username' => '{$this->f3->DB_username}',
  'DB_password' => '{$this->f3->DB_password}',
  'DB_name' => '{$this->f3->DB_name}',
]);";

        file_put_contents('config/database.php', $db_config);

        $this->f3->locationAdmin = $this->f3->URI_ADMIN;
        $this->f3->subTemplate = 'install/success.php';
        echo \View::instance()->render('install.php');
    }

    private function actions_check_db_settings()
    {
        $host = $this->f3->POST['db_host'];
        $port = $this->f3->POST['db_port'];
        $username = $this->f3->POST['db_username'];
        $password = $this->f3->POST['db_password'];
        $name = $this->f3->POST['db_name'];

        $params = [
            'db_host' => $host,
            'db_port' => $port,
            'db_username' => $username,
            'db_password' => $password,
            'db_name' => $name,
        ];

        try {
            if ($this->f3->TYPE_DATABASE == 'mysql') {
                if (!strlen($port)) {
                    $port = '3306';
                }
                $db = new \DB\SQL("mysql:host={$host};port={$port};dbname={$name}", $username, $password);
            } elseif ($this->f3->TYPE_DATABASE == 'sqlite') {
                $db = new \DB\SQL("db/{$this->f3->POST['db_name']}.sqlite");
            } else {
                exit('Type database not defined');
            }
        } catch (\PDOException $e) {
            \Flash::instance()->setKey('error', "Error!: " . $e->getMessage());
            \Flash::instance()->setKey('params', base64_encode(json_encode($params)));

            $this->f3->reroute(
                $this->f3->DOMAIN . 'set/install/config_database/' . $this->f3->APP_LANGUAGE_SHORT_CODE
            );
        }

        $user_privileges_query = $db->exec('SHOW PRIVILEGES');

        $user_privileges_list = \Matrix::instance()->pick($user_privileges_query, 'Privilege');

        $required_privileges = ['Select', 'Insert', 'Update', 'Delete', 'Create', 'Drop', 'Alter'];

        $missed_privileges = [];
        foreach ($required_privileges as $v) {
            if (!in_array($v, $user_privileges_list)) {
                $missed_privileges[] = $v;
            }
        }

        if (count($missed_privileges) > 0) {
            \Flash::instance()->setKey('error', sprintf($this->f3->TEXT_DB_MISSED_PRIVILEGES, $missed_privileges));
            \Flash::instance()->setKey('params', base64_encode(json_encode($params)));

            $this->f3->reroute(
                $this->f3->DOMAIN . 'set/install/config_database/' . $this->f3->APP_LANGUAGE_SHORT_CODE
            );
        }

        return $params;
    }

    private function _initSqlSchema()
    {
        $db = \Model::instance()->db;
        $db->exec("ALTER DATABASE `" . $this->f3->DB_name . "` CHARACTER SET utf8mb4");

        $sql_file = 'config/sql/' . $this->f3->APP_LANGUAGE_SHORT_CODE . '.sql';

        if (file_exists($sql_file)) {
            $install_query = file_get_contents($sql_file);
        } else {
            echo 'SQL file does not exist: ' . $sql_file;
            exit();
        }

        $install_query_array = explode(';', $install_query);

        foreach ($install_query_array as $query) {
            $query = trim($query);
            if (strlen($query) > 0) {
                $db->exec($query);
            }
        }
    }

    private function _setSqlConfig()
    {
        $db = \Model::instance()->db;
        $insert_query = "INSERT INTO app_configuration VALUES
('11','CFG_APP_LOGO',''),
('10','CFG_APP_SHORT_NAME', :app_short_name),
('9','CFG_APP_NAME', :app_name),
('12','CFG_EMAIL_USE_NOTIFICATION','1'),
('13','CFG_EMAIL_SUBJECT_LABEL',''),
('14','CFG_EMAIL_AMOUNT_PREVIOUS_COMMENTS','2'),
('15','CFG_EMAIL_COPY_SENDER','0'),
('16','CFG_EMAIL_SEND_FROM_SINGLE','0'),
('17','CFG_EMAIL_ADDRESS_FROM', :email_address_from),
('18','CFG_EMAIL_NAME_FROM', :email_name_from),
('19','CFG_EMAIL_USE_SMTP','0'),
('20','CFG_EMAIL_SMTP_SERVER',''),
('21','CFG_EMAIL_SMTP_PORT',''),
('22','CFG_EMAIL_SMTP_ENCRYPTION',''),
('23','CFG_EMAIL_SMTP_LOGIN',''),
('24','CFG_EMAIL_SMTP_PASSWORD',''),
('25','CFG_LDAP_USE','0'),
('26','CFG_LDAP_SERVER_NAME',''),
('27','CFG_LDAP_SERVER_PORT',''),
('28','CFG_LDAP_BASE_DN',''),
('29','CFG_LDAP_UID',''),
('30','CFG_LDAP_USER',''),
('31','CFG_LDAP_EMAIL_ATTRIBUTE',''),
('32','CFG_LDAP_USER_DN',''),
('33','CFG_LDAP_PASSWORD',''),
('34','CFG_LOGIN_PAGE_HEADING',''),
('35','CFG_LOGIN_PAGE_CONTENT',''),
('36','CFG_APP_TIMEZONE', :app_time_zone),
('37','CFG_APP_DATE_FORMAT','m/d/Y'),
('38','CFG_APP_DATETIME_FORMAT','m/d/Y H:i'),
('39','CFG_APP_ROWS_PER_PAGE','10'),
('40','CFG_REGISTRATION_EMAIL_SUBJECT',''),
('41','CFG_REGISTRATION_EMAIL_BODY',''),
('42','CFG_PASSWORD_MIN_LENGTH','5'),
('43','CFG_APP_LANGUAGE', :lang),
('44','CFG_APP_SKIN',''),
('45','CFG_PUBLIC_USER_PROFILE_FIELDS','')";

        $db->exec(
            $insert_query,
            [
                ':app_short_name' => $this->f3->POST['app_short_name'],
                ':app_name' => $this->f3->POST['app_name'],
                ':email_address_from' => $this->f3->POST['email_address_from'],
                ':email_name_from' => $this->f3->POST['email_name_from'],
                ':app_time_zone' => $this->f3->POST['app_time_zone'],
                ':lang' => $this->f3->APP_LANGUAGE_SHORT_CODE,
            ]
        );

        $insert_query2 = "INSERT INTO app_entity_1 VALUES
('1',0,'0','0','0',:time,'0',NULL,'0',:user_password,'',1,'1','0',:fields7,:fields8,:fields9,'',:fields12,:lang,'blue',:time2)";

        $db->exec(
            $insert_query2,
            [
                ':time' => time(),
                ':user_password' => \Libs\PasswordHash::instance(11, false)->HashPassword(
                    $this->f3->POST['user_password']
                ),
                ':fields7' => $this->f3->POST['fields'][7],
                ':fields8' => $this->f3->POST['fields'][8],
                ':fields9' => $this->f3->POST['fields'][9],
                ':fields12' => $this->f3->POST['fields'][12],
                ':lang' => $this->f3->APP_LANGUAGE_SHORT_CODE,
                ':time2' => time(),
            ]
        );
    }
}