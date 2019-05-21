/**
 * convert cidr to ip range
 * From: <https://github.com/www-schat-top>
 * Link: https://www.php.net/manual/zh/function.ip2long.php
 * @param  string $cidr "192.168.0.0/23"
 * @return array  ["192.168.0.0", "192.168.1.255"]
 */
function cidr2range($cidr){
   list( $subnet, $mask ) = explode( '/', $cidr );
   return [$subnet, long2ip(ip2long( $subnet ) | (pow(2,( 32 - $mask ))-1))];
}
