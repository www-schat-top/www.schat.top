<?php
/**
 * From: https://schat.top
 * Usage:
    $rtv = Curl::go("https://schat.top");
    $rtv = Curl::go(["url"=>"https://schat.top"]);
    $rtv = Curl::go(["url"=>"https://schat.top", "data"=>["user"=>"john","age"=>28]]);
    $rtv = Curl::go(["url"=>"https://schat.top", "data"=>["user"=>"john","age"=>28], "json"=>true]);
    $rtv = Curl::go(["url"=>"https://schat.top", "data"=>["user"=>"john","age"=>28, 'file' => new \CURLFile(realpath("/etc/nginx/nginx.conf"))]]);
    $rtv = Curl::go(["url"=>"https://schat.top", "data"=>["user"=>"john","age"=>28, 'file' => new \CURLFile(realpath("/etc/nginx/nginx.conf")), 'max' => '20M']]);
    $rtv = Curl::go(["url"=>"https://schat.top", "data"=>["user"=>"john","age"=>28, 'file' => new \CURLFile(realpath("/etc/nginx/nginx.conf")),'max' => '20M', 'header'=>[....]]]);
*/

class Curl {
    public static function go($arr) {
      if(!is_array($arr) && is_string($arr) && preg_match("/^https?:\/\//",$arr) == 1) {
  	      $arr=["url"=>$arr];
      }

      $arr_fix = [
        'data'  => [],
        'header'=> [],
        'json'  => false,
        'max'   => '20M',
        'timeout' => 600,
      ];
    
      $arr=array_merge($arr_fix,$arr);
      ini_set('upload_max_filesize', $arr['max']);
      ini_set('post_max_size', '20M');
      ini_set('memory_limit', '128M');

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $arr['url']);

      $data = $arr['data'];
      if(count($data)){
         curl_setopt($ch, CURLOPT_POST, 1);
         if($arr['json'])
           $header[] = ['Content-Type:application/json'];
         else
           $data = http_build_query($data);

         curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	       $header[] = 'Content-Length:' . strlen($data);
      }

      $header_fix = [
              'Connection: keep-alive',
              'Upgrade-Insecure-Requests: 1',
              'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36',
              'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
              'Accept-Encoding: gzip, deflate, br',
              'Accept-Language: zh-CN,zh;q=0.9'
      ];
      
      $header = array_merge($header_fix,$arr['header']);
      
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      curl_setopt($ch, CURLOPT_HEADER, TRUE);
      curl_setopt($ch, CURLOPT_NOBODY, FALSE);
      curl_setopt($ch, CURLOPT_TIMEOUT, $arr['timeout']);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_ENCODING , "");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_COOKIEJAR, tmpfile());

      $rtv = curl_exec($ch);
      $errno = curl_errno($ch); // 0 is no error
      $error = curl_error($ch);

      $code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
      $resp_header = mb_substr($rtv,0,curl_getinfo($ch, CURLINFO_HEADER_SIZE));
      $body = mb_substr($rtv,curl_getinfo($ch, CURLINFO_HEADER_SIZE));
      $info = curl_getinfo($ch);       
      curl_close($ch);

      /**
       * $errno 0 no error  :  $code 200 / 301 / 302 / 400 / 404 / 403 / 500
       * $errno != 0        :  $code 0
       */
      return [
	      'errno'  => $errno,
	      'error'  => $error,
	      'code'   => $code, 
	      'url'    => $info['url'], 
	      'ip'     => $info['primary_ip'], 
	      'header' => $resp_header, 
	      'body'   => $body, 
	      'info'   => $info
      ];
   }
}
