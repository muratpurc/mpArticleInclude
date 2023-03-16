<?php
/**
 * CONTENIDO module class for mp_article_include.
 *
 * @package     Module
 * @subpackage  MpArticleIncludeModule
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

use CONTENIDO\Plugin\MpDevTools\Module\AbstractBase;
use Purc\Snoopy\Snoopy;

/**
 * CONTENIDO module class for mp_article_include
 *
 * @property int cmsCatID
 * @property int cmsArtID
 * @property string cmsStartMarker
 * @property string cmsEndMarker
 * @property string cmsIncludeMode
 * @property cDb db
 * @property bool articleIsAvailable
 * @property int incIdcatart
 * @property int incIdcat
 * @property int incIdart
 * @property string incLastModified
 * @property array i18n
 */
class MpArticleIncludeModule extends AbstractBase
{

    /**
     * Default start marker.
     *
     * @var string
     */
    const DEFAULT_START_MARKER = '<!--start:content-->';

    /**
     * Default end marker.
     *
     * @var string
     */
    const DEFAULT_END_MARKER = '<!--end:content-->';

    /**
     * Default include mode.
     *
     * @var string
     */
    const DEFAULT_INCLUDE_MODE = 'fsockopen';

    /**
     * Setting include mode.
     *
     * @var string
     */
    const SETTING_INCLUDE_MODE = 'setting';

    /**
     * Cache lifetime.
     *
     * @var int
     */
    const CACHE_LIFETIME = 2592000; // 1 month (60 * 60 * 24 * 30)

    /**
     * To store extracted output code of included article.
     *
     * @var string
     */
    protected $code = '';

    /**
     * Unique module id (module id + container)
     * @var  string
     */
    protected $uid = '';

    /**
     * File cache instance.
     *
     * @var cFileCache
     */
    protected $cache = null;

    /**
     * Identifier for the file cache.
     *
     * @var string
     */
    protected $cacheId = '';

    /**
     * @var float
     */
    protected $startTime;

    /**
     * Module properties structure.
     *
     * See {@see AbstractBase::$baseProperties} for base properties. Only
     * properties being defined here and in the base class ($baseProperties)
     * will be taken over to the $properties structure.
     *
     * @var  array
     */
    protected $properties = [
        'cmsCatID' => 0,
        'cmsArtID' => 0,
        'cmsStartMarker' => '',
        'cmsEndMarker' => '',
        'cmsIncludeMode' => '',

        'articleIsAvailable' => false,
        'incIdcatart' => 0,
        'incIdcat' => 0,
        'incIdart' => 0,
        'incLastModified' => '',

        'db' => null,
        'i18n' => [],
    ];

    /**
     * Constructor, sets some properties.
     *
     * @param array $properties Properties array
     * @throws cException
     */
    public function __construct(array $properties = [])
    {
        parent::__construct('mp_article_include', $properties);

        $this->validate();

        $this->uid = $this->moduleId . '_' . $this->containerNumber;
        $this->code = '';

        $this->cache = new cFileCache([
            'cacheDir' => $this->getClientInfo()->getCachePath(),
            'lifeTime' => self::CACHE_LIFETIME,
        ]);

        $this->cacheId = implode('_', [$this->uid, $this->cmsCatID, $this->cmsArtID]);
    }

    /**
     * Main function to retrieve the article, runs some checks, like if article
     * and category is available, and finally it requests the article.
     *
     * @return  bool  Success state
     * @throws cDbException|cException|cInvalidArgumentException
     */
    public function includeArticle(): bool
    {
        $this->startTime = getmicrotime();
        $this->_printInfo("[INFO] idcat $this->cmsCatID, idart $this->cmsArtID");

        if ($this->cmsCatID < 0) {
            $this->_printInfo("[NOTICE] No idcat!");
            return false;
        }

        if (!$this->checkArticle()) {
            $this->_printInfo("[NOTICE] Article is not available!");
            return false;
        }

        if (!$this->checkCategory()) {
            $this->_printInfo("[NOTICE] Category is not public or visible!");
            return false;
        }

        if ($this->retrieveFromCache()) {
            return true;
        }

        if (!$this->requestArticle()) {
            return false;
        }

        return true;
    }

    /**
     * Returns the extracted code (HTML output) from article.
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Validates module configuration/data
     */
    protected function validate()
    {
        // Selected category id
        $this->cmsCatID = cSecurity::toInteger($this->cmsCatID);

        // Selected article id
        $this->cmsArtID = cSecurity::toInteger($this->cmsArtID);

        // Include mode
        if (!$this->validateIncludeMode($this->cmsIncludeMode)) {
            $this->cmsIncludeMode = '';
        }

        // Start and end marker
        if (empty($this->cmsStartMarker) && !empty($this->defaultStartMarker)) {
            $this->cmsStartMarker = conHtmlSpecialChars($this->defaultStartMarker);
        } if (empty($this->cmsStartMarker)) {
            $this->cmsStartMarker = conHtmlSpecialChars(self::DEFAULT_START_MARKER);
        }

        if (empty($this->cmsEndMarker) && !empty($this->defaultEndMarker)) {
            $this->cmsEndMarker = conHtmlSpecialChars($this->defaultEndMarker);
        } if (empty($this->cmsEndMarker)) {
            $this->cmsEndMarker = conHtmlSpecialChars(self::DEFAULT_END_MARKER);
        }
    }

    protected function validateIncludeMode($includeMode, bool $fromSetting = false): bool
    {
        if ($fromSetting) {
            // Include mode from setting cannot have the value 'setting'!
            $allowedModes = ['curl', 'fsockopen', 'file_get_contents', 'snoopy'];
        } else {
            $allowedModes = ['setting', 'curl', 'fsockopen', 'file_get_contents', 'snoopy'];
        }

        return is_string($includeMode) && !empty($includeMode) && in_array($includeMode, $allowedModes);
    }

    /**
     * Returns the id attribute value by concatenating passed name with the module uid.
     *
     * @param string $name
     * @return string
     */
    public function getIdValue(string $name): string
    {
		return $name . '_' . $this->getUid();
    }

    /**
     * Returns the module uid (module id + container number).
     * @return string
     */
	public function getUid(): string
    {
		return $this->uid;
	}

    /**
     * Renders the select for the include mode.
     *
     * @param string $name
     * @param string $selectedValue
     * @return string
     * @throws cDbException
     * @throws cException
     */
    public function renderIncludeModeSelect(string $name, string $selectedValue): string
    {
        $setting = $this->getEffectiveSetting('include_mode');
        if (!$this->validateIncludeMode($setting)) {
            $settingLbl = $this->i18n['LBL_NO_SETTING'];
            $setting = self::DEFAULT_INCLUDE_MODE;
        } else {
            $settingLbl = $setting;
            $setting = self::SETTING_INCLUDE_MODE;
        }

        if (!$this->validateIncludeMode($selectedValue)) {
            $selectedValue = $setting;
        }

        $select = new cHTMLSelectElement($name);
        $options = [
            '' => $this->i18n['VAL_PLEASE_CHOOSE'],
            'setting' => sprintf($this->i18n['VAL_FROM_SETTINGS_%S'], $settingLbl),
            'curl' => $this->i18n['VAL_CURL'],
            'fsockopen' => $this->i18n['VAL_FSOCKOPEN'],
            'file_get_contents' => $this->i18n['VAL_FILE_GET_CONTENTS'],
            'snoopy' => $this->i18n['VAL_SNOOPY'],
        ];
        $select->autoFill($options);
        $select->setDefault($selectedValue);

        return $select->render();
    }

    /**
     * Renders the JavaScript code for the module input.
     *
     * @return string
     */
    public function renderModuleInputJavaScript(): string
    {
        $cmsCatIdSelector = '#' . $this->getIdValue('cmsCatID') . ' select';
        return '
<script type="text/javascript">
    (function($) {
        $(function() {
            // Register event handler for category select change
            $("' . $cmsCatIdSelector . '").change(function() {
                $("form[name=tplcfgform]").submit();
            });
        });
    })(jQuery);
</script>
';

    }

    /**
     * Checks if article exists and is online.
     *
     * @return  bool
     * @throws cDbException|cInvalidArgumentException
     */
    protected function checkArticle(): bool
    {
        $this->articleIsAvailable = false;

        $comment = '-- ' . __CLASS__ . '->' . __FUNCTION__ . '()';

        // Get idcat, idcatart, idart and lastmodified from the database
        $sql = "$comment
            SELECT
                ca.idart,
                ca.idcat,
                ca.idcatart,
                al.lastmodified 
            FROM
                `" . cRegistry::getDbTableName('cat_art') . "` AS ca,
                `" . cRegistry::getDbTableName('art_lang') . "` AS al 
            WHERE
                ca.idart = al.idart AND
                al.online = 1 AND
                al.idlang = " . $this->languageId . " AND
        ";

        if ($this->cmsArtID == 0) {
            // Get the latest article of category, if only idcat is configured
            $sql .= "    ca.idcat = " . $this->cmsCatID . "\n    ORDER BY al.lastmodified DESC";
        } else {
            // Article specified
            $sql .= "    al.idart = " . $this->cmsArtID;
        }
        $this->_printInfo("[INFO] SQL to check article: $sql");

        $this->db->query($sql);
        if ($this->db->nextRecord()) {
            $this->articleIsAvailable = true;
            $this->incIdcatart = cSecurity::toInteger($this->db->f('idcatart'));
            $this->incIdcat = cSecurity::toInteger($this->db->f('idcat'));
            $this->incIdart = cSecurity::toInteger($this->db->f('idart'));
            $this->incLastModified = $this->db->f('lastmodified');
        }
        $this->db->free();

        return $this->articleIsAvailable;
    }

    /**
     * Checks if category exists, is online and public
     * @return  bool
     * @throws cException
     */
    protected function checkCategory(): bool
    {
        // Check if category is online or protected
        $catLang = new cApiCategoryLanguage();
        $catLang->loadByCategoryIdAndLanguageId($this->incIdcat, $this->languageId);
        $this->_printInfo('[INFO] $this->incIdcat: ' . print_r($this->incIdcat, true));
        $this->_printInfo('[INFO] $this->languageId: ' . print_r($this->languageId, true));
        $this->_printInfo('[INFO] $catLang->toArray(): ' . print_r($catLang->toArray(), true));
        $catIsPublic = cSecurity::toInteger($catLang->get('public'));
        $catIsVisible = cSecurity::toInteger($catLang->get('visible'));

        return ($catIsPublic && $catIsVisible);
    }

    /**
     * Requests the article by using one of the defined include modes.
     *
     * @return  bool
     * @throws cDbException|cException|cInvalidArgumentException
     */
    protected function requestArticle(): bool
    {
        // Get article output

        $url = cUri::getInstance()->build([
            'idart' => $this->incIdart, 'lang' => $this->languageId
        ], true);

        $result = $this->retrieveFromRequest($url);
        if (is_string($result)) {
            $this->code = trim($result);
        }
        $this->_printInfo('[INFO] Execution time: ' . (getmicrotime() - $this->startTime) . ' seconds');

        // Extract content from article code output
        if (!empty($this->code)) {
            $cmsStartMarker = htmlspecialchars_decode($this->cmsStartMarker);
            $cmsEndMarker = htmlspecialchars_decode($this->cmsEndMarker);

            $startPos = cString::findFirstPos($this->code, $cmsStartMarker);
            $endPos   = cString::findFirstPos($this->code, $cmsEndMarker);

            if ($startPos !== false || $endPos !== false) {
                $diffLen = $endPos - $startPos + strlen($cmsEndMarker);
                $this->code = cString::getPartOfString($this->code, $startPos, $diffLen);
                if (!empty($this->code)) {
                    $this->cache->save($this->code, $this->cacheId, $this->getModuleName());
                }
                return true;
            } else {
                $msg = "[ERROR] In module " . $this->getModuleName() . "<pre>Couldn't detect marker $this->cmsStartMarker and/or $this->cmsEndMarker!\n"
                   . "idcat $this->cmsCatID, idart $this->cmsArtID, idlang $this->languageId, idclient $this->clientId";
                $this->_printInfo($msg);
                return false;
            }
        } else {
            $msg = "[ERROR] In module " . $this->getModuleName() . "<pre>Can't get article to include!\n"
               . "idcat $this->cmsCatID, idart $this->cmsArtID, idlang $this->languageId, idclient $this->clientId\n";
            $this->_printInfo($msg);
            return false;
        }
    }

    /**
     * Retrieves the article to include from the cache.
     *
     * @return bool
     * @throws cException
     * @throws cInvalidArgumentException
     */
    protected function retrieveFromCache(): bool
    {
        if ($this->validateCache()) {
            $code = $this->cache->get($this->cacheId, $this->getModuleName());
            if (is_string($code)) {
                $code = trim($code);
                if (!empty($code)) {
                    $this->_printInfo("[INFO] Cache hit!");
                    $this->code = $code;
                    $this->_printInfo('[INFO] Execution time: ' . (getmicrotime() - $this->startTime) . ' seconds');
                    return true;
                }
            }
        } else {
            $this->cache->remove($this->cacheId, $this->getModuleName());
        }

        return false;
    }

    /**
     * Checks if the article modification date newer as the cache file date,
     * in case the cache file exists.
     *
     * @return bool
     * @throws cException
     * @throws cInvalidArgumentException
     */
    protected function validateCache(): bool
    {
        $cacheFilePath = $this->cache->getDestination($this->cacheId, $this->getModuleName());
        if (cFileHandler::exists($cacheFilePath)) {
            clearstatcache();
            $info = cFileHandler::info($cacheFilePath);
            $fileMtime = $info['mtime'];
            $articleMtime = strtotime($this->incLastModified);
            $this->_printInfo("[INFO] Cache file modified time: $fileMtime, article modified time: $articleMtime");
            if (is_numeric($fileMtime) && is_numeric($articleMtime) && $articleMtime <= $fileMtime) {
                return true;
            } else {
                $this->cache->remove($this->cacheId, $this->getModuleName());
                return false;
            }
        }
        return false;
    }

    /**
     * Does the real request depending on selected include mode.
     *
     * @param string $url
     * @return bool|mixed|string
     * @throws cException
     */
    protected function retrieveFromRequest(string $url)
    {
        $mode = $this->cmsIncludeMode;

        if (!$this->validateIncludeMode($mode)) {
            $mode = self::DEFAULT_INCLUDE_MODE;
        }
        if ($mode === self::SETTING_INCLUDE_MODE) {
            $mode = $this->getEffectiveSetting('include_mode');
            if (!$this->validateIncludeMode($mode, true)) {
                $mode = self::DEFAULT_INCLUDE_MODE;
            }
        }

        $this->_printInfo("[INFO] Include request mode: $mode");

        switch ($mode) {
            case 'curl':
                $request = new cHttpRequestCurl($url);
                return $request->getRequest();
            case 'snoopy':
                include_once($this->getModulePath() . 'lib/Snoopy/src/Snoopy.class.php');
                $snoopy = new Snoopy();
                $snoopy->fetch($url);
                return $snoopy->results;
            case 'file_get_contents':
                return file_get_contents($url);
            case 'fsockopen':
            default:
                $request = new cHttpRequestSocket($url);
                return $request->getRequest();
        }
    }

    /**
     * Simple debugger, prints preformatted text, if debugging is enabled.
     *
     * @param string $msg
     */
    protected function _printInfo(string $msg)
    {
        if ($this->debug) {
            echo "<pre>$msg</pre>";
        }
    }

}
