<?php

class Controller
{
    private const CONFIG = __DIR__ . '/../core/config.json';
    protected static $_twig = null;
    protected static $_baseUrl = null;



    const SESSION_STARTED = TRUE;
    const SESSION_NOT_STARTED = FALSE;
    private $sessionState = self::SESSION_NOT_STARTED;
    private static $instance;
    public function __construct()
    {
        $this->twig = self::getTwig();
        $this->baseUrl = self::getBaseUrl();

    }

    protected static function getTwig()
    {

        if (is_null(self::$_twig)) {
            $loader = new \Twig\Loader\FilesystemLoader('Views');
            self::$_twig = new \Twig\Environment($loader, [
                'debug' => true,
                'cache' => false
            ]);
            self::$_twig->addExtension(new \Twig\Extension\DebugExtension());
            self::$_twig->addGlobal('baseUrl', self::getBaseUrl());
            self::getInstance();
        }
        
        return self::$_twig;
    }

    protected static function getBaseUrl()
    {
        if (is_null(self::$_baseUrl)) {
            $config = json_decode(file_get_contents(self::CONFIG));
            self::$_baseUrl = $config->baseUrl;
        }
        return self::$_baseUrl;
    }
    public function startSession()
    { 
        var_dump("startSession");
        if ($this->sessionState == self::SESSION_NOT_STARTED) {
            $this->sessionState = session_start(); 
            $_SESSION['status'] = null;
            $_SESSION['utilisateur'] = "Visiteur";
        }
        return $this->sessionState;
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {

            self::$instance = new self;
        }
        self::$instance->startSession();

        return self::$instance;
    }

    public function __get($name)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
    }

    public function __isset($name)
    {
        return isset($_SESSION[$name]);
    }

    public function __unset($name)
    {
        unset($_SESSION[$name]);
    }

    public function destroy()
    {
        if ($this->sessionState == self::SESSION_STARTED) {
            $this->sessionState = !session_destroy();
            unset($_SESSION);

            return !$this->sessionState;
        }

        return FALSE;
    }
}
