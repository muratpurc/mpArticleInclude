<?php
/**
 * CONTENIDO module class for mpArticleInclude
 *
 * @package     CONTENIDO_Modules
 * @subpackage  mpArticleInclude
 * @author      Murat Purç <murat@purc.de>
 * @copyright   Copyright (c) 2013-2019 Murat Purç (http://www.purc.de)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html - GNU General Public License, version 2
 */

if (!defined('CON_FRAMEWORK')) {
    die('Illegal call');
}

/**
 * CONTENIDO module class for mpNivoSlider
 */
class ModuleMpArticleInclude {

    /**
     * Default start marker
     * @var string
     */
    const DEFAULT_START_MARKER = '<!--start:content-->';

    /**
     * Default end marker
     * @var string
     */
    const DEFAULT_END_MARKER = '<!--end:content-->';

    /**
     * To store extracted output code of included article
     * @var string
     */
    protected $_code = '';

    /**
     * Unique module id (module id + container)
     * @var  string
     */
    protected $_uid = '';

    /**
     * Module properties structure.
     * NOTE:
     * Only options defined here will be accepted within passed $options to the module constructor!
     * @var  array
     */
    protected $_properties = array(
        'debug' => false,
        'name' => 'mpArticleInclude',
        'idmod' => 0,
        'container' => 0,

        'cmsCatID' => '',
        'cmsArtID' => '',
        'cmsStartMarker' => '',
        'cmsEndMarker' => '',

        'db' => '',
        'cfg' => '',
        'client' => 0,
        'lang' => 0,

        'articleIsAvailable' => false,
        'incIdcatart' => 0,
        'incIdcat' => 0,
        'incIdart' => 0,
    );

    /**
     * Module translations
     * @var  array
     */
    protected $_i18n = array();

    /**
     * Constructor, sets some properties
     * @param  array  $options  Options array
     * @param  array  $translations  Assoziative translations list
     */
    public function __construct(array $options, array $translations = array()) {

        foreach ($options as $k => $v) {
            $this->$k = $v;
        }

        $this->_validate();

        $this->_i18n = $translations;
        $this->_uid = $this->idmod . '_' . $this->container;
        $this->_code = '';
    }

    /**
     * Main function to retrieve the article, runs some checks, like if article and category is
     * available and finally it requests the article.
     * @return  bool  Success state
     */
    public function includeArticle() {
        $this->_printInfo("idcat {$this->cmsCatID}, idart {$this->cmsArtID}");

        if ($this->cmsCatID < 0) {
            $this->_printInfo("No idcat!");
            return false;
        }

        if (false === $this->_checkArticle()) {
            $this->_printInfo("Article is not available!");
            return false;
        }

        if (false === $this->_checkCategory()) {
            $this->_printInfo("Category is not public or visible!");
            return false;
        }

        if (false === $this->_requestArticle()) {
            return false;
        }

        return true;
    }

    /**
     * Returns the extracted code (HTML output) from article
     * @return string
     */
    public function getCode() {
        return $this->_code;
    }

    /**
     * Magic getter, see PHP doc...
     */
    public function __get($name) {
        return (isset($this->_properties[$name])) ? $this->_properties[$name] : null;
    }

    /**
     * Magic setter, see PHP doc...
     */
    public function __set($name, $value) {
        if (isset($this->_properties[$name])) {
            $this->_properties[$name] = $value;
        }
    }

    /**
     * Magic method, see PHP doc...
     */
    public function __isset($name) {
        return (isset($this->_properties[$name]));
    }

    /**
     * Magic method, see PHP doc...
     */
    public function __unset($name) {
        if (isset($this->_properties[$name])) {
            unset($this->_properties[$name]);
        }
    }

    /**
     * Validates module configuration/data
     */
    protected function _validate() {
        // debug mode
        $this->debug = (bool) $this->debug;

        $this->name = (string) $this->name;
        $this->idmod = (int) $this->idmod;
        $this->container = (int) $this->container;
        $this->client = (int) $this->client;
        $this->lang = (int) $this->lang;

        // selected category id
        $this->cmsCatID = (int) $this->cmsCatID;

        // selected article id
        $this->cmsArtID = (int) $this->cmsArtID;

        // start and end marker
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

    /**
     * Returns the checked attribute sub string usable for checkboxes.
     * @param string $name Configuration item name
     * @return string
     */
    public function getCheckedAttribute($name) {
        if (isset($this->$name) && '' !== $this->$name) {
            return ' checked="checked"';
        } else {
            return '';
        }
    }

    /**
     * Returns the id attribute value by concatenating passed name with the module uid.
     * @param string $name
     * @return string
     */
    public function getIdValue($name) {
		return $name . '_' . $this->getUid();
    }

    /**
     * Returns the module uid (module id + container).
     * @return string
     */
	public function getUid() {
		return $this->_uid;
	}

    /**
     * Checks if article exists and is online
     * @return  bool
     */
    protected function _checkArticle() {
        $this->articleIsAvailable = false;

        // get idcat, idcatart, idart and lastmodified from the database
        $sql = "SELECT ca.idart, ca.idcat, ca.idcatart, al.lastmodified "
             . "FROM " . $this->cfg["tab"]["cat_art"] . " AS ca, " . $this->cfg["tab"]["art_lang"] . " AS al "
             . "WHERE ca.idart = al.idart AND al.online = 1 AND al.idlang = " . $this->lang . " AND ";
        if ($this->cmsArtID == 0) {
            // if only idcat specified, get latest article of category
            $sql .= "ca.idcat = " . $this->cmsCatID . " ORDER BY al.lastmodified DESC";
        } else {
            // article specified
            $sql .= "al.idart = " . $this->cmsArtID;
        }
        $this->_printInfo("SQL to check article $sql");

        $this->db->query($sql);
        if ($this->db->nextRecord()) {
            $this->articleIsAvailable = true;
            $this->incIdcatart = $this->db->f('idcatart');
            $this->incIdcat = $this->db->f('idcat');
            $this->incIdart = $this->db->f('idart');
        }
        $this->db->free();

        return $this->articleIsAvailable;
    }

    /**
     * Checks if category exists, is online and public
     * @return  bool
     */
    protected function _checkCategory() {
        // check if category is online or protected
        $oCatLang = new cApiCategoryLanguage();
        $oCatLang->loadByCategoryIdAndLanguageId($this->incIdcat, $this->lang);
        $this->_printInfo('$this->incIdcat: ' . print_r($this->incIdcat, true));
        $this->_printInfo('$this->lang: ' . print_r($this->lang, true));
        $this->_printInfo('$oCatLang->toArray(): ' . print_r($oCatLang->toArray(), true));
        $catIsPublic = (int) $oCatLang->get('public');
        $catIsVisible = (int) $oCatLang->get('visible');

        if ($catIsPublic && $catIsVisible) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Requests the article by using Snoopy
     * @return  bool
     */
    protected function _requestArticle() {
        // Get article output
        $moduleHandler = new cModuleHandler($this->idmod);
        include_once($moduleHandler->getModulePath() . 'lib/Snoopy.class.php');

        $url = cUri::getInstance()->build(array(
            'idart' => $this->incIdart, 'lang' => $this->lang
        ), true);

        $snoopy = new Snoopy();
        $snoopy->fetch($url);
        $this->_code = trim($snoopy->results);

        // Extract content from article code output
        if (!empty($this->_code)) {
            $cmsStartMarker = htmlspecialchars_decode($this->cmsStartMarker);
            $cmsEndMarker = htmlspecialchars_decode($this->cmsEndMarker);

            $startPos = strpos($this->_code, $cmsStartMarker);
            $endPos   = strpos($this->_code, $cmsEndMarker);

            if ($startPos !== false || $endPos !== false) {
                $diffLen = $endPos - $startPos + strlen($cmsEndMarker);
                $this->_code = substr($this->_code, $startPos, $diffLen);
                return true;
            } else {
                $msg = "ERROR in module " . $this->name . "<pre>Couldn't detect marker {$this->cmsStartMarker} and/or {$this->cmsEndMarker}!\n"
                   . "idcat {$this->cmsCatID}, idart {$this->cmsArtID}, idlang {$this->lang}, idclient {$this->client}";
                $this->_printInfo($msg);
                return false;
            }
        } else {
            $msg = "ERROR in module " . $this->name . "<pre>Can't get article to include!\n" 
               . "idcat {$this->cmsCatID}, idart {$this->cmsArtID}, idlang {$this->lang}, idclient {$this->client}\n";
            $this->_printInfo($msg);
            return false;
        }
    }

    /**
     * Simple debugger, print preformatted text, if debugging is enabled
     * @param  $msg
     */
    protected function _printInfo($msg) {
        if ($this->debug) {
            echo "<pre>{$msg}</pre>";
        }
    }
}
