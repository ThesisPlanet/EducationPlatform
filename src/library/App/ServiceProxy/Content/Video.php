<?php
namespace App\ServiceProxy\Content;

class Video extends \App\ServiceProxy\aService implements 
        \App\Service\Content\iVideo
{

    protected function loadService ()
    {
        $this->_service = new \App\Service\Content\Video();
    }

    /**
     * (non-PHPdoc)
     *
     * @see \App\Service\Content\iAudio::acl_getStreamUrl()
     * @param integer $id            
     * @param array $options            
     * @return string
     */
    public function acl_getStreamUrl ($id, $options = array())
    {
        return $this->_service->acl_getStreamUrl($id, $options);
    }

    /**
     * Gets the distribution streamer url
     *
     * @return string
     */
    public function getDistributionStreamer ()
    {
        return $this->_service->getDistributionStreamer();
    }

    /**
     *
     * @param integer $id            
     * @return array
     * @see \App\Service\iContent::find()
     */
    public function find ($id)
    {
        return $this->_service->find($id);
    }

    /**
     *
     * @param integer $courseId            
     * @return array
     * @see \App\Service\iContent::findByCourseId()
     */
    public function findByCourseId ($courseId)
    {
        return $this->_service->findByCourseId($courseId);
    }

    /**
     *
     * @param array $data            
     * @return integer
     * @see \App\Service\iContent::acl_create()
     */
    public function acl_create (array $data)
    {
        return $this->_service->acl_create($data);
    }

    /**
     *
     * @param integer $id            
     * @param array $data            
     * @return boolean
     * @see \App\Service\iContent::acl_update()
     */
    public function acl_update ($id, array $data)
    {
        return $this->_service->acl_update($id, $data);
    }

    /**
     *
     * @param integer $id            
     * @return boolean
     * @see \App\Service\iContent::acl_delete()
     */
    public function acl_delete ($id)
    {
        return $this->_service->acl_delete($id);
    }

    /**
     *
     * @param integer $id            
     * @return boolean
     * @see \App\Service\iContent::acl_publish()
     */
    public function acl_publish ($id)
    {
        return $this->_service->acl_publish($id);
    }

    /**
     *
     * @param integer $id            
     * @return boolean
     * @see \App\Service\iContent::acl_unpublish()
     */
    public function acl_unpublish ($id)
    {
        return $this->_service->acl_unpublish($id);
    }

    /**
     *
     * @param integer $id            
     * @return boolean
     * @see \App\Service\iContent::acl_enable()
     */
    public function acl_enable ($id)
    {
        return $this->_service->acl_enable($id);
    }

    /**
     *
     * @param integer $id            
     * @return boolean
     * @see \App\Service\iContent::acl_disable()
     */
    public function acl_disable ($id)
    {
        return $this->_service->acl_disable($id);
    }

    /**
     *
     * @return string
     * @see \App\Service\iContent::getForm()
     */
    public function getForm ()
    {
        return $this->_service->getForm()->__toString();
    }

    /**
     *
     * @return string
     * @see \App\Service\iContent::getDeleteForm()
     */
    public function getDeleteForm ()
    {
        return $this->_service->getDeleteForm()->__toString();
    }

    /**
     *
     * @param integer $id            
     * @param array $options            
     * @return string
     * @see \App\Service\iContent::acl_getDownloadUrl()
     */
    public function acl_getDownloadUrl ($id, $options = array())
    {
        return $this->_service->acl_getDownloadUrl($id, $options);
    }

    /**
     *
     * @param integer $id            
     * @param array $options            
     * @return string
     * @see \App\Service\Content\iVideo::acl_getThumbnailUrl()
     */
    public function acl_getThumbnailUrl ($id, $options = array())
    {
        return $this->_service->acl_getThumbnailUrl($id, $options);
    }
}