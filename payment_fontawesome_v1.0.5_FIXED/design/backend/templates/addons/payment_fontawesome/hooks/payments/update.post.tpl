{* FontAwesome Icon Configuration - v1.0.5 FIXED *}
{* This hook adds FA icon fields to the payment method edit form *}

<div class="control-group">
    <label class="control-label" for="elm_payment_fa_icon_class_{$id}">
        {__("fontawesome_icon_class")}:
    </label>
    <div class="controls">
        <input id="elm_payment_fa_icon_class_{$id}"
            type="text"
            name="payment_data[fa_icon_class]"
            value="{$payment.fa_icon_class|default:''}"
            class="input-large"
            placeholder="e.g., fab fa-cc-visa, fas fa-credit-card"
        >
        <p class="muted description">
            {__("tt_payment_fontawesome_icon_class")}
        </p>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="elm_payment_fa_icon_style_{$id}">
        {__("fontawesome_custom_style")}:
    </label>
    <div class="controls">
        <input id="elm_payment_fa_icon_style_{$id}"
            type="text"
            name="payment_data[fa_icon_style]"
            value="{$payment.fa_icon_style|default:''}"
            class="input-large"
            placeholder="e.g., color: #ff0000; font-size: 2.5em;"
        >
        <p class="muted description">
            {__("tt_payment_fontawesome_custom_style")}
        </p>
    </div>
</div>

{* Preview section - shows what the icon will look like *}
<div class="control-group">
    <label class="control-label">{__("fontawesome_preview")}:</label>
    <div class="controls">
        <div id="fa_icon_preview_{$id}" style="padding: 10px; background: #f5f5f5; border-radius: 4px; display: inline-block; min-width: 60px; text-align: center;">
            {if $payment.fa_icon_class}
                <i class="{$payment.fa_icon_class}" style="{$payment.fa_icon_style|default:'font-size: 2em;'}"></i>
            {else}
                <span class="muted">No icon configured</span>
            {/if}
        </div>
    </div>
</div>

{* Live preview JavaScript *}
<script>
(function() {
    var iconInput = document.getElementById('elm_payment_fa_icon_class_{$id}');
    var styleInput = document.getElementById('elm_payment_fa_icon_style_{$id}');
    var preview = document.getElementById('fa_icon_preview_{$id}');
    
    function updatePreview() {
        var iconClass = iconInput ? iconInput.value.trim() : '';
        var iconStyle = styleInput ? styleInput.value.trim() : 'font-size: 2em;';
        
        if (iconClass) {
            preview.innerHTML = '<i class="' + iconClass + '" style="' + iconStyle + '"></i>';
        } else {
            preview.innerHTML = '<span class="muted">No icon configured</span>';
        }
    }
    
    if (iconInput) iconInput.addEventListener('input', updatePreview);
    if (styleInput) styleInput.addEventListener('input', updatePreview);
})();
</script>

