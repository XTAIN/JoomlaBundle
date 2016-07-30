<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Component\Symfony;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use XTAIN\Bundle\JoomlaBundle\Joomla\ResourceLocator;
use XTAIN\Bundle\JoomlaBundle\Library\Joomla\Event\AfterEvent;

/**
 * @author Maximilian Ruta <mr@xtain.net>
 */
class CustomFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var ResourceLocator
     */
    protected $resourceLocator;

    /**
     * @var array
     */
    protected $forms;

    /**
     * CustomFieldSubscriber constructor.
     *
     * @param array $forms
     */
    public function __construct(
        ResourceLocator $resourceLocator,
        array $forms
    ) {
        $this->forms = $forms;
        $this->resourceLocator = $resourceLocator;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'joomla.after.on_content_prepare_form' => 'prepareForm'
        );
    }

    public function prepareForm(AfterEvent $event)
    {
        /** @var \JForm $form */
        list($jform, $data) = $event->getArguments();

        $app    = \JFactory::getApplication();
        $option = $app->input->get('option');

        if ($app->isAdmin()) {
            if (isset($this->forms[$option])) {
                foreach ($this->forms[$option] as $form) {
                    $xmlFile = $this->resourceLocator->locate($form['form']);
                    if (!file_exists($xmlFile)) {
                        throw new \Exception('file ' . $xmlFile . ' dont exists');
                    }

                    $jform->loadFile($xmlFile, false);
                }
            }
        }
    }
}