<?php

/**
 * Module mp_article_include output.
 *
 * Based on Article Include v 1.0 created by Willi Man, Andreas Lindner, 4fb, B. Behrens
 *
 * @package     Module
 * @subpackage  mp_article_include
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

(function() {

    ############################################################################
    ########## Initialization/Settings

    if (!class_exists(\CONTENIDO\Plugin\MpDevTools\Module\AbstractBase::class)) {
        new cException('This module requires the plugin "Mp Dev Tools", please download, install and activate it!');
    }

    // Includes
    if (!class_exists(MpArticleIncludeModule::class)) {
        cInclude('module', 'includes/class.mp.article.include.module.php');
    }

    // Create mp_article_include module instance
    $module = new MpArticleIncludeModule([
        'debug' => false,

        // Selected category id
        'cmsCatID' => "CMS_VALUE[1]",

        // Selected article id
        'cmsArtID' => "CMS_VALUE[2]",

        // Start and end marker
        'cmsStartMarker' => "CMS_VALUE[3]",
        'cmsEndMarker' => "CMS_VALUE[4]",
        'defaultStartMarker' => '<!--start:content-->',
        'defaultEndMarker' => '<!--end:content-->',

        // Include mode
        'cmsIncludeMode' => "CMS_VALUE[5]",

        'db' => cRegistry::getDb(),
    ]);

    ############################################################################
    ########## Output

    // Retrieve article
    if (true === $module->includeArticle()) {
        echo $module->getCode();
    } else {
        // Do your error handling here...
    }

})();

?>