<?php

use Illuminate\Support\Str;

const PO_STATUS = [
    'pending' => [
        'label' => 'Pending',
        'color' => 'warning',
        'text' => 'Waiting for approval',
        'style' => null,
    ],
    'approve' => [
        'label' => 'Approve',
        'color' => 'info',
        'text' => 'Your request has been approved',
        'style' => null,
    ],
    'ready' => [
        'label' => 'Ready',
        'color' => 'info',
        'text' => 'Your request is ready to pair',
        'style' => null,
    ],
    'reject' => [
        'label' => 'Reject',
        'color' => 'dark',
        'text' => 'Request rejected',
        'style' => null,
    ],
    'revert' => [
        'label' => 'Revert',
        'color' => 'dark',
        'text' => 'Request reverted',
        'style' => null,
    ],
    'void' => [
        'label' => 'Void',
        'color' => 'danger',
        'text' => 'Request void',
        'style' => null,
    ],
    'done' => [
        'label' => 'Done',
        'color' => 'success',
        'text' => 'Request Completed',
        'style' => null,
    ],
    'partial-sent' => [
        'label' => 'Partial',
        'color' => 'primary',
        'text' => 'Partially completed',
        'style' => null,
    ],
    'close' => [
        'label' => 'Close',
        'color' => 'dark',
        'text' => 'Purchase order closed',
        'style' => null,
    ],
];

const SO_STATUS = [
    'pending' => [
        'label' => 'Pending',
        'color' => 'warning',
        'text' => 'Waiting for approval',
        'style' => null,
    ],
    'approve' => [
        'label' => 'Approve',
        'color' => 'success',
        'text' => 'Pairing order',
        'style' => null,
    ],
    'reject' => [
        'label' => 'Reject',
        'color' => 'dark',
        'text' => 'Order rejected',
        'style' => null,
    ],
    'revert' => [
        'label' => 'Revert',
        'color' => 'dark',
        'text' => 'Request reverted',
        'style' => null,
    ],
    'paired' => [
        'label' => 'Paired',
        'color' => 'info',
        'text' => 'Your request is paired',
        'style' => null,
    ],
    'ready' => [
        'label' => 'Ready',
        'color' => 'info',
        'text' => 'Your request is ready for delivery',
        'style' => null,
    ],
    'void' => [
        'label' => 'Void',
        'color' => 'danger',
        'text' => 'Request void',
        'style' => null,
    ],
    'done' => [
        'label' => 'Done',
        'color' => 'success',
        'text' => 'Order Completed',
        'style' => null,
    ],
    'do_not_created' => [
        'label' => 'Delivery Not Created Yet',
        'color' => 'warning',
        'text' => 'Delivery for this order not created yet',
        'style' => null,
    ],
    'not_yet_send' => [
        'label' => 'Not Yet Sent',
        'color' => 'info',
        'text' => 'Waiting in line.',
        'style' => null,
    ],
    'partial_sent' => [
        'label' => 'Partial',
        'color' => 'primary',
        'text' => 'Some of your orders have been sent',
        'style' => null,
    ],
    'partial-sended' => [
        'label' => 'Partial',
        'color' => 'primary',
        'text' => 'Some of your orders have been sent',
        'style' => null,
    ],
    'delivery_complete' => [
        'label' => 'Delivery Complete',
        'color' => 'primary',
        'text' => 'Your order has been completed, please proceed to invoice or payment',
        'style' => null,
    ],
];

const PO_SO_DETAIL_STATUS = [
    'done' => [
        'label' => 'Done',
        'color' => 'success',
        'text' => 'Order Complete',
        'style' => null,
    ],
    'pending' => [
        'label' => 'Pending',
        'color' => 'warning',
        'text' => 'Waiting for approval',
        'style' => null,
    ],
    'pairing' => [
        'label' => 'Pairing',
        'color' => 'success',
        'text' => 'Pairing order',
        'style' => null,
    ],
    'reject' => [
        'label' => 'Reject',
        'color' => 'dark',
        'text' => 'Purchase order rejected',
        'style' => null,
    ],
    'revert' => [
        'label' => 'Revert',
        'color' => 'dark',
        'text' => 'Request reverted',
        'style' => null,
    ],
    'void' => [
        'label' => 'Void',
        'color' => 'danger',
        'text' => 'Request void',
        'style' => null,
    ],
    'cancel' => [
        'label' => 'Void',
        'color' => 'danger',
        'text' => 'Request canceled',
        'style' => null,
    ],
    'close' => [
        'label' => 'Close',
        'color' => 'dark',
        'text' => 'Purchase order closed',
        'style' => null,
    ],
];

const ITEM_STATUS = [
    'active' => [
        'label' => 'Active',
        'color' => 'success',
        'text' => 'Item is active',
        'style' => null,
    ],
    'inactive' => [
        'label' => 'Inactive',
        'color' => 'danger',
        'text' => 'Item is inactive',
        'style' => null,
    ],
];

const DO_STATUS = [
    'pending' => [
        'label' => 'Pending',
        'color' => 'secondary',
        'text' => 'Waiting for Approval',
        'style' => null,
    ],
    'approve' => [
        'label' => 'Approve',
        'color' => 'success',
        'text' => 'Delivery approved',
        'style' => null,
    ],
    'expired' => [
        'label' => 'Expired',
        'color' => 'dark',
        'text' => 'Delivery order expired',
        'style' => null,
    ],
    'preparing' => [
        'label' => 'Preparing',
        'color' => 'warning',
        'text' => 'Preparing your order',
        'style' => null,
    ],
    'void' => [
        'label' => 'Void',
        'color' => 'danger',
        'text' => 'Your delivery order was canceled',
        'style' => null,
    ],
    'reject' => [
        'label' => 'Reject',
        'color' => 'danger',
        'text' => 'Your delivery order was rejected',
        'style' => null,
    ],
    'sending' => [
        'label' => 'Sending',
        'color' => 'info',
        'text' => 'Your order is on the way',
        'style' => null,
    ],
    'done' => [
        'label' => 'Done',
        'color' => 'success',
        'text' => 'Your order was successful',
        'style' => null,
    ],
    'submitted' => [
        'label' => 'Submited',
        'color' => 'warning',
        'text' => 'Your change was submitted. Please wait for approval',
        'style' => null,
    ],
    'submit-rejected' => [
        'label' => 'Submit rejected',
        'color' => 'dark',
        'text' => 'Your submission was rejected',
        'style' => null,
    ],
    'submit-approved' => [
        'label' => 'Submit approved',
        'color' => 'success',
        'text' => 'Your submission was approved ',
        'style' => null,
    ],
    'request-print' => [
        'label' => 'Request Print',
        'color' => 'warning',
        'text' => 'Request to print data',
        'style' => null,
    ],
    'approve-request-print' => [
        'label' => 'Request Print approved',
        'color' => 'success',
        'text' => 'Request to print data approved',
        'style' => null,
    ],
    'reject-request-print' => [
        'label' => 'Request Print rejected',
        'color' => 'dark',
        'text' => 'Request to print data rejected',
        'style' => null,
    ],
];

/**
 * get vechicle types
 *
 * @return array
 */
function get_vechicle_types(): array
{
    return [
        '4x2 truck' => Str::headline('4x2 truck'),
        '6x4 truck' => Str::headline('6x4 truck'),
    ];
}

function get_currencies()
{
    return json_decode('{"USD":{"symbol":"$","name":"US Dollar","symbol_native":"$","decimal_digits":2,"rounding":0,"code":"USD","name_plural":"US dollars"},"CAD":{"symbol":"CA$","name":"Canadian Dollar","symbol_native":"$","decimal_digits":2,"rounding":0,"code":"CAD","name_plural":"Canadian dollars"},"EUR":{"symbol":"€","name":"Euro","symbol_native":"€","decimal_digits":2,"rounding":0,"code":"EUR","name_plural":"euros"},"AED":{"symbol":"AED","name":"United Arab Emirates Dirham","symbol_native":"د.إ.‏","decimal_digits":2,"rounding":0,"code":"AED","name_plural":"UAE dirhams"},"AFN":{"symbol":"Af","name":"Afghan Afghani","symbol_native":"؋","decimal_digits":0,"rounding":0,"code":"AFN","name_plural":"Afghan Afghanis"},"ALL":{"symbol":"ALL","name":"Albanian Lek","symbol_native":"Lek","decimal_digits":0,"rounding":0,"code":"ALL","name_plural":"Albanian lekë"},"AMD":{"symbol":"AMD","name":"Armenian Dram","symbol_native":"դր.","decimal_digits":0,"rounding":0,"code":"AMD","name_plural":"Armenian drams"},"ARS":{"symbol":"AR$","name":"Argentine Peso","symbol_native":"$","decimal_digits":2,"rounding":0,"code":"ARS","name_plural":"Argentine pesos"},"AUD":{"symbol":"AU$","name":"Australian Dollar","symbol_native":"$","decimal_digits":2,"rounding":0,"code":"AUD","name_plural":"Australian dollars"},"AZN":{"symbol":"man.","name":"Azerbaijani Manat","symbol_native":"ман.","decimal_digits":2,"rounding":0,"code":"AZN","name_plural":"Azerbaijani manats"},"BAM":{"symbol":"KM","name":"Bosnia-Herzegovina Convertible Mark","symbol_native":"KM","decimal_digits":2,"rounding":0,"code":"BAM","name_plural":"Bosnia-Herzegovina convertible marks"},"BDT":{"symbol":"Tk","name":"Bangladeshi Taka","symbol_native":"৳","decimal_digits":2,"rounding":0,"code":"BDT","name_plural":"Bangladeshi takas"},"BGN":{"symbol":"BGN","name":"Bulgarian Lev","symbol_native":"лв.","decimal_digits":2,"rounding":0,"code":"BGN","name_plural":"Bulgarian leva"},"BHD":{"symbol":"BD","name":"Bahraini Dinar","symbol_native":"د.ب.‏","decimal_digits":3,"rounding":0,"code":"BHD","name_plural":"Bahraini dinars"},"BIF":{"symbol":"FBu","name":"Burundian Franc","symbol_native":"FBu","decimal_digits":0,"rounding":0,"code":"BIF","name_plural":"Burundian francs"},"BND":{"symbol":"BN$","name":"Brunei Dollar","symbol_native":"$","decimal_digits":2,"rounding":0,"code":"BND","name_plural":"Brunei dollars"},"BOB":{"symbol":"Bs","name":"Bolivian Boliviano","symbol_native":"Bs","decimal_digits":2,"rounding":0,"code":"BOB","name_plural":"Bolivian bolivianos"},"BRL":{"symbol":"R$","name":"Brazilian Real","symbol_native":"R$","decimal_digits":2,"rounding":0,"code":"BRL","name_plural":"Brazilian reals"},"BWP":{"symbol":"BWP","name":"Botswanan Pula","symbol_native":"P","decimal_digits":2,"rounding":0,"code":"BWP","name_plural":"Botswanan pulas"},"BYN":{"symbol":"Br","name":"Belarusian Ruble","symbol_native":"руб.","decimal_digits":2,"rounding":0,"code":"BYN","name_plural":"Belarusian rubles"},"BZD":{"symbol":"BZ$","name":"Belize Dollar","symbol_native":"$","decimal_digits":2,"rounding":0,"code":"BZD","name_plural":"Belize dollars"},"CDF":{"symbol":"CDF","name":"Congolese Franc","symbol_native":"FrCD","decimal_digits":2,"rounding":0,"code":"CDF","name_plural":"Congolese francs"},"CHF":{"symbol":"CHF","name":"Swiss Franc","symbol_native":"CHF","decimal_digits":2,"rounding":0.05,"code":"CHF","name_plural":"Swiss francs"},"CLP":{"symbol":"CL$","name":"Chilean Peso","symbol_native":"$","decimal_digits":0,"rounding":0,"code":"CLP","name_plural":"Chilean pesos"},"CNY":{"symbol":"CN¥","name":"Chinese Yuan","symbol_native":"CN¥","decimal_digits":2,"rounding":0,"code":"CNY","name_plural":"Chinese yuan"},"COP":{"symbol":"CO$","name":"Colombian Peso","symbol_native":"$","decimal_digits":0,"rounding":0,"code":"COP","name_plural":"Colombian pesos"},"CRC":{"symbol":"₡","name":"Costa Rican Colón","symbol_native":"₡","decimal_digits":0,"rounding":0,"code":"CRC","name_plural":"Costa Rican colóns"},"CVE":{"symbol":"CV$","name":"Cape Verdean Escudo","symbol_native":"CV$","decimal_digits":2,"rounding":0,"code":"CVE","name_plural":"Cape Verdean escudos"},"CZK":{"symbol":"Kč","name":"Czech Republic Koruna","symbol_native":"Kč","decimal_digits":2,"rounding":0,"code":"CZK","name_plural":"Czech Republic korunas"},"DJF":{"symbol":"Fdj","name":"Djiboutian Franc","symbol_native":"Fdj","decimal_digits":0,"rounding":0,"code":"DJF","name_plural":"Djiboutian francs"},"DKK":{"symbol":"Dkr","name":"Danish Krone","symbol_native":"kr","decimal_digits":2,"rounding":0,"code":"DKK","name_plural":"Danish kroner"},"DOP":{"symbol":"RD$","name":"Dominican Peso","symbol_native":"RD$","decimal_digits":2,"rounding":0,"code":"DOP","name_plural":"Dominican pesos"},"DZD":{"symbol":"DA","name":"Algerian Dinar","symbol_native":"د.ج.‏","decimal_digits":2,"rounding":0,"code":"DZD","name_plural":"Algerian dinars"},"EEK":{"symbol":"Ekr","name":"Estonian Kroon","symbol_native":"kr","decimal_digits":2,"rounding":0,"code":"EEK","name_plural":"Estonian kroons"},"EGP":{"symbol":"EGP","name":"Egyptian Pound","symbol_native":"ج.م.‏","decimal_digits":2,"rounding":0,"code":"EGP","name_plural":"Egyptian pounds"},"ERN":{"symbol":"Nfk","name":"Eritrean Nakfa","symbol_native":"Nfk","decimal_digits":2,"rounding":0,"code":"ERN","name_plural":"Eritrean nakfas"},"ETB":{"symbol":"Br","name":"Ethiopian Birr","symbol_native":"Br","decimal_digits":2,"rounding":0,"code":"ETB","name_plural":"Ethiopian birrs"},"GBP":{"symbol":"£","name":"British Pound Sterling","symbol_native":"£","decimal_digits":2,"rounding":0,"code":"GBP","name_plural":"British pounds sterling"},"GEL":{"symbol":"GEL","name":"Georgian Lari","symbol_native":"GEL","decimal_digits":2,"rounding":0,"code":"GEL","name_plural":"Georgian laris"},"GHS":{"symbol":"GH₵","name":"Ghanaian Cedi","symbol_native":"GH₵","decimal_digits":2,"rounding":0,"code":"GHS","name_plural":"Ghanaian cedis"},"GNF":{"symbol":"FG","name":"Guinean Franc","symbol_native":"FG","decimal_digits":0,"rounding":0,"code":"GNF","name_plural":"Guinean francs"},"GTQ":{"symbol":"GTQ","name":"Guatemalan Quetzal","symbol_native":"Q","decimal_digits":2,"rounding":0,"code":"GTQ","name_plural":"Guatemalan quetzals"},"HKD":{"symbol":"HK$","name":"Hong Kong Dollar","symbol_native":"$","decimal_digits":2,"rounding":0,"code":"HKD","name_plural":"Hong Kong dollars"},"HNL":{"symbol":"HNL","name":"Honduran Lempira","symbol_native":"L","decimal_digits":2,"rounding":0,"code":"HNL","name_plural":"Honduran lempiras"},"HRK":{"symbol":"kn","name":"Croatian Kuna","symbol_native":"kn","decimal_digits":2,"rounding":0,"code":"HRK","name_plural":"Croatian kunas"},"HUF":{"symbol":"Ft","name":"Hungarian Forint","symbol_native":"Ft","decimal_digits":0,"rounding":0,"code":"HUF","name_plural":"Hungarian forints"},"IDR":{"symbol":"Rp","name":"Indonesian Rupiah","symbol_native":"Rp","decimal_digits":0,"rounding":0,"code":"IDR","name_plural":"Indonesian rupiahs"},"ILS":{"symbol":"₪","name":"Israeli New Sheqel","symbol_native":"₪","decimal_digits":2,"rounding":0,"code":"ILS","name_plural":"Israeli new sheqels"},"INR":{"symbol":"Rs","name":"Indian Rupee","symbol_native":"টকা","decimal_digits":2,"rounding":0,"code":"INR","name_plural":"Indian rupees"},"IQD":{"symbol":"IQD","name":"Iraqi Dinar","symbol_native":"د.ع.‏","decimal_digits":0,"rounding":0,"code":"IQD","name_plural":"Iraqi dinars"},"IRR":{"symbol":"IRR","name":"Iranian Rial","symbol_native":"﷼","decimal_digits":0,"rounding":0,"code":"IRR","name_plural":"Iranian rials"},"ISK":{"symbol":"Ikr","name":"Icelandic Króna","symbol_native":"kr","decimal_digits":0,"rounding":0,"code":"ISK","name_plural":"Icelandic krónur"},"JMD":{"symbol":"J$","name":"Jamaican Dollar","symbol_native":"$","decimal_digits":2,"rounding":0,"code":"JMD","name_plural":"Jamaican dollars"},"JOD":{"symbol":"JD","name":"Jordanian Dinar","symbol_native":"د.أ.‏","decimal_digits":3,"rounding":0,"code":"JOD","name_plural":"Jordanian dinars"},"JPY":{"symbol":"¥","name":"Japanese Yen","symbol_native":"￥","decimal_digits":0,"rounding":0,"code":"JPY","name_plural":"Japanese yen"},"KES":{"symbol":"Ksh","name":"Kenyan Shilling","symbol_native":"Ksh","decimal_digits":2,"rounding":0,"code":"KES","name_plural":"Kenyan shillings"},"KHR":{"symbol":"KHR","name":"Cambodian Riel","symbol_native":"៛","decimal_digits":2,"rounding":0,"code":"KHR","name_plural":"Cambodian riels"},"KMF":{"symbol":"CF","name":"Comorian Franc","symbol_native":"FC","decimal_digits":0,"rounding":0,"code":"KMF","name_plural":"Comorian francs"},"KRW":{"symbol":"₩","name":"South Korean Won","symbol_native":"₩","decimal_digits":0,"rounding":0,"code":"KRW","name_plural":"South Korean won"},"KWD":{"symbol":"KD","name":"Kuwaiti Dinar","symbol_native":"د.ك.‏","decimal_digits":3,"rounding":0,"code":"KWD","name_plural":"Kuwaiti dinars"},"KZT":{"symbol":"KZT","name":"Kazakhstani Tenge","symbol_native":"тңг.","decimal_digits":2,"rounding":0,"code":"KZT","name_plural":"Kazakhstani tenges"},"LBP":{"symbol":"L.L.","name":"Lebanese Pound","symbol_native":"ل.ل.‏","decimal_digits":0,"rounding":0,"code":"LBP","name_plural":"Lebanese pounds"},"LKR":{"symbol":"SLRs","name":"Sri Lankan Rupee","symbol_native":"SL Re","decimal_digits":2,"rounding":0,"code":"LKR","name_plural":"Sri Lankan rupees"},"LTL":{"symbol":"Lt","name":"Lithuanian Litas","symbol_native":"Lt","decimal_digits":2,"rounding":0,"code":"LTL","name_plural":"Lithuanian litai"},"LVL":{"symbol":"Ls","name":"Latvian Lats","symbol_native":"Ls","decimal_digits":2,"rounding":0,"code":"LVL","name_plural":"Latvian lati"},"LYD":{"symbol":"LD","name":"Libyan Dinar","symbol_native":"د.ل.‏","decimal_digits":3,"rounding":0,"code":"LYD","name_plural":"Libyan dinars"},"MAD":{"symbol":"MAD","name":"Moroccan Dirham","symbol_native":"د.م.‏","decimal_digits":2,"rounding":0,"code":"MAD","name_plural":"Moroccan dirhams"},"MDL":{"symbol":"MDL","name":"Moldovan Leu","symbol_native":"MDL","decimal_digits":2,"rounding":0,"code":"MDL","name_plural":"Moldovan lei"},"MGA":{"symbol":"MGA","name":"Malagasy Ariary","symbol_native":"MGA","decimal_digits":0,"rounding":0,"code":"MGA","name_plural":"Malagasy Ariaries"},"MKD":{"symbol":"MKD","name":"Macedonian Denar","symbol_native":"MKD","decimal_digits":2,"rounding":0,"code":"MKD","name_plural":"Macedonian denari"},"MMK":{"symbol":"MMK","name":"Myanma Kyat","symbol_native":"K","decimal_digits":0,"rounding":0,"code":"MMK","name_plural":"Myanma kyats"},"MOP":{"symbol":"MOP$","name":"Macanese Pataca","symbol_native":"MOP$","decimal_digits":2,"rounding":0,"code":"MOP","name_plural":"Macanese patacas"},"MUR":{"symbol":"MURs","name":"Mauritian Rupee","symbol_native":"MURs","decimal_digits":0,"rounding":0,"code":"MUR","name_plural":"Mauritian rupees"},"MXN":{"symbol":"MX$","name":"Mexican Peso","symbol_native":"$","decimal_digits":2,"rounding":0,"code":"MXN","name_plural":"Mexican pesos"},"MYR":{"symbol":"RM","name":"Malaysian Ringgit","symbol_native":"RM","decimal_digits":2,"rounding":0,"code":"MYR","name_plural":"Malaysian ringgits"},"MZN":{"symbol":"MTn","name":"Mozambican Metical","symbol_native":"MTn","decimal_digits":2,"rounding":0,"code":"MZN","name_plural":"Mozambican meticals"},"NAD":{"symbol":"N$","name":"Namibian Dollar","symbol_native":"N$","decimal_digits":2,"rounding":0,"code":"NAD","name_plural":"Namibian dollars"},"NGN":{"symbol":"₦","name":"Nigerian Naira","symbol_native":"₦","decimal_digits":2,"rounding":0,"code":"NGN","name_plural":"Nigerian nairas"},"NIO":{"symbol":"C$","name":"Nicaraguan Córdoba","symbol_native":"C$","decimal_digits":2,"rounding":0,"code":"NIO","name_plural":"Nicaraguan córdobas"},"NOK":{"symbol":"Nkr","name":"Norwegian Krone","symbol_native":"kr","decimal_digits":2,"rounding":0,"code":"NOK","name_plural":"Norwegian kroner"},"NPR":{"symbol":"NPRs","name":"Nepalese Rupee","symbol_native":"नेरू","decimal_digits":2,"rounding":0,"code":"NPR","name_plural":"Nepalese rupees"},"NZD":{"symbol":"NZ$","name":"New Zealand Dollar","symbol_native":"$","decimal_digits":2,"rounding":0,"code":"NZD","name_plural":"New Zealand dollars"},"OMR":{"symbol":"OMR","name":"Omani Rial","symbol_native":"ر.ع.‏","decimal_digits":3,"rounding":0,"code":"OMR","name_plural":"Omani rials"},"PAB":{"symbol":"B/.","name":"Panamanian Balboa","symbol_native":"B/.","decimal_digits":2,"rounding":0,"code":"PAB","name_plural":"Panamanian balboas"},"PEN":{"symbol":"S/.","name":"Peruvian Nuevo Sol","symbol_native":"S/.","decimal_digits":2,"rounding":0,"code":"PEN","name_plural":"Peruvian nuevos soles"},"PHP":{"symbol":"₱","name":"Philippine Peso","symbol_native":"₱","decimal_digits":2,"rounding":0,"code":"PHP","name_plural":"Philippine pesos"},"PKR":{"symbol":"PKRs","name":"Pakistani Rupee","symbol_native":"₨","decimal_digits":0,"rounding":0,"code":"PKR","name_plural":"Pakistani rupees"},"PLN":{"symbol":"zł","name":"Polish Zloty","symbol_native":"zł","decimal_digits":2,"rounding":0,"code":"PLN","name_plural":"Polish zlotys"},"PYG":{"symbol":"₲","name":"Paraguayan Guarani","symbol_native":"₲","decimal_digits":0,"rounding":0,"code":"PYG","name_plural":"Paraguayan guaranis"},"QAR":{"symbol":"QR","name":"Qatari Rial","symbol_native":"ر.ق.‏","decimal_digits":2,"rounding":0,"code":"QAR","name_plural":"Qatari rials"},"RON":{"symbol":"RON","name":"Romanian Leu","symbol_native":"RON","decimal_digits":2,"rounding":0,"code":"RON","name_plural":"Romanian lei"},"RSD":{"symbol":"din.","name":"Serbian Dinar","symbol_native":"дин.","decimal_digits":0,"rounding":0,"code":"RSD","name_plural":"Serbian dinars"},"RUB":{"symbol":"RUB","name":"Russian Ruble","symbol_native":"₽.","decimal_digits":2,"rounding":0,"code":"RUB","name_plural":"Russian rubles"},"RWF":{"symbol":"RWF","name":"Rwandan Franc","symbol_native":"FR","decimal_digits":0,"rounding":0,"code":"RWF","name_plural":"Rwandan francs"},"SAR":{"symbol":"SR","name":"Saudi Riyal","symbol_native":"ر.س.‏","decimal_digits":2,"rounding":0,"code":"SAR","name_plural":"Saudi riyals"},"SDG":{"symbol":"SDG","name":"Sudanese Pound","symbol_native":"SDG","decimal_digits":2,"rounding":0,"code":"SDG","name_plural":"Sudanese pounds"},"SEK":{"symbol":"Skr","name":"Swedish Krona","symbol_native":"kr","decimal_digits":2,"rounding":0,"code":"SEK","name_plural":"Swedish kronor"},"SGD":{"symbol":"S$","name":"Singapore Dollar","symbol_native":"$","decimal_digits":2,"rounding":0,"code":"SGD","name_plural":"Singapore dollars"},"SOS":{"symbol":"Ssh","name":"Somali Shilling","symbol_native":"Ssh","decimal_digits":0,"rounding":0,"code":"SOS","name_plural":"Somali shillings"},"SYP":{"symbol":"SY£","name":"Syrian Pound","symbol_native":"ل.س.‏","decimal_digits":0,"rounding":0,"code":"SYP","name_plural":"Syrian pounds"},"THB":{"symbol":"฿","name":"Thai Baht","symbol_native":"฿","decimal_digits":2,"rounding":0,"code":"THB","name_plural":"Thai baht"},"TND":{"symbol":"DT","name":"Tunisian Dinar","symbol_native":"د.ت.‏","decimal_digits":3,"rounding":0,"code":"TND","name_plural":"Tunisian dinars"},"TOP":{"symbol":"T$","name":"Tongan Paʻanga","symbol_native":"T$","decimal_digits":2,"rounding":0,"code":"TOP","name_plural":"Tongan paʻanga"},"TRY":{"symbol":"TL","name":"Turkish Lira","symbol_native":"TL","decimal_digits":2,"rounding":0,"code":"TRY","name_plural":"Turkish Lira"},"TTD":{"symbol":"TT$","name":"Trinidad and Tobago Dollar","symbol_native":"$","decimal_digits":2,"rounding":0,"code":"TTD","name_plural":"Trinidad and Tobago dollars"},"TWD":{"symbol":"NT$","name":"New Taiwan Dollar","symbol_native":"NT$","decimal_digits":2,"rounding":0,"code":"TWD","name_plural":"New Taiwan dollars"},"TZS":{"symbol":"TSh","name":"Tanzanian Shilling","symbol_native":"TSh","decimal_digits":0,"rounding":0,"code":"TZS","name_plural":"Tanzanian shillings"},"UAH":{"symbol":"₴","name":"Ukrainian Hryvnia","symbol_native":"₴","decimal_digits":2,"rounding":0,"code":"UAH","name_plural":"Ukrainian hryvnias"},"UGX":{"symbol":"USh","name":"Ugandan Shilling","symbol_native":"USh","decimal_digits":0,"rounding":0,"code":"UGX","name_plural":"Ugandan shillings"},"UYU":{"symbol":"$U","name":"Uruguayan Peso","symbol_native":"$","decimal_digits":2,"rounding":0,"code":"UYU","name_plural":"Uruguayan pesos"},"UZS":{"symbol":"UZS","name":"Uzbekistan Som","symbol_native":"UZS","decimal_digits":0,"rounding":0,"code":"UZS","name_plural":"Uzbekistan som"},"VEF":{"symbol":"Bs.F.","name":"Venezuelan Bolívar","symbol_native":"Bs.F.","decimal_digits":2,"rounding":0,"code":"VEF","name_plural":"Venezuelan bolívars"},"VND":{"symbol":"₫","name":"Vietnamese Dong","symbol_native":"₫","decimal_digits":0,"rounding":0,"code":"VND","name_plural":"Vietnamese dong"},"XAF":{"symbol":"FCFA","name":"CFA Franc BEAC","symbol_native":"FCFA","decimal_digits":0,"rounding":0,"code":"XAF","name_plural":"CFA francs BEAC"},"XOF":{"symbol":"CFA","name":"CFA Franc BCEAO","symbol_native":"CFA","decimal_digits":0,"rounding":0,"code":"XOF","name_plural":"CFA francs BCEAO"},"YER":{"symbol":"YR","name":"Yemeni Rial","symbol_native":"ر.ي.‏","decimal_digits":0,"rounding":0,"code":"YER","name_plural":"Yemeni rials"},"ZAR":{"symbol":"R","name":"South African Rand","symbol_native":"R","decimal_digits":2,"rounding":0,"code":"ZAR","name_plural":"South African rand"},"ZMK":{"symbol":"ZK","name":"Zambian Kwacha","symbol_native":"ZK","decimal_digits":0,"rounding":0,"code":"ZMK","name_plural":"Zambian kwachas"},"ZWL":{"symbol":"ZWL$","name":"Zimbabwean Dollar","symbol_native":"ZWL$","decimal_digits":0,"rounding":0,"code":"ZWL","name_plural":"Zimbabwean Dollar"}}');
}

function get_currency_and_countries()
{
    return json_decode('[{"name":"Afghanistan","code":"AF","capital":"Kabul","region":"AS","currency":{"code":"AFN","name":"Afghan afghani","symbol":"؋"},"language":{"code":"ps","name":"Pashto"},"flag":"https://restcountries.eu/data/afg.svg"},{"name":"Åland Islands","code":"AX","capital":"Mariehamn","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"sv","name":"Swedish"},"flag":"https://restcountries.eu/data/ala.svg"},{"name":"Albania","code":"AL","capital":"Tirana","region":"EU","currency":{"code":"ALL","name":"Albanian lek","symbol":"L"},"language":{"code":"sq","name":"Albanian"},"flag":"https://restcountries.eu/data/alb.svg"},{"name":"Algeria","code":"DZ","capital":"Algiers","region":"AF","currency":{"code":"DZD","name":"Algerian dinar","symbol":"د.ج"},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/dza.svg"},{"name":"American Samoa","code":"AS","capital":"Pago Pago","region":"OC","currency":{"code":"USD","name":"United State Dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/asm.svg"},{"name":"Andorra","code":"AD","capital":"Andorra la Vella","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"ca","name":"Catalan"},"flag":"https://restcountries.eu/data/and.svg"},{"name":"Angola","code":"AO","capital":"Luanda","region":"AF","currency":{"code":"AOA","name":"Angolan kwanza","symbol":"Kz"},"language":{"code":"pt","name":"Portuguese"},"flag":"https://restcountries.eu/data/ago.svg"},{"name":"Anguilla","code":"AI","capital":"The Valley","region":"NA","currency":{"code":"XCD","name":"East Caribbean dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/aia.svg"},{"name":"Antigua and Barbuda","code":"AG","capital":"Saint John\'s","region":"NA","currency":{"code":"XCD","name":"East Caribbean dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/atg.svg"},{"name":"Argentina","code":"AR","capital":"Buenos Aires","region":"SA","currency":{"code":"ARS","name":"Argentine peso","symbol":"$"},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/arg.svg"},{"name":"Armenia","code":"AM","capital":"Yerevan","region":"AS","currency":{"code":"AMD","name":"Armenian dram","symbol":""},"language":{"code":"hy","name":"Armenian"},"flag":"https://restcountries.eu/data/arm.svg"},{"name":"Aruba","code":"AW","capital":"Oranjestad","region":"SA","currency":{"code":"AWG","name":"Aruban florin","symbol":"ƒ"},"language":{"code":"nl","name":"Dutch"},"flag":"https://restcountries.eu/data/abw.svg"},{"name":"Australia","code":"AU","capital":"Canberra","region":"OC","currency":{"code":"AUD","name":"Australian dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/aus.svg"},{"name":"Austria","code":"AT","capital":"Vienna","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"de","name":"German"},"flag":"https://restcountries.eu/data/aut.svg"},{"name":"Azerbaijan","code":"AZ","capital":"Baku","region":"AS","currency":{"code":"AZN","name":"Azerbaijani manat","symbol":null},"language":{"code":"az","name":"Azerbaijani"},"flag":"https://restcountries.eu/data/aze.svg"},{"name":"Bahamas","code":"BS","capital":"Nassau","region":"NA","currency":{"code":"BSD","name":"Bahamian dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/bhs.svg"},{"name":"Bahrain","code":"BH","capital":"Manama","region":"AS","currency":{"code":"BHD","name":"Bahraini dinar","symbol":".د.ب"},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/bhr.svg"},{"name":"Bangladesh","code":"BD","capital":"Dhaka","region":"AS","currency":{"code":"BDT","name":"Bangladeshi taka","symbol":"৳"},"language":{"code":"bn","name":"Bengali"},"flag":"https://restcountries.eu/data/bgd.svg"},{"name":"Barbados","code":"BB","capital":"Bridgetown","region":"NA","currency":{"code":"BBD","name":"Barbadian dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/brb.svg"},{"name":"Belarus","code":"BY","capital":"Minsk","region":"EU","currency":{"code":"BYN","name":"New Belarusian ruble","symbol":"Br"},"language":{"code":"be","name":"Belarusian"},"flag":"https://restcountries.eu/data/blr.svg"},{"name":"Belgium","code":"BE","capital":"Brussels","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"nl","name":"Dutch"},"flag":"https://restcountries.eu/data/bel.svg"},{"name":"Belize","code":"BZ","capital":"Belmopan","region":"NA","currency":{"code":"BZD","name":"Belize dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/blz.svg"},{"name":"Benin","code":"BJ","capital":"Porto-Novo","region":"AF","currency":{"code":"XOF","name":"West African CFA franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/ben.svg"},{"name":"Bermuda","code":"BM","capital":"Hamilton","region":"NA","currency":{"code":"BMD","name":"Bermudian dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/bmu.svg"},{"name":"Bhutan","code":"BT","capital":"Thimphu","region":"AS","currency":{"code":"BTN","name":"Bhutanese ngultrum","symbol":"Nu."},"language":{"code":"dz","name":"Dzongkha"},"flag":"https://restcountries.eu/data/btn.svg"},{"name":"Bolivia (Plurinational State of)","code":"BO","capital":"Sucre","region":"SA","currency":{"code":"BOB","name":"Bolivian boliviano","symbol":"Bs."},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/bol.svg"},{"name":"Bonaire, Sint Eustatius and Saba","code":"BQ","capital":"Kralendijk","region":"SA","currency":{"code":"USD","name":"United States dollar","symbol":"$"},"language":{"code":"nl","name":"Dutch"},"flag":"https://restcountries.eu/data/bes.svg"},{"name":"Bosnia and Herzegovina","code":"BA","capital":"Sarajevo","region":"EU","currency":{"code":"BAM","name":"Bosnia and Herzegovina convertible mark","symbol":null},"language":{"code":"bs","name":"Bosnian"},"flag":"https://restcountries.eu/data/bih.svg"},{"name":"Botswana","code":"BW","capital":"Gaborone","region":"AF","currency":{"code":"BWP","name":"Botswana pula","symbol":"P"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/bwa.svg"},{"name":"Bouvet Island","code":"BV","capital":"","region":"AN","currency":{"code":"NOK","name":"Norwegian krone","symbol":"kr"},"language":{"code":"no","name":"Norwegian"},"flag":"https://restcountries.eu/data/bvt.svg"},{"name":"Brazil","code":"BR","capital":"Brasília","region":"SA","currency":{"code":"BRL","name":"Brazilian real","symbol":"R$"},"language":{"code":"pt","name":"Portuguese"},"flag":"https://restcountries.eu/data/bra.svg"},{"name":"British Indian Ocean Territory","code":"IO","capital":"Diego Garcia","region":"AF","currency":{"code":"USD","name":"United States dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/iot.svg"},{"name":"United States Minor Outlying Islands","code":"UM","capital":"","region":"NA","currency":{"code":"USD","name":"United States Dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/umi.svg"},{"name":"Virgin Islands (British)","code":"VG","capital":"Road Town","region":"NA","currency":{"code":"USD","name":"United States dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/vgb.svg"},{"name":"Virgin Islands (U.S.)","code":"VI","capital":"Charlotte Amalie","region":"NA","currency":{"code":"USD","name":"United States dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/vir.svg"},{"name":"Brunei Darussalam","code":"BN","capital":"Bandar Seri Begawan","region":"AS","currency":{"code":"BND","name":"Brunei dollar","symbol":"$"},"language":{"code":"ms","name":"Malay"},"flag":"https://restcountries.eu/data/brn.svg"},{"name":"Bulgaria","code":"BG","capital":"Sofia","region":"EU","currency":{"code":"BGN","name":"Bulgarian lev","symbol":"лв"},"language":{"code":"bg","name":"Bulgarian"},"flag":"https://restcountries.eu/data/bgr.svg"},{"name":"Burkina Faso","code":"BF","capital":"Ouagadougou","region":"AF","currency":{"code":"XOF","name":"West African CFA franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/bfa.svg"},{"name":"Burundi","code":"BI","capital":"Bujumbura","region":"AF","currency":{"code":"BIF","name":"Burundian franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/bdi.svg"},{"name":"Cambodia","code":"KH","capital":"Phnom Penh","region":"AS","currency":{"code":"KHR","name":"Cambodian riel","symbol":"៛"},"language":{"code":"km","name":"Khmer"},"flag":"https://restcountries.eu/data/khm.svg"},{"name":"Cameroon","code":"CM","capital":"Yaoundé","region":"AF","currency":{"code":"XAF","name":"Central African CFA franc","symbol":"Fr"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/cmr.svg"},{"name":"Canada","code":"CA","capital":"Ottawa","region":"NA","currency":{"code":"CAD","name":"Canadian dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/can.svg"},{"name":"Cabo Verde","code":"CV","capital":"Praia","region":"AF","currency":{"code":"CVE","name":"Cape Verdean escudo","symbol":"Esc"},"language":{"code":"pt","iso639_2":"por","name":"Portuguese","nativeName":"Português"},"flag":"https://restcountries.eu/data/cpv.svg"},{"name":"Cayman Islands","code":"KY","capital":"George Town","region":"NA","demonym":"Caymanian","currency":{"code":"KYD","name":"Cayman Islands dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/cym.svg"},{"name":"Central African Republic","code":"CF","capital":"Bangui","region":"AF","currency":{"code":"XAF","name":"Central African CFA franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/caf.svg"},{"name":"Chad","code":"TD","capital":"N\'Djamena","region":"AF","currency":{"code":"XAF","name":"Central African CFA franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/tcd.svg"},{"name":"Chile","code":"CL","capital":"Santiago","region":"SA","currency":{"code":"CLP","name":"Chilean peso","symbol":"$"},"language":{"code":"es","iso639_2":"spa","name":"Spanish","nativeName":"Español"},"flag":"https://restcountries.eu/data/chl.svg"},{"name":"China","code":"CN","capital":"Beijing","region":"AS","currency":{"code":"CNY","name":"Chinese yuan","symbol":"¥"},"language":{"code":"zh","name":"Chinese"},"flag":"https://restcountries.eu/data/chn.svg"},{"name":"Christmas Island","code":"CX","capital":"Flying Fish Cove","region":"OC","currency":{"code":"AUD","name":"Australian dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/cxr.svg"},{"name":"Cocos (Keeling) Islands","code":"CC","capital":"West Island","region":"OC","currency":{"code":"AUD","name":"Australian dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/cck.svg"},{"name":"Colombia","code":"CO","capital":"Bogotá","region":"SA","currency":{"code":"COP","name":"Colombian peso","symbol":"$"},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/col.svg"},{"name":"Comoros","code":"KM","capital":"Moroni","region":"AF","currency":{"code":"KMF","name":"Comorian franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/com.svg"},{"name":"Congo","code":"CG","capital":"Brazzaville","region":"AF","currency":{"code":"XAF","name":"Central African CFA franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/cog.svg"},{"name":"Congo (Democratic Republic of the)","code":"CD","capital":"Kinshasa","region":"AF","currency":{"code":"CDF","name":"Congolese franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/cod.svg"},{"name":"Cook Islands","code":"CK","capital":"Avarua","region":"OC","currency":{"code":"NZD","name":"New Zealand dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/cok.svg"},{"name":"Costa Rica","code":"CR","capital":"San José","region":"NA","currency":{"code":"CRC","name":"Costa Rican colón","symbol":"₡"},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/cri.svg"},{"name":"Croatia","code":"HR","capital":"Zagreb","region":"EU","currency":{"code":"HRK","name":"Croatian kuna","symbol":"kn"},"language":{"code":"hr","name":"Croatian"},"flag":"https://restcountries.eu/data/hrv.svg"},{"name":"Cuba","code":"CU","capital":"Havana","region":"NA","currency":{"code":"CUC","name":"Cuban convertible peso","symbol":"$"},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/cub.svg"},{"name":"Curaçao","code":"CW","capital":"Willemstad","region":"SA","currency":{"code":"ANG","name":"Netherlands Antillean guilder","symbol":"ƒ"},"language":{"code":"nl","name":"Dutch"},"flag":"https://restcountries.eu/data/cuw.svg"},{"name":"Cyprus","code":"CY","capital":"Nicosia","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"tr","name":"Turkish"},"flag":"https://restcountries.eu/data/cyp.svg"},{"name":"Czech Republic","code":"CZ","capital":"Prague","region":"EU","currency":{"code":"CZK","name":"Czech koruna","symbol":"Kč"},"language":{"code":"cs","name":"Czech"},"flag":"https://restcountries.eu/data/cze.svg"},{"name":"Denmark","code":"DK","capital":"Copenhagen","region":"EU","currency":{"code":"DKK","name":"Danish krone","symbol":"kr"},"language":{"code":"da","name":"Danish"},"flag":"https://restcountries.eu/data/dnk.svg"},{"name":"Djibouti","code":"DJ","capital":"Djibouti","region":"AF","currency":{"code":"DJF","name":"Djiboutian franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/dji.svg"},{"name":"Dominica","code":"DM","capital":"Roseau","region":"NA","currency":{"code":"XCD","name":"East Caribbean dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/dma.svg"},{"name":"Dominican Republic","code":"DO","capital":"Santo Domingo","region":"NA","currency":{"code":"DOP","name":"Dominican peso","symbol":"$"},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/dom.svg"},{"name":"Ecuador","code":"EC","capital":"Quito","region":"SA","currency":{"code":"USD","name":"United States dollar","symbol":"$"},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/ecu.svg"},{"name":"Egypt","code":"EG","capital":"Cairo","region":"AF","currency":{"code":"EGP","name":"Egyptian pound","symbol":"£"},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/egy.svg"},{"name":"El Salvador","code":"SV","capital":"San Salvador","region":"NA","currency":{"code":"USD","name":"United States dollar","symbol":"$"},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/slv.svg"},{"name":"Equatorial Guinea","code":"GQ","capital":"Malabo","region":"AF","currency":{"code":"XAF","name":"Central African CFA franc","symbol":"Fr"},"language":{"code":"es","iso639_2":"spa","name":"Spanish","nativeName":"Español"},"flag":"https://restcountries.eu/data/gnq.svg"},{"name":"Eritrea","code":"ER","capital":"Asmara","region":"AF","currency":{"code":"ERN","name":"Eritrean nakfa","symbol":"Nfk"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/eri.svg"},{"name":"Estonia","code":"EE","capital":"Tallinn","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"et","name":"Estonian"},"flag":"https://restcountries.eu/data/est.svg"},{"name":"Ethiopia","code":"ET","capital":"Addis Ababa","region":"AF","currency":{"code":"ETB","name":"Ethiopian birr","symbol":"Br"},"language":{"code":"am","name":"Amharic"},"flag":"https://restcountries.eu/data/eth.svg"},{"name":"Falkland Islands (Malvinas)","code":"FK","capital":"Stanley","region":"SA","currency":{"code":"FKP","name":"Falkland Islands pound","symbol":"£"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/flk.svg"},{"name":"Faroe Islands","code":"FO","capital":"Tórshavn","region":"EU","currency":{"code":"DKK","name":"Danish krone","symbol":"kr"},"language":{"code":"fo","name":"Faroese"},"flag":"https://restcountries.eu/data/fro.svg"},{"name":"Fiji","code":"FJ","capital":"Suva","region":"OC","currency":{"code":"FJD","name":"Fijian dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/fji.svg"},{"name":"Finland","code":"FI","capital":"Helsinki","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"fi","iso639_2":"fin","name":"Finnish","nativeName":"suomi"},"flag":"https://restcountries.eu/data/fin.svg"},{"name":"France","code":"FR","capital":"Paris","region":"EU","demonym":"French","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/fra.svg"},{"name":"French Guiana","code":"GF","capital":"Cayenne","region":"SA","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/guf.svg"},{"name":"French Polynesia","code":"PF","capital":"Papeetē","region":"OC","currency":{"code":"XPF","name":"CFP franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/pyf.svg"},{"name":"French Southern Territories","code":"TF","capital":"Port-aux-Français","region":"AF","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/atf.svg"},{"name":"Gabon","code":"GA","capital":"Libreville","region":"AF","currency":{"code":"XAF","name":"Central African CFA franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/gab.svg"},{"name":"Gambia","code":"GM","capital":"Banjul","region":"AF","currency":{"code":"GMD","name":"Gambian dalasi","symbol":"D"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/gmb.svg"},{"name":"Georgia","code":"GE","capital":"Tbilisi","region":"AS","currency":{"code":"GEL","name":"Georgian Lari","symbol":"ლ"},"language":{"code":"ka","name":"Georgian"},"flag":"https://restcountries.eu/data/geo.svg"},{"name":"Germany","code":"DE","capital":"Berlin","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"de","name":"German"},"flag":"https://restcountries.eu/data/deu.svg"},{"name":"Ghana","code":"GH","capital":"Accra","region":"AF","currency":{"code":"GHS","name":"Ghanaian cedi","symbol":"₵"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/gha.svg"},{"name":"Gibraltar","code":"GI","capital":"Gibraltar","region":"EU","currency":{"code":"GIP","name":"Gibraltar pound","symbol":"£"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/gib.svg"},{"name":"Greece","code":"GR","capital":"Athens","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"el","name":"Greek (modern)"},"flag":"https://restcountries.eu/data/grc.svg"},{"name":"Greenland","code":"GL","capital":"Nuuk","region":"NA","currency":{"code":"DKK","name":"Danish krone","symbol":"kr"},"language":{"code":"kl","name":"Kalaallisut"},"flag":"https://restcountries.eu/data/grl.svg"},{"name":"Grenada","code":"GD","capital":"St. George\'s","region":"NA","currency":{"code":"XCD","name":"East Caribbean dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/grd.svg"},{"name":"Guadeloupe","code":"GP","capital":"Basse-Terre","region":"NA","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/glp.svg"},{"name":"Guam","code":"GU","capital":"Hagåtña","region":"OC","currency":{"code":"USD","name":"United States dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/gum.svg"},{"name":"Guatemala","code":"GT","capital":"Guatemala City","region":"NA","currency":{"code":"GTQ","name":"Guatemalan quetzal","symbol":"Q"},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/gtm.svg"},{"name":"Guernsey","code":"GG","capital":"St. Peter Port","region":"EU","currency":{"code":"GBP","name":"British pound","symbol":"£"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/ggy.svg"},{"name":"Guinea","code":"GN","capital":"Conakry","region":"AF","currency":{"code":"GNF","name":"Guinean franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/gin.svg"},{"name":"Guinea-Bissau","code":"GW","capital":"Bissau","region":"AF","currency":{"code":"XOF","name":"West African CFA franc","symbol":"Fr"},"language":{"code":"pt","name":"Portuguese"},"flag":"https://restcountries.eu/data/gnb.svg"},{"name":"Guyana","code":"GY","capital":"Georgetown","region":"SA","currency":{"code":"GYD","name":"Guyanese dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/guy.svg"},{"name":"Haiti","code":"HT","capital":"Port-au-Prince","region":"Americas","currency":{"code":"HTG","name":"Haitian gourde","symbol":"G"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/hti.svg"},{"name":"Heard Island and McDonald Islands","code":"HM","capital":"","region":"","currency":{"code":"AUD","name":"Australian dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/hmd.svg"},{"name":"Holy See","code":"VA","capital":"Rome","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/vat.svg"},{"name":"Honduras","code":"HN","capital":"Tegucigalpa","region":"NA","currency":{"code":"HNL","name":"Honduran lempira","symbol":"L"},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/hnd.svg"},{"name":"Hong Kong","code":"HK","capital":"City of Victoria","region":"AS","currency":{"code":"HKD","name":"Hong Kong dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/hkg.svg"},{"name":"Hungary","code":"HU","capital":"Budapest","region":"EU","currency":{"code":"HUF","name":"Hungarian forint","symbol":"Ft"},"language":{"code":"hu","name":"Hungarian"},"flag":"https://restcountries.eu/data/hun.svg"},{"name":"Iceland","code":"IS","capital":"Reykjavík","region":"EU","currency":{"code":"ISK","name":"Icelandic króna","symbol":"kr"},"language":{"code":"is","name":"Icelandic"},"flag":"https://restcountries.eu/data/isl.svg"},{"name":"India","code":"IN","capital":"New Delhi","region":"AS","currency":{"code":"INR","name":"Indian rupee","symbol":"₹"},"language":{"code":"hi","name":"Hindi"},"flag":"https://restcountries.eu/data/ind.svg"},{"name":"Indonesia","code":"ID","capital":"Jakarta","region":"AS","currency":{"code":"IDR","name":"Indonesian rupiah","symbol":"Rp"},"language":{"code":"id","name":"Indonesian"},"flag":"https://restcountries.eu/data/idn.svg"},{"name":"Côte d\'Ivoire","code":"CI","capital":"Yamoussoukro","region":"AF","currency":{"code":"XOF","name":"West African CFA franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/civ.svg"},{"name":"Iran (Islamic Republic of)","code":"IR","capital":"Tehran","region":"AS","currency":{"code":"IRR","name":"Iranian rial","symbol":"﷼"},"language":{"code":"fa","name":"Persian (Farsi)"},"flag":"https://restcountries.eu/data/irn.svg"},{"name":"Iraq","code":"IQ","capital":"Baghdad","region":"AS","currency":{"code":"IQD","name":"Iraqi dinar","symbol":"ع.د"},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/irq.svg"},{"name":"Ireland","code":"IE","capital":"Dublin","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"ga","name":"Irish"},"flag":"https://restcountries.eu/data/irl.svg"},{"name":"Isle of Man","code":"IM","capital":"Douglas","region":"EU","currency":{"code":"GBP","name":"British pound","symbol":"£"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/imn.svg"},{"name":"Israel","code":"IL","capital":"Jerusalem","region":"AS","currency":{"code":"ILS","name":"Israeli new shekel","symbol":"₪"},"language":{"code":"he","name":"Hebrew (modern)"},"flag":"https://restcountries.eu/data/isr.svg"},{"name":"Italy","code":"IT","capital":"Rome","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"it","name":"Italian"},"flag":"https://restcountries.eu/data/ita.svg"},{"name":"Jamaica","code":"JM","capital":"Kingston","region":"NA","currency":{"code":"JMD","name":"Jamaican dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/jam.svg"},{"name":"Japan","code":"JP","capital":"Tokyo","region":"AS","currency":{"code":"JPY","name":"Japanese yen","symbol":"¥"},"language":{"code":"ja","name":"Japanese"},"flag":"https://restcountries.eu/data/jpn.svg"},{"name":"Jersey","code":"JE","capital":"Saint Helier","region":"EU","currency":{"code":"GBP","name":"British pound","symbol":"£"},"language":{"code":"en","iso639_2":"eng","name":"English","nativeName":"English"},"flag":"https://restcountries.eu/data/jey.svg"},{"name":"Jordan","code":"JO","capital":"Amman","region":"AS","currency":{"code":"JOD","name":"Jordanian dinar","symbol":"د.ا"},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/jor.svg"},{"name":"Kazakhstan","code":"KZ","capital":"Astana","region":"AS","currency":{"code":"KZT","name":"Kazakhstani tenge","symbol":null},"language":{"code":"kk","name":"Kazakh"},"flag":"https://restcountries.eu/data/kaz.svg"},{"name":"Kenya","code":"KE","capital":"Nairobi","region":"AF","currency":{"code":"KES","name":"Kenyan shilling","symbol":"Sh"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/ken.svg"},{"name":"Kiribati","code":"KI","capital":"South Tarawa","region":"OC","currency":{"code":"AUD","name":"Australian dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/kir.svg"},{"name":"Kuwait","code":"KW","capital":"Kuwait City","region":"AS","currency":{"code":"KWD","name":"Kuwaiti dinar","symbol":"د.ك"},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/kwt.svg"},{"name":"Kyrgyzstan","code":"KG","capital":"Bishkek","region":"AS","currency":{"code":"KGS","name":"Kyrgyzstani som","symbol":"с"},"language":{"code":"ky","name":"Kyrgyz"},"flag":"https://restcountries.eu/data/kgz.svg"},{"name":"Lao People\'s Democratic Republic","code":"LA","capital":"Vientiane","region":"AS","currency":{"code":"LAK","name":"Lao kip","symbol":"₭"},"language":{"code":"lo","name":"Lao"},"flag":"https://restcountries.eu/data/lao.svg"},{"name":"Latvia","code":"LV","capital":"Riga","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"lv","name":"Latvian"},"flag":"https://restcountries.eu/data/lva.svg"},{"name":"Lebanon","code":"LB","capital":"Beirut","region":"AS","currency":{"code":"LBP","name":"Lebanese pound","symbol":"ل.ل"},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/lbn.svg"},{"name":"Lesotho","code":"LS","capital":"Maseru","region":"AF","currency":{"code":"LSL","name":"Lesotho loti","symbol":"L"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/lso.svg"},{"name":"Liberia","code":"LR","capital":"Monrovia","region":"AF","currency":{"code":"LRD","name":"Liberian dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/lbr.svg"},{"name":"Libya","code":"LY","capital":"Tripoli","region":"AF","currency":{"code":"LYD","name":"Libyan dinar","symbol":"ل.د"},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/lby.svg"},{"name":"Liechtenstein","code":"LI","capital":"Vaduz","region":"EU","currency":{"code":"CHF","name":"Swiss franc","symbol":"Fr"},"language":{"code":"de","name":"German"},"flag":"https://restcountries.eu/data/lie.svg"},{"name":"Lithuania","code":"LT","capital":"Vilnius","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"lt","name":"Lithuanian"},"flag":"https://restcountries.eu/data/ltu.svg"},{"name":"Luxembourg","code":"LU","capital":"Luxembourg","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/lux.svg"},{"name":"Macao","code":"MO","capital":"","region":"AS","currency":{"code":"MOP","name":"Macanese pataca","symbol":"P"},"language":{"code":"zh","name":"Chinese"},"flag":"https://restcountries.eu/data/mac.svg"},{"name":"Macedonia (the former Yugoslav Republic of)","code":"MK","capital":"Skopje","region":"EU","currency":{"code":"MKD","name":"Macedonian denar","symbol":"ден"},"language":{"code":"mk","name":"Macedonian"},"flag":"https://restcountries.eu/data/mkd.svg"},{"name":"Madagascar","code":"MG","capital":"Antananarivo","region":"AF","currency":{"code":"MGA","name":"Malagasy ariary","symbol":"Ar"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/mdg.svg"},{"name":"Malawi","code":"MW","capital":"Lilongwe","region":"AF","currency":{"code":"MWK","name":"Malawian kwacha","symbol":"MK"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/mwi.svg"},{"name":"Malaysia","code":"MY","capital":"Kuala Lumpur","region":"AS","currency":{"code":"MYR","name":"Malaysian ringgit","symbol":"RM"},"language":{"code":null,"name":"Malaysian"},"flag":"https://restcountries.eu/data/mys.svg"},{"name":"Maldives","code":"MV","capital":"Malé","region":"AS","currency":{"code":"MVR","name":"Maldivian rufiyaa","symbol":".ރ"},"language":{"code":"dv","name":"Divehi"},"flag":"https://restcountries.eu/data/mdv.svg"},{"name":"Mali","code":"ML","capital":"Bamako","region":"AF","currency":{"code":"XOF","name":"West African CFA franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/mli.svg"},{"name":"Malta","code":"MT","capital":"Valletta","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"mt","name":"Maltese"},"flag":"https://restcountries.eu/data/mlt.svg"},{"name":"Marshall Islands","code":"MH","capital":"Majuro","region":"OC","currency":{"code":"USD","name":"United States dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/mhl.svg"},{"name":"Martinique","code":"MQ","capital":"Fort-de-France","region":"Americas","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/mtq.svg"},{"name":"Mauritania","code":"MR","capital":"Nouakchott","region":"AF","currency":{"code":"MRO","name":"Mauritanian ouguiya","symbol":"UM"},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/mrt.svg"},{"name":"Mauritius","code":"MU","capital":"Port Louis","region":"AF","currency":{"code":"MUR","name":"Mauritian rupee","symbol":"₨"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/mus.svg"},{"name":"Mayotte","code":"YT","capital":"Mamoudzou","region":"AF","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/myt.svg"},{"name":"Mexico","code":"MX","capital":"Mexico City","region":"NA","currency":{"code":"MXN","name":"Mexican peso","symbol":"$"},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/mex.svg"},{"name":"Micronesia (Federated States of)","code":"FM","capital":"Palikir","region":"OC","currency":{"code":"USD","name":"United States dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/fsm.svg"},{"name":"Moldova (Republic of)","code":"MD","capital":"Chișinău","region":"EU","currency":{"code":"MDL","name":"Moldovan leu","symbol":"L"},"language":{"code":"ro","name":"Romanian"},"flag":"https://restcountries.eu/data/mda.svg"},{"name":"Monaco","code":"MC","capital":"Monaco","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/mco.svg"},{"name":"Mongolia","code":"MN","capital":"Ulan Bator","region":"AS","currency":{"code":"MNT","name":"Mongolian tögrög","symbol":"₮"},"language":{"code":"mn","name":"Mongolian"},"flag":"https://restcountries.eu/data/mng.svg"},{"name":"Montenegro","code":"ME","capital":"Podgorica","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"sr","name":"Serbian"},"flag":"https://restcountries.eu/data/mne.svg"},{"name":"Montserrat","code":"MS","capital":"Plymouth","region":"NA","currency":{"code":"XCD","name":"East Caribbean dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/msr.svg"},{"name":"Morocco","code":"MA","capital":"Rabat","region":"AF","currency":{"code":"MAD","name":"Moroccan dirham","symbol":"د.م."},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/mar.svg"},{"name":"Mozambique","code":"MZ","capital":"Maputo","region":"AF","currency":{"code":"MZN","name":"Mozambican metical","symbol":"MT"},"language":{"code":"pt","name":"Portuguese"},"flag":"https://restcountries.eu/data/moz.svg"},{"name":"Myanmar","code":"MM","capital":"Naypyidaw","region":"AS","currency":{"code":"MMK","name":"Burmese kyat","symbol":"Ks"},"language":{"code":"my","name":"Burmese"},"flag":"https://restcountries.eu/data/mmr.svg"},{"name":"Namibia","code":"NA","capital":"Windhoek","region":"AF","currency":{"code":"NAD","name":"Namibian dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/nam.svg"},{"name":"Nauru","code":"NR","capital":"Yaren","region":"OC","currency":{"code":"AUD","name":"Australian dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/nru.svg"},{"name":"Nepal","code":"NP","capital":"Kathmandu","region":"AS","currency":{"code":"NPR","name":"Nepalese rupee","symbol":"₨"},"language":{"code":"ne","name":"Nepali"},"flag":"https://restcountries.eu/data/npl.svg"},{"name":"Netherlands","code":"NL","capital":"Amsterdam","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"nl","name":"Dutch"},"flag":"https://restcountries.eu/data/nld.svg"},{"name":"New Caledonia","code":"NC","capital":"Nouméa","region":"OC","currency":{"code":"XPF","name":"CFP franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/ncl.svg"},{"name":"New Zealand","code":"NZ","capital":"Wellington","region":"OC","currency":{"code":"NZD","name":"New Zealand dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/nzl.svg"},{"name":"Nicaragua","code":"NI","capital":"Managua","region":"NA","currency":{"code":"NIO","name":"Nicaraguan córdoba","symbol":"C$"},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/nic.svg"},{"name":"Niger","code":"NE","capital":"Niamey","region":"AF","currency":{"code":"XOF","name":"West African CFA franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/ner.svg"},{"name":"Nigeria","code":"NG","capital":"Abuja","region":"AF","currency":{"code":"NGN","name":"Nigerian naira","symbol":"₦"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/nga.svg"},{"name":"Niue","code":"NU","capital":"Alofi","region":"OC","currency":{"code":"NZD","name":"New Zealand dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/niu.svg"},{"name":"Norfolk Island","code":"NF","capital":"Kingston","region":"OC","currency":{"code":"AUD","name":"Australian dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/nfk.svg"},{"name":"Korea (Democratic People\'s Republic of)","code":"KP","capital":"Pyongyang","region":"AS","currency":{"code":"KPW","name":"North Korean won","symbol":"₩"},"language":{"code":"ko","name":"Korean"},"flag":"https://restcountries.eu/data/prk.svg"},{"name":"Northern Mariana Islands","code":"MP","capital":"Saipan","region":"OC","currency":{"code":"USD","name":"United States dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/mnp.svg"},{"name":"Norway","code":"NO","capital":"Oslo","region":"EU","currency":{"code":"NOK","name":"Norwegian krone","symbol":"kr"},"language":{"code":"no","name":"Norwegian"},"flag":"https://restcountries.eu/data/nor.svg"},{"name":"Oman","code":"OM","capital":"Muscat","region":"AS","currency":{"code":"OMR","name":"Omani rial","symbol":"ر.ع."},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/omn.svg"},{"name":"Pakistan","code":"PK","capital":"Islamabad","region":"AS","currency":{"code":"PKR","name":"Pakistani rupee","symbol":"₨"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/pak.svg"},{"name":"Palau","code":"PW","capital":"Ngerulmud","region":"OC","currency":{"code":"USD","name":"United States dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/plw.svg"},{"name":"Palestine, State of","code":"PS","capital":"Ramallah","region":"AS","currency":{"code":"ILS","name":"Israeli new sheqel","symbol":"₪"},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/pse.svg"},{"name":"Panama","code":"PA","capital":"Panama City","region":"NA","currency":{"code":"USD","name":"United States dollar","symbol":"$"},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/pan.svg"},{"name":"Papua New Guinea","code":"PG","capital":"Port Moresby","region":"OC","currency":{"code":"PGK","name":"Papua New Guinean kina","symbol":"K"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/png.svg"},{"name":"Paraguay","code":"PY","capital":"Asunción","region":"SA","currency":{"code":"PYG","name":"Paraguayan guaraní","symbol":"₲"},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/pry.svg"},{"name":"Peru","code":"PE","capital":"Lima","region":"SA","currency":{"code":"PEN","name":"Peruvian sol","symbol":"S/."},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/per.svg"},{"name":"Philippines","code":"PH","capital":"Manila","region":"AS","currency":{"code":"PHP","name":"Philippine peso","symbol":"₱"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/phl.svg"},{"name":"Pitcairn","code":"PN","capital":"Adamstown","region":"OC","currency":{"code":"NZD","name":"New Zealand dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/pcn.svg"},{"name":"Poland","code":"PL","capital":"Warsaw","region":"EU","currency":{"code":"PLN","name":"Polish złoty","symbol":"zł"},"language":{"code":"pl","name":"Polish"},"flag":"https://restcountries.eu/data/pol.svg"},{"name":"Portugal","code":"PT","capital":"Lisbon","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"pt","name":"Portuguese"},"flag":"https://restcountries.eu/data/prt.svg"},{"name":"Puerto Rico","code":"PR","capital":"San Juan","region":"NA","currency":{"code":"USD","name":"United States dollar","symbol":"$"},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/pri.svg"},{"name":"Qatar","code":"QA","capital":"Doha","region":"AS","currency":{"code":"QAR","name":"Qatari riyal","symbol":"ر.ق"},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/qat.svg"},{"name":"Republic of Kosovo","code":"XK","capital":"Pristina","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"sq","name":"Albanian"},"flag":"https://restcountries.eu/data/kos.svg"},{"name":"Réunion","code":"RE","capital":"Saint-Denis","region":"AF","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/reu.svg"},{"name":"Romania","code":"RO","capital":"Bucharest","region":"EU","currency":{"code":"RON","name":"Romanian leu","symbol":"lei"},"language":{"code":"ro","name":"Romanian"},"flag":"https://restcountries.eu/data/rou.svg"},{"name":"Russian Federation","code":"RU","capital":"Moscow","region":"EU","currency":{"code":"RUB","name":"Russian ruble","symbol":"₽"},"language":{"code":"ru","name":"Russian"},"flag":"https://restcountries.eu/data/rus.svg"},{"name":"Rwanda","code":"RW","capital":"Kigali","region":"AF","currency":{"code":"RWF","name":"Rwandan franc","symbol":"Fr"},"language":{"code":"rw","name":"Kinyarwanda"},"flag":"https://restcountries.eu/data/rwa.svg"},{"name":"Saint Barthélemy","code":"BL","capital":"Gustavia","region":"NA","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/blm.svg"},{"name":"Saint Helena, Ascension and Tristan da Cunha","code":"SH","capital":"Jamestown","region":"AF","currency":{"code":"SHP","name":"Saint Helena pound","symbol":"£"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/shn.svg"},{"name":"Saint Kitts and Nevis","code":"KN","capital":"Basseterre","region":"NA","currency":{"code":"XCD","name":"East Caribbean dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/kna.svg"},{"name":"Saint Lucia","code":"LC","capital":"Castries","region":"NA","currency":{"code":"XCD","name":"East Caribbean dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/lca.svg"},{"name":"Saint Martin (French part)","code":"MF","capital":"Marigot","region":"NA","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/maf.svg"},{"name":"Saint Pierre and Miquelon","code":"PM","capital":"Saint-Pierre","region":"NA","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/spm.svg"},{"name":"Saint Vincent and the Grenadines","code":"VC","capital":"Kingstown","region":"NA","currency":{"code":"XCD","name":"East Caribbean dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/vct.svg"},{"name":"Samoa","code":"WS","capital":"Apia","region":"OC","currency":{"code":"WST","name":"Samoan tālā","symbol":"T"},"language":{"code":"sm","name":"Samoan"},"flag":"https://restcountries.eu/data/wsm.svg"},{"name":"San Marino","code":"SM","capital":"City of San Marino","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"it","name":"Italian"},"flag":"https://restcountries.eu/data/smr.svg"},{"name":"Sao Tome and Principe","code":"ST","capital":"São Tomé","region":"AF","currency":{"code":"STD","name":"São Tomé and Príncipe dobra","symbol":"Db"},"language":{"code":"pt","name":"Portuguese"},"flag":"https://restcountries.eu/data/stp.svg"},{"name":"Saudi Arabia","code":"SA","capital":"Riyadh","region":"AS","currency":{"code":"SAR","name":"Saudi riyal","symbol":"ر.س"},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/sau.svg"},{"name":"Senegal","code":"SN","capital":"Dakar","region":"AF","currency":{"code":"XOF","name":"West African CFA franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/sen.svg"},{"name":"Serbia","code":"RS","capital":"Belgrade","region":"EU","currency":{"code":"RSD","name":"Serbian dinar","symbol":"дин."},"language":{"code":"sr","name":"Serbian"},"flag":"https://restcountries.eu/data/srb.svg"},{"name":"Seychelles","code":"SC","capital":"Victoria","region":"AF","currency":{"code":"SCR","name":"Seychellois rupee","symbol":"₨"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/syc.svg"},{"name":"Sierra Leone","code":"SL","capital":"Freetown","region":"AF","currency":{"code":"SLL","name":"Sierra Leonean leone","symbol":"Le"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/sle.svg"},{"name":"Singapore","code":"SG","capital":"Singapore","region":"AS","currency":{"code":"SGD","name":"Singapore dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/sgp.svg"},{"name":"Sint Maarten (Dutch part)","code":"SX","capital":"Philipsburg","region":"Americas","currency":{"code":"ANG","name":"Netherlands Antillean guilder","symbol":"ƒ"},"language":{"code":"nl","name":"Dutch"},"flag":"https://restcountries.eu/data/sxm.svg"},{"name":"Slovakia","code":"SK","capital":"Bratislava","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"sk","name":"Slovak"},"flag":"https://restcountries.eu/data/svk.svg"},{"name":"Slovenia","code":"SI","capital":"Ljubljana","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"sl","name":"Slovene"},"flag":"https://restcountries.eu/data/svn.svg"},{"name":"Solomon Islands","code":"SB","capital":"Honiara","region":"OC","currency":{"code":"SBD","name":"Solomon Islands dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/slb.svg"},{"name":"Somalia","code":"SO","capital":"Mogadishu","region":"AF","currency":{"code":"SOS","name":"Somali shilling","symbol":"Sh"},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/som.svg"},{"name":"South Africa","code":"ZA","capital":"Pretoria","region":"AF","currency":{"code":"ZAR","name":"South African rand","symbol":"R"},"language":{"code":"en","iso639_2":"eng","name":"English","nativeName":"English"},"flag":"https://restcountries.eu/data/zaf.svg"},{"name":"South Georgia and the South Sandwich Islands","code":"GS","capital":"King Edward Point","region":"NA","currency":{"code":"GBP","name":"British pound","symbol":"£"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/sgs.svg"},{"name":"Korea (Republic of)","code":"KR","capital":"Seoul","region":"AS","currency":{"code":"KRW","name":"South Korean won","symbol":"₩"},"language":{"code":"ko","name":"Korean"},"flag":"https://restcountries.eu/data/kor.svg"},{"name":"South Sudan","code":"SS","capital":"Juba","region":"AF","currency":{"code":"SSP","name":"South Sudanese pound","symbol":"£"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/ssd.svg"},{"name":"Spain","code":"ES","capital":"Madrid","region":"EU","currency":{"code":"EUR","name":"Euro","symbol":"€"},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/esp.svg"},{"name":"Sri Lanka","code":"LK","capital":"Colombo","region":"AS","currency":{"code":"LKR","name":"Sri Lankan rupee","symbol":"Rs"},"language":{"code":"si","iso639_2":"sin","name":"Sinhalese","nativeName":"සිංහල"},"flag":"https://restcountries.eu/data/lka.svg"},{"name":"Sudan","code":"SD","capital":"Khartoum","region":"AF","currency":{"code":"SDG","name":"Sudanese pound","symbol":"ج.س."},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/sdn.svg"},{"name":"Suriname","code":"SR","capital":"Paramaribo","region":"SA","currency":{"code":"SRD","name":"Surinamese dollar","symbol":"$"},"language":{"code":"nl","name":"Dutch"},"flag":"https://restcountries.eu/data/sur.svg"},{"name":"Svalbard and Jan Mayen","code":"SJ","capital":"Longyearbyen","region":"EU","currency":{"code":"NOK","name":"Norwegian krone","symbol":"kr"},"language":{"code":"no","name":"Norwegian"},"flag":"https://restcountries.eu/data/sjm.svg"},{"name":"Swaziland","code":"SZ","capital":"Lobamba","region":"AF","currency":{"code":"SZL","name":"Swazi lilangeni","symbol":"L"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/swz.svg"},{"name":"Sweden","code":"SE","capital":"Stockholm","region":"EU","currency":{"code":"SEK","name":"Swedish krona","symbol":"kr"},"language":{"code":"sv","name":"Swedish"},"flag":"https://restcountries.eu/data/swe.svg"},{"name":"Switzerland","code":"CH","capital":"Bern","region":"EU","currency":{"code":"CHF","name":"Swiss franc","symbol":"Fr"},"language":{"code":"de","name":"German"},"flag":"https://restcountries.eu/data/che.svg"},{"name":"Syrian Arab Republic","code":"SY","capital":"Damascus","region":"AS","currency":{"code":"SYP","name":"Syrian pound","symbol":"£"},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/syr.svg"},{"name":"Taiwan","code":"TW","capital":"Taipei","region":"AS","currency":{"code":"TWD","name":"New Taiwan dollar","symbol":"$"},"language":{"code":"zh","name":"Chinese"},"flag":"https://restcountries.eu/data/twn.svg"},{"name":"Tajikistan","code":"TJ","capital":"Dushanbe","region":"AS","currency":{"code":"TJS","name":"Tajikistani somoni","symbol":"ЅМ"},"language":{"code":"tg","name":"Tajik"},"flag":"https://restcountries.eu/data/tjk.svg"},{"name":"Tanzania, United Republic of","code":"TZ","capital":"Dodoma","region":"AF","currency":{"code":"TZS","name":"Tanzanian shilling","symbol":"Sh"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/tza.svg"},{"name":"Thailand","code":"TH","capital":"Bangkok","region":"AS","currency":{"code":"THB","name":"Thai baht","symbol":"฿"},"language":{"code":"th","name":"Thai"},"flag":"https://restcountries.eu/data/tha.svg"},{"name":"Timor-Leste","code":"TL","capital":"Dili","region":"AS","currency":{"code":"USD","name":"United States dollar","symbol":"$"},"language":{"code":"pt","name":"Portuguese"},"flag":"https://restcountries.eu/data/tls.svg"},{"name":"Togo","code":"TG","capital":"Lomé","region":"AF","currency":{"code":"XOF","name":"West African CFA franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/tgo.svg"},{"name":"Tokelau","code":"TK","capital":"Fakaofo","region":"OC","currency":{"code":"NZD","name":"New Zealand dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/tkl.svg"},{"name":"Tonga","code":"TO","capital":"Nuku\'alofa","region":"OC","currency":{"code":"TOP","name":"Tongan paʻanga","symbol":"T$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/ton.svg"},{"name":"Trinidad and Tobago","code":"TT","capital":"Port of Spain","region":"SA","currency":{"code":"TTD","name":"Trinidad and Tobago dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/tto.svg"},{"name":"Tunisia","code":"TN","capital":"Tunis","region":"AF","currency":{"code":"TND","name":"Tunisian dinar","symbol":"د.ت"},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/tun.svg"},{"name":"Turkey","code":"TR","capital":"Ankara","region":"AS","currency":{"code":"TRY","name":"Turkish lira","symbol":null},"language":{"code":"tr","name":"Turkish"},"flag":"https://restcountries.eu/data/tur.svg"},{"name":"Turkmenistan","code":"TM","capital":"Ashgabat","region":"AS","currency":{"code":"TMT","name":"Turkmenistan manat","symbol":"m"},"language":{"code":"tk","name":"Turkmen"},"flag":"https://restcountries.eu/data/tkm.svg"},{"name":"Turks and Caicos Islands","code":"TC","capital":"Cockburn Town","region":"NA","currency":{"code":"USD","name":"United States dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/tca.svg"},{"name":"Tuvalu","code":"TV","capital":"Funafuti","region":"OC","currency":{"code":"AUD","name":"Australian dollar","symbol":"$"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/tuv.svg"},{"name":"Uganda","code":"UG","capital":"Kampala","region":"AF","currency":{"code":"UGX","name":"Ugandan shilling","symbol":"Sh"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/uga.svg"},{"name":"Ukraine","code":"UA","capital":"Kiev","region":"EU","currency":{"code":"UAH","name":"Ukrainian hryvnia","symbol":"₴"},"language":{"code":"uk","name":"Ukrainian"},"flag":"https://restcountries.eu/data/ukr.svg"},{"name":"United Arab Emirates","code":"AE","capital":"Abu Dhabi","region":"AS","currency":{"code":"AED","name":"United Arab Emirates dirham","symbol":"د.إ"},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/are.svg"},{"name":"United Kingdom of Great Britain and Northern Ireland","code":"GB","capital":"London","region":"EU","currency":{"code":"GBP","name":"British pound","symbol":"£"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/gbr.svg"},{"name":"United States of America","code":"US","capital":"Washington, D.C.","region":"NA","currency":{"code":"USD","name":"United States dollar","symbol":"$"},"language":{"code":"en","iso639_2":"eng","name":"English","nativeName":"English"},"flag":"https://restcountries.eu/data/usa.svg"},{"name":"Uruguay","code":"UY","capital":"Montevideo","region":"SA","currency":{"code":"UYU","name":"Uruguayan peso","symbol":"$"},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/ury.svg"},{"name":"Uzbekistan","code":"UZ","capital":"Tashkent","region":"AS","currency":{"code":"UZS","name":"Uzbekistani so\'m","symbol":null},"language":{"code":"uz","name":"Uzbek"},"flag":"https://restcountries.eu/data/uzb.svg"},{"name":"Vanuatu","code":"VU","capital":"Port Vila","region":"OC","currency":{"code":"VUV","name":"Vanuatu vatu","symbol":"Vt"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/vut.svg"},{"name":"Venezuela (Bolivarian Republic of)","code":"VE","capital":"Caracas","region":"SA","currency":{"code":"VEF","name":"Venezuelan bolívar","symbol":"Bs F"},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/ven.svg"},{"name":"Viet Nam","code":"VN","capital":"Hanoi","region":"AS","currency":{"code":"VND","name":"Vietnamese đồng","symbol":"₫"},"language":{"code":"vi","name":"Vietnamese"},"flag":"https://restcountries.eu/data/vnm.svg"},{"name":"Wallis and Futuna","code":"WF","capital":"Mata-Utu","region":"OC","currency":{"code":"XPF","name":"CFP franc","symbol":"Fr"},"language":{"code":"fr","name":"French"},"flag":"https://restcountries.eu/data/wlf.svg"},{"name":"Western Sahara","code":"EH","capital":"El Aaiún","region":"AF","currency":{"code":"MAD","name":"Moroccan dirham","symbol":"د.م."},"language":{"code":"es","name":"Spanish"},"flag":"https://restcountries.eu/data/esh.svg"},{"name":"Yemen","code":"YE","capital":"Sana\'a","region":"AS","currency":{"code":"YER","name":"Yemeni rial","symbol":"﷼"},"language":{"code":"ar","name":"Arabic"},"flag":"https://restcountries.eu/data/yem.svg"},{"name":"Zambia","code":"ZM","capital":"Lusaka","region":"AF","currency":{"code":"ZMW","name":"Zambian kwacha","symbol":"ZK"},"language":{"code":"en","name":"English"},"flag":"https://restcountries.eu/data/zmb.svg"},{"name":"Zimbabwe","code":"ZW","capital":"Harare","region":"AF","currency":{"code":"BWP","name":"Botswana pula","symbol":"P"},"language":{"code":"en","iso639_2":"eng","name":"English","nativeName":"English"},"flag":"https://restcountries.eu/data/zwe.svg"}]');
}

/**
 * get status purchase orders
 *
 * @return array
 */
function status_purchase_orders(): array
{
    return PO_STATUS;
}

/**
 * get status purchase orders details
 *
 * @return array
 */
function status_purchase_order_details(): array
{
    return PO_SO_DETAIL_STATUS;
}

/**
 * get status sale orders
 *
 * @return array
 */
function status_sale_orders(): array
{
    return SO_STATUS;
}

/**
 * get status sale orders details
 *
 * @return array
 */
function status_sale_orders_details(): array
{
    return PO_SO_DETAIL_STATUS;
}

/**
 * get invoice status
 *
 * @return array
 */
function pairing_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for pairing',
            'style' => null,
        ],
        'ready' => [
            'label' => 'Ready',
            'color' => 'primary',
            'text' => 'Ready for pairing',
            'style' => null,
        ],
        'pairing' => [
            'label' => 'Pairing',
            'color' => 'primary',
            'text' => 'Waiting for pairing',
            'style' => null,
        ],
        'partial' => [
            'label' => 'Partial',
            'color' => 'info',
            'text' => 'Some of your orders were paired',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'text' => 'Your order was void',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'dark',
            'text' => 'Your order was rejected',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Pairing Complete',
            'style' => null,
        ],
    ];
}


/**
 * get invoice status
 *
 * @return array
 */
function get_invoice_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'secondary',
            'text' => 'Waiting for Approval',
            'style' => null,
        ],
        'cancel' => [
            'label' => 'Cancel',
            'color' => 'danger',
            'text' => 'Your invoice was canceled',
            'style' => null,
        ],
        'unpaid' => [
            'label' => 'unpaid',
            'color' => 'warning',
            'text' => 'Your invoice was unpaid',
            'style' => null,
        ],
        'partial-paid' => [
            'label' => 'partial paid',
            'color' => 'warning',
            'text' => 'invoice has been partially paid',
            'style' => null,
        ],
        'paid' => [
            'label' => 'paid',
            'color' => 'success',
            'text' => 'Your invoice was done',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'danger',
            'text' => 'Your invoice was rejected',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'success',
            'text' => 'Approved',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Your invoice was successful',
            'style' => null,
        ],
        'revert' => [
            'label' => 'Revert',
            'color' => 'warning',
            'text' => 'Your invoice was successful',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'text' => 'Your invoice was void',
            'style' => null,
        ],
    ];
}

/**
 * payment_status
 *
 * @return array
 */

function payment_status(): array
{
    return [
        'unpaid' => [
            'label' => 'Unpaid',
            'color' => 'danger',
            'text' => 'waiting for payment',
            'style' => null,
        ],
        'partial-paid' => [
            'label' => 'Partial Paid',
            'color' => 'warning',
            'text' => 'some of your payment was paid',
            'style' => null,
        ],
        'paid' => [
            'label' => 'Paid',
            'color' => 'success',
            'text' => 'Your payment was successfully completed',
            'style' => null,
        ],
    ];
}

/**
 * get invoice status
 *
 * @return array
 */

function sale_order_trading_status(): array
{
    return SO_STATUS;
}

function sale_order_general_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'secondary',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'cancel' => [
            'label' => 'Cancel',
            'color' => 'danger',
            'text' => 'General Sales Order Canceled',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'danger',
            'text' => 'General Sales Order Rejected',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'success',
            'text' => 'General Sales Order Approved',
            'style' => null,
        ],
        'partial-sent' => [
            'label' => 'Partial',
            'color' => 'warning',
            'text' => 'Partially Sent',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'info',
            'text' => 'General Sales Order Completed',
            'style' => null,
        ],
        'revert' => [
            'label' => 'Revert',
            'color' => 'warning',
            'text' => 'General Sales Order Reverted',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'dark',
            'text' => 'General Sales Order Canceled',
            'style' => null,
        ],
    ];
}

/**
 * get employee positions
 *
 * @return array
 */
function get_employee_positions(): array
{
    return ['Direktur utama', 'Trading manager', 'Trading staff', 'Operasional manager', 'Operasional staff', 'Transporter manager', 'Transporter SPV', 'Driver', 'Mekanik'];
}

/**
 * get item staus
 *
 * @return array
 */
function get_item_status(): array
{
    return ITEM_STATUS;
}

/**
 * get delivery order status
 *
 * @return array
 */
function get_delivery_order_status(): array
{
    return DO_STATUS;
}

/**
 * get delivery order send with
 *
 * @return array
 */
function get_delivery_order_send_with(): array
{
    return [
        'vendor' => 'Vendor',
        'pribadi' => 'Own Use',
    ];
}

/**
 * purchase request status
 *
 * @return array
 */
function purchase_request_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'info',
            'text' => 'Your request has been approved',
            'style' => null,
        ],
        'partial' => [
            'label' => 'Partial',
            'color' => 'info',
            'text' => 'Some of your requests are partially done',
            'style' => null,
        ],
        'partial-rejected' => [
            'label' => 'Partial rejected',
            'color' => 'primary',
            'text' => 'Some of your requests were rejected',
            'style' => null,
        ],
        'partial-approve' => [
            'label' => 'Partial approve',
            'color' => 'primary',
            'text' => 'Some of your requests were approved',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'dark',
            'text' => 'Request rejected',
            'style' => null,
        ],
        'revert' => [
            'label' => 'Revert',
            'color' => 'dark',
            'text' => 'Request reverted',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'text' => 'Request void',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Request Completed',
            'style' => null,
        ],
        'return-all' => [
            'label' => 'Returned',
            'color' => 'dark',
            'text' => 'All returned',
            'style' => null,
        ],
    ];
}

/**
 * labor demand status
 *
 * @return array
 */
function labor_demand_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'info',
            'text' => 'Your request has been approved',
            'style' => null,
        ],
        'partial' => [
            'label' => 'Partial',
            'color' => 'info',
            'text' => 'Some of your requests are done',
            'style' => null,
        ],
        'partial-rejected' => [
            'label' => 'Partial rejected',
            'color' => 'primary',
            'text' => 'Some of your requests were rejected',
            'style' => null,
        ],
        'partial-approve' => [
            'label' => 'Partial approve',
            'color' => 'primary',
            'text' => 'Some of your requests were approved',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'dark',
            'text' => 'Request rejected',
            'style' => null,
        ],
        'revert' => [
            'label' => 'Revert',
            'color' => 'dark',
            'text' => 'Request reverted',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'text' => 'Request void',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Request Completed',
            'style' => null,
        ],
    ];
}

/**
 * purchase request status
 *
 * @return array
 */
function purchase_service_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'info',
            'text' => 'Your request has been approved',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'dark',
            'text' => 'Purchase rejected',
            'style' => null,
        ],
        'partial' => [
            'label' => 'Partial',
            'color' => 'info',
            'text' => 'Some of your purchases have been completed',
            'style' => null,
        ],
        'partial-approve' => [
            'label' => 'Partial Approve',
            'color' => 'info',
            'text' => 'Some of your purchases have been approved',
            'style' => null,
        ],
        'partial-rejected' => [
            'label' => 'Partial Reject',
            'color' => 'dark',
            'text' => 'Some of your purchases have been rejected',
            'style' => null,
        ],
        'revert' => [
            'label' => 'Revert',
            'color' => 'dark',
            'text' => 'Purchase reverted',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'text' => 'Purchase void',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Purchase Completed',
            'style' => null,
        ],
        'close' => [
            'label' => 'Close',
            'color' => 'success',
            'text' => 'Purchase Closed',
            'style' => null,
        ]
    ];
}

/**
 * purchase request status
 *
 * @return array
 */
function cash_advance_return(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'revert' => [
            'label' => 'Revert',
            'color' => 'dark',
            'text' => 'Di revert',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'info',
            'text' => 'Approved',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'dark',
            'text' => 'Rejected',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'text' => 'Canceled',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Telah Done',
            'style' => null,
        ],
    ];
}

/**
 * purchase request status
 *
 * @return array
 */
function cash_bond_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'revert' => [
            'label' => 'Revert',
            'color' => 'dark',
            'text' => 'Your request has been revert',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'info',
            'text' => 'Your request has been approved',
            'style' => null,
        ],
        'partial' => [
            'label' => 'Partial',
            'color' => 'info',
            'text' => 'Cash bond returned partial',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'dark',
            'text' => 'Purchase rejected',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'text' => 'Purchase Void',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Cash bond complete',
            'style' => null,
        ],
    ];
}
/**
 * purchase request status
 *
 * @return array
 */
function cash_bond_return_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'revert' => [
            'label' => 'Revert',
            'color' => 'dark',
            'text' => 'Your request has been revert',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'info',
            'text' => 'Your request has been approved',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'dark',
            'text' => 'Purchase rejected',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'text' => 'Purchase Void',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Purchase Completed',
            'style' => null,
        ],
    ];
}

/**
 * purchase request status
 *
 * @return array
 */
function purchase_transport_status(): array
{
    // Return Before
    // return [
    //     'pending' => [
    //         'label' => 'Pending',
    //         'color' => 'warning',
    //         'text' => 'Waiting for approval',
    //         'style' => null,
    //     ],
    //     'approve' => [
    //         'label' => 'Approve',
    //         'color' => 'info',
    //         'text' => 'Your request has been approved',
    //         'style' => null,
    //     ],
    //     'partial-sent' => [
    //         'label' => 'Partial Sent',
    //         'color' => 'info',
    //         'text' => 'some of your delivery order has been sent',
    //         'style' => null,
    //     ],
    //     'reject' => [
    //         'label' => 'Reject',
    //         'color' => 'dark',
    //         'text' => 'Purchase rejected',
    //         'style' => null,
    //     ],
    //     'void' => [
    //         'label' => 'Void',
    //         'color' => 'danger',
    //         'text' => 'Purchase Void',
    //         'style' => null,
    //     ],
    //     'done' => [
    //         'label' => 'Done',
    //         'color' => 'success',
    //         'text' => 'Purchase Completed',
    //         'style' => null,
    //     ],
    //     'ready' => [
    //         'label' => 'Ready',
    //         'color' => 'info',
    //         'text' => 'Your request is ready for delivery',
    //         'style' => null,
    //     ],
    // ];

    // Return after
    return PO_STATUS;
}

/**
 * purchase request status
 *
 * @return array
 */
function purchase_general_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'partial' => [
            'label' => 'Partial',
            'color' => 'warning',
            'text' => 'Partial',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'info',
            'text' => 'Your request has been approved',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'dark',
            'text' => 'Purchase rejected',
            'style' => null,
        ],
        'cancel' => [
            'label' => 'Cancel',
            'color' => 'danger',
            'text' => 'Purchase Canceled',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Purchase Completed',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Purchase Completed',
            'style' => null,
        ],
        'close' => [
            'label' => 'Close',
            'color' => 'success',
            'text' => 'Purchase Closed',
            'style' => null,
        ],
    ];
}

/**
 * purchase request status
 *
 * @return array
 */
function purchase_request_detail_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'waiting' => [
            'label' => 'Waiting',
            'color' => 'info',
            'text' => 'Waiting for purchase order',
            'style' => null,
        ],
        'cancel' => [
            'label' => 'Cancel',
            'color' => 'danger',
            'text' => 'Request Canceled',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Request Completed',
            'style' => null,
        ],
    ];
}

/**
 * purchase order general status
 *
 * @return array
 */
function purchase_order_general_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'info',
            'text' => 'Purchase has been approved.',
            'style' => null,
        ],
        'partial' => [
            'label' => 'Partial',
            'color' => 'primary',
            'text' => 'Some of your purchase was received.',
            'style' => null,
        ],
        'partial-approve' => [
            'label' => 'Partial approve',
            'color' => 'primary',
            'text' => 'Some of your purchase was approved.',
            'style' => null,
        ],
        'partial-rejected' => [
            'label' => 'Partial reject',
            'color' => 'primary',
            'text' => 'Some of your purchase was rejected.',
            'style' => null,
        ],
        'revert' => [
            'label' => 'Revert',
            'color' => 'dark',
            'text' => 'Purchase revert',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'text' => 'Purchase void',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'dark',
            'text' => 'Purchase rejected',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Purchase Completed',
            'style' => null,
        ],
        'close' => [
            'label' => 'Close',
            'color' => 'success',
            'text' => 'Purchase Closed',
            'style' => null,
        ],
    ];
}

/**
 * stock usage status
 *
 * @return array
 */
function stock_usage_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'info',
            'text' => 'Approved',
            'style' => null,
        ],
        'revert' => [
            'label' => 'Revert',
            'color' => 'dark',
            'text' => 'Request revert',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'text' => 'Request void',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'dark',
            'text' => 'Request rejected',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Request Completed',
            'style' => null,
        ],
    ];
}

/**
 * item receiving report status
 *
 * @return array
 */
function item_report_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'info',
            'text' => 'Item receiving report approved',
            'style' => null,
        ],
        'revert' => [
            'label' => 'Revert',
            'color' => 'dark',
            'text' => 'Item receiving report revert',
            'style' => null,
        ],
        'void' => [
            'label' => 'void',
            'color' => 'danger',
            'text' => 'Item receiving report Void',
            'style' => null,
        ],
        'reject' => [
            'label' => 'reject',
            'color' => 'dark',
            'text' => 'Item receiving report reject',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Item receiving report Completed',
            'style' => null,
        ],
        'return-all' => [
            'label' => 'Return All',
            'color' => 'primary',
            'text' => 'All returned',
            'style' => null,
        ],
    ];
}

/**
 * item receiving report status
 *
 * @return array
 */
function permission_letter_employee_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'info',
            'text' => 'Item receiving report approved',
            'style' => null,
        ],
        'reject' => [
            'label' => 'reject',
            'color' => 'dark',
            'text' => 'Item receiving report reject',
            'style' => null,
        ]
    ];
}

/**
 * journal
 *
 * @return array
 */
function journal_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'info',
            'text' => 'The Journal Was Approved',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'dark',
            'text' => 'Journal rejected',
            'style' => null,
        ],
        'cancel' => [
            'label' => 'Cancel',
            'color' => 'danger',
            'text' => 'Journal Canceled',
            'style' => null,
        ],
        'revert' => [
            'label' => 'Revert',
            'color' => 'dark',
            'text' => 'Journal reverted',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'text' => 'Journal void',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Request Completed',
            'style' => null,
        ],
    ];
}

/**
 * journal
 *
 * @return array
 */
function delivery_order_general_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'info',
            'text' => 'The Delivery Was Approved',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'dark',
            'text' => 'Delivery rejected',
            'style' => null,
        ],
        'cancel' => [
            'label' => 'Cancel',
            'color' => 'danger',
            'text' => 'Delivery Canceled',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'text' => 'Delivery void',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Delivery Completed',
            'style' => null,
        ],
        'revert' => [
            'label' => 'Dikembalikan',
            'color' => 'dark',
            'text' => 'Revert',
            'style' => null,
        ],
    ];
}

/**
 * journal
 *
 * @return array
 */
function delivery_order_ship_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'info',
            'text' => 'The Delivery Was Approved',
            'style' => null,
        ],
        'partial-used' => [
            'label' => 'Partial',
            'color' => 'info',
            'text' => 'The Delivery was partial used',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'dark',
            'text' => 'Delivery rejected',
            'style' => null,
        ],
        'cancel' => [
            'label' => 'Cancel',
            'color' => 'danger',
            'text' => 'Delivery Canceled',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'text' => 'Delivery void',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Delivery Completed',
            'style' => null,
        ],
    ];
}

function account_type()
{
    return [
        'activa',
        'pasiva',
        'equity',
        'revenue',
        'expense',
    ];
}

function get_coa_types()
{
    return [
        'activa' => [
            'Cash & Bank',
            'Receivable',
            'Inventory',
            'Other Current Asset',
            'Fixed Asset',
            'Accumulated Depreciation',
            'Other Asset',
        ],
        'pasiva' => [
            'Payable',
            'Other Current Liability',
            'Long Term Liability',
        ],
        'equity' => [
            'Equity',
        ],
        'revenue' => [
            'Revenue',
            'Other Income',
        ],
        'expense' => [
            'Cost Of Good Sold',
            'Expense',
            'Other Expense',
        ],
    ];
}

/**
 * customer coa types
 *
 * @return array
 */
function customer_coa_types(): array
{
    return [
        Str::headline('Account Receivable Coa'),
        Str::headline('Sale Discounts Coa'),
        Str::headline('Customer Deposite Coa'),
    ];
}

function lost_tolerance_types()
{
    return [
        'percent',
        'liter',
    ];
}

function customerTypes()
{
    return [
        'general',
        'trading',
    ];
}

/**
 * vendor coa types
 *
 * @return array
 */
function vendor_coa_types(): array
{
    return [
        Str::headline('Account Payable Coa'),
        Str::headline('Purchase Discounts Coa'),
        Str::headline('Vendor Deposite Coa'),
    ];
}


/**
 * item type coas
 *
 * @return array
 */
function item_type_coas(): array
{
    return [
        'purchase item' => [
            'sales',
            'inventory',
            'work in progress',
            'hpp',
            'sales return',
            'purchase inventory return',
            'expense'
        ],
        'service' => [
            'sales',
            'expense',
        ],
        'asset' => [
            'asset'
        ],
        'biaya dibayar dimuka' => [
            'biaya dibayar dimuka'
        ],
    ];
}


/**
 * tax types
 *
 */
function tax_types(): array
{
    return [
        Str::headline('value added tax'),
        Str::headline('Luxury goods addition tax'),
        Str::headline('income tax article (4) 2'),
        Str::headline('income tax article 15'),
        Str::headline('income tax article 21'),
        Str::headline('income tax article 22'),
        Str::headline('income tax article 23'),
    ];
}


/**
 * invoice coa types
 *
 * @return array
 */
function invoice_coa_type()
{
    return [
        'coa_sale', // item type
        'coa_tax',
        'coa_customer', // customer, account receiveable coa
        'coa_sale_discount', // customer, sale discount coa
        'coa_revenue', // master
        'coa_cash_different', // master
    ];
}

/**
 * purchase_trading_coa
 *
 * @return array
 */
function purchase_coa(): array
{
    return [
        'coa_inventory',
        'coa_tax',
        'coa_vendor',
        'coa_purchase_discount',
    ];
}

/**
 * incoming payment
 *
 * @return array
 */
function incoming_payment_status(): array
{
    return [
        'pending' => [
            'label' => 'Waiting for approval',
            'color' => 'warning',
            'text' => 'Pending',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approved',
            'color' => 'info',
            'text' => 'Approve',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Rejected',
            'color' => 'dark',
            'text' => 'Reject',
            'style' => null,
        ],
        'cancel' => [
            'label' => 'Cancelled',
            'color' => 'danger',
            'text' => 'Cancel',
            'style' => null,
        ],
        'revert' => [
            'label' => 'Reverted',
            'color' => 'dark',
            'text' => 'Revert',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'text' => 'Void',
            'style' => null,
        ],
        'partial' => [
            'label' => 'Partially completed',
            'color' => 'danger',
            'text' => 'Partial',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Done',
            'style' => null,
        ],
    ];
}

/**
 * journal
 *
 * @return array
 */
function permission_letter_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'info',
            'text' => 'Approved',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'dark',
            'text' => 'Rejected',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'text' => 'Void',
            'style' => null,
        ],
        'change_file' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Ganti Dokumen',
            'style' => null,
        ],
    ];
}

/**
 * asset
 *
 * @return array
 */
function asset_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Complete the data',
            'style' => null,
        ],
        'active' => [
            'label' => 'Active',
            'color' => 'info',
            'text' => 'Active',
            'style' => null,
        ],
        'inactive' => [
            'label' => 'Inactive',
            'color' => 'dark',
            'text' => 'Inactive',
            'style' => null,
        ],
        'cancel' => [
            'label' => 'Cancel',
            'color' => 'dark',
            'text' => 'Canceled',
            'style' => null,
        ]
    ];
}

/**
 * asset
 *
 * @return array
 */
function project_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'active' => [
            'label' => 'Active',
            'color' => 'info',
            'text' => 'Active',
            'style' => null,
        ],
        'inactive' => [
            'label' => 'Inactive',
            'color' => 'dark',
            'text' => 'Inactive',
            'style' => null,
        ],
        'done' => [
            'label' => 'Done',
            'color' => 'success',
            'text' => 'Project has been completed',
            'style' => null,
        ],
        'cancel' => [
            'label' => 'Project cancelled',
            'color' => 'danger',
            'text' => 'Project cancelled',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Project rejected',
            'color' => 'dark',
            'text' => 'Project rejected',
            'style' => null,
        ],
        'revert' => [
            'label' => 'Project reverted',
            'color' => 'warning',
            'text' => 'Project reverted',
            'style' => null,
        ],
        'void' => [
            'label' => 'Project void',
            'color' => 'dark',
            'text' => 'Project void',
            'style' => null,
        ],
    ];
}

/**
 * asset
 *
 * @return array
 */
function fleet_status(): array
{
    return [
        'incomplete' => [
            'label' => 'Incomplete',
            'color' => 'warning',
            'text' => 'Complete the data',
            'style' => null,
        ],
        'complete' => [
            'label' => '',
            'color' => '',
            'text' => '',
            'style' => null,
        ],
    ];
}

/**
 * fund submission
 *
 * @return array
 */
function fund_submission_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'info',
            'text' => 'Approved',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'dark',
            'text' => 'Rejected',
            'style' => null,
        ],
        'cancel' => [
            'label' => 'Cancel',
            'color' => 'danger',
            'text' => 'Canceled',
            'style' => null,
        ],
        'revert' => [
            'label' => 'Revert',
            'color' => 'dark',
            'text' => 'Reverted',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'text' => 'Canceled',
            'style' => null,
        ],
    ];
}

/**
 * fund submission
 *
 * @return array
 */
function specific_time_work_agreement_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'info',
            'text' => 'Approved',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'dark',
            'text' => 'Rejected',
            'style' => null,
        ],
        'cancel' => [
            'label' => 'Cancel',
            'color' => 'danger',
            'text' => 'Canceled',
            'style' => null,
        ],
        'revert' => [
            'label' => 'Revert',
            'color' => 'dark',
            'text' => 'Reverted',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'text' => 'Canceled',
            'style' => null,
        ],
    ];
}

/**
 * fund submission
 *
 * @return array
 */
function contract_extension_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'Waiting for approval',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'info',
            'text' => 'Approved',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'dark',
            'text' => 'Rejected',
            'style' => null,
        ],
    ];
}

/**
 * fund submission
 *
 * @return array
 */
function giro_status(): array
{
    return [
        'pending' => [
            'label' => 'Not yet disbursed',
            'color' => 'warning',
            'text' => '',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Already disbursed',
            'color' => 'success',
            'text' => '',
            'style' => null,
        ],
        'cancel' => [
            'label' => 'Cancelled disbursement',
            'color' => 'danger',
            'text' => '',
            'style' => null,
        ],
    ];
}

/**
 * fund submission
 *
 * @return array
 */
function fund_submission_usage_status(): array
{
    return [
        1 => [
            'label' => 'success',
            'color' => 'success',
            'text' => 'Already disbursed',
            'style' => null,
        ],
        0 => [
            'label' => 'danger',
            'color' => 'danger',
            'text' => 'Not yet disbursed',
            'style' => null,
        ],
    ];
}

/**
 * complete status
 *
 * @return array
 */
function complete_status(): array
{
    return [
        1 => [
            'label' => '',
            'color' => '',
            'text' => '',
            'style' => null,
        ],
        0 => [
            'label' => 'Incomplete',
            'color' => 'danger',
            'text' => 'Data is incomplete',
            'style' => null,
        ],
    ];
}

function offering_letter_status(): array
{
    return [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'text' => 'No Answer Yet',
            'style' => null,
        ],
        'approve' => [
            'label' => 'Approve',
            'color' => 'success',
            'text' => 'Accepted',
            'style' => null,
        ],
        'reject' => [
            'label' => 'Reject',
            'color' => 'danger',
            'text' => 'Rejected',
            'style' => null,
        ],
    ];
}

function closing_delivery_order_ship(): array
{
    return [
        'approve' => [
            'label' => 'Approve',
            'color' => 'success',
            'text' => 'Accepted',
            'style' => null,
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'text' => 'Canceled',
            'style' => null,
        ],
    ];
}

const AUTHORIZATION_STATUS = [
    'draft' => [
        'label' => 'Waiting',
        'color' => 'secondary',
        'text' => 'Waiting for another approval',
        'style' => null,
    ],
    'pending' => [
        'label' => 'Pending',
        'color' => 'warning',
        'text' => 'Waiting for approval',
        'style' => null,
    ],
    'approve' => [
        'label' => 'Approve',
        'color' => 'success',
        'text' => 'Approved',
        'style' => null,
    ],
    'reject' => [
        'label' => 'Reject',
        'color' => 'danger',
        'text' => 'Rejected',
        'style' => null,
    ],
    'revert' => [
        'label' => 'Revert',
        'color' => 'warning',
        'text' => 'Reverted',
        'style' => null,
    ],
    'void' => [
        'label' => 'Void',
        'color' => 'dark',
        'text' => 'Canceled',
        'style' => null,
    ],
];

const MARITIAL_STATUS_ENUMS = [
    [
        'value' => 'tk',
        'label' => 'Tidak Kawin',
    ],
    [
        'value' => 'k0',
        'label' => 'Kawin 0 Anak',
    ],
    [
        'value' => 'k1',
        'label' => 'Kawin 1 Anak',
    ],
    [
        'value' => 'k2',
        'label' => 'Kawin 2 Anak',
    ],
    [
        'value' => 'k3',
        'label' => 'Kawin 3 Anak',
    ]
];

const AUTHORIZATIONS = [
    'sales' => [
        'invoice-general',
        'invoice-trading',
        'retur-penjualan',
        'sales-order-general',
        'sales-order-trading',
        'surat-jalan-general',
        'surat-jalan-trading',
        'invoice-down-payment',
        'penawaran',
    ],
    'purchase' => [
        'penerimaan-barang-general',
        'penerimaan-barang-service',
        'penerimaan-barang-trading',
        'penerimaan-barang-transport',
        'purchase-order-general',
        'purchase-order-jasa',
        'purchase-order-trading',
        'purchase-order-transport',
        'purchase-request-service',
        'purchase-request-general',
        'retur-pembelian',
        'tagihan-supplier',
        'tagihan-supplier-general',
        'Purchase Request Trading',
        'purchase-down-payment',
    ],
    'warehouse' => [
        'pemakaian-stock',
        'stock-adjustment',
        'transfer-stock',
        'closing-gudang'
    ],
    'finance' => [
        'disposisi',
        'giro-masuk',
        'jurnal',
        'kas-keluar',
        'kas-masuk',
        'kasbon',
        'pembayaran-piutang',
        'pembayaran-hutang',
        'penerimaan-uang-muka',
        'pembayaran-uang-muka',
        'pengajuan-dana',
        'pengembalian-kasbon',
        'pengembalian-uang-muka',
        'rekonsiliasi-pajak',
        'tutup-buku',
    ],
    'hrd' => [
        'assestment-pegawai',
        'cuti',
        'izin pegawai',
        'form-pemindahan-tenaga-kerja',
        'general-performance-evaluation',
        'hrd-assessment',
        'lamaran-pekerjaan',
        'offering-letter',
        'penggajian',
        'permintaan-tenaga-kerja',
        'perpanjangan-kontrak',
        'pkwt',
        'rekrutment',
        'perubahan-file-cuti',
    ],
    'lainnya' => []
];

const REVERT_VOID_REQ_STATUS = [
    'submitted' => [
        'label' => 'Pending',
        'color' => 'warning',
        'text' => 'Waiting for approval',
        'style' => null,
    ],
    'approve' => [
        'label' => 'approve',
        'color' => 'success',
        'text' => 'Approved',
        'style' => null,
    ],
    'reject' => [
        'label' => 'Reject',
        'color' => 'danger',
        'text' => 'Rejected',
        'style' => null,
    ],
];

const PURCHASE_REQUEST_TRADING_STATUS = [
    'pending' => [
        'label' => 'Pending',
        'color' => 'danger',
        'text' => 'No Purchase Order Yet',
    ],
    'done' => [
        'label' => 'Done',
        'color' => 'success',
        'text' => 'Purchase Order has been Completed',
    ],
    'partial' => [
        'label' => 'Partial',
        'color' => 'warning',
        'text' => 'Some are already in the Purchase Order',

    ]
];


function months()
{
    return [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];
}
