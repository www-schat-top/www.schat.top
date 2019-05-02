<?php
/**
 * From: https://schat.top
 * Usage:
    1)get
      $rtv = Curl::go("https://schat.top");
      $rtv = Curl::go(["url"=>"https://schat.top"]);

    2)post 
      $rtv = Curl::go(["url"=>"https://schat.top", "data"=>["user"=>"john","age"=>28]]);
       ==> $_POST['user'] = "john", $_POST['age'] = 28

    3)post json string
      $rtv = Curl::go(["url"=>"https://schat.top", "data"=>json_encode(["user"=>"john","age"=>28]), "json"=>true]);
       ==>
         $_POST  // []
         $_GET   // []
         $_FILES // []
         file_get_contents("php://input")              // '{"user":"john","age":20}'
         json_decode(file_get_contents("php://input")) // ( [user] => john [age] => 20 )

    4)upload file and add headers
      $rtv = Curl::go(["url"=>"https://schat.top", "data"=>["user"=>"john","age"=>28, 'file' => new \CURLFile(realpath("/tmp/2.tar.gz"))]]);
      $rtv = Curl::go(["url"=>"https://schat.top", "data"=>["user"=>"john","age"=>28, 'file' => new \CURLFile(realpath("/tmp/2.tar.gz")), 'max' => '20M']]);
      $rtv = Curl::go(["url"=>"https://schat.top", "data"=>["user"=>"john","age"=>28, 'file' => new \CURLFile(realpath("/tmp/2.tar.gz")),'max' => '20M', 'header'=>[....]]]);

      ==> $_POST['user'] = "john", 
	  $_POST['age'] = 28

          $_FILES['file']['name']     => 2.tar.gz
          $_FILES['file']['type']     => application/octet-stream
          $_FILES['file']['tmp_name'] => /tmp/phpW75BcK
          $_FILES['file']['error']    => 0
          $_FILES['file']['size']     => 6645
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
      if(is_array($data) && count($data)>0 || is_string($data)){
         curl_setopt($ch, CURLOPT_POST, 1);
         if($arr['json'])
            $arr['header'][] = 'Content-Type: application/json; charset=utf-8;';

         curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      }

      $header_fix = [
         'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36',
      ];
      $header = array_merge($header_fix,$arr['header']);

      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      curl_setopt($ch, CURLOPT_HEADER, TRUE);
      curl_setopt($ch, CURLOPT_NOBODY, FALSE);
      curl_setopt($ch, CURLOPT_TIMEOUT, $arr['timeout']);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_ENCODING , "");
      curl_setopt($ch, CURLOPT_VERBOSE, false);
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
} //~

$rtv = Curl::go([
   "url"=>"http://schat.top/a.php", 
   "data"=>[
      "user"=>"john",
      "age"=>20,
      "file"=>new CURLFile("/tmp/2.tar.gz")
   ]
]);

var_dump($rtv);

/*
  [
    "errno"   => 0,
    "error"   => "",
    "code"    => 200,
    "url"     => "http://schat.top/a.php",
    "ip"      =>  "127.0.0.1",
    
    "header"  => "
        HTTP/1.1 200 OK
        Server: nginx/1.14.0 (Ubuntu)
        Date: Thu, 02 May 2019 05:57:31 GMT
        Content-Type: text/html; charset=UTF-8
        Transfer-Encoding: chunked
        Connection: keep-alive
        Vary: Accept-Encoding
        X-XSS-Protection: 1; mode=block
        Access-Control-Allow-Origin: *
        X-Frame-Options: SAMEORIGIN
        Content-Encoding: gzip
    ",
    
    "body" => "
        _POST [
            "user" => "john",
            "age" => 20
        ]

        _GET []

        _FILES["file"][
                    "name" => "2.tar.gz",
                    "type" => "application/octet-stream",
                    "tmp_name" => "/tmp/phplr55Il",
                    "error" => 0,
                    "size" => 6645
       ]
    ",
    
    "info" => [
      "url"           => "http://schat.top/a.php",
      "content_type"  => "text/html; charset=UTF-8",
      "http_code"     => 200,
      "header_size"   => 345,
      "primary_ip"    => "127.0.0.1",
      "primary_port"  => 80,
      "local_ip"      => "127.0.0.1",
      "local_port"    => 51082,

      "total_time"      => 0.004927,
      "namelookup_time" => 0.004158,
      "connect_time"    => 0.004292,

      "speed_download"  => 43500,
      "speed_upload"    => 1760250,

      "redirect_time"   => 0,
      "redirect_url"    => 0,
    ]
  ]
*/
