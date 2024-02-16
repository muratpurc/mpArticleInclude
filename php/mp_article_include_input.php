?><?php

/**
 * Module mp_article_include input.
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

        'i18n' => [
            'VAL_PLEASE_CHOOSE' => mi18n("VAL_PLEASE_CHOOSE"),
            'VAL_FROM_SETTING' => mi18n("VAL_FROM_SETTING"),
            'VAL_CURL' => mi18n("VAL_CURL"),
            'VAL_FSOCKOPEN' => mi18n("VAL_FSOCKOPEN"),
            'VAL_FILE_GET_CONTENTS' => mi18n("VAL_FILE_GET_CONTENTS"),
            'VAL_SNOOPY' => mi18n("VAL_SNOOPY"),
            'VAL_FROM_SETTINGS_%S' => mi18n("VAL_FROM_SETTINGS_%S"),
            'LBL_NO_SETTING' => mi18n("LBL_NO_SETTING"),
        ],
    ]);

    ############################################################################
    ########## Output

    $table = $module->getGuiTable(['style' => 'width:100%;']);

    // Debug row
    if ($module->debug) {
        $table->addContrastRow([
            "<pre>idcat $module->cmsCatID, idart $module->cmsArtID, idclient $module->clientId, idlang $module->languageId</pre>"
        ], [], [['colspan' => 2]]);
    }

    // Select category row
    $table->addRow(
        [mi18n("SELECT_CATEGORY"), buildCategorySelect("CMS_VAR[1]", $module->cmsCatID)],
        [],
        [[], ['id' => $module->getIdValue('cmsCatID')]]
    );

    // Select article row
    $infoBox = new cGuiBackendHelpbox(mi18n("SELECT_ARTICLE_INFO_TEXT"));
    if ($module->cmsCatID > 0) {
        $select = buildArticleSelect("CMS_VAR[2]", $module->cmsCatID, $module->cmsArtID);
    } else {
        $select = (new cHTMLSelectElement("CMS_VAR[2]"))->setDisabled(true)->autoFill(['' => mi18n("VAL_PLEASE_CHOOSE")])->render();
    }
    $table->addRow([
        mi18n("SELECT_ARTICLE"),
        $select . $infoBox->render()
    ]);

    // Select include mode row
    $infoBox = new cGuiBackendHelpbox(mi18n("INCLUDE_MODE_INFO_TEXT"));
    $table->addRow([
        mi18n("INCLUDE_MODE"),
        $module->renderIncludeModeSelect("CMS_VAR[5]", $module->cmsIncludeMode)
            . $infoBox->render()
    ]);

    // Marker
    $table->addContrastRow(
        [mi18n("MARKER")], [], [['colspan' => 2]]
    );

    $markerTable = $module->getGuiTable(['style' => 'width:100%;']);

    // Start marker row
    $infoBox = new cGuiBackendHelpbox('(' . mi18n("START_MARKER_EXAMPLE") . ')');
    $cmsToken = $module->getCmsToken(3);
    $textBox = new cHTMLTextbox($cmsToken->getVar(), $cmsToken->getValue());
    $textBox->setID($module->getIdValue('cmsStartMarker'))->setStyle('width:250px');
    $markerTable->addRow([
        mi18n("START_MARKER"),
        $textBox->render() . $infoBox->render()
    ]);

    // End marker row
    $infoBox = new cGuiBackendHelpbox('(' . mi18n("END_MARKER_EXAMPLE") . ')');
    $cmsToken = $module->getCmsToken(4);
    $textBox = new cHTMLTextbox($cmsToken->getVar(), $cmsToken->getValue());
    $textBox->setID($module->getIdValue('cmsEndMarker'))->setStyle('width:250px');
    $markerTable->addRow([
        mi18n("END_MARKER"),
        $textBox->render() . $infoBox->render()
    ]);

    $table->addRow(
        [$markerTable->render() . '<p>' . mi18n("MARKER_INFO_TEXT") . '</p>'],
        [],
        [['colspan' => 2]]
    );

    echo $table->render();

    echo $module->renderModuleInputJavaScript();

})();
