<?php
/**
 * This file is part of the XTAIN Joomla package.
 *
 * (c) Maximilian Ruta <mr@xtain.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XTAIN\Bundle\JoomlaBundle\Entity;

/**
 * Class Extension
 *
 * @author Maximilian Ruta <mr@xtain.net>
 * @package XTAIN\Bundle\JoomlaBundle\Entity
 */
class Extension
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $element;

    /**
     * @var string
     */
    protected $folder = '';

    /**
     * @var int
     */
    protected $clientId = 0;

    /**
     * @var boolean
     */
    protected $enabled = 1;

    /**
     * @var int
     */
    protected $access = 1;

    /**
     * @var int
     */
    protected $protected = 0;

    /**
     * @var array
     */
    protected $manifestCache = [];

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var array
     */
    protected $customData = [];

    /**
     * @var array
     */
    protected $systemData = [];

    /**
     * @var int
     */
    protected $checkedOut = 0;

    /**
     * @var \DateTime
     */
    protected $checkedOutTime;

    /**
     * @var int
     */
    protected $ordering = 0;

    /**
     * @var int
     */
    protected $state = 0;

    public function __construct()
    {
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('UTC'));
        $date->setDate(0, 0, 0);
        $date->setTime(0, 0, 0);
        $this->checkedOutTime = $date;
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
     * @return string
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param string $element
     */
    public function setElement($element)
    {
        $this->element = $element;
    }

    /**
     * @return string
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * @param string $folder
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    /**
     * @return int
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param int $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return int
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param int $access
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }

    /**
     * @return int
     */
    public function getProtected()
    {
        return $this->protected;
    }

    /**
     * @param int $protected
     */
    public function setProtected($protected)
    {
        $this->protected = $protected;
    }

    /**
     * @return array
     */
    public function getManifestCache()
    {
        return $this->manifestCache;
    }

    /**
     * @param array $manifestCache
     */
    public function setManifestCache($manifestCache)
    {
        $this->manifestCache = $manifestCache;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getCustomData()
    {
        return $this->customData;
    }

    /**
     * @param array $customData
     */
    public function setCustomData($customData)
    {
        $this->customData = $customData;
    }

    /**
     * @return array
     */
    public function getSystemData()
    {
        return $this->systemData;
    }

    /**
     * @param array $systemData
     */
    public function setSystemData($systemData)
    {
        $this->systemData = $systemData;
    }

    /**
     * @return int
     */
    public function getCheckedOut()
    {
        return $this->checkedOut;
    }

    /**
     * @param int $checkedOut
     */
    public function setCheckedOut($checkedOut)
    {
        $this->checkedOut = $checkedOut;
    }

    /**
     * @return \DateTime
     */
    public function getCheckedOutTime()
    {
        return $this->checkedOutTime;
    }

    /**
     * @param \DateTime $checkedOutTime
     */
    public function setCheckedOutTime($checkedOutTime)
    {
        $this->checkedOutTime = $checkedOutTime;
    }

    /**
     * @return int
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * @param int $ordering
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }
}