{*
* NOTICE OF LICENSE
*
* This file is create by Afone
* For the installation of the software in your application
* You accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code
*
*  @author    Afone
*  @copyright 2015-2017 ComNpay
*  @license   comnpay.com
*}
{extends "$layout"}
{block name="content"}
<div id="comnpay_redirect">
	{if $isCartEmpty == true}
        <p style="color:red; text-align:center;">
            {l s='Forbidden ! You couldn\'t view this page. Please make an order. ' mod='comnpay'}
        </p>
    {else}
        <h2 style="text-align:center;">{l s='Redirect to the payment page' mod='comnpay'}</h2>
        <p style="text-align:center;">
            <a href="javascript:$('#comnpay_form').submit();" id="btnForForm">{l s='You will now be redirected to comNpay. If this does not happen automatically, please press here.' mod='comnpay'}</a>
        </p>
        <form name="comnpay_form" id="comnpay_form" method="post" action="{$form_action|escape:'htmlall':'UTF-8'}">
        	<input type="hidden" name="montant" value="{$montant|escape:'htmlall':'UTF-8'}"/>
            <input type="hidden" name="idTPE" value="{$idTPE|escape:'htmlall':'UTF-8'}"/>
            <input type="hidden" name="idTransaction" value="{$idTransaction|escape:'htmlall':'UTF-8'}"/>
            <input type="hidden" name="devise" value="{$devise|escape:'htmlall':'UTF-8'}"/>
            <input type="hidden" name="lang" value="{$lang|escape:'htmlall':'UTF-8'}"/>
            <input type="hidden" name="nom_produit" value=""/>
            <input type="hidden" name="source" value="{$source|escape:'htmlall':'UTF-8'}"/>
            <input type="hidden" name="urlRetourOK" value="{$urlRetourOK|escape:'htmlall':'UTF-8'}"/>
            <input type="hidden" name="urlRetourNOK" value="{$urlRetourNOK|escape:'htmlall':'UTF-8'}"/>
            <input type="hidden" name="urlIPN" value="{$urlIPN|escape:'htmlall':'UTF-8'}"/>
            <input type="hidden" name="extension" value="{$extension|escape:'htmlall':'UTF-8'}"/>
            <input type="hidden" name="data" value="{$data|escape:'htmlall':'UTF-8'}"/>
            <input type="hidden" name="typeTr" value="{$typeTr|escape:'htmlall':'UTF-8'}"/>
            <input type="hidden" name="porteur" value="{$porteur|escape:'htmlall':'UTF-8'}"/>
            <input type="hidden" name="codeTemplate" value="{$codeTemplate|escape:'htmlall':'UTF-8'}"/>
            <input type="hidden" name="sec" value="{$sec|escape:'htmlall':'UTF-8'}"/>
        	<input type="submit" style="visibility:hidden; display:none"/>
        </form>
        <script type="text/javascript">
            document.getElementById('comnpay_form').submit();
			var form = document.getElementById("comnpay_form");
			document.getElementById("btnForForm").addEventListener("click", function () {
			  form.submit();
			});
        </script>
    {/if}
</div>
{/block}