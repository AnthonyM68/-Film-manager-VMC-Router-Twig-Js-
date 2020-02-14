<?php

class HomeController extends Controller
{

    private $SESSION_STARTED = TRUE;
    private $SESSION_NOT_STARTED = FALSE;
    private $sessionState = false;
    private static $instance;

    
    public function __construct()
    {
        $this->twig = parent::getTwig();
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) 
        {
            self::$instance = new self;
        }
        self::$instance->startSession();
        var_dump(self::$instance);

        return self::$instance;
    }

    public function startSession()
    {
        if (false == $this->SESSION_NOT_STARTED) {
            $_SESSION['status'] = null;
            $_SESSION['utilisateur'] = "Visiteur";
        }
        return $this->sessionState;
    }

    public function __set($name, $value)
    {
        $_SESSION[$name] = $value;
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
        if (true == $this->SESSION_STARTED) {
            session_start();
            session_destroy();
            unset($_SESSION);
            $this->sessionState = false;
            $_SESSION['status'] = null;
            $_SESSION['utilisateur'] = "Visiteur";
            return !$this->sessionState;
        }

        return FALSE;
    }
    public function index()
    {
        $pageTwig = 'index.html.twig';
        $template = $this->twig->load($pageTwig);
        echo $template->render();
    }
}
