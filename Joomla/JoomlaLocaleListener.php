<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Joomla;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * The class retrieve a request and ask joomla to build the content
 *
 * @author Maximilian Ruta <mr@xtain.net>
 */
class JoomlaLocaleListener implements JoomlaAwareInterface
{
    /**
     * @var JoomlaInterface
     */
    protected $joomla;

    /**
     * @param JoomlaInterface $joomla
     *
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setJoomla(JoomlaInterface $joomla = null)
    {
        $this->joomla = $joomla;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        $language = $this->joomla->getApplication()->getLanguage();

        if ($language !== null) {
            $locale = str_replace('-', '_', $language->getTag());

            $request->getSession()->set('_locale', $locale);
            // if no explicit locale has been set on this request, use one from the session
            $request->setLocale($request->getSession()->get('_locale', $locale));
        }
    }
}
