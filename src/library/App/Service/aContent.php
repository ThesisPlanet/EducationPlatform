<?php
namespace App\Service;

abstract class aContent extends Base implements iContent
{

    protected $_em = null;

    protected $_user = null;

    const NOT_FOUND = "That content was not found.";

    const SERVER_NOT_DEFINED = "a valid IP address hosting the content must be provided.";

    const CONTENT_OBJECT_NOT_SET = "A content object must be provided to run ACL against.";

    public function dynamicACLIsAllowed ($contentObj, $action)
    {
        if (! is_object($contentObj)) {
            throw new \exception(self::CONTENT_OBJECT_NOT_SET);
        }
        
        if (! isset($this->_user)) {
            throw new \exception(self::USER_NOT_SET);
        }
        if (! is_object($this->_user)) {
            throw new \exception(self::USER_NOT_OBJECT);
        }
        if (! method_exists($this->_user, 'getSubscriptions')) {
            throw new \exception(self::USER_MUST_IMPLEMENT_GETSUBSCRIPTIONS);
        }
        $userService = new \App\Service\User();
        
        if (! method_exists($contentObj, 'getCourse')) {
            throw new \exception(self::INVALID_PROTECTED_OBJECT);
        }
        $subs = $this->_user->getsubscriptions();
        foreach ($subs as $key => $subscriptionObject) {
            if ($subscriptionObject->getCourse()->getId() ===
                     $contentObj->getCourse()->getId()) {
                if ($contentObj->getAcl()->isAllowed(
                        $subscriptionObject->getRole(), null, $action)) {
                    return true;
                }
            }
        }
        $role = $userService->authorize($this->_user->getId());
        if ($this->_acl->isAllowed($role, null, $action)) {
            return true;
        }
        return false;
    }

    public function isAllowed ($contentObj, $action)
    {
        if (! is_object($contentObj)) {
            throw new \exception(self::CONTENT_OBJECT_NOT_SET);
        }
        
        if (! isset($this->_user)) {
            throw new \exception(self::USER_NOT_SET);
        }
        if (! is_object($this->_user)) {
            throw new \exception(self::USER_NOT_OBJECT);
        }
        if (! method_exists($this->_user, 'getSubscriptions')) {
            throw new \exception(self::USER_MUST_IMPLEMENT_GETSUBSCRIPTIONS);
        }
        $userService = new \App\Service\User();
        
        if (! method_exists($contentObj, 'getCourse')) {
            throw new \exception(self::INVALID_PROTECTED_OBJECT);
        }
        $subs = $this->_user->getsubscriptions();
        foreach ($subs as $key => $subscriptionObject) {
            if ($subscriptionObject->getCourse()->getId() ===
                     $contentObj->getCourse()->getId()) {
                if ($this->_acl->isAllowed($subscriptionObject->getRole(), null, 
                        $action)) {
                    return true;
                }
            }
        }
        // User-level role overrides enable employees to be able to perform
        // actions system-wide.
        $role = $userService->authorize($this->_user->getId());
        if ($this->_acl->isAllowed($role, null, $action)) {
            return true;
        }
        return false;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \App\Service\iContent::acl_publish()
     */
    public function acl_publish ($id)
    {
        $obj = $this->_repository->find($id);
        if (is_object($obj)) {
            if (! $this->isAllowed($obj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::NOT_FOUND);
        }
        $obj->setIsPublished(true);
        $this->_em->persist($obj);
        $this->_em->flush();
        return true;
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\iContent::acl_unpublish()
     */
    public function acl_unpublish ($id)
    {
        $obj = $this->_repository->find($id);
        if (is_object($obj)) {
            if (! $this->isAllowed($obj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::NOT_FOUND);
        }
        $obj->setIsPublished(false);
        $this->_em->persist($obj);
        $this->_em->flush();
        return true;
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\iContent::acl_enable()
     */
    public function acl_enable ($id)
    {
        $obj = $this->_repository->find($id);
        if (is_object($obj)) {
            if (! $this->isAllowed($obj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::NOT_FOUND);
        }
        $obj->setIsEnabled(true);
        $this->_em->persist($obj);
        $this->_em->flush();
        return true;
    }
    
    /*
     * (non-PHPdoc) @see \App\Service\iContent::acl_disable()
     */
    public function acl_disable ($id)
    {
        $obj = $this->_repository->find($id);
        if (is_object($obj)) {
            if (! $this->isAllowed($obj, __FUNCTION__)) {
                throw new \exception(self::PERMISSION_DENIED);
            }
        } else {
            throw new \exception(self::NOT_FOUND);
        }
        $obj->setIsEnabled(false);
        $this->_em->persist($obj);
        $this->_em->flush();
        return true;
    }
}

?>