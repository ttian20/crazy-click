<?php
namespace Common\Lib;
class Idhandler {
    private static function mix($l){
        $ver  = 1;
        
        $ret  =$l;
        $digit = 0;
        while($ret > 0){
            $digit ++ ;
            $ret = $ret >> 3;
        }

        $i = 0;
        $md = intval(($digit - 1) /5 +1);
        $mix = intval( $l & (( 1 << (3 *$md)) -1 ));

        while ($digit > 0 ){
            $ret += ((( $l & (( 1 << 15) -1 )) + (( $mix & ((( 1 << 3) - 1 ) << ( 3 * --$md))) << (15 - 3 * $md))) << $i );
            $l = ($l >> 15);
            $digit -= 5;
            $i += 18;
        }

        $l = $ret;
        return array($ver, $l);
    }

    private static function setVersion($mixed) {
        return (($mixed[1] >> 8) << 12) + ($mixed[0] << 8) + ($mixed[1] & 255);
    }

    private static function getVersion($l) {
        return array(($l >> 8) & 15, (($l >> 12) << 8) + ($l & 255));
    }

    private static function demix($l) {
        $vs = self::getVersion($l);
        $l = $vs[1];
        switch ($vs[0]) {
        case 1:
            $dig = 0;
            $ret = 0;
            while ($l > 0) {
                $ret += (($l & ((1 << 15) - 1)) << $dig);
                $l = ($l >> 18);
                $dig += 15;
            }
            $l = $ret;
            break;
        }
        return $l;
    }

    public static function encode($l){
        $a = self::mix($l);
        return strtoupper(base_convert(self::setVersion($a), 10, 36));
    }

    public static function decode($id){
        $l = base_convert($id, 36, 10);
        return self::demix($l);
    }
}
