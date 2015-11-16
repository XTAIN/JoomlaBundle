<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Mail;

use XTAIN\Bundle\JoomlaBundle\Library\Joomla\Mail\Mail;

/**
 * Class MailFactory
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Factory\Joomla\Log
 */
class MailFactory implements MailFactoryInterface
{
    /**
     * @var \Swift_Mailer
     */
    static protected $mailer;

    /**
     * @param \Swift_Mailer $mailer
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setMailer(\Swift_Mailer $mailer)
    {
        self::$mailer = $mailer;
    }

    /**
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function injectStaticDependencies()
    {
        Mail::setMailer(self::$mailer);
    }
}
