<?php
/**
  * @version   $Id: Property.php 10871 2013-05-30 04:06:26Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

class RokGallery_Job_Property
{
    /** @var bool */
    protected $completed = false;

    /** @var bool */
    protected $error = false;

    /** @var string */
    protected $status;

    /**
     * @param boolean $completed
     */
    public function setCompleted($completed = true)
    {
        $this->completed = $completed;
    }

    /**
     * @return boolean
     */
    public function isCompleted()
    {
        return $this->completed;
    }

    /**
     * @param boolean $error
     */
    public function setError($error = true)
    {
        $this->error = $error;
    }

    /**
     * @return boolean
     */
    public function isError()
    {
        return $this->error;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
