<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Component\Module;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use XTAIN\Bundle\JoomlaBundle\Entity\Module;

/**
 * Class AbstractModule
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Component\Module
 */
abstract class AbstractModule implements ModuleRendererInterface
{
    /**
     * @var Module
     */
    protected $module;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param FormFactoryInterface $formFactory
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setFormFactory(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param \Twig_Environment $twig
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setTwigEnvironment(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param Module $module
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setModule(Module $module)
    {
        $this->module = $module;
    }

    public function render()
    {
        return $this->module->getTitle();
    }

    /**
     * @return FormBuilderInterface
     * @author Maximilian Ruta <mr@xtain.net>
     */
    protected function createFormBuilder()
    {
        $data = [
            'params' => $this->module->getParams()
        ];

        $builder = $this->formFactory->createNamedBuilder('jform', 'form', $data);

        $paramsBuilder = $builder->create('params', 'form', [
            'label' => false
        ]);
        $settingsBuilder = $paramsBuilder->create('settings', 'form', [
            'label' => false
        ]);

        $paramsBuilder->add($settingsBuilder);
        $builder->add($paramsBuilder);

        return $builder;
    }

    /**
     * @return \Symfony\Component\Form\FormView
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getSettingsFormView()
    {
        $builder = $this->createFormBuilder();

        return $builder->getForm()->createView();
    }

    public function getFormTemplate()
    {
        return 'XTAINJoomlaBundle::Joomla/Module/settings.html.twig';
    }

    /**
     * @return string
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function renderSettings()
    {
        return $this->twig->render(
            $this->getFormTemplate(),
            [
                'form_view' => $this->getSettingsFormView()
            ]
        );
    }
}