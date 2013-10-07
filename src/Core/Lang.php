<?php

/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@Kryn.org>
 *
 * To get the full copyright and license information, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */

namespace Core;
use Core\Exceptions\FileNotWritableException;
use Symfony\Component\Finder\Finder;

/**
 * krynLanguage - a class that handles .po files
 */

class Lang
{
    public static function getLanguage($bundle, $lang)
    {
        $res = self::parsePo($bundle, $lang);

        $pluralForm = self::getPluralForm($lang);
        preg_match('/^nplurals=([0-9]+);/', $pluralForm, $match);

        $res['pluralCount'] = intval($match[1]);
        $res['pluralForm'] = $pluralForm;

        return $res;
    }

    public static function getPluralForm($lang, $onlyAlgorithm = false)
    {
        //csv based on (c) http://translate.sourceforge.net/wiki/l10n/pluralforms
        $file = Kryn::resolvePath('@CoreBundle/Resources/package/gettext-plural-forms.csv');
        if (!file_exists($file)) {
            return false;
        }

        $fh = fopen($file, 'r');
        if (!$fh) {
            return false;
        }
        while (($buffer = fgetcsv($fh, 1000)) !== false) {
            if ($buffer[0] == $lang) {
                fclose($fh);

                $result = $buffer[2];
                break;
            }
        }

        if ($onlyAlgorithm) {
            $pos = strpos($result, 'plural=');
            return substr($result, $pos + 7);
        } else {
            return $result;
        }
    }

    public static function toPoString($string)
    {
        $string = addcslashes($string, '"');
        $string = str_replace("\n", '\n"' . "\n" . '"', $string);
        $res = preg_replace('/([^\\\\])"/', '$1\"', $string);

        return '"' . $res . '"';
    }

    /**
     * @param $lang
     *
     * @return string Returns the public accessible file path
     */
    public static function getPluralJsFunctionFile($lang)
    {
        if (!WebFile::exists('cache/core_gettext_plural_fn_' . $lang . '.js')) {
            $pluralForm = Lang::getPluralForm($lang, true);

            $code = "function gettext_plural_fn_$lang(n){\n";
            $code .= "    return " . $pluralForm . ";\n";
            $code .= "}";
            WebFile::setContent('cache/core_gettext_plural_fn_' . $lang . '.js', $code);
        }

        return 'cache/core_gettext_plural_fn_' . $lang . '.js';
    }

    public static function getPluralPhpFunctionFile($lang)
    {
        $file = 'core_gettext_plural_fn_' . $lang . '.php';
        if (!TempFile::exists($file)) {
            $pluralForm = Lang::getPluralForm($lang, true);

            $code = "<?php \nfunction gettext_plural_fn_$lang(\$n){\n";
            $code .= "    return " . str_replace('n', '$n', $pluralForm) . ";\n";
            $code .= "}\n?>";

            TempFile::setContent($file, $code);
        }

        return Kryn::getTempFolder() . $file;
    }

    public static function parsePo($bundle, $lang)
    {
        $file = Kryn::resolvePath("@$bundle/$lang.po", 'Resources/translations');

        $res = array('header' => array(), 'translations' => array(), 'file' => $file);
        if (!file_exists($file)) {
            return $res;
        }

        $fh = fopen($file, 'r');

        while (($buffer = fgets($fh)) !== false) {

            if (preg_match('/^msgctxt "(((\\\\.)|[^"])*)"/', $buffer, $match)) {
                $lastWasPlural = false;
                $nextIsThisContext = $match[1];
            }

            if (preg_match('/^msgid "(((\\\\.)|[^"])*)"/', $buffer, $match)) {
                $lastWasPlural = false;
                if ($match[1] == '') {
                    $inHeader = true;
                } else {
                    $inHeader = false;
                    $lastId = $match[1];
                    if ($nextIsThisContext) {
                        $lastId = $nextIsThisContext . "\004" . $lastId;
                        $nextIsThisContext = false;
                    }

                }
            }

            if (preg_match('/^msgstr "(((\\\\.)|[^"])*)"/', $buffer, $match)) {
                if ($inHeader == false) {
                    $lastWasPlural = false;
                    $res['translations'][self::evalString($lastId)] = self::evalString($match[1]);
                }
            }

            if (preg_match('/^msgid_plural "(((\\\\.)|[^"])*)"/', $buffer, $match)) {
                if ($inHeader == false) {
                    $lastWasPlural = true;
                    $res['plurals'][self::evalString($lastId)] = self::evalString($match[1]);
                }
            }

            if (preg_match('/^msgstr\[([0-9]+)\] "(((\\\\.)|[^"])*)"/', $buffer, $match)) {
                if ($inHeader == false) {
                    $lastPluralId = intval($match[1]);
                    $res['translations'][self::evalString($lastId)][$lastPluralId] = self::evalString($match[2]);
                }
            }

            if (preg_match('/^"(((\\\\.)|[^"])*)"/', $buffer, $match)) {
                if ($inHeader == true) {
                    $fp = strpos($match[1], ': ');
                    $res['header'][substr($match[1], 0, $fp)] = str_replace('\n', '', substr($match[1], $fp + 2));
                } else {
                    if (is_array($res['translations'][$lastId])) {
                        $res['translations'][self::evalString($lastId)][$lastPluralId] .= self::evalString($match[1]);
                    } else {
                        if ($lastWasPlural) {
                            $res['plurals'][self::evalString($lastId)] .= self::evalString($match[1]);
                        } else {
                            $res['translations'][self::evalString($lastId)] .= self::evalString($match[1]);
                        }
                    }
                }
            }

        }

        return $res;

    }

    /**
     * @param string $bundle
     * @param string $lang
     * @param array  $translation
     * @return bool
     * @throws Exceptions\FileNotWritableException
     */
    public static function saveLanguage($bundle, $lang, $translation)
    {
        $file = Kryn::resolvePath("@$bundle/$lang.po", 'Resources/translations');

        @mkdir(dirname($file));

        if (!is_writable($file)) {
            throw new FileNotWritableException(t('File `%s` is not writable.', $file));
        }

        $translations = json_decode($translation, true);
        $current = self::parsePo($bundle, $lang);

        $fh = fopen($file, 'w');

        if ($fh == false) {
            return false;
        }

        $pluralForms = self::getPluralForm($lang) ?: 'nplurals=2; plural=(n!=1);';

        if ($current) {
            $current['header']['Plural-Forms'] = $pluralForms;
            $current['header']['PO-Revision-Date'] = date('Y-m-d H:iO');

            fwrite($fh, 'msgid ""' . "\n" . 'msgstr ""' . "\n");

            foreach ($current['header'] as $k => $v) {
                fwrite($fh, '"' . $k . ': ' . $v . '\n"' . "\n");
            }
            fwrite($fh, "\n\n");

        } else {

            //write initial header
            fwrite(
                $fh,
                '
               msgid ""
               msgstr ""
               "Project-Id-Version: Kryn.cms - ' . $bundle . '\n"
"PO-Revision-Date: ' . date('Y-m-d H:iO') . '\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"Language: ' . $lang . '\n"
"Plural-Forms: ' . $pluralForms . '\n"' . "\n\n"
            );

        }
        if (count($translations) > 0) {

            foreach ($translations as $key => $translation) {

                if (strpos($key, "\004") !== false) {
                    //we have a context
                    $context = self::toPoString(substr($key, 0, strpos($key, "\004")));
                    $id = self::toPoString(substr($key, strpos($key, "\004") + 1));
                    fwrite($fh, 'msgctxt ' . $context . "\n");
                    fwrite($fh, 'msgid ' . $id . "\n");
                } else {
                    fwrite($fh, 'msgid ' . self::toPoString($key) . "\n");
                }

                if (is_array($translation)) {

                    fwrite($fh, 'msgid_plural ' . self::toPoString($translation['plural']) . "\n");
                    unset($translation['plural']);

                    foreach ($translation as $k => $v) {
                        fwrite($fh, 'msgstr[' . $k . '] ' . self::toPoString($v) . "\n");
                    }

                } else {
                    fwrite($fh, 'msgstr ' . self::toPoString($translation) . "\n");
                }

                fwrite($fh, "\n");

            }

        }
        fclose($fh);

        Kryn::clearLanguageCache($lang);

        return true;

    }

    public static function extractLanguage($bundle)
    {
        $GLOBALS['moduleTempLangs'] = array();

        $bundleObject = Kryn::getBundle($bundle);
        $path = $bundleObject->getPath();

        self::readDirectory($path. 'Views');
        self::readDirectory($path. 'Resources/public');
        self::readConfig($bundleObject);

        $files = Finder::create()
            ->files()
            ->in($path)
            ->name('*.php');

        foreach ($files as $file) {
            $classPlain = file_get_contents($file);
            if (preg_match('/ extends ObjectCrud/', $classPlain)) {
                preg_match('/^\s*\t*class ([a-z0-9_]+)/mi', $classPlain, $className);
                if (isset($className[1]) && $className[1]){
                    preg_match('/\s*\t*namespace ([a-zA-Z0-9_\\\\]+)/', $classPlain, $namespace);
                    $className = (count($namespace) > 1 ? $namespace[1] . '\\' : '' ) . $className[1];
                    $tempObj = new $className();
                    if ($tempObj->columns) {
                        self::extractFrameworkFields($tempObj->getColumns());
                    }
                    if ($tempObj->fields) {
                        self::extractFrameworkFields($tempObj->getFields());
                    }
                    if ($tempObj->tabFields) {
                        foreach ($tempObj->tabFields as $key => $fields) {
                            $GLOBALS['moduleTempLangs'][$key] = $key;
                            self::extractFrameworkFields($fields);
                        }
                    }
                }
            }
        }

        unset($GLOBALS['moduleTempLangs']['']);

        return $GLOBALS['moduleTempLangs'];
    }

    public static function readConfig(Bundle $bundle)
    {
        $files = $bundle->getConfigFiles();
        foreach ($files as $file) {
            $xml = simplexml_load_file($file);
            $labels = $xml->xpath("//label");
            foreach ($labels as $label) {
                /** @var \SimpleXMLElement $label */
                $GLOBALS['moduleTempLangs'][(string)$label] = (string)$label;
            }
        }
    }

    public static function extractFrameworkFields($fields)
    {
        foreach ($fields as $field) {
            $GLOBALS['moduleTempLangs'][$field['label']] = $field['label'];
            $GLOBALS['moduleTempLangs'][$field['desc']] = $field['desc'];
        }
    }

    public static function extractAdmin($admin)
    {
        if (is_array($admin)) {
            foreach ($admin as $key => $value) {
                if ($value['title']) {
                    $GLOBALS['moduleTempLangs'][$value['title']] = $value['title'];
                }
                if ($value['type'] == 'add' || $value['type'] == 'edit' || $value['type'] == 'list') {

                }
                if (is_array($value['childs'])) {
                    self::extractAdmin($value['childs']);
                }
            }
        }
    }

    public static function evalString($p)
    {
        return stripcslashes($p);
    }

    /*
     *
     * extracts the calls of the translation methods
     *
     * @params string $pFile
     */

    public static function extractFile($file)
    {
        $content = file_get_contents($file);

        $regex = array(

            //t('asd'), _('asd')
            '/[\s\(\)\.](_l|_|t)\(\s*"(((\\\\.)|[^"])*)"\s*\)/',
            //t("asd"), _("asd")
            "/[\s\(\)\.](_l|_|t)\(\s*'(((\\\\.)|[^'])*)'\s*\)/" => '[\Core\Lang::evalString($p[2])] = true',
            //[[asd]]
            "/(\[\[)([^\]]*)\]\]/",
            //tc('context', 'translation')
            "/[\s\(\)\.]tc\(\s*'(((\\\\.)|[^'])*)'\s*,\s*'(((\\\\.)|[^'])*)'\s*\)/" => '[$p[1]."\004".$p[4]] = true',
            //tc("context", "translation")
            '/[\s\(\)\.]tc\(\s*"(((\\\\.)|[^"])*)"\s*,\s*"(((\\\\.)|[^"])*)"\s*\)/' => '[\Core\Lang::evalString($p[1]."\004".$p[4])] = true',
            // t("singular", "plural", $count, "context"
            '/[\s\(\)\.]t\(\s*"(((\\\\.)|[^"])*)"\s*,\s*"(((\\\\.)|[^"])*)"\s*,[^,]*,\s*"(((\\\\.)|[^"])*)"\s*\)/' => '[\Core\Lang::evalString($p[7]."\004".$p[1])] = array($p[1], $p[4])',
            // t('singular', 'plural', *, 'context'
            "/[\s\(\)\.]t\(\s*'(((\\\\.)|[^'])*)'\s*,\s*'(((\\\\.)|[^'])*)'\s*,[^,]*,\s*'(((\\\\.)|[^'])*)'\s*\)/" => '[$p[7]."\004".$p[1]] = array($p[1], $p[4])',
            // t("singular", "plural", $count)
            '/[\s\(\)\.]t\(\s*"(((\\\\.)|[^"])*)"\s*,\s*"(((\\\\.)|[^"])*)"\s*,[^\)]*\)/' => '[\Core\Lang::evalString($p[1])] = array($p[1], $p[4])',
            // t('singular', 'plural', $count)
            "/[\s\(\)\.]t\(\s*'(((\\\\.)|[^'])*)'\s*,\s*'(((\\\\.)|[^'])*)'\s*,[^\)]*\)/" => '[$p[1]] = array($p[1], $p[4])',
            //{t "singular" "plural" $count}
            '/\{t\s+"(((\\\\.)|[^"])*)"\s+"(((\\\\.)|[^"])*)"\s+[^\}"]*\s*\}/' => '[\Core\Lang::evalString($p[1])] = array($p[1], $p[4])',
            //{t "singular" "plural" $count "context}
            '/\{t\s+"(((\\\\.)|[^"])*)"\s+"(((\\\\.)|[^"])*)"\s+[^\}]* \s*"(((\\\\.)|[^"])*)"\}/' => '[\Core\Lang::evalString($p[7]."\004".$p[1])] = array($p[1], $p[4])',
            //{tc "context" "translation"}
            '/\{tc\s+"(((\\\\.)|[^"])*)"\s*"(((\\\\.)|[^"])*)"\s*\}/' => '[\Core\Lang::evalString($p[1]."\004".$p[4])] = true',

        );
        //$GLOBALS['moduleTempLangs'][$file] = true;

        foreach ($regex as $k => $val) {
            if (is_numeric($k)) {
                $ex = $val;
                $fn = '$GLOBALS[\'moduleTempLangs\'][$p[2]] = true; return "";';
            } else {
                $ex = $k;
                $fn = '$GLOBALS[\'moduleTempLangs\']' . $val . '; return "";';
            }

            $content = preg_replace_callback(
                $ex . 'mu',
                create_function(
                    '$p',
                    $fn
                ),
                $content
            );

        }
    }

    public static function readDirectory($path)
    {
        if (!file_exists($path)) return;
        $h = opendir($path);
        while ($file = readdir($h)) {
            if ($file == '.' || $file == '..' || $file == '.svn') {
                continue;
            }
            if (is_dir($path . '/' . $file)) {
                self::readDirectory($path . '/' . $file);
            } else {
                self::extractFile($path . '/' . $file);
            }
        }
    }

}
