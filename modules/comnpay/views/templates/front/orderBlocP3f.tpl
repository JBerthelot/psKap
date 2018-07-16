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
<br/>
<div class="col-lg-7">
    <div class="panel">
        <fieldset>
            <div class="panel-heading">
                <legend>
                    <img src="../img/os/16.gif" width="16" height="16"/>
                    Détail des transactions ComNpay
                </legend>
            </div>
            <table class="table table-responsive" width="100%">
                <thead>
                    <tr>
                        <th>
                            ID de transaction
                        </th>
                        <th>
                            Date
                        </th>
                        <th>
                            Montant
                        </th>
                        <th>
                            État du paiement
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {section name=data loop=$transac}
                        <tr>
                            <td>
                                {$transac[data].transacId|escape:'htmlall':'UTF-8'}
                            </td>
                            <td>
                                {$transac[data].transacDate|escape:'htmlall':'UTF-8'}
                            </td>
                            <td>
                                {$transac[data].transacMontant|escape:'htmlall':'UTF-8'}
                            </td>
                            <td>
                                {$transac[data].transacOK|escape:'htmlall':'UTF-8'}
                            </td>
                        </tr>
                    {/section}
                </tbody>
            </table>
        </fieldset>
    </div>
</div>
