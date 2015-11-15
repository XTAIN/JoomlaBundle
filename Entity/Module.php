<?php
/**
 * @author Maximilian Ruta <mr@xtain.net>
 */

namespace XTAIN\Bundle\JoomlaBundle\Entity;


class Module
{
    protected $id;

    protected $assetId;

    protected $title;

    protected $note;

    protected $content;

    protected $ordering;

    protected $position;

    protected $checkedOut;

    protected $checkedOutTime;

    protected $publishUp;

    protected $publishDown;

    protected $published;

    /**
     * @var Extension
     */
    protected $module;

    protected $access;

    protected $showtitle;

    /**
     * @var array
     */
    protected $params;

    protected $client;

    protected $language;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getAssetId()
    {
        return $this->assetId;
    }

    /**
     * @param mixed $assetId
     */
    public function setAssetId($assetId)
    {
        $this->assetId = $assetId;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * @param mixed $ordering
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getCheckedOut()
    {
        return $this->checkedOut;
    }

    /**
     * @param mixed $checkedOut
     */
    public function setCheckedOut($checkedOut)
    {
        $this->checkedOut = $checkedOut;
    }

    /**
     * @return mixed
     */
    public function getCheckedOutTime()
    {
        return $this->checkedOutTime;
    }

    /**
     * @param mixed $checkedOutTime
     */
    public function setCheckedOutTime($checkedOutTime)
    {
        $this->checkedOutTime = $checkedOutTime;
    }

    /**
     * @return mixed
     */
    public function getPublishUp()
    {
        return $this->publishUp;
    }

    /**
     * @param mixed $publishUp
     */
    public function setPublishUp($publishUp)
    {
        $this->publishUp = $publishUp;
    }

    /**
     * @return mixed
     */
    public function getPublishDown()
    {
        return $this->publishDown;
    }

    /**
     * @param mixed $publishDown
     */
    public function setPublishDown($publishDown)
    {
        $this->publishDown = $publishDown;
    }

    /**
     * @return mixed
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param mixed $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * @return Extension
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param Extension $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @return mixed
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param mixed $access
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }

    /**
     * @return mixed
     */
    public function getShowtitle()
    {
        return $this->showtitle;
    }

    /**
     * @param mixed $showtitle
     */
    public function setShowtitle($showtitle)
    {
        $this->showtitle = $showtitle;
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
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $clientId
     */
    public function setClient($clientId)
    {
        $this->client = $clientId;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param mixed $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }
}