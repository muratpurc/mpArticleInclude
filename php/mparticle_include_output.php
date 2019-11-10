<?php
/**
 * Module mpArticle_Include output
 * @package     Module
 * @subpackage  mpArticle_Include
 * @version     1.3.1
 * @author      Willi Man
 * @author      Murat Purc <murat@purc.de>
 * @copyright   four for business AG
 * @link        http://www.4fb.de
 * $Id: mparticle_include_output.php 13 2013-09-18 22:32:56Z murat $
 */


################################################################################
########## Initialization/Settings

$db = cRegistry::getDb();
$cfg = cRegistry::getConfig();
$client = cRegistry::getClientId();
$lang = cRegistry::getLanguageId();

// module context object
$modContext = new stdClass();

// debug mode
$modContext->debug = false;

// selected category id
$modContext->cmsCatID = (int) "CMS_VALUE[1]";

// selected article id
$modContext->cmsArtID = (int) "CMS_VALUE[2]";

// start and end marker
$modContext->cmsStartMarker = ("CMS_VALUE[3]" == '') ? '<!--start:content-->' : "CMS_VALUE[3]";
$modContext->cmsEndMarker   = ("CMS_VALUE[4]" == '') ? '<!--end:content-->' : "CMS_VALUE[4]";


################################################################################
########## Output

if ($modContext->debug) {
    echo "<pre>idcat {$modContext->cmsCatID}, idart {$modContext->cmsArtID}</pre>";
}

if ($modContext->cmsCatID >= 0 && $modContext->cmsArtID >= 0) {

    $modContext->articleIsAvailable = false;

    // get idcat, idcatart, idart and lastmodified from the database
    $sql = "SELECT ca.idart, ca.idcat, ca.idcatart, al.lastmodified "
         . "FROM " . $cfg["tab"]["cat_art"] . " AS ca, " . $cfg["tab"]["art_lang"] . " AS al "
         . "WHERE ca.idart = al.idart AND al.online = 1 AND al.idlang = " . $lang . " AND ";
    if ($modContext->cmsArtID == 0) {
        // if only idcat specified, get latest article of category
        $sql .= "ca.idcat = " . $modContext->cmsCatID . " ORDER BY al.lastmodified DESC";
    } else {
        // article specified
        $sql .= "al.idart = " . $modContext->cmsArtID;
    }
    if ($modContext->debug) {
        echo "<pre>sql $sql</pre>";
    }

    $db->query($sql);
    if ($db->next_record()) {
        $modContext->articleIsAvailable = true;
        $modContext->catArtID = $db->f('idcatart');
        $modContext->catID = $db->f('idcat');
        $modContext->artID = $db->f('idart');
    }
    $db->free();

    // check if category is online or protected
    $oCatLang = new cApiCategoryLanguage();
    $oCatLang->loadByCategoryIdAndLanguageId($modContext->catID, $lang);
    $modContext->public = (int) $oCatLang->get('public');
    $modContext->visible = (int) $oCatLang->get('visible');

    // if the article is online and the according category is not protected and visible, include the article
    if ($modContext->articleIsAvailable && $modContext->public == 1 && $modContext->visible == 1) {

        $modContext->code = '';

        // get article output
        $modContext->oModule = new cApiModule($cCurrentModule);
        cInclude('frontend', 'data/modules/' . $modContext->oModule->get('alias') . '/vendor/Snoopy.class.php');

        $modContext->url = cUri::getInstance()->build(array(
            'idart' => $modContext->artID, 'lang' => $lang
        ), true);

        $modContext->oSnoopy = new Snoopy();
        $modContext->oSnoopy->fetch($modContext->url);
        $modContext->code = trim($modContext->oSnoopy->results);

        // article code output
        if (!empty($modContext->code)) {
            $modContext->startPos = strpos($modContext->code, $modContext->cmsStartMarker);
            $modContext->endPos   = strpos($modContext->code, $modContext->cmsEndMarker);

            if ($modContext->startPos !== false || $modContext->endPos !== false) {
                $modContext->diffLen = $modContext->endPos - $modContext->startPos + strlen($modContext->cmsEndMarker);
                $modContext->code = substr($modContext->code, $modContext->startPos, $modContext->diffLen);
                echo $modContext->code;
            } else {
                echo "<!-- ERROR in module " . $modContext->oModule->get('alias') . "<pre>Coudn't detect marker {$modContext->cmsStartMarker} and/or {$modContext->cmsEndMarker}!<br>"
                   . "idcat {$modContext->cmsCatID}, idart {$modContext->cmsArtID}, idlang {$lang}, idclient {$client}</pre>-->";
            }
        } else {
            echo "<!-- ERROR in module " . $modContext->oModule->get('alias') . "<pre>Can't get article to include!<br>" 
               . "idcat {$modContext->cmsCatID}, idart {$modContext->cmsArtID}, idlang {$lang}, idclient {$client}</pre>-->";
        }
    }
}


################################################################################
########## Cleanup

unset($modContext);

?>