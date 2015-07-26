<?php namespace MiladRahimi\PHPSession;

/**
 * Class Session
 * Session class is the main class which developers must interactive with to
 * dispatch all website routes.
 *
 * @package MiladRahimi\PHPRouter
 * @author Milad Rahimi <info@miladrahimi.com>
 */
class Session implements SessionInterface
{

    /**
     * Expiry time (minutes)
     *
     * @var int
     */
    private $lifetime = 0;

    /**
     * Construct
     */
    public function __construct()
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
     * Set key/value pair data in session
     *
     * @param string $name : Information name
     * @param mixed $value : Information value
     * @throws InvalidArgumentException
     */
    public function set($name, $value)
    {
        if (!isset($name) || !is_scalar($name))
            throw new InvalidArgumentException("Name must be a scalar value");
        if (!isset($value))
            throw new InvalidArgumentException("Value must be set");
        $_SESSION[$name] = $value;
    }

    /**
     * Get a value in the session or array of values
     *
     * @param string|null $name : Information name
     * @return mixed : Information value
     * @throws UntrustedSession
     */
    public function get($name = null)
    {
        if (!$this->isTrusted())
            throw new UntrustedSession();
        if (is_null($name))
            return $_SESSION;
        if (!is_scalar($name))
            throw new InvalidArgumentException("Name must be a scalar value");
        if (isset($_SESSION[$name]))
            return $_SESSION[$name];
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
     * @param int $minutes : Lifetime minutes
     */
    public function setLifeTime($minutes = 0)
    {
        if (!is_int($minutes))
            throw new InvalidArgumentException("Minutes must be an integer value");
        if ($this->lifetime != $minutes) {
            $this->lifetime = $minutes;
            $_SESSION["X_MR_ET"] = (time() + $this->lifetime * 60) * (int)(bool)$this->lifetime;
        }
    }

}