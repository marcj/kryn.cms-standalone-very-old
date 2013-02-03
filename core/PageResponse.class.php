<?php

namespace Core;

use \Symfony\Component\HttpFoundation\Response;

class PageResponse extends Response {


    public static $docType = 'html5';


    public static $docTypeMap = array(

        'html 4.01 transitional' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
        'html 4.01 strict' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
        'html 4.01 frameset' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',

        'xhtml 1.0 transitional' =>
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
        'xhtml 1.0 strict' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
        'xhtml 1.0 frameset' =>
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
        'xhtml 1.1 dtd' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',

        'html5' => '<!DOCTYPE html>'
    );


    private $cssFiles = array();
    private $jsFiles = array();

    private $css = array();
    private $js = array();


    /**
     * Constructor
     */
    public function __construct($content = '', $status = 200, $headers = array()){
        $this->setEndTag((strpos(strtolower($this->getDocType()), 'xhtml') !== false) ? '/>' : '>'."\n");
        parent::__construct($content, $status, $headers);
    }

    /**
     * @param string $endTag
     */
    public function setEndTag($endTag) {
        $this->endTag = $endTag;
    }

    /**
     * @return string
     */
    public function getEndTag() {
        return $this->endTag;
    }

    public function addCssFile($pPath, $pType  = 'text/css'){
        $this->cssFiles[] = array('path' => $pPath, 'type' => $pType);
    }

    public function addCss($pContent, $pType  = 'text/css'){
        $this->css[] = array('content' => $pContent, 'type' => $pType);
    }

    public function addJsFile($pPath, $pPosition = 'top', $pType = 'text/javascript'){
        $this->jsFiles[] = array('path' => $pPath, 'position' => $pPosition, 'type' => $pType);
    }

    public function addJs($pContent, $pPosition = 'top', $pType = 'text/javascript'){
        $this->js[] = array('content' => $pContent, 'position' => $pPosition, 'type' => $pType);
    }


    public function send(){

        //build html skeleton
        $header = '';

        $header .= $this->getTitle();
        $header .= $this->getBaseHref();
        $header .= $this->getMetaLanguage();

        $header .= $this->getCssLinks();
        //$header .= $this->getCssContent();

        $header .= $this->getJsFiles('top');
        //$header .= $this->getJsScripts();

        $beforeBodyClose = '';

        $beforeBodyClose .= $this->getJsFiles('bottom');
        //$beforeBodyClose .= $this->getJsBottomScripts();

        $docType = $this->getDocType();
        $htmlOpener = $this->getHtmlOpener();

        $html = sprintf("%s
%s
<head>
%s
</head>
<body>
%s
%s
</body>
</html>
", $docType, $htmlOpener, $header, $this->getContent(), $beforeBodyClose);

        $html = Kryn::parseObjectUrls($html);

        $this->setContent($html);

        global $_start;
        error_log('Kryn.cms - page generation tooks '.(microtime(true)-$_start).' seconds.');

        return parent::send();
    }


    public function getDocType(){
        return self::$docTypeMap[self::$docType];
    }

    public function getHtmlOpener(){
        return "<html>";
    }

    public function getBaseHref(){
        return sprintf('<base href="%s" %s', Kryn::getBaseUrl(), $this->getEndTag());
    }

    public function getMetaLanguage(){
        return sprintf('<meta name="DC.language" content="%s" %s', Kryn::$domain->getLang(), $this->getEndTag());
    }

    public function getTitle(){
        $title = Kryn::$domain->getTitleFormat();

        if (Kryn::$page){
            $title = str_replace(
                array(
                     '%title'
                ),
                array(
                     Kryn::$page->getAlternativeTitle() ?: Kryn::$page->getTitle()
                )
                , $title);
        }

        return sprintf("<title>%s</title>\n", $title);
    }

    public function getCssLinks(){

        $myCssFiles = array();

        foreach (Kryn::$cssFiles as $css) {
            $myCssFiles[] = $css;
        }

        $result = '';

        if (!Kryn::$domain->getResourcecompression()) {
            foreach ($myCssFiles as $css) {
                if (strpos($css, "http://") !== false) {
                    $result .= sprintf('<link rel="stylesheet" type="text/css" href="%s" %s', $css, $this->getEndTag());
                } else {

                    $mtime = @filemtime(PATH . (substr($css,0,1) != '/' ? PATH_MEDIA : ''). $css);
                    $css .= '?c=' . $mtime;
                    $result .= sprintf('<link rel="stylesheet" type="text/css" href="%s" %s',
                        (substr($css,0,1) != '/' ? PATH_MEDIA.$css:substr($css, 1)), $this->getEndTag());
                }
            }
        } else {
            $cssCode = '';
            foreach ($myCssFiles as $css) {
                if (strpos($css, "http://") !== false) {
                    $result .= sprintf('<link rel="stylesheet" type="text/css" href="%s" %s', $css, $this->getEndTag());
                } else {
                    //local
                    $file = PATH_MEDIA . $css;
                    if (file_exists(PATH . $file) && $mtime = @filemtime(PATH . $file)) {
                        $cssCode .= $file . '_' . $mtime;
                    }
                }
            }

            $cssmd5 = md5($cssCode);

            $cssCachedFile = PATH_MEDIA_CACHE . 'cachedCss_' . $cssmd5 . '.css';

            $cssContent = '';

            if (!file_exists(PATH . $cssCachedFile)) {
                foreach ($myCssFiles as $css) {
                    $file = PATH_MEDIA . $css;
                    if (file_exists($file)) {
                        $cssContent .= "/* $file: */\n\n";
                        $temp = file_get_contents($file) . "\n\n\n";

                        //replace relative urls to absolute
                        $mypath = '../../'.dirname($file);
                        $temp = preg_replace('/url\(\n*\'/', 'url("' . $mypath . '/', $temp);
                        $temp = preg_replace('/url\(\n*"/', 'url("' . $mypath . '/', $temp);
                        $temp = preg_replace('/url\(\n*/', 'url(' . $mypath . '/', $temp);

                        $cssContent .= $temp;
                    }
                }
                file_put_contents($cssCachedFile, $cssContent);
            }
            $result .= sprintf('<link rel="stylesheet" type="text/css" href="%s" %s', $cssCachedFile, $this->getEndTag());
        }

        return $result;

    }


    public function getJsFiles($pPosition = 'top'){

        $result = '';

        if (!Kryn::$domain->getResourcecompression()) {
            foreach ($this->jsFiles as $js) {

                if ($js['position'] != $pPosition) continue;

                if (strpos($js, "http://") !== false) {
                    $result .= sprintf('<script type="text/javascript" src="%s"></script>'.chr(13), $js);
                } else {
                    $mtime = @filemtime(PATH . (substr($js,0,1) != '/' ? PATH_MEDIA : ''). $js);

                    $result .= sprintf(
                        '<script type="text/javascript" src="%s"></script>'.chr(13),
                        ((substr($js,0,1) != '/') ? PATH_MEDIA . $js . '?c=' : substr($js, 1)).$mtime
                    );
                }
            }
        } else {
            $jsCode = '';
            foreach ($this->jsFiles as $js) {

                if ($js['position'] != $pPosition) continue;

                if (strpos($js, "http://") !== false) {
                    $result .= '<script type="text/javascript" src="' . $js . '" ></script>' . "\n";
                } else {
                    //local
                    $file = PATH_MEDIA . $js;
                    if (file_exists(PATH . $file) && $mtime = @filemtime(PATH . $file)) {
                        $jsCode .= $file . '_' . $mtime;
                    }
                }
            }
            $jsmd5 = md5($jsCode);
            $jsCachedFile = PATH_MEDIA_CACHE . 'cachedJs_' . $jsmd5 . '.js';
            $jsContent = '';

            if (!file_exists(PATH . $jsCachedFile)) {

                foreach ($this->jsFiles as $js) {

                    if ($js['position'] != $pPosition) continue;

                    $file = PATH_MEDIA . $js;
                    if (file_exists( $file)) {
                        $jsContent .= "/* $file: */\n\n";
                        $jsContent .= file_get_contents($file) . "\n\n\n";
                    }
                }
                file_put_contents($jsCachedFile, $jsContent);
            }

            $result .= '<script type="text/javascript" src="' . $jsCachedFile . '" ></script>' . "\n";
        }

        return $result;

    }


}