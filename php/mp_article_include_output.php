<?php
/**
 * Module mpArticleInclude output.
 *
 * Based on Article Include v 1.0 created by Willi Man, Andreas Lindner, 4fb, B. Behrens
 *
 * @package     Module
 * @subpackage  mpArticleInclude
 * @author      Murat Purc <murat@purc.de>
 * @copyright   Copyright (c) 2011-2019 Murat Purc (http://www.purc.de)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html - GNU General Public License, version 2
 */


// Includes
cInclude('module', 'includes/class.module.mparticleinclude.php');

// Module configuration
$aModuleConfiguration = array(
    'debug' => false,
    'name' => 'mpArticleInclude',
    'idmod' => $cCurrentModule,
    'container' => $cCurrentContainer,

    // Selected category id
    'cmsCatID' => (int) "CMS_VALUE[1]",

    // Selected article id
    'cmsArtID' => (int) "CMS_VALUE[2]",

    // Start and end marker
    'cmsStartMarker' => "CMS_VALUE[3]",
    'cmsEndMarker' => "CMS_VALUE[4]",
    'defaultStartMarker' => '<!--start:content-->',
    'defaultEndMarker' => '<!--end:content-->',

    'db' => cRegistry::getDb(),
    'cfg' => cRegistry::getConfig(),
    'client' => cRegistry::getClientId(),
    'lang' => cRegistry::getLanguageId(),
);
//##echo "<pre>" . print_r($aModuleConfiguration, true) . "</pre>";

// Create mpArticleInclude module instance
$oModule = new ModuleMpArticleInclude($aModuleConfiguration);

// Retrieve article
if (true === $oModule->includeArticle()) {
    echo $oModule->getCode();
} else {
    // Do your error handling here...
}

// Cleanup
unset($oModule);

?>