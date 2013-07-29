<?php
namespace App\Service;

interface iContent
{

    /**
     *
     * @return object
     * @param integer $id            
     */
    public function find ($id);

    /**
     *
     * @param integer $courseId            
     */
    public function findByCourseId ($courseId);

    /**
     * Create a new content object *
     *
     * @param array $data            
     * @return integer
     */
    public function acl_create (array $data);

    /**
     * Update a content object
     *
     * @param integer $id            
     * @param array $data            
     */
    public function acl_update ($id, array $data);

    /**
     * Delete a content object
     *
     * @param integer $id            
     * @return boolean
     */
    public function acl_delete ($id);

    /**
     * Publish a content object
     *
     * @param integer $id            
     * @return boolean
     */
    public function acl_publish ($id);

    /**
     * Unpublish a content object
     *
     * @param integer $id            
     * @return boolean
     */
    public function acl_unpublish ($id);

    /**
     * Enable a content Object
     *
     * @param integer $id            
     * @return boolean
     */
    public function acl_enable ($id);

    /**
     * Disable a content object
     *
     * @param integer $id            
     * @return boolean
     */
    public function acl_disable ($id);

    /**
     * Return the form object for creating a content object
     *
     * @return object
     */
    public function getForm ();

    /**
     * Return the delete form object to delete a given object
     *
     * @return object
     */
    public function getDeleteForm ();

    /**
     * Get the signed url where a file can be downloaded
     *
     * @param integer $id            
     * @param array $options            
     */
    public function acl_getDownloadUrl ($id, $options = array());
}