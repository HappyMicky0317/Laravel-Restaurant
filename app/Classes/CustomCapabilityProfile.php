<?php 
namespace App\Classes;
use Mike42\Escpos\CapabilityProfiles\SimpleCapabilityProfile;

class CustomCapabilityProfile extends SimpleCapabilityProfile
{
    function getCustomCodePages()
    {
        /*
         * Example to print in a specific, user-defined character set
         * on a printer which has been configured to use i
         */
        return array('CP858' => "ÇüéâäàåçêëèïîìÄÅ" . "ÉæÆôöòûùÿÖÜø£Ø×ƒ" . "áíóúñÑªº¿®¬½¼¡«»" . "░▒▓│┤ÁÂÀ©╣║╗╝¢¥┐" . "└┴┬├─┼ãÃ╚╔╩╦╠═╬¤" . "ðÐÊËÈ€ÍÎÏ┘┌█▄¦Ì▀" . "ÓßÔÒõÕµþÞÚÛÙýÝ¯´" . " ±‗¾¶§÷¸°¨·¹³²■ ");
    }
    function getSupportedCodePages()
    {
        return array(0 => 'custom:CP858');
    }
}
