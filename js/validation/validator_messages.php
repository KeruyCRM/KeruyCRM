<script type="text/javascript">
    $.extend($.validator.messages, {
        required: '<?php echo addslashes(TEXT_ERROR_REQUIRED) ?>',
        number: '<?php echo addslashes(TEXT_ERROR_REQUIRED_NUMBER) ?>',
        extension: '<?php echo addslashes(TEXT_ERROR_FILE_EXTENSION) ?>',
        email: '<?php echo addslashes(TEXT_ERROR_REQUIRED_EMAIL) ?>',
        digits: '<?php echo addslashes(TEXT_ERROR_REQUIRED_DIGITS) ?>',
        url: '<?php echo addslashes(TEXT_ERROR_REQUIRED_URL) ?>',
        min: '<?php echo addslashes(TEXT_MIN_VALUE_WARNING) ?>',
        max: '<?php echo addslashes(TEXT_MAX_VALUE_WARNING) ?>',
    });

    jQuery.validator.addMethod("email", function (value, element) {
        return this.optional(element) || /^[a-zA-Zа-яА-Я0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Zа-яА-Я0-9](?:[a-zA-Zа-яА-Я0-9-]{0,61}[a-zA-Zа-яА-Я0-9])?(?:\.[a-zA-Zа-яА-Я0-9](?:[a-zA-Zа-яА-Я0-9-]{0,61}[a-zA-Zа-яА-Я0-9])?)*$/.test(value);
    }, "<?php echo addslashes(TEXT_ERROR_REQUIRED_EMAIL) ?>");

    jQuery.validator.addMethod('accept', function () {
        return true;
    });
</script>