<?php namespace MiladRahimi\PHPSession;

/**
 * Class Session
 *
 * Session class is the main class which developers must interactive with to
 * dispatch all website routes.
 *
 * @package MiladRahimi\PHPRouter
 *
 * @author Milad Rahimi <info@miladrahimi.com>
 */
class Session
{
    /**
     * Singleton instance
     *
     * @var Session
     */
    private static $instance;

    /**
     * Expiry time (minutes)
     *
     * @var int
     */
    private $lifetime = 0;

    /**
     * Construct
     */
    protected function __construct()
    {
        $this->start();
        $this->checkExpiration();
        $this->setLifeTime($this->lifetime);
        if (!$this->isInitialized()) {
            $this->refresh();
        }
    }

    /**
     * Start session
     */
    private function start()
    {
        if (session_id() == '')
            session_start();
    }

    private function checkExpiration()
    {
        $this->start();
        if (isset($_SESSION["X_MR_ET"]) && $_SESSION["X_MR_ET"] != 0 && ((int)$_SESSION["X_MR_ET"] < time())) {
            $this->clear();
            $this->start();
        }
    }

    /**
     * End and clear sessions
     */
    public function clear()
    {
        $this->start();
        session_unset();
        session_destroy();
        $_SESSION = array();
    }

    /**
     * Check whether the session base info is set and is valid
     *
     * @return bool
     */
    private function isInitialized()
    {
        $this->start();
        if (isset($_SESSION["X_MR_UA"]) && isset($_SESSION["X_MR_IP"]))
            return true;
        return false;
    }

    /**
     * Regenerate user and session information
     */
    public function refresh()
    {
        $this->start();
        session_regenerate_id();
        $_SESSION["X_MR_UA"] = $_SERVER["HTTP_USER_AGENT"];
        $_SESSION["X_MR_IP"] = $_SERVER["REMOTE_ADDR"];
        $_SESSION["X_MR_ET"] = (time() + $this->lifetime * 60) * (int)((bool)$this->lifetime);
    }

    /**
     * @return Session
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof Session)
            self::$instance = new Session();
        return self::$instance;
    }

    /**
     * Set key/value pair data in session
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     */
    public function set($key, $value)
    {
        if (!is_scalar($key))
            throw new InvalidArgumentException("Invalid key type");
        $_SESSION[$key] = $value;
    }

    /**
     * Get session value or array of session
     *
     * @param mixed $key
     * @return mixed
     *
     * @throws UntrustedSession
     */
    public function get($key = null)
    {
        if (!$this->isTrusted())
            throw new UntrustedSession();
        if (is_null($key))
            return $_SESSION;
        if (!is_scalar($key))
            throw new InvalidArgumentException("Invalid key type");
        if (isset($_SESSION[$key]))
            return $_SESSION[$key];
        return null;
    }


    /**
     * Check whether session user is trusted
     *
     * @return bool
     */
    private function isTrusted()
    {
        if (!isset($_SESSION["X_MR_UA"]) || $_SESSION["X_MR_UA"] != $_SERVER["HTTP_USER_AGENT"])
            return false;
        if (!isset($_SESSION["X_MR_IP"]) || $_SESSION["X_MR_IP"] != $_SERVER["REMOTE_ADDR"])
            return false;
        return true;
    }

    /**
     * @return int
     */
    public function getLifeTime()
    {
        return $this->lifetime;
    }

    /**
     * @param $minutes
     */
    public function setLifeTime($minutes)
    {
        if (!is_int($minutes))
            throw new InvalidArgumentException("Invalid lifetime time value");
        if($this->lifetime != $minutes) {
            $this->lifetime = $minutes;
            $_SESSION["X_MR_ET"] = (time() + $this->lifetime * 60) * (int)(bool)$this->lifetime;
        }
    }

}