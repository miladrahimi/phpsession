<?php namespace MiladRahimi\PHPSession;

/**
 * Interface SessionInterface
 * Session interface must have `set()` and `get()` methods.
 *
 * @package MiladRahimi\PHPSession
 * @author Milad Rahimi <info@miladrahimi.com>
 */
interface SessionInterface
{
    /**
     * Set key/value pair data in session
     *
     * @param string $name : Information name
     * @param mixed $value : Information value
     * @throws InvalidArgumentException
     */
    public function set($name, $value);

    /**
     * Get a value in the session or array of values
     *
     * @param string|null $name : Information name
     * @return mixed : Information value
     * @throws UntrustedSession
     */
    public function get($name = null);

}