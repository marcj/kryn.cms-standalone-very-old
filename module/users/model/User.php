<?php



/**
 * Skeleton subclass for representing a row from the 'kryn_system_user' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.kryn
 */
class User extends BaseUser {

    /**
     * Converts $pPassword in a hash and save it.
     * If no salt is already generated, this generates one.
     *
     * @param string $pPassword plain password
     *
     */
    public function setPassword($pPassword){

        if (!$this->getPasswdSalt()){
            $this->setPasswdSalt(Core\Auth::getSalt());
        }

        $passwd = Core\Auth::getHashedPassword( $pPassword, $this->getPasswdSalt() );

        $this->setPasswd($passwd);
    }

} // User
