<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * Exception Class for Services_YouTube
 *
 * PHP versions 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category Services
 * @package  Services_YouTube
 * @author   Shin Ohno <ganchiku@gmail.com>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  CVS: $Id$
 * @link     http://pear.php.net/Services_YouTube
 * @link     http://www.youtube.com/dev
 * @since    0.1
 */

/**
 * uses PEAR_Exception
 */
require_once 'PEAR/Exception.php';

/**
 * Services_YouTube_Exception
 *
 * This class is used in all place in the package where Exceptions
 * are raised.
 *
 * @category Services
 * @package  Services_YouTube
 * @author   Shin Ohno <ganchiku@gmail.com>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  Release: @package_version@
 * @link     http://pear.php.net/Services_YouTube
 */
class Services_YouTube_Exception extends PEAR_Exception
{
    /**
     * errorHandlerCallback
     *
     * @param int    $code    Error code
     * @param string $string  Error message
     * @param string $file    File that caused the error
     * @param int    $line    Line that caused the error
     * @param array  $context Unknown
     *
     * @static
     * @access public
     * throw Services_YouTube_Exception
     * @return void
     */
    public static function errorHandlerCallback($code, $string, $file,
                                                $line, $context)
    {
        $e = new self($string, $code);

        $e->line = $line;
        $e->file = $file;

        throw $e;
    }
}
?>
