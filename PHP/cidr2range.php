/**
 * convert cidr to ip range
 * From: <https://github.com/www-schat-top>
 * @param  string $cidr "192.168.0.0/23"
 * @return array  ["192.168.0.0", "192.168.1.255"]
 */
function cidr2range($cidr){
   list( $subnet, $mask ) = explode( '/', $cidr );
   return [ip2long($subnet), long2ip(ip2long( $subnet ) | (pow(2,( 32 - $mask ))-1))];
}
