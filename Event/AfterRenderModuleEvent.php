<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Event;


use Symfony\Component\EventDispatcher\Event;

class AfterRenderModuleEvent extends Event
{
    /**
     * @var object
     */
    protected $module;

    /**
     * @var array
     */
    protected $attribs;

    /**
     * @var string
     */
    protected $content;

    /**
     * BeforeRenderModuleEvent constructor.
     *
     * @param object $module
     * @param array $attribs
     * @param string $content
     */
    public function __construct($module, $attribs, $content)
    {
        $this->module = $module;
        $this->attribs = $attribs;
        $this->content = $content;
    }

    /**
     * @return object
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @return array
     */
    public function getAttribs()
    {
        return $this->attribs;
    }

    /**
     * @param string|null $content
     *
     * @return void
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string|null
     * @author Maximilian Ruta <mr@xtain.net>
     */
    public function getContent()
    {
        return $this->content;
    }
}