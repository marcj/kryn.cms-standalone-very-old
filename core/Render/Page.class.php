<?php


namespace Core\Render;

use Core\Kryn;

class Page {

    /**
     * Contains the end tag for html elements. on XHTML it's `/>` on normal HTML just `>`.
     *
     * @var string
     */
    private $endTag;

    /**
     * Constructor
     */
    public function __construct(){
        $this->setEndTag((strpos(strtolower($this->getDocType()), 'xhtml') !== false) ? '/>' : '>'."\n");
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


    public function getHtml($pBody){
        $header = '';

        $header .= $this->getTitle();
        $header .= $this->getBaseHref();
        $header .= $this->getMetaLanguage();
        $header .= $this->getCssLinks();
        $header .= $this->getJsScripts();

        $docType = $this->getDocType();
        $htmlOpener = $this->getHtmlOpener();

        return sprintf("%s
%s
<head>
%s
</head>
<body>
%s
</body>
</html>
", $docType, $htmlOpener, $header, $pBody);
    }


    public function getDocType(){
        return Utils::$docTypeMap[Utils::getDocType()];
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
                        $temp = Kryn::fileRead($file) . "\n\n\n";
                        //$cssContent .= Kryn::fileRead( $file )."\n\n\n";

                        //replace relative urls to absolute
                        $mypath = dirname($file);
                        $temp = preg_replace('/url\(/', 'url(' . $mypath . '/', $temp);

                        $cssContent .= $temp;
                    }
                }
                Kryn::fileWrite($cssCachedFile, $cssContent);
            }
            $result .= sprintf('<link rel="stylesheet" type="text/css" href="%s" %s', $cssCachedFile, $this->getEndTag());
        }

        return $result;

    }


    public function getJsScripts(){

        $result = '';
        $myJsFiles = array();

        foreach (Kryn::$jsFiles as $js) {
            $myJsFiles[] = $js;
        }

        if (!Kryn::$domain->getResourcecompression()) {
            foreach ($myJsFiles as $js) {
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
            foreach ($myJsFiles as $js) {
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

                foreach ($myJsFiles as $js) {
                    $file = PATH_MEDIA . $js;
                    if (file_exists( $file)) {
                        $jsContent .= "/* $file: */\n\n";
                        $jsContent .= Kryn::fileRead($file) . "\n\n\n";
                    }
                }
                Kryn::fileWrite($jsCachedFile, $jsContent);
            }

            $result .= '<script type="text/javascript" src="' . $jsCachedFile . '" ></script>' . "\n";
        }

        return $result;

    }


}