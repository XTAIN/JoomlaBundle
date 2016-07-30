<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Entity;

class ModuleRuleGroup
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var Module[]
     */
    protected $modules;

    /**
     * @var ModuleRule[]
     */
    protected $rules;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @return Module[]
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * @param Module[] $modules
     */
    public function setModules($modules)
    {
        $this->modules = $modules;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return ModuleRule[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param ModuleRule[] $rules
     */
    public function setRules($rules)
    {
        $this->rules = $rules;
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