<?php

namespace Users\Admin;

class UserEdit extends \Admin\ObjectWindow {

    public $object = 'user';

    public $loadSettingsAfterSave = true;
    
    private static $cacheUser = false;
    
    public $titleField = 'username';

    public $fields = array(
        '__general__' => array(
            'type' => 'tab',
            'label' => '[[General]]',
            'layout' => '   <table width="600"><tr>
                                <td width="110">
                                     <div style="height: 100px; padding-left: 28px; margin:5px" id="picture"></div>
                                </td>
                                <td id="names">
                                </td>
                            </tr><tr>
                                <td colspan="2" style="padding: 10px;">
                                    <table width="100%">
                                        <tbody id="__default__"></tbody>
                                    </table>
                                </td>
                            </tr></table>',
            'children' => array(
                'picture' => array(
                    'noWrapper' => 1,
                    'target' => 'picture',
                    'type' => 'usersPicture'
                ),
                'firstName' => array(
                    'label' => 'First name',
                    'target' => 'name',
                    'type' => 'text',
                    'target' => 'names'
                ),
            	'lastName' => array(
                    'label' => 'Last name',
                    'target' => 'lastname',
                    'type' => 'text',
                    'target' => 'names'
                ),
                'company' => array(
                    'label' => 'Company',
                    'tableItem' => true,
                    'type' => 'text'
                ),
                'street' => array(
                    'label' => 'Street',
                    'tableItem' => true,
                    'type' => 'text'
                ),
                'city' => array(
                    'label' => 'City',
                    'tableItem' => true,
                    'type' => 'text'
                ),
                'zip' => array(
                    'label' => 'Zipcode',
                    'tableItem' => true,
                    'type' => 'number',
                    'maxLength' => 10,
                    'inputWidth' => 100
                ),
                'country' => array(
                    'label' => 'Country',
                    'tableItem' => true,
                    'type' => 'text'
                ),
                'phone' => array(
                    'label' => 'Phone',
                    'tableItem' => true,
                    'type' => 'text'
                ),
                'fax' => array(
                    'label' => 'Fax',
                    'tableItem' => true,
                    'type' => 'text'
                ),
            )
        ),
        '__account__'  => array(
            'type' => 'tab',
            'label' => '[[Account]]',
            'children' => array(
                'username' => array(
                    'label' => 'Username',
                    'desc' => '(and the administration login)',
                    'type' => 'text',
                    'required' => true
                    //'empty' => false // TODO: FE users don't need a username, just an email address [Ferdi]
                ),

                'password' => array( //it's a virtual field from the user model
                    'label' => 'Password',
                    'type' => 'password',
                    'desc' => 'Leave empty to change nothing',
                    'startEmpty' => true,
                    'saveOnlyFilled' => true
                ),

                'email' => array(
                    'label' => 'Email',
                    'type' => 'text',
                    'required' => true,
                    'requiredRegex' => '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-_]+'
                ),
                'activate' => array(
                    'label' => 'Active account',
                    'type' => 'checkbox'
                ),
                'userBg' => array(
                   'label' => 'Desktop background image',
                    'type' => 'file',
                    'noSave' => true,
                    'customValue' => 'userBgValue',
                ),
                'groupMembership'
            )
        ),
        '__administration__' => array(
            'type' => 'tab',
            'label' => '[[Administration]]',
            'children' => array(
                'userBg' => array(
                   'label' => 'Desktop background image',
                    'type' => 'file',
                    'noSave' => true,
                    'customValue' => 'userBgValue',
                ),
                'css3Shadow' => array(
                    'label' => 'Use CSS3 box-shadows',
                    'desc' => 'Can affect performance in some browsers, but activates better window feeling',
                    'type' => 'checkbox',
                    'noSave' => true,
                    'customValue' => 'getCssShadow',
                ),
                'autocrawler' => array(
                    'label' => 'Activate autocrawler',
                    'desc' => 'This activates the internal searchengine autocrawler, when you are working in the administration. Can affect performance, especially when you have low bandwith internet',
                    'type' => 'checkbox',
                    'children' => array(
                        'autocrawler_minddelay' => array(
                            'needValue' => 1,
                            'label' => 'Min. delay (Milliseconds)',
                            'desc' => 'If you have problems with the speed, try to increase this delay.',
                            'type' => 'number',
                            'default' => 200,
                            'length' => 10,
                            'noSave' => true,
                            'customValue' => 'getAutocrawlerDelay'
                        )
                    ),
                    'noSave' => true,
                    'customValue' => 'getAutocrawler'
                )
            )
        )
    );
    
    public function saveItem($pPk){
        $result = parent::saveItem($pPk);

        //todo, save settings

        return $result;
    }

    private function saveSetting( $pKey, $pVal ){
        
        $temp = dbTableFetch('system_user', $this->getPrimaryKey());
        $settings = unserialize( $temp['settings'] );
        
        $settings[$pKey] = $pVal;
        $ssettings = serialize( $settings );

        dbUpdate('system_user', $this->getPrimaryKey(), array('settings' => $ssettings));
    }

    private function getSetting( $pKey ){
        
        if( !self::$cacheUser )
            self::$cacheUser = dbTableFetch('system_user', $this->getPrimaryKey());
            
        $settings = unserialize(self::$cacheUser['settings']);
        return $settings[$pKey];
    }
    
    
    /*
     * Saver
     * 
     */
    public function saveUserBg(){
        $this->saveSetting('userBg', getArgv('userBg',1));
    }

    public function saveLanguage(){
        $this->saveSetting('adminLanguage', getArgv('adminLanguage'));
    }

    public function saveAutocrawler(){
        $this->saveSetting('css3Shadow', getArgv('css3Shadow'));
    }
    
    public function saveCssShadow(){
        $this->saveSetting('autocrawler', getArgv('autocrawler'));
    }

    public function saveAutocrawlerDelay(){
        $this->saveSetting('autocrawler_minddelay', getArgv('autocrawler_minddelay'));
    }
    
    
    /*
     * Getter
     * 
     */

    public function userBgValue($pPrimary, $pItem){
        return $this->getSetting('userBg');
    }
    
    public function getCssShadow(){
        return $this->getSetting('css3Shadow');
    }
 
    public function getAutocrawler(){
        return $this->getSetting('autocrawler');
    }
    
    public function getAutocrawlerDelay(){
        $val = $this->getSetting('autocrawler_minddelay');
        if( !$val ) return 200;
        return $val;
    }    

    public function savePasswd( &$pRow ){
        
        $salt = krynAuth::getSalt();
        $passwd = krynAuth::getHashedPassword( getArgv('passwd'), $salt );
        $pRow['passwd'] = $passwd;
        $pRow['passwd_salt'] = $salt;

    }

}
