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
    //On démarre unsession 
    public function startSession()
    {
        if ($this->sessionState== $this->SESSION_NOT_STARTED) {
            $this->sessionState = session_start();
            $_SESSION['status'] = null;
            $_SESSION['utilisateur'] = "Visiteur";
        }
        return $this->sessionState;
    }
    //On crée une super global
    public function __set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    //::On recherche la valeur d'une super-global
    public function __get($name)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
    }

    //on vérifie qu'une super-global nomé existe
    public function __isset($name)
    {
        return isset($_SESSION[$name]);
    }

    //On détruit une super-global $_
    public function __unset($name)
    {
        unset($_SESSION[$name]);
    }
    //On arrête la session
    public function destroy()
    {

        if ($this->sessionState == $this->SESSION_STARTED) {
            session_start();
            session_destroy();
            unset($_SESSION);
            $this->sessionState = $this->SESSION_NOT_STARTED;
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
