<?php

use \XTAIN\Bundle\JoomlaBundle\Joomla\ApplicationInterface;

$application = \JFactory::getApplication();
if (!($application instanceof ApplicationInterface)) {
    throw new \LogicException(sprintf(
        'Joomla application does not implement %s interface',
        ApplicationInterface::class
    ));
}

if (!isset($module->renderer)) {
    $container = $application->getContainer();

    $manager = $container->get('joomla.component.module.module_manager');
    $repository = $container->get('joomla.module_repository');

    $moduleEntity = $repository->find($module->id);
    $module->renderer = $manager->getModuleRenderer($moduleEntity);
}

echo $module->renderer->render();