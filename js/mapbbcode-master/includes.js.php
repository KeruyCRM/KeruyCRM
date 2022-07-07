<script src="<?= \K::$fw->DOMAIN ?>js/mapbbcode-master/dist/lib/leaflet.js"></script>
<script src="<?= \K::$fw->DOMAIN ?>js/mapbbcode-master/dist/lib/leaflet.draw.js"></script>
<script src="<?= \K::$fw->DOMAIN ?>js/mapbbcode-master/src/MapBBCode.js"></script>
<script src="<?= \K::$fw->DOMAIN ?>js/mapbbcode-master/src/MapBBCodeUI.js"></script>
<script src="<?= \K::$fw->DOMAIN ?>js/mapbbcode-master/src/MapBBCodeUI.Editor.js"></script>
<script src="<?= \K::$fw->DOMAIN ?>js/mapbbcode-master/src/images/EditorSprites.js"></script>
<script src="<?= \K::$fw->DOMAIN ?>js/mapbbcode-master/src/controls/FunctionButton.js"></script>
<script src="<?= \K::$fw->DOMAIN ?>js/mapbbcode-master/src/controls/LetterIcon.js"></script>
<script src="<?= \K::$fw->DOMAIN ?>js/mapbbcode-master/src/controls/PopupIcon.js"></script>
<script src="<?= \K::$fw->DOMAIN ?>js/mapbbcode-master/src/controls/Leaflet.Search.js"></script>
<script src="<?= \K::$fw->DOMAIN ?>js/mapbbcode-master/src/handlers/Handler.Text.js"></script>
<script src="<?= \K::$fw->DOMAIN ?>js/mapbbcode-master/src/handlers/Handler.Color.js"></script>
<script src="<?= \K::$fw->DOMAIN ?>js/mapbbcode-master/src/handlers/Handler.Length.js"></script>


<?php if(is_file('js/mapbbcode-master/dist/lang/' . \K::$fw->TEXT_APP_LANGUAGE_SHORT_CODE . '.js')){ ?>
<script
    src="<?= \K::$fw->DOMAIN ?>js/mapbbcode-master/dist/lang/<?= \K::$fw->TEXT_APP_LANGUAGE_SHORT_CODE ?>.js"></script>
<?php }else{ ?>
<script src="<?= \K::$fw->DOMAIN ?>js/mapbbcode-master/dist/lang/en.js"></script>
<?php } ?>
