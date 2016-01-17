<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Library\CMS\Application;

/**
 * Class Site
 *
 * @author  Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Library\CMS\Application
 */
class Site extends \JProxy_JApplicationSite
{
    /**
     * @param null $component
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function dispatch($component = null)
    {
        // Mark beforeDispatch in the profiler.
        JDEBUG ? $this->profiler->mark('beforeDispatch') : null;

        parent::dispatch($component);
    }

    protected function initialiseApp($options = [])
    {
        // Mark beforeInitialise in the profiler.
        JDEBUG ? $this->profiler->mark('beforeInitialise') : null;

        parent::initialiseApp($options);
    }

    protected function route()
    {
        // Mark beforeRoute in the profiler.
        JDEBUG ? $this->profiler->mark('beforeRoute') : null;

        parent::route();
    }

    protected function render()
    {
        // Mark beforeRender in the profiler.
        JDEBUG ? $this->profiler->mark('beforeRender') : null;

        parent::render();
    }

    /**
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function symfonyInitialise()
    {
        $user = \JFactory::getUser();

        // If a language was specified it has priority, otherwise use user or default language settings
        \JPluginHelper::importPlugin('system', 'languagefilter');

        if (empty($options['language'])) {
            // Detect the specified language
            $lang = $this->input->getString('language', null);

            // Make sure that the user's language exists
            if ($lang && \JLanguage::exists($lang)) {
                $options['language'] = $lang;
            }
        }

        if ($this->_language_filter && empty($options['language'])) {
            // Detect cookie language
            $lang = $this->input->cookie->get(md5($this->get('secret') . 'language'), null, 'string');

            // Make sure that the user's language exists
            if ($lang && \JLanguage::exists($lang)) {
                $options['language'] = $lang;
            }
        }

        if (empty($options['language'])) {
            // Detect user language
            $lang = $user->getParam('language');

            // Make sure that the user's language exists
            if ($lang && \JLanguage::exists($lang))
            {
                $options['language'] = $lang;
            }
        }

        if ($this->_detect_browser && empty($options['language'])) {
            // Detect browser language
            $lang = \JLanguageHelper::detectLanguage();

            // Make sure that the user's language exists
            if ($lang && \JLanguage::exists($lang)) {
                $options['language'] = $lang;
            }
        }

        if (empty($options['language'])) {
            // Detect default language
            $params = \JComponentHelper::getParams('com_languages');
            $options['language'] = $params->get('site', $this->get('language', 'en-GB'));
        }

        // One last check to make sure we have something
        if (!\JLanguage::exists($options['language'])) {
            $lang = $this->config->get('language', 'en-GB');

            if (\JLanguage::exists($lang)) {
                $options['language'] = $lang;
            } else {
                // As a last ditch fail to english
                $options['language'] = 'en-GB';
            }
        }

        // Check that we were given a language in the array (since by default may be blank).
        if (isset($options['language'])) {
            $this->set('language', $options['language']);
        }

        // Build our language object
        $lang = \JLanguage::getInstance($this->get('language'), $this->get('debug_lang'));

        // Load the language to the API
        $this->loadLanguage($lang);

        // Register the language object with JFactory
        \JFactory::$language = $this->getLanguage();
    }
}
