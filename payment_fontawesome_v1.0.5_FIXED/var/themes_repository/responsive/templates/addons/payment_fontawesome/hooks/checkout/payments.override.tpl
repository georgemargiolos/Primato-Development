{*
 * Payment FontAwesome Add-on
 * Override template for checkout payments
 * Version: 1.0.5 FIXED
 *
 * This template overrides the default payment methods display at checkout
 * to show FontAwesome icons when configured.
 *}

<div class="litecheckout__group">
    {hook name="checkout:payments"}
    {if $cart.payment_id}
    {foreach $payment_methods as $payment}
        <div class="litecheckout__shipping-method litecheckout__field litecheckout__field--xsmall{if $payment.payment_id == $cart.payment_id} litecheckout__shipping-method--active{/if}">

            <input type="radio"
                   name="selected_payment_method"
                   id="radio_{$payment.payment_id}"
                   data-ca-target-form="litecheckout_payments_form"
                   data-ca-url="checkout.checkout"
                   data-ca-result-ids="litecheckout_final_section,litecheckout_step_payment,shipping_rates_list,litecheckout_terms,checkout*"
                   class="litecheckout__shipping-method__radio cm-select-payment hidden"
                   value="{$payment.payment_id}"
                   {if $payment.payment_id == $cart.payment_id}checked{/if}
            />

            <label id="payments_{$payment.payment_id}"
                   class="litecheckout__shipping-method__wrapper js-litecheckout-toggle"
                   for="radio_{$payment.payment_id}"
                   data-ca-toggling="payments_form_wrapper_{$payment.payment_id}"
                   data-ca-hide-all-in=".litecheckout__payment-methods"
            >
                {* FIX: Use isset() check and proper default handling *}
                {if !empty($payment.fa_icon_class)}
                    <div class="litecheckout__shipping-method__logo">
                        <i class="{$payment.fa_icon_class|escape:'html'} litecheckout__shipping-method__logo-icon" 
                           style="{if !empty($payment.fa_icon_style)}{$payment.fa_icon_style|escape:'html'}{else}font-size: 2em;{/if}"
                           aria-hidden="true"></i>
                    </div>
                {elseif $payment.image}
                    <div class="litecheckout__shipping-method__logo">
                        {include file="common/image.tpl" obj_id=$payment.payment_id images=$payment.image class="litecheckout__shipping-method__logo-image"}
                    </div>
                {/if}
                <p class="litecheckout__shipping-method__title">{$payment.payment}</p>
                {if $payment.description}
                    <p class="litecheckout__shipping-method__delivery-time">{$payment.description nofilter}</p>
                {/if}
            </label>

        </div>
    {/foreach}
    {else}
        <div class="litecheckout__item">
            <p>
                {__("text_no_payments_required")}
            </p>
        </div>
    {/if}
    {/hook}
</div>

