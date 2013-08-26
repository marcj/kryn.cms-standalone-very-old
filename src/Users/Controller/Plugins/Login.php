<?php

namespace Users\Controller\Plugins;

use Core\Kryn;
use \Core\Controller;

class Login extends Controller
{
    public function display($pluginProperties)
    {
        if (getArgv('users-loggedOut') || getArgv('users-logout')) {
            Kryn::disableSearchEngine();
            Kryn::getClient()->logout();
            if ($pluginProperties['logoutTarget']) {
                Kryn::redirectToPage($pluginProperties['logoutTarget']);
            } else {
                Kryn::redirectToPage(Kryn::getPage()->getId());
            }
        }

        if (getArgv('users-login')) {
            Kryn::disableSearchEngine();
            $login = getArgv('users-username') ? getArgv('users-username') : getArgv('users-email');

            Kryn::getClient()->login($login, getArgv('users-passwd'));

            if (Kryn::getClient()->getUserId() > 0) {
                if ($pluginProperties['logoutTarget']) {
                    Kryn::redirectToPage($pluginProperties['logoutTarget']);
                } else {
                    Kryn::redirectToPage(Kryn::getPage()->getId());
                }
            } else {
                tAssign('loginFailed', 1);
            }
        }

        if (!strpos($pluginProperties['template'], '/') > 0) {
            $pluginProperties['template'] = 'users/login/' . $pluginProperties['template'] . '.tpl';
        }

        if (!strpos($pluginProperties['templateLoggedIn'], '/') > 0) {
            $pluginProperties['templateLoggedIn'] = 'users/loggedIn/' . $pluginProperties['templateLoggedIn'] . '.tpl';
        }

        if (Kryn::getClient()->getUserId() > 0) {
            return Kryn::unsearchable(tFetch($pluginProperties['templateLoggedIn']));
        } else {
            return Kryn::unsearchable(tFetch($pluginProperties['template']));
        }
    }
}
