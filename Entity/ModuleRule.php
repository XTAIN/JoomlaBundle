<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Entity;

class ModuleRule
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var ModuleRuleGroup
     */
    protected $group;

    /**
     * @var string
     */
    protected $expression;

    /**
     * @var string
     */
    protected $script;

    /**
     * @return ModuleRuleGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param ModuleRuleGroup $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @param string $expression
     */
    public function setExpression($expression)
    {
        $this->expression = $expression;
    }

    /**
     * @return string
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * @param string $script
     */
    public function setScript($script)
    {
        $this->script = $script;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}