<?php

namespace Users\Plugins;

use \Core\Kryn;

class Login extends \Controller{


	public function display($pPluginProperties){

        if (getArgv('users-loggedOut') || getArgv('users-logout')){
            Kryn::disableSearchEngine();
            Kryn::getClient()->logout();
            if ($pPluginProperties['logoutTarget']){
                Kryn::redirectToPage($pPluginProperties['logoutTarget']);
            } else {
                Kryn::redirectToPage(Kryn::getPage()->getId());
            }
        }
        

        if (getArgv('users-login')){
            Kryn::disableSearchEngine();
            $login = getArgv('users-username')? getArgv('users-username') : getArgv('users-email');
            
            Kryn::getClient()->login( $login, getArgv('users-passwd') );

            if( Kryn::getClient()->getUserId() > 0 ){
                if ($pPluginProperties['logoutTarget'])
                    Kryn::redirectToPage( $pPluginProperties['logoutTarget'] );
                else
                    Kryn::redirectToPage( Kryn::getPage()->getId() );
            } else {
                tAssign('loginFailed', 1);
            }
        }
        
        if(! strpos($pPluginProperties['template'], '/') > 0 )
            $pPluginProperties['template'] = 'users/login/'.$pPluginProperties['template'].'.tpl';

        if(! strpos($pPluginProperties['templateLoggedIn'], '/') > 0 )
            $pPluginProperties['templateLoggedIn'] = 'users/loggedIn/'.$pPluginProperties['templateLoggedIn'].'.tpl';

        if( Kryn::getClient()->getUserId() > 0 ){
            return Kryn::unsearchable(tFetch($pPluginProperties['templateLoggedIn']));
        } else {
            return Kryn::unsearchable(tFetch($pPluginProperties['template']));
        }
    }
}