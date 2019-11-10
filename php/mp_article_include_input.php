?><?php
/**
 * Module mpArticleInclude input.
 *
 * Based on Article Include v 1.0 created by Willi Man, Andreas Lindner, 4fb, B. Behrens
 *
 * @package     Module
 * @subpackage  mpArticleInclude
 * @author      Murat Purc <murat@purc.de>
 * @copyright   Copyright (c) 2011-2019 Murat Purc (http://www.purc.de)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html - GNU General Public License, version 2
 */


################################################################################
########## Initialization/Settings

// Includes
cInclude('module', 'includes/class.module.mparticleinclude.php');

$client = cRegistry::getClientId();
$lang = cRegistry::getLanguageId();

// Module configuration
$aModuleConfiguration = array(
    'debug' => false,
    'name' => 'mpArticleInclude',
    'idmod' => $cCurrentModule,
    'container' => $cCurrentContainer,

    // Selected category id
    'cmsCatID' => (int) "CMS_VALUE[1]",

    // Delected article id
    'cmsArtID' => (int) "CMS_VALUE[2]",

    // Start and end marker
    'cmsStartMarker' => "CMS_VALUE[3]",
    'cmsEndMarker' => "CMS_VALUE[4]",
    'defaultStartMarker' => '<!--start:content-->',
    'defaultEndMarker' => '<!--end:content-->',

    'db' => cRegistry::getDb(),
    'cfg' => cRegistry::getConfig(),
    'client' => $client,
    'lang' => $lang,
);
//##echo "<pre>" . print_r($aModuleConfiguration, true) . "</pre>";

// Create mpNivoSlider module instance
$oModule = new ModuleMpArticleInclude($aModuleConfiguration);


################################################################################
########## Output

?>

<?php if ($oModule->debug) : ?>
    <pre>idcat <?php echo $oModule->cmsCatID ?>, idart <?php echo $oModule->cmsArtID ?>, idclient <?php echo $client ?>, idlang <?php echo $lang ?></pre>
<?php endif; ?>

<table cellpadding="0" cellspacing="0" border="0">
<tr>
    <td class="text_medium" id="<?php echo $oModule->getIdValue('cmsCatID') ?>">
        <?php echo mi18n("SELECT_CATEGORY")?>:<br />
        <?php echo buildCategorySelect("CMS_VAR[1]", $oModule->cmsCatID) ?>
        <br />
    </td>
</tr>
<?php if ($oModule->cmsCatID > 0) : ?>
<tr>
    <td class="text_medium">
        <?php echo mi18n("SELECT_ARTICLE")?>:<br />
        <?php echo buildArticleSelect("CMS_VAR[2]", $oModule->cmsCatID, $oModule->cmsArtID); ?>
        <br />
        <small><?php echo mi18n("SELECT_ARTICLE_INFO_TEXT")?></small>
    </td>
</tr>
<?php endif; ?>
<tr><td class="text_medium"><?php echo mi18n("MARKER")?>: </td></tr>
<tr>
    <td class="text_medium">
        <table cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td class="text_medium">
                <label for="<?php echo $oModule->getIdValue('cmsStartMarker') ?>"><?php echo mi18n("START_MARKER")?></label>
            </td>
            <td class="text_medium">
                <input type="text" name="CMS_VAR[3]" style="width:250px" value="<?php echo $oModule->cmsStartMarker ?>" id="<?php echo $oModule->getIdValue('cmsStartMarker') ?>" />
                (<?php echo mi18n("START_MARKER_EXAMPLE") ?>)
            </td>
        </tr>
        <tr>
            <td class="text_medium">
                <label for="<?php echo $oModule->getIdValue('cmsEndMarker') ?>"><?php echo mi18n("END_MARKER")?></label>
            </td>
            <td class="text_medium">
                <input type="text" name="CMS_VAR[4]" style="width:250px" value="<?php echo $oModule->cmsEndMarker ?>" id="<?php echo $oModule->getIdValue('cmsEndMarker') ?>" />
                (<?php echo mi18n("END_MARKER_EXAMPLE") ?>)
            </td>
        </tr>
        </table>
        <small><?php echo mi18n("MARKER_INFO_TEXT")?></small>
    </td>
</tr>
</table>
<script type="text/javascript">
(function($) {
    $(function() {
        // Register event handler for category select change
        $('#<?php echo $oModule->getIdValue('cmsCatID') ?> select').change(function(e) {
            $('form[name="tplcfgform"]').submit();
        });
    });
})(jQuery);
</script>

<?php

################################################################################
########## Cleanup

unset($oModule);
