<?php

namespace Users\Controller\Admin;

class User extends \Admin\ObjectCrud
{
    public $columns = array(
        'lastName' =>
        array(
            'label' => 'Last name',
            'type' => 'text',
        ),
        'firstName' =>
        array(
            'label' => 'First name',
            'type' => 'text',
        ),
        'username' =>
        array(
            'label' => 'Username',
            'width' => '100',
            'type' => 'text',
        ),
        'email' =>
        array(
            'label' => 'Email',
            'type' => 'text',
        ),
        'activate' =>
        array(
            'label' => 'Active',
            'width' => '35',
            'type' => 'checkbox',
            'align' => 'center'
        ),
        'groupMembership.name' =>
        array(
            'label' => 'Group membership'
        ),
    );

    public $itemLayout = '
    <div title="#{id}">
        <b>{username}</b>
        {if firstName || lastName}
            (<span>{firstName}</span>{if lastName} <span>{lastName}</span>{/if})
        {/if}
        {if email}<div class="sub">{email}</div>{/if}
        <div class="sub">{if groupMembership}{groupMembership.name}{/if}</div>
    </div>';

    public $itemsPerPage = 20;

    public $addIcon = '#icon-plus-5';

    public $add = true;

    public $editIcon = '#icon-pencil-8';
    public $edit = true;

    public $removeIcon = '#icon-minus-5';

    public $remove = true;

    public $export = false;

    public $object = 'Users\User';

    public $preview = false;

    public $workspace = false;

    public $multiLanguage = false;

    public $multiDomain = false;

    public $versioning = false;

    public $order = array('username' => 'asc');

    public $titleField = 'username';

    public $fields = array(
        '__general__' => array(
            'type' => 'tab',
            'label' => '[[General]]',
            'layout' => '   <table style="table-layout: fixed; width: 596px"><tr>
                                <td width="150">
                                    <div>
                                        <div style="height: 100px; margin:5px" id="picture"></div>
                                     </div>
                                </td>
                                <td id="names">
                                </td>
                            </tr><tr>
                                <td colspan="2" style="padding: 8px;">
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
                    'type' => 'text',
                    'target' => 'names'
                ),
                'lastName' => array(
                    'label' => 'Last name',
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
        '__account__' => array(
            'type' => 'tab',
            'label' => '[[Account]]',
            'children' => array(
                'username' => array(
                    'label' => 'Username',
                    'desc' => '(and the administration login)',
                    'type' => 'text'
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
                    'desc' => '(and the administration login if enabled)',
                    'required' => true,
                    'requiredRegex' => '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-_]+'
                ),
                'activate' => array(
                    'label' => 'Active account',
                    'type' => 'checkbox'
                ),
                'groupMembership' => array(
                    'fieldWidth' => 'auto'
                )
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

    public function collectData($fields = null, $data = null)
    {
        $data2 = parent::collectData($fields, $data);

        //save settings
        $settings = array();
        if ($this->primaryKey) {
            $item = $this->getItem($this->primaryKey);
            $settings = $item['settings'];
        }

        $settingsFields = array('autocrawler', 'css3Shadow', 'userBg');
        foreach ($settingsFields as $field) {
            $settings[$field] = $_POST[$field] ? : $_GET[$field];
        }

        $data2['settings'] = new \Core\Properties($settings);

        return $data2;
    }

    private function getSettings()
    {
        if (!$this->cachedUser) {

            $options['fields'][] = 'settings';
            $options['permissionCheck'] = $this->getPermissionCheck();

            $this->cachedUser = \Core\Object::get($this->object, $this->primaryKey, $options);
        }

        $settings = array();
        if ($this->cachedUser['settings']) {
            if (is_string($this->cachedUser['settings'])) {
                $this->cachedUser['settings'] = unserialize($this->cachedUser['settings']);
            }
            $settings = $this->cachedUser['settings']->toArray();
        }

        return $settings;
    }

    private function getSetting($key)
    {
        $settings = $this->getSettings();

        return $settings[$key];
    }

    /*
     * Getter
     *
     */

    public function userBgValue($primary, $item)
    {
        return $this->getSetting('userBg');
    }

    public function getCssShadow()
    {
        return $this->getSetting('css3Shadow');
    }

    public function getAutocrawler()
    {
        return $this->getSetting('autocrawler');
    }

    public function getAutocrawlerDelay()
    {
        $val = $this->getSetting('autocrawler_minddelay');
        if (!$val) {
            return 200;
        }

        return $val;
    }

    public function savePasswd(&$row)
    {
        $salt = krynAuth::getSalt();
        $passwd = krynAuth::getHashedPassword(getArgv('passwd'), $salt);
        $row['passwd'] = $passwd;
        $row['passwd_salt'] = $salt;

    }

}
