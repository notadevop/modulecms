<?php 

/**
 *  Mailer Класс для отправки емайлов пользователю
 *  типа ссылок авторизации и тому подобные...
 */


class Mailer {

    public function __construct() { 

    }


    private $to; 	// кому
    private $from; 	// от кого
    private $body;  // тело обьекта
    private $subject; // заголовок


    function initTo(string $to):void {

        $this->to = $to;
    }

    function initFrom(string $from): void {

        $this->from = $from;
    }

    function initSubject(string $subject):void {

        $this->subject = $subject;
    }

    function initBody(string $body): void {

        $this->body = $body;
    }

    function initMailTemplate(string $tplName): bool {

        // тут достаем шаблон для емайла
    }


    function sendMessage(): bool {

        $to     = $this->to;
        $from   = $this->from;
        $subject= $this->subject;
        $body   = $this->body;

        try {
            if (empty($to) || empty($from) || empty($subject) || empty($body)) {
                throw new Exception('у вас есть пустые поля!');
            }
            
        } catch (Exception $e) {
            return false;
        }
    }

}