?><?php
/**
 * Module mpArticle_Include input
 * @package     Module
 * @subpackage  mpArticle_Include
 * @version     1.3.1
 * @author      Willi Man
 * @author      Murat Purc <murat@purc.de>
 * @copyright   four for business AG
 * @link        http://www.4fb.de
 * $Id: mparticle_include_input.php 13 2013-09-18 22:32:56Z murat $
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

$modContext->id = uniqid();

// category select name
$modContext->catSelName = "CMS_VAR[1]";

// article select name
$modContext->artSelName = "CMS_VAR[2]";

// selected category id
$modContext->cmsCatID = (int) "CMS_VALUE[1]";

// selected article id
$modContext->cmsArtID = (int) "CMS_VALUE[2]";

// start and end marker
$modContext->cmsStartMarker = ("CMS_VALUE[3]" == '') ? '<!--start:content-->' : "CMS_VALUE[3]";
$modContext->cmsEndMarker   = ("CMS_VALUE[4]" == '') ? '<!--end:content-->' : "CMS_VALUE[4]";

$modContext->catSelectWrapId = 'cat_select_wrap_' . $modContext->id;
$modContext->startMarkerId = 'var_startmarker_' . $modContext->id;
$modContext->endMarkerId   = 'var_endmarker_' . $modContext->id;

################################################################################
########## Output

?>

<?php if ($modContext->debug) : ?>
    <pre>idcat <?php echo $modContext->cmsCatID ?>, idart <?php echo $modContext->cmsArtID ?>, idclient <?php echo $client ?>, idlang <?php echo $lang ?></pre>
<?php endif; ?>

<table cellpadding="0" cellspacing="0" border="0">
<tr>
    <td class="text_medium" id="<?php echo $modContext->catSelectWrapId ?>">
        <?php echo mi18n("SELECT_CATEGORY")?>:<br />
        <?php echo buildCategorySelect($modContext->catSelName, $modContext->cmsCatID) ?>
        <br />
    </td>
</tr>
<?php if ($modContext->cmsCatID > 0) : ?>
<tr>
    <td class="text_medium">
        <?php echo mi18n("SELECT_ARTICLE")?>:<br />
        <?php echo buildArticleSelect($modContext->artSelName, $modContext->cmsCatID, $modContext->cmsArtID); ?>
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
                <label for="<?php echo $modContext->startMarkerId ?>"><?php echo mi18n("START_MARKER")?></label>
            </td>
            <td class="text_medium">
                <input type="text" name="CMS_VAR[3]" style="width:250px" value="<?php echo conHtmlSpecialChars($modContext->cmsStartMarker) ?>" id="<?php echo $modContext->startMarkerId ?>" />
                (<?php echo mi18n("START_MARKER_EXAMPLE") ?>)
            </td>
        </tr>
        <tr>
            <td class="text_medium">
                <label for="<?php echo $modContext->endMarkerId ?>"><?php echo mi18n("END_MARKER")?></label>
            </td>
            <td class="text_medium">
                <input type="text" name="CMS_VAR[4]" style="width:250px" value="<?php echo conHtmlSpecialChars($modContext->cmsEndMarker) ?>" id="<?php echo $modContext->endMarkerId ?>" />
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
    $(document).ready(function() {
        $('#<?php echo $modContext->catSelectWrapId ?> select').change(function(e) {
            document.forms["tplcfgform"].submit();
        });
    });
})(jQuery);
</script>

<?php

################################################################################
########## Cleanup

unset($modContext);
