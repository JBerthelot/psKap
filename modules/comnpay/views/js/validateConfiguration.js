/**
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
*/

$(function() {
	ComNpayFX =	{
		validateFormComNpay: function()	{
			// Values configuration
			var plateformSelect = $('input[name=COMNPAY_GATEWAY_CONFIG]:checked').val();
			var numberTPE = $("#tpe_number").val();
			var secretKEY = $("#secret_key").val();
			var activation_p3f = $("#p3f").val();
			var seuil_p3f = parseInt($("#seuil_p3f").val());


			if(seuil_p3f >= 50)	{
				$("#formComNpay").submit();
			} else {
				alert("Le seuil minimum de déclenchement du paiement en 3 fois est invalide. Rappel : 50€ minimum .");
				$("#seuil_p3f").val("50");
				return false;
			}


			if(plateformSelect == "HOMOLOGATION")	{
				if((numberTPE == "DEMO") && (secretKEY == "DEMO"))	{
					$("#formComNpay").submit();
				}
				else if((numberTPE == "PRESTASHOP") && (secretKEY == "UN6Ek9rm1aZQl44mMeR5"))	{
					$("#formComNpay").submit();
				}else{
					var strTPE = numberTPE.substring(0, 3);
					if(strTPE == "HOM")	{
						$("#formComNpay").submit();
					}else{
						var messageAlert = 'Pour tester votre site en Homologation, veuillez saisir le numéro de TPE qui vous a été communiqué (exemple : HOM-XXX-XXX). Si vous ne le possédez pas, vous pouvez utiliser le compte de démonstration qui a pour numéro TPE "DEMO" et pour clé secrète "DEMO"';
						alert(messageAlert);
						return false;
					}
				}
			}else{
				if((numberTPE !== "") && (secretKEY !== ""))	{
					var strTPE = numberTPE.substring(0, 3);
					if(strTPE == "VAD")	{
						$("#formComNpay").submit();
					}else{
						if(strTPE == "HOM")	{
							var messageAlert = 'Vous utilisez actuellement le numéro de TPE provisoire, veuillez saisir le numéro de TPE valide qui vous a été communiqué.';
						}else{
							var messageAlert = 'Veuillez saisir le numéro de TPE valide qui vous a été communiqué (exemple : VAD-XXX-XXX)';
						}
						alert(messageAlert);
						return false;
					}
				}else{
					var messageAlert = 'Pour mettre votre site en Production, veuillez saisir le numéro de TPE ainsi que la clé secrète qui vous ont été communiqués';
					alert(messageAlert);
					return false;
				}
			}
		},
		showCompteAFP: function()	{
			$("#displayCompteAFP").removeClass("DisplayNone");
		},
		hideCompteAFP: function()	{
			$("#displayCompteAFP").addClass("DisplayNone");
		}
	};
});
