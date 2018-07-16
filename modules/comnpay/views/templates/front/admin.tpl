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
<div id="config_comnpaydirect" style="max-width:900px; margin-left:auto; margin-right:auto;">
	{if $display_msg_information == '1'}
    <div class="panel">
    	<div class="alert_comnpay_admin{$msg_information_class|escape:'htmlall':'UTF-8'}">
    		{$msg_information|escape:'htmlall':'UTF-8'}
        </div>
    </div>
    {/if}
    {if $display_msg_confirmation == '1'}
    <div class="panel">
    	<div class="alert_comnpay_admin{$msg_confirmation_class|escape:'htmlall':'UTF-8'}">
    		{$msg_confirmation|escape:'htmlall':'UTF-8'}
        </div>
    </div>
    {/if}
    {if $display_msg_souscription == '1'}
    <div class="panel">
    	<div class="alert_comnpay_admin{$msg_souscription_class|escape:'htmlall':'UTF-8'}">
    		{$msg_souscription|escape:'htmlall':'UTF-8'}
        </div>
    </div>
    {/if}
    <div class="panel">
        <div id="pspconfig_comnpay-header" class="row">
            <img src="{$image_header|escape:'htmlall':'UTF-8'}" width="845" style="margin:0 auto;" />
        </div>
        <hr>
        <!-- DEBUT CONTENU MARKETING -->
        <div id="pspconfig_comnpay-content">
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <p class="text-center">
                        <a aria-controls="pspconfig_comnpay-marketing-content" aria-expanded="false" href="#pspconfig_comnpay-marketing-content" data-toggle="collapse" class="btn btn-primary collapsed bgColorOrangeComNpay borderColorOrangeComNpay">
                            {l s='More information' mod='comnpay'}
                        </a>
                         &nbsp; &nbsp; 
                        <a href="#tabPanelAdminComNpay" class="btn btn-primary" onclick="document.getElementById('config_comnpay_step1_tabHeader').click();">
                            {l s='Suscribe' mod='comnpay'}
                        </a>
                    </p>
                    <div id="pspconfig_comnpay-marketing-content" class="collapse" style="height: 0px;">
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <img src="{$image_header_bottom|escape:'htmlall':'UTF-8'}" width="845" style="margin:0 auto;" />
                                <p>&nbsp;</p>
                                <h4 class="colorOrangeComNpay">{l s='MORE THAN AN ONLINE SOLUTION PAYMENT SOLUTION' mod='comnpay'}</h4>
                                <p>{l s='ComNpay offers each e-trader the ability to accept online payments through simple and comprehensive solutions.' mod='comnpay'}</p>
                                <ul>
                                    <li>{l s='Accept payments in BC, Visa and MasterCard.rehensive solutions.' mod='comnpay'}</li>
                                    <li>{l s='The VAD contract is included. No need to open one with your bank.' mod='comnpay'}</li>
                                    <li>{l s='Customize the payment page to suit your needs.' mod='comnpay'}</li>
                                    <li>{l s='Have effective tools to fight online fraud.' mod='comnpay'}</li>
                                    <li>{l s='Set up and adapt 3D Secure to your business to reduce dropout rate.' mod='comnpay'}</li>
                                    <li>{l s='Boost your sales thanks to the payment in 3 times without expenses.' mod='comnpay'}</li>
                                    <li>{l s='Anticipate your development with mobility and cross-channel payment.' mod='comnpay'}</li>
                                    <li>{l s='Cash American Express cards if you have an affiliation agreement.' mod='comnpay'}</li>
                                </ul>
                                <h4 class="colorOrangeComNpay">{l s='PRESTASHOP SPECIAL OFFER' mod='comnpay'}</h4>
                                <p>{l s='ComNpay offers a simple solution with advantageous pricing since you only pay for transactions made without a monthly subscription and without any conditions on the amount of the transaction or the volume of business:' mod='comnpay'}</p>
                                <ul>
                                    <li><strong>{l s='French Cards: 0.90% + € 0.20 per transaction' mod='comnpay'}</strong></li>
                                    <li><strong>{l s='Foreign Cards: 2.30% + € 0.20 per transaction' mod='comnpay'}</strong></li>
                                    <li>{l s='Offer valid for new customers who do not have an active account Afone Payment' mod='comnpay'}</li>
                                </ul>
                                <h4 class="colorOrangeComNpay">{l s='STEPS TO FOLLOW TO OPEN A COMNPAY ACCOUNT' mod='comnpay'}</h4>
                                <ol>
                                    <li>{l s='Register for ComNpay by completing the subscription form available below.' mod='comnpay'}</li>
                                    <li>{l s='Our Customer Relations call you under D + 2 to complete your registration.' mod='comnpay'}</li>
                                    <li>{l s='Start cash on your e-commerce website as soon as your ComNpay account is validated.' mod='comnpay'}</li>
                                </ol>
                                <h4 class="colorOrangeComNpay">{l s='CONTACT US' mod='comnpay'}</h4>
                                <p>{l s='Do you have any questions about the pricing, functionality or integration of our payment solution? We are available to answer your requests:' mod='comnpay'}<a href="https://addons.prestashop.com/fr/contactez-nous?id_product=25846" title="Contacter ComNpay">{l s='Contact ComNpay' mod='comnpay'}</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- FIN CONTENU MARKETING -->
    <p id="tabPanelAdminComNpay">&nbsp;</p>
    <!-- DEBUT CONTENU CONFIGURATION -->
    <div role="tabpanel">
        <ul role="tablist" class="nav nav-tabs">
            <li class="{$activeTabList_1|escape:'htmlall':'UTF-8'}" role="presentation">
                <a data-toggle="tab" role="tab" id="config_comnpay_step1_tabHeader" aria-controls="config_comnpay_step1" href="#config_comnpay_step1" class="colorOrangeComNpay">
                    {l s='Souscription' mod='comnpay'}
                </a>
            </li>
            <li class="{$activeTabList_2|escape:'htmlall':'UTF-8'}" role="presentation">
                <a data-toggle="tab" role="tab" aria-controls="config_comnpay_step2" href="#config_comnpay_step2" class="colorOrangeComNpay">
                    {l s='Configuration' mod='comnpay'}
                </a>
            </li>
            <li role="presentation">
                <a data-toggle="tab" role="tab" aria-controls="config_comnpay_step3" href="#config_comnpay_step3" class="colorOrangeComNpay">
                    {l s='FAQ' mod='comnpay'}
                </a>
            </li>
        </ul>
        
        <div class="tab-content">
            <div id="config_comnpay_step1" class="tab-pane{$activeTab_1|escape:'htmlall':'UTF-8'}" role="tabpanel">
                <div class="panel" style="min-height:750px;">
                	<p>&nbsp;</p>
                    <form method="post" action="https://www.comnpay.com/souscription-prestashop.html" class="account-creation" enctype="multipart/form-data">
                        <div class="form-wrapper">
                            <div class="form-group">
                                <label class="control-label col-lg-3">{l s='AFONE PAYMENT customer?' mod='comnpay'}</label>
                                <div class="col-lg-9">
                                    <label for="client_afone_off" onclick="ComNpayFX.hideCompteAFP();">
                                        <input type="radio" name="client_afone" id="client_afone_off" value="Non" checked="checked"/>
                                        &nbsp; {l s='No' mod='comnpay'} &nbsp;
                                    </label>
                                    <label for="client_afone_on" onclick="ComNpayFX.showCompteAFP();">
                                        <input type="radio" name="client_afone" id="client_afone_on" value="Oui"/>
                                        &nbsp; {l s='Yes' mod='comnpay'}
                                    </label>
                                </div>
                            </div>
                            <div id="displayCompteAFP" class="form-group DisplayNone">
                                <label class="control-label col-lg-3">{l s='Account number' mod='comnpay'}</label>
                                <div class="col-lg-9">
                                    <input type="text" id="compteAFP" name="compteAFP" value="{$compteAFP|escape:'htmlall':'UTF-8'}" placeholder="" style="width:14em;" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                </div>
                            </div>
                        </div>
                        <div style="float:left; clear:both;">
                            <hr />
                            <div class="clear-bis">
                                <label for="lastname">{l s='Last Name' mod='comnpay'}</label>
                                <input type="text" id="lastname" name="lastname" value="{$lastname|escape:'htmlall':'UTF-8'}">
                            </div>
                            <div class="clear-bis">
                                <label for="firstname">{l s='Name' mod='comnpay'}</label>
                                <input type="text" id="firstname" name="firstname" value="{$firstname|escape:'htmlall':'UTF-8'}">
                            </div>
                            <div class="clear-bis">
                                <label for="email">{l s='E-mail' mod='comnpay'}</label>
                                <input type="text" id="email" name="email" value="{$email|escape:'htmlall':'UTF-8'}">
                            </div>
                            <div class="clear-bis">
                                <label for="telephone">{l s='Phone number' mod='comnpay'}</label>
                                <input type="text" id="telephone" name="telephone" value="{$telephone|escape:'htmlall':'UTF-8'}" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 0" maxlength="10">
                            </div>
                            <div class="clear-bis">
                                <label for="raison">{l s='Social reason of the company' mod='comnpay'}</label>
                                <input type="text" id="raison" name="raison" value="{$raison|escape:'htmlall':'UTF-8'}">
                            </div>
                            <div class="clear-bis">
                                <label for="url_website">{l s='Website URL' mod='comnpay'}</label>
                                <input type="text" id="url_website" name="url_website" value="{$url_website|escape:'htmlall':'UTF-8'}">
                            </div>
                            <div class="clear-bis">
                                <label for="forme_juridique">{l s='Legal form' mod='comnpay'}</label>
                                <select name="forme_juridique" id="forme_juridique">
                                    <option value="SA">{l s='Limited Company' mod='comnpay'}</option>
                                    <option value="SARL">{l s='Private Limited Company' mod='comnpay'}</option>
                                    <option value="EURL">{l s='One-person Limited Liability Undertakings' mod='comnpay'}</option>
                                    <option value="SAS">{l s='Simplified Joint-stock Company' mod='comnpay'}</option>
                                    <option value="SASU">{l s='Simplified Single Shareholder Company' mod='comnpay'}</option>
                                    <option value="EI">{l s='Individual Enterprises' mod='comnpay'}</option>
                                    <option value="EIRL">{l s='Individual Enterprises with Limited Liability' mod='comnpay'}</option>
                                    <option value="Auto-entrepreneur">{l s='Self-employed person' mod='comnpay'}</option>
                                    <option value="Autre">{l s='Other' mod='comnpay'}</option>
                                </select>
                            </div>
                            <div class="clear-bis">
                                <label for="secteur">{l s='Activity area' mod='comnpay'}</label><input type="text" id="secteur" name="secteur" value="{$secteur|escape:'htmlall':'UTF-8'}">
                            </div>
                            <div class="clear-bis">
                                <label for="siret">{l s='SIRET number' mod='comnpay'}</label>
                                <input type="text" id="siret" name="siret" value="{$siret|escape:'htmlall':'UTF-8'}" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 0" maxlength="9">
                            </div>
                            <div class="clear-bis">
                                <label for="adresse">{l s='Adress' mod='comnpay'}</label>
                                <input type="text" id="adresse" name="adresse" value="{$adresse|escape:'htmlall':'UTF-8'}">
                            </div>
                            <div class="clear-bis">
                                <label for="code_postal">{l s='Zip code' mod='comnpay'}</label>
                                <input type="text" id="code_postal" name="code_postal" value="{$code_postal|escape:'htmlall':'UTF-8'}" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 0" maxlength="5">
                            </div>
                            <div class="clear-bis">
                                <label for="ville">{l s='City' mod='comnpay'}</label>
                                <input type="text" id="ville" name="ville" value="{$ville|escape:'htmlall':'UTF-8'}">
                            </div>
                            <div class="clear-bis">
                                <label for="carte_identite_recto">{l s='Identity Card (front):' mod='comnpay'}</label>
                                <input type="file" name="carte_identite_recto" id="carte_identite_recto" />
                            </div>
                            <div class="clear-bis">
                                <label for="carte_identite_verso">{l s='Identity card (back):' mod='comnpay'}</label>
                                <input type="file" name="carte_identite_verso" id="carte_identite_verso" />
                            </div>
                            <div class="clear-bis">
                                <label for="rib">{l s='RIB :' mod='comnpay'}</label>
                                <input type="file" name="rib" id="rib" />
                                <span style="color: #f39217;">{l s='Please note that the account and the Nickel account are not accepted' mod='comnpay'}</span>
                            </div>
                            <div class="clear-bis">
                                <label for="kbis">{l s='Certificate of Incorporation' mod='comnpay'}</label>
                                <input type="file" name="kbis" id="kbis" />
                            </div>
                        </div>
                        <br/>
                        <div class="form-wrapper">
                            <div class="form-group">
                                <label class="">{l s='Choice between a transfer to the physical account per week (free) or a transfer on D + 2 at 0.15 € the transaction (see FAQ for more question)' mod='comnpay'}</label>
                                <div class="">
                                    <label>
                                        <input type="radio" name="choixTransaction"  value="GRATUIT" checked="checked"/>
                                        &nbsp; {l s='1 transfer per week' mod='comnpay'} &nbsp;
                                    </label>
                                    <label>
                                        <input type="radio" name="choixTransaction"  value="J+2"/>
                                        &nbsp; {l s='1 transfer on D + 2 (0.15€)' mod='comnpay'}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="clear-bis">
                            <input type="submit" name="create_account_action" class="btn btn-default btn-primary" value="{l s='Send' mod='comnpay'}">
                        </div><br/><br/>
                    </form>
                </div>
            </div>
            <div id="config_comnpay_step2" class="tab-pane{$activeTab_2|escape:'htmlall':'UTF-8'}" role="tabpanel">
                <div class="panel" style="min-height:470px;">
                    <form method="post" action="{$actionForm|escape:'htmlall':'UTF-8'}" class="account-creation" id="formComNpay">
                        <div class="clear-bis">
                            <label for="tpe_number">
                                {$label_tpe_number|escape:'htmlall':'UTF-8'}
                            </label>
                            <input type="text" id="tpe_number" name="COMNPAY_GATEWAY_TPE_NUMBER" value="{$value_tpe_number|escape:'htmlall':'UTF-8'}">
                        </div>
                        <div class="clear-bis">
                            <label for="secret_key">
                                {$label_secret_key|escape:'htmlall':'UTF-8'}
                            </label>
                            <input type="text" id="secret_key" name="COMNPAY_GATEWAY_SECRET_KEY" value="{$value_secret_key|escape:'htmlall':'UTF-8'}">
                        </div>
                        <div class="clear-bis">
                            <label for="url_homologation_DISPLAY">
                                {l s=' Url Homologation ' mod='comnpay'}
                            </label>
                            <input type="text" id="url_homologation_DISPLAY" name="COMNPAY_GATEWAY_HOMOLOGATION_DISPLAY" value="https://secure-homologation.comnpay.com" readonly>
                        </div>
                        <div class="clear-bis">
                            <label for="url_production_DISPLAY">
                                {l s='Url Production' mod='comnpay'}
                            </label>
                            <input type="text" id="url_production_DISPLAY" name="COMNPAY_GATEWAY_PRODUCTION_DISPLAY" value="https://secure.comnpay.com" readonly>
                        </div>
                        <div class="clear-bis" style="min-height:105px;">
                            <label for="plateforme">{l s='Platform' mod='comnpay'}</label>
                            <span class="switch prestashop-switch">
                                <input type="radio" name="COMNPAY_GATEWAY_CONFIG" id="plateforme_production" value="PRODUCTION"{$plateformeProduction|escape:'htmlall':'UTF-8'}/>
                                <label for="plateforme_production">{l s='Production' mod='comnpay'}</label>
                                <input type="radio" name="COMNPAY_GATEWAY_CONFIG" id="plateforme_homologation" value="HOMOLOGATION"{$plateformeHomologation|escape:'htmlall':'UTF-8'}/>
                                <label for="plateforme_homologation">{l s='Homologation' mod='comnpay'}</label>
                                <a class="slide-button btn"/></a>
                            </span>
                            <br />
                            <a href="https://homologation.comnpay.com/login.html" title="Testez gratuitement ComNpay ici" target="_blank">Testez gratuitement ComNpay ici</a>
                        </div>
                        <div class="clear-bis" style="min-height:105px;">
                            <label for="p3f">{l s='Payment in 3 times' mod='comnpay'}</label>
                            <span class="switch prestashop-switch">
                                <input type="radio" name="COMNPAY_GATEWAY_P3F" id="activer_p3f" value="on"{$p3f_on|escape:'htmlall':'UTF-8'}/>
                                <label for="activer_p3f">{l s='Activate' mod='comnpay'}</label>
                                <input type="radio" name="COMNPAY_GATEWAY_P3F" id="desactiver_p3f" value="off"{$p3f_off|escape:'htmlall':'UTF-8'}/>
                                <label for="desactiver_p3f">{l s='Deactivate' mod='comnpay'}</label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
                        <div class="clear-bis">
                            <label for="seuil_p3f">{l s='Trigger payment in 3 times from' mod='comnpay'} </label>
                            <input type="text" id="seuil_p3f" name="COMNPAY_TRIGGER_P3F" value="{$seuil_p3f|escape:'htmlall':'UTF-8'}" placeholder="50" ; >{l s='(Minimum : 50&euro;)' mod='comnpay'}
                        </div>
                        <div class="clear-bis">
                            <label for="seuil_p3f">{l s='Template code' mod='comnpay'} </label>
                            <input type="text" id="code_template" name="COMNPAY_CODE_TEMPLATE" value="{$code_template|escape:'htmlall':'UTF-8'}" placeholder="" ; >{l s='Useful if you have multiple sites and multiple payment pages' mod='comnpay'} : <a href="{l s='http://docs.comnpay.com/terminologie-en.html#customization-of-the-payment-page' mod='comnpay'}" target="_blank">{l s='Explanation' mod='comnpay'}</a>
                        </div>
                        <hr>
                        <div class="clear-bis">
                            <p>&nbsp;</p>
                            <input type="button" name="submitComnpay" class="btn btn-default btn-primary" value="{l s='Update configuration' mod='comnpay'}" id="submitComnpay" onclick="ComNpayFX.validateFormComNpay();" style="margin-top:-5px;">
                        </div>
                        <div class="DisplayNone">
                            <input type="text" id="url_homologation" name="COMNPAY_GATEWAY_HOMOLOGATION" value="https://secure-homologation.comnpay.com">
                            <input type="text" id="url_production" name="COMNPAY_GATEWAY_PRODUCTION" value="https://secure.comnpay.com">
                        </div>
                    </form>
                </div>
            </div>
            <div id="config_comnpay_step3" class="tab-pane" role="tabpanel">
                <div class="panel" style="min-height:650px;">
                    <h2 class="colorBlueComNpay">{l s='Should you purchase a DS (Distance Selling) contract to use ComNpay?' mod='comnpay'}</h2>
                    <p>{l s='Yes, in the case of ComNpay, the DS contract is included at the time of subscription. You have no further steps to take with your bank or another organization.' mod='comnpay'}</p>
                    <h2 class="colorBlueComNpay">{l s='How much does it cost ?' mod='comnpay'}</h2>
                    <p>{l s='The ComNpay module is non-subscription and non-commitment. You pay only on cashed transactions:' mod='comnpay'}</p>
                    <ul>
                        <li>{l s='For CB cards France: 0,90% + 0,20 € per transaction' mod='comnpay'}</li>
                        <li>{l s='For EMV International cards: 2.30% + € 0.20 per transaction' mod='comnpay'}</li>
                    </ul>
                    <p>{l s='If you wish to have a customized offer adapted to your business volume, please send us your request with your contact details and your current offer by email at: prestashop@comnpay.com. Our team will make you a return within 24 hours with a very advantageous proposal.' mod='comnpay'}</p>
                    <h2 class="colorBlueComNpay">{l s='How money is transfered on my physical account ? (2 possibilities)' mod='comnpay'}</h2>
                    <ul>
                        <li>{l s='You can choose to be transfered one time a week' mod='comnpay'}</li>
                        <li>{l s='You can choose option D+2 :' mod='comnpay'}
                            <ul>
                                <li>{l s='The amounts of the transactions are transferred to the account chosen by the customer by means of a transfer whose cost is € 0.15 per transfer. (The transfer cost of € 0.15 does not depend on the number of transactions in the day)' mod='comnpay'}</li>
                                <li>{l s='Bank charges are deducted before transfers' mod='comnpay'}</li>
                                <li>{l s='Monday transactions are paid on Wednesday' mod='comnpay'}</li>
                                <li>{l s='Tuesday transactions are paid on Thursday' mod='comnpay'}</li>
                                <li>{l s='Wednesday transactions are paid on Friday' mod='comnpay'}</li>
                                <li>{l s='Thursday transactions are paid on Monday' mod='comnpay'}</li>
                                <li>{l s='Friday, Saturday, Sunday transactions are paid on Monday. Depending on the bank receiving the transfer, the date of receipt may vary (D or D + 1 compared to the realization of the transfer by Afone Paiement)' mod='comnpay'}</li>
                            </ul>
                        </li>
                    </ul>
                    <h2 class="colorBlueComNpay">{l s='What is the activation time for an account?' mod='comnpay'}</h2>
                    <p>{l s='Your account is usually activated within two business days after receiving the required documents.' mod='comnpay'}.</p>
                    <p>{l s='Our Customer Service is committed to contact you within two working days after your registration to validate the creation of your account.' mod='comnpay'}</p>
                    <h2 class="colorBlueComNpay">{l s='Is it possible to test ComNpay?' mod='comnpay'}</h2>
                    <p>{l s='Yes, you can test the solution for free:' mod='comnpay'} <a href="https://homologation.comnpay.com/" target="_blank">https://homologation.comnpay.com/</a></p>
                    <p>{l s='The test interface allows you to see the rendering for you and your clients. It also allows you to test the main module settings such as customizing your payment page or configuring 3D Secure.' mod='comnpay'}</p>
                    <h2 class="colorBlueComNpay">{l s='How do I transfer my account to my bank account?' mod='comnpay'}</h2>
                    <p>{l s='Transactions occcur on your Afone Paiement account. With access to your Afone Paiement personal space, you can follow the history of your transactions.' mod='comnpay'}</p>
                    <p>{l s='The partnership between PrestaShop and ComNpay allows you to benefit in free weekly transfer.' mod='comnpay'}</p>
                    <p>{l s='Every Tuesday a transfer of the total amount of last week will be made from your Afone Paiement account to your bank account.' mod='comnpay'}</p>
                    <p>{l s='You have the possibility to ask for a daily payment of your receipts. This is a paid option. For this you can contact our customer service.' mod='comnpay'}</p>
                    <h2 class="colorBlueComNpay">{l s='How to configure the ComNpay module?' mod='comnpay'}</h2>
                    <p>{l s='Find all settings of ComNpay on your Afone Paiement personal area.' mod='comnpay'}</p>
                    <p>{l s='You can customize your payment page, configure the triggers of 3D Secure, adapt risk management to your business ...' mod='comnpay'}</p>
                    <h2 class="colorBlueComNpay">{l s='What are the payment methods accepted by ComNpay?' mod='comnpay'}</h2>
                    <p>{l s='ComNpay allows the collection of the most represented bank cards in France and abroad: CC, Visa, MasterCard and American Express.' mod='comnpay'}</p>
                    <h2 class="colorBlueComNpay">{l s='Who are we ?' mod='comnpay'}</h2>
                    <p>{l s='ComNpay is the solution for the online payments of the payment institution Afone Paiement, approved in 2011 by the Prudential Control Authority of Banque de France (ACPR). ComNpay offers solutions for collecting bank cards in France and abroad and has passports to carry out its activity in all countries of the Sepa space.' mod='comnpay'} </p>
                    <p>{l s='You can send us your questions and suggestions on this form:' mod='comnpay'} <a href="https://addons.prestashop.com/fr/contactez-nous?id_product=25846" title="Contacter ComNpay">{l s='Contact ComNpay' mod='comnpay'}</a></p>
                    <p>&nbsp;</p>
                </div>
            </div>
         </div>
     </div>
</div>

<script>

    $("#csrf").val(makeid(20));


    function makeid(number) {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < number; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

    $("#rib").click(function() {
        alert("{l s='Please note that the account and the Nickel account are not accepted' mod='comnpay'}");
    });
</script>