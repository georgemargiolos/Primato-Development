{* FontAwesome Icon Configuration *}
<div class="control-group">
    <label class="control-label" for="elm_payment_fa_icon_class_{$id}">
        {__("fontawesome_icon_class")}:
    </label>
    <div class="controls">
        <input id="elm_payment_fa_icon_class_{$id}"
            type="text"
            name="payment_data[fa_icon_class]"
            value="{$payment.fa_icon_class}"
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
            value="{$payment.fa_icon_style}"
            class="input-large"
            placeholder="e.g., color: #ff0000; font-size: 2.5em;"
        >
        <p class="muted description">
            {__("tt_payment_fontawesome_custom_style")}
        </p>
    </div>
</div>
