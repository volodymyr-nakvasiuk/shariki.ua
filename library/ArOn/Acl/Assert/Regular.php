<?php
class ArOn_Acl_Assert_Regular implements Zend_Acl_Assert_Interface
{
    /**
     * This assertion should receive the actual User and BlogPost objects.
     *
     * @param Zend_Acl $acl
     * @param Zend_Acl_Role_Interface $user
     * @param Zend_Acl_Resource_Interface $blogPost
     * @param $privilege
     * @return bool
     */
    public function assert(Zend_Acl $acl, Zend_Acl_Role_Interface $user = null, Zend_Acl_Resource_Interface $resource = null, $privilege = null)
    {
    	/*echo ' == Checking the assertion ==' . PHP_EOL; // only here for the purposes of article

        if (!$user instanceof User) {
            throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . ' expects the role to be an instance of User');
        }

        if (!$blogPost instanceof BlogPost) {
            throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . ' expects the resource to be an instance of BlogPost');
        }*/

        // if role is publisher, he can always modify a post
        if ($user->getRoleId() == 'publisher') {
        	return true;
        }
        return true;
//var_dump($resource);
//var_dump($privilege);die;
        // check to ensure that everyone else is only modifying their own post
        if ($user->id != null && $blogPost->ownerUserId == $user->id) {
        	return true;
        } else {
        	return false;
        }
    }
}
