<?php

class auth_imap extends krynAuth
{
    public function checkCredentials($pLogin, $pPassword)
    {
        if (function_exists('imap_open')) {

            $host = $this->config['auth_params']['server'];
            $port = $this->config['auth_params']['port'];

            $string = '{' . $host . ':' . $port . '';

            if ($this->config['auth_params']['ssl']) {
                $string .= '/ssl';
            }

            if ($this->config['auth_params']['novalidate-cert']) {
                $string .= '/novalidate-cert';
            }

            if ($this->config['auth_params']['tls']) {
                $string .= '/tls';
            } else {
                $string .= '/notls';
            }

            $string .= '}';

            $imap = imap_open($string, $pLogin, $pPassword, OP_HALFOPEN);

            if ($imap) {
                imap_close($imap);

                return true;
            }
        } else {
            klog(
                'auth',
                'Imap credentials check failed. Can not found imap_open(). Please install php5-imap on your server.'
            );
        }

        return false;
    }
}
