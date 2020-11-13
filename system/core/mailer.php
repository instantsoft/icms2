<?php
class cmsMailer {

    private $mailer;
    private $errorInfo;

    public function __construct($config = false) {

        if(!$config){
            $config = cmsConfig::getInstance();
        }

        cmsCore::loadLib('phpmailer/class.phpmailer', 'PHPMailer');

        $this->mailer = new PHPMailer();

        $this->mailer->CharSet  = 'UTF-8';
        $this->mailer->XMailer  = ' ';
        $this->mailer->Hostname = ltrim(strstr($config->mail_from, '@'), '@');

        $this->mailer->setLanguage($config->language);

        $this->initTransport($config)->
            setFrom( $config->mail_from, (!empty($config->mail_from_name) ? $config->mail_from_name : '') )->
            setBodyText( LANG_MAIL_DEFAULT_ALT );

    }

    /**
     * Возвращает объект PHPMailer
     * @return \PHPMailer
     */
    public function callMailer() {
        return $this->mailer;
    }

    /**
     * Инициализирует почтовый транспорт по настройкам
     *
     * @param object $config
     * @return \cmsMailer
     */
    public function initTransport($config){

        // PHP mail()
        if ($config->mail_transport == 'mail') {
            $this->mailer->isMail();
        } else

        // SMTP Server
        if ($config->mail_transport == 'smtp') {

            cmsCore::loadLib('phpmailer/class.smtp', 'SMTP');

            $this->mailer->IsSMTP();
            $this->mailer->Host          = $config->mail_smtp_server;
            $this->mailer->Port          = $config->mail_smtp_port;
            $this->mailer->SMTPAuth      = (bool)$config->mail_smtp_auth;
            $this->mailer->SMTPKeepAlive = true;
            $this->mailer->Username      = $config->mail_smtp_user;
            $this->mailer->Password      = $config->mail_smtp_pass;

			if (!empty($config->mail_smtp_enc)){
				$this->mailer->SMTPSecure = $config->mail_smtp_enc;
			}

        } else

        // SendMail
        if ($config->mail_transport == 'sendmail') {
            $this->mailer->IsSendmail();
        }

        return $this;

    }

//============================================================================//
//============================================================================//

    /**
     * Добавляет заголовок к письму
     *
     * @param string $name Имя заголовка (Может быть в формате name:value, при этом параметр $value не требуется)
     * @param string $value Значение заголовка
     * @return \cmsMailer
     */
    public function addCustomHeader($name, $value = null) {
        $this->mailer->addCustomHeader($name, $value);
        return $this;
    }

    /**
     * Устанавливает адрес отправителя
     * @param string $email
     * @param string $name
     * @return \cmsMailer
     */
    public function setFrom($email, $name=''){
        $this->mailer->SetFrom($email, $name);
        return $this;
    }

	/**
	 * Устанавливает обратный адрес
	 * @param string $email
	 * @param string $name
	 * @return \cmsMailer
	 */
	public function setReplyTo($email, $name=''){
		$this->mailer->ClearReplyTos();
		$this->mailer->AddReplyTo($email, $name);
		return $this;
	}

    /**
     * Добавляет адрес получателя
     * @param string $email
     * @param string $name
     * @return \cmsMailer
     */
    public function addTo($email, $name=''){
        $this->mailer->AddAddress($email, $name);
        return $this;
    }

    /**
     * Устанавливает тему письма
     * @param string $subject
     * @return \cmsMailer
     */
    public function setSubject($subject){
        $this->mailer->Subject = $subject;
        return $this;
    }

    /**
     * Устанавливает HTML-тело письма
     * @param string $message
     * @param bool $is_auto_alt Создавать альтернативное текстовое тело письма, вырезанием тегов из HTML-тела
     * @return \cmsMailer
     */
    public function setBodyHTML($message, $is_auto_alt = true){

        $this->mailer->MsgHTML( $message );

        if ($is_auto_alt){
            $this->setBodyText( $this->mailer->html2text($message) );
        }

        return $this;

    }

    /**
     * Устанавливает текстовое тело письма
     * @param string $message
     * @return \cmsMailer
     */
    public function setBodyText($message){
        $this->mailer->AltBody = $message;
        return $this;
    }

//============================================================================//
//============================================================================//

    /**
     * Находит в тексте письма выражение [subject:Тема письма] и заполняет
     * тему письма
     *
     * @param string $letter_text
     * @return string
     */
    public function parseSubject($letter_text){

        // Парсим тему письма
        if(preg_match('/\[subject:(.+)\]/iu', $letter_text, $matches)){

            list($subj_tag, $subject) = $matches;

            $letter_text = trim(str_replace($subj_tag, '', $letter_text));

            $this->setSubject($subject);

        }

        return $letter_text;

    }

    /**
     * Находит в тексте письма все выражения [attachment:path/to/file.ext]
     * и добавляет во вложение указанные файлы
     *
     * @param string $letter_text
     * @return string
     */
    public function parseAttachments($letter_text){

        // Парсим вложения
        if(preg_match_all('/\[attachment:(.+?)\]/iu', $letter_text, $matches)){

            $config = cmsConfig::getInstance();

            list($tags, $files) = $matches;

            foreach($tags as $idx => $att_tag){

                $letter_text = trim(str_replace($att_tag, '', $letter_text));

                $this->addAttachment($config->root_path . $files[$idx]);

            }

        }

        return $letter_text;

    }

    /**
     * Добавляет файл во вложение к письму
     * @param string $file Абсолютный путь к файлу
     * @param string $name Имя файла
     * @return \cmsMailer
     */
    public function addAttachment($file, $name = ''){
        $this->mailer->AddAttachment($file, $name);
        return $this;
    }

    /**
     * Очищает список получателей
     * @return \cmsMailer
     */
    public function clearTo(){
        $this->mailer->ClearAddresses();
        return $this;
    }

    /**
     * Очищает список вложений
     * @return \cmsMailer
     */
    public function clearAttachments(){
        $this->mailer->ClearAttachments();
        return $this;
    }

//============================================================================//
//============================================================================//

    public function getErrorInfo(){
        return $this->errorInfo;
    }

    /**
     * Отправляет письмо
     * @return bool Результат отправки
     */
    public function send(){
        $result = $this->mailer->Send();
        if (!$result) {
            $this->errorInfo = $this->mailer->ErrorInfo;
        }
        return $result;
    }

}
