<?php

namespace SmsaeroApiV2;

class SmsaeroApiV2
{
    const URL_SMSAERO_API = 'https://gate.smsaero.ru/v2';
    private $email = ''; //Ваш логин|email
    private $api_key = ''; //Ваш api_key можно получить по адресу https://smsaero.ru/cabinet/settings/apikey/
    private $sign = 'SMS Aero'; //Подпись по умолчанию

    public function __construct($email, $api_key, $sign = false)
    {
        $this->email = $email;
        $this->api_key = $api_key;
        if ($sign) {
            $this->sign = $sign;
        }
    }

    /**
     * Формирование curl запроса
     * @param $url
     * @param $post
     * @param $options
     * @return mixed
     */
    private function curl_post($url, array $post = null, array $options = [])
    {
        $defaults = [
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POSTFIELDS => http_build_query($post),
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_USERPWD => $this->email . ":" . $this->api_key,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        if (!$result = curl_exec($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }

    /**
     * Отправка сообщения
     * @param $number string|array  - Номер телефона(ов)
     * @param $text string          - Текст сообщения
     * @param $channel string       - Канал отправки
     * @param $dateSend integer     - Дата для отложенной отправки сообщения (в формате unixtime)
     * @param $callbackUrl string   - url для отправки статуса сообщения в формате http://your.site или https://your.site (в ответ система ждет статус 200)
     * @return array
     */
    public function send($number, $text, $channel, $dateSend = null, $callbackUrl = null)
    {
        return json_decode(
            self::curl_post(self::URL_SMSAERO_API . '/sms/send/', [
                is_array($number) ? 'numbers' : 'number' => $number,
                'sign' => $this->sign,
                'text' => $text,
                'channel' => $channel,
                'dateSend' => $dateSend,
                'callbackUrl' => $callbackUrl
            ]),
            true
        );
    }

    /**
     * Проверка статуса SMS сообщения
     * @param id - Идентификатор сообщения
     * @return array
     */
    public function check_send($id)
    {
        return json_decode(
            $this->curl_post(self::URL_SMSAERO_API . '/sms/status/', [
                'id' => $id
            ]),
            true
        );
    }

    /**
     * Получение списка отправленных sms сообщений
     * @param $number string - Фильтровать сообщения по номеру телефона
     * @param $text string   - Фильтровать сообщения по тексту
     * @param $page integer  - Номер страницы
     * @return array
     */
    public function sms_list($number = null, $text = null, $page = null)
    {
        isset($page) ? $page = '?page=' . $page : $page = '';
        return json_decode(
            self::curl_post(self::URL_SMSAERO_API . '/sms/list' . $page, [
                'number' => $number,
                'text' => $text
            ]),
            true
        );
    }

    /**
     * Запрос баланса
     * @return array
     */
    public function balance()
    {
        return json_decode(self::curl_post(self::URL_SMSAERO_API . '/balance', []), true);
    }

    /**
     * Запрос тарифа
     * @return array
     */
    public function tariffs()
    {
        return json_decode(self::curl_post(self::URL_SMSAERO_API . '/tariffs', []), true);
    }

    /**
     * Добавление подписи
     * @param $name - Имя подписи
     * @return array
     */
    public function sign_add($name)
    {
        return json_decode(
            self::curl_post(self::URL_SMSAERO_API . '/sign/add', [
                'name' => $name
            ]),
            true
        );
    }

    /**
     * Получить список подписей
     * @return array
     */
    public function sign_list()
    {
        isset($page) ? $page = '?page=' . $page : $page = '';
        return json_decode(self::curl_post(self::URL_SMSAERO_API . '/sign/list' . $page, []), true);
    }

    /**
     * Добавление шруппы
     * @param $name string - Имя  группы
     * @return array
     */
    public function group_add($name)
    {
        return json_decode(
            self::curl_post(self::URL_SMSAERO_API . '/group/add', [
                'name' => $name
            ]),
            true
        );
    }

    /**
     * Удаление группы
     * @param $id integer - Идентификатор группы
     * @return array
     */
    public function group_delete($id)
    {
        return json_decode(
            self::curl_post(self::URL_SMSAERO_API . '/group/delete', [
                'id' => $id
            ]),
            true
        );
    }

    /**
     * Получение списка групп
     * @param $page integer - Пагинация
     * @return array
     */
    public function group_list($page = null)
    {
        isset($page) ? $page = '?page=' . $page : $page = '';
        return json_decode(self::curl_post(self::URL_SMSAERO_API . '/group/list' . $page, []), true);
    }

    /**
     * Добавление контакта
     * @param $number string - Номер абонента
     * @param null $groupId integer - Идентификатор группы
     * @param null $birthday integer - Дата рождения абонента (в формате unixtime)
     * @param null $sex string - Пол
     * @param null $lname string - Фамилия абонента
     * @param null $fname string - Имя абонента
     * @param null $sname string - Отчество абонента
     * @param null $param1 string - Свободный параметр
     * @param null $param2 string - Свободный параметр
     * @param null $param3 string - Свободный параметр
     * @return array
     */
    public function contact_add(
        $number,
        $groupId = null,
        $birthday = null,
        $sex = null,
        $lname = null,
        $fname = null,
        $sname = null,
        $param1 = null,
        $param2 = null,
        $param3 = null
    ) {
        return json_decode(
            self::curl_post(self::URL_SMSAERO_API . '/contact/add', [
                'number' => $number,
                'groupId' => $groupId,
                'birthday' => $birthday,
                'sex' => $sex,
                'lname' => $lname,
                'fname' => $fname,
                'sname' => $sname,
                'param1' => $param1,
                'param2' => $param2,
                'param3' => $param3
            ]),
            true
        );
    }

    /**
     * Удаление контакта
     * @param $id integer - Идентификатор абонента
     * @return array
     */
    public function contact_delete($id)
    {
        return json_decode(
            self::curl_post(self::URL_SMSAERO_API . '/contact/delete', [
                'id' => $id
            ]),
            true
        );
    }

    /**
     * Список контактов
     * @param null $number string - Номер абонента
     * @param null $groupId integer - Идентификатор группы
     * @param null $birthday integer - Дата рождения абонента (в формате unixtime)
     * @param null $sex string - Пол
     * @param null $operator string - Оператор
     * @param null $lname string - Фамилия абонента
     * @param null $fname string - Имя абонента
     * @param null $sname string - Отчество абонента
     * @param null $param1 string - Свободный параметр
     * @param null $param2 string - Свободный параметр
     * @param null $param3 string - Свободный параметр
     * @param null $page integer - Пагинация
     * @return array
     */
    public function contact_list(
        $number = null,
        $groupId = null,
        $birthday = null,
        $sex = null,
        $operator = null,
        $lname = null,
        $fname = null,
        $sname = null,
        $param1 = null,
        $param2 = null,
        $param3 = null,
        $page = null
    ) {
        isset($page) ? $page = '?page=' . $page : $page = '';
        return json_decode(
            self::curl_post(self::URL_SMSAERO_API . '/contact/list' . $page, [
                'number' => $number,
                'groupId' => $groupId,
                'birthday' => $birthday,
                'sex' => $sex,
                'operator' => $operator,
                'lname' => $lname,
                'fname' => $fname,
                'sname' => $sname,
                'param1' => $param1,
                'param2' => $param2,
                'param3' => $param3
            ]),
            true
        );
    }

    /**
     * Добавление в чёрный список
     * @param $number array|string - Номера телефонов|Номер абонента
     * @return array
     */
    public function blacklist_add($number)
    {
        return json_decode(
            self::curl_post(self::URL_SMSAERO_API . '/blacklist/add', [
                is_array($number) ? 'numbers' : 'number' => $number
            ]),
            true
        );
    }

    /**
     * Удаление из черного списка
     * @param $id integer - Идентификатор абонента
     * @return array
     */
    public function blacklist_delete($id)
    {
        return json_decode(
            self::curl_post(self::URL_SMSAERO_API . '/blacklist/delete', [
                'id' => $id
            ]),
            true
        );
    }

    /**
     * Список контактов в черном списке
     * @param null $number string - Номер абонента
     * @param null $page integer - Пагинация
     * @return array
     */
    public function blacklist_list($number = null, $page = null)
    {
        isset($page) ? $page = '?page=' . $page : $page = '';
        return json_decode(
            self::curl_post(self::URL_SMSAERO_API . '/blacklist/list' . $page, [
                'number' => $number
            ]),
            true
        );
    }

    /**
     * Создание запроса на проверку HLR
     * @param $number array|string - Номера телефонов|Номер абонента
     * @return array
     */
    public function hlr_check($number)
    {
        return json_decode(
            self::curl_post(self::URL_SMSAERO_API . '/hlr/check', [
                is_array($number) ? 'numbers' : 'number' => $number
            ]),
            true
        );
    }

    /**
     * Получение статуса HLR
     * @param $id integer - Идентификатор запроса
     * @return array
     */
    public function hlr_status($id)
    {
        return json_decode(
            self::curl_post(self::URL_SMSAERO_API . '/hlr/status', [
                'id' => $id
            ]),
            true
        );
    }

    /**
     * Определение оператора
     * @param $number array|string - Номера телефонов|Номер абонента
     * @return array
     */
    public function number_operator($number)
    {
        return json_decode(
            self::curl_post(self::URL_SMSAERO_API . '/number/operator', [
                is_array($number) ? 'numbers' : 'number' => $number
            ]),
            true
        );
    }

    /**
     * Отправка Viber-рассылок
     * @param null $number string|array - Номер телефона|Номера телефонов. Максимальное количество 50
     * @param null $groupId integer|string - ID группы по которой будет произведена рассылка. Для выбора всех контактов необходимо передать значение "all"
     * @param $sign string - Подпись отправителя
     * @param $channel string - Канал отправки Viber
     * @param $text string - Текст сообщения
     * @param null $imageSource string - Картинка кодированная в base64 формат, не должна превышать размер 300 kb.
     * Отправка поддерживается только в 3 форматах: png, jpg, gif. Перед кодированной картинкой необходимо указывать её формат.
     * Пример: jpg#TWFuIGlzIGRpc3Rpbmd1aXNoZ. Отправка доступна только методом POST. Параметр передается совместно с textButton и linkButton
     * @param null $textButton string - Текст кнопки. Максимальная длина 30 символов. Параметр передается совместно с imageSource и linkButton
     * @param null $linkButton string - Ссылки для перехода при нажатие кнопки. Ссылка должна быть с указанием http:// или https://. Параметр передается совместно с imageSource и textButton
     * @param null $dateSend integer - Дата для отложенной отправки рассылки (в формате unixtime)
     * @param null $signSms string - Подпись для SMS-рассылки. Используется при выборе канала "Viber-каскад" (channel=CASCADE). Параметр обязателен
     * @param null $channelSms string - Канал отправки SMS-рассылки. Используется при выборе канала "Viber-каскад" (channel=CASCADE). Параметр обязателен
     * @param null $textSms string - Текст сообщения для SMS-рассылки. Используется при выборе канала "Viber-каскад" (channel=CASCADE). Параметр обязателен
     * @param null $priceSms integer - Максимальная стоимость SMS-рассылки. Используется при выборе канала "Viber-каскад" (channel=CASCADE). Если параметр не передан, максимальная стоимость будет рассчитана автоматически
     * @return array
     */
    public function viber_send(
        $number = null,
        $groupId = null,
        $sign = '',
        $channel = '',
        $text = '',
        $imageSource = null,
        $textButton = null,
        $linkButton = null,
        $dateSend = null,
        $signSms = null,
        $channelSms = null,
        $textSms = null,
        $priceSms = null
    ) {
        return json_decode(
            self::curl_post(self::URL_SMSAERO_API . '/viber/send', [
                is_array($number) && !empty($number) ? 'numbers' : 'number' => $number,
                'groupId' => $groupId,
                'sign' => $sign,
                'channel' => $channel,
                'text' => $text,
                '$imageSource' => $imageSource,
                'textButton' => $textButton,
                'linkButton' => $linkButton,
                'dateSend' => $dateSend,
                'signSms' => $signSms,
                'channelSms' => $channelSms,
                'textSms' => $textSms,
                'priceSms' => $priceSms
            ]),
            true
        );
    }

    /**
     * Статистика по Viber-рассылке
     * @param $sendingId integer - Идентификатор Viber-рассылки в системе
     * @param $page integer - Пагинация
     * @return array
     */
    public function viber_statistic($sendingId, $page = null)
    {
        isset($page) ? $page = '?page=' . $page : $page = '';
        return json_decode(
            self::curl_post(self::URL_SMSAERO_API . '/viber/statistic' . $page, [
                'sendingId' => $sendingId
            ]),
            true
        );
    }

    /**
     * Список Viber-рассылок
     * @return array
     */
    public function viber_list()
    {
        return json_decode(self::curl_post(self::URL_SMSAERO_API . '/viber/list', []), true);
    }

    /**
     * Список доступных подписей для Viber-рассылок
     * @return array
     */
    public function viber_sign_list()
    {
        return json_decode(self::curl_post(self::URL_SMSAERO_API . '/viber/sign/list', []), true);
    }
}

