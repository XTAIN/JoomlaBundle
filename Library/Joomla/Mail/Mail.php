<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\Joomla\Mail;

/**
 * Class Mail
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\Joomla\Mail
 */
class Mail extends \JProxy_JMail
{
    /**
     * @var \Swift_Mailer
     */
    protected static $mailer = null;

    /**
     * @param \Swift_Mailer $mailer
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public static function setMailer(\Swift_Mailer $mailer)
    {
        self::$mailer = $mailer;
    }

    public static function httpParseHeaders($raw_headers) {
        $headers = array();
        $key = '';

        foreach(explode("\n", $raw_headers) as $i => $h) {
            $h = explode(':', $h, 2);

            if (isset($h[1])) {
                if (!isset($headers[$h[0]]))
                    $headers[$h[0]] = trim($h[1]);
                elseif (is_array($headers[$h[0]])) {
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])));
                }
                else {
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])));
                }

                $key = $h[0];
            }
            else {
                if (substr($h[0], 0, 1) == "\t")
                    $headers[$key] .= "\r\n\t".trim($h[0]);
                elseif (!$key)
                    $headers[0] = trim($h[0]);
            }
        }

        return $headers;
    }

    /**
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function IsMail()
    {
        $this->Mailer = 'swiftmailer';
    }

    public function swiftmailerSend($rawHeader, $rawBody)
    {
        return $this->mailSend($rawHeader, $rawBody);
    }

    /**
     * Actually send a message.
     * Send the email via the selected mechanism
     * @throws phpmailerException
     * @return boolean
     */
    public function postSend()
    {
        return parent::postSend();
    }
}