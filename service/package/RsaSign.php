<?php
/*
+----------------------------------------------------------------------
| time       2018-07-20
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  加密解密函数
+----------------------------------------------------------------------
*/
namespace service\package;

/**
    $rsa = new \service\package\RsaSign();
    $str = $rsa->ensign('haha');
    var_dump($str);
    $str = $rsa->design($str);
    var_dump($str);
 */


class RsaSign
{
	private $public_key = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAgno/f2fWyh5aWnqbQ00O7t0GhsKk0MVir+yQZfo8jYAtNR2EquRqedHTlMCBENjMsforptQ2zIrNUZjVEWCtwNXv8ljqiiYdeQNqw9YicaJq7+Y7NmK3p9V3Qptyt7NN7fdNfW9fc+8sYIGuSERr6eFOlDaYdrGEJ9OGcJr8TQLFWRlaNuhMN4RyNpZ4ztzzUQMW2MHQlSmlu78rJuHbkrsHcIr83VNSTawkSmsNzK7LA5Vxx+yK+BDyMsLiaCdY0DNnM4MzilOaZCqSNzcILZ7+o12zW7rIfNLpnsISVpdozpcU2lDw4y+R8f6grdXCvWd5a0soDBsAUcaJR0Vr6wIDAQAB';
	private $private_key = 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCCej9/Z9bKHlpaeptDTQ7u3QaGwqTQxWKv7JBl+jyNgC01HYSq5Gp50dOUwIEQ2Myx+ium1DbMis1RmNURYK3A1e/yWOqKJh15A2rD1iJxomrv5js2Yren1XdCm3K3s03t9019b19z7yxgga5IRGvp4U6UNph2sYQn04ZwmvxNAsVZGVo26Ew3hHI2lnjO3PNRAxbYwdCVKaW7vysm4duSuwdwivzdU1JNrCRKaw3MrssDlXHH7Ir4EPIywuJoJ1jQM2czgzOKU5pkKpI3Nwgtnv6jXbNbush80umewhJWl2jOlxTaUPDjL5Hx/qCt1cK9Z3lrSygMGwBRxolHRWvrAgMBAAECggEACK2m4Yt/jsv8CH6VKyHJ93s9/uKdYcFvMfJTHGVLd8Hpv9mpxFATAO1C3Gb9bqhs3P2dv6fnyS0GQQIgUdqTU/smzYC6gNvOJAllJYdtnQ9He5Ndpt1kB8a7+vMp6ywC5+wF/GzW9XgYBIc7l1TttI2m01baRzLBboC0NXMpitmmsjv+HwW86YkdBEnsk0KX22xmLOK1ftPwpll77YJcwfkbg63/KL1DFsBAeJJFv2nJWQasckxKhqn/84wgwOPD2MiWgjNpe2/jo1FWjcdp2Uskz7kgMR5Ms/XgjPUnreWS6tsnQaVurabgDsV95wrc7Q5gV2wOq1A98QUbRQM2gQKBgQDXCm0n95uImcvDDjXQxj1hbK7gcGi9Y5WWU31I5+aJg2KMLO1YaTqKAAbANDKUEs3/HYxmTLJAkUw7aWuMJg0oA32Nf6ODejeU8LOG5jU05ff71KFlrapJa03afjWzY6SasbSvsmUfzQNef6Kjre/LJVsAg4qZTVHigQOCPwAPwQKBgQCbVHJEvvwxEi3+EOOwXMWtnG31nNvIk4NLWkWgSy4qC7ZcU49hdYpQ2P14tgmXSQc4JbJGNkuROzgjo1a7Y30bGK4jxbtpa7R6QEEye1pLC7DA+udMUJj+QqRU6C+Ko/FoEz+meXST4s+aL0IqqNEexV6rbJLrdOjfuTVqKPRmqwKBgQCux7+u03bsESznbh5ZgTcUf3Sn4VWQUWec1mEnJ61eKZaonj+PM4Ar+BeEkyhk59/cshSSdVYQWsheJbIPKEJbOnMK1ip9y5FvkjAoveWTCDOF0O4ZqYyVvgx6QUi+dIeuYC+e+l1s+oH5hb4YUWBsegE8Kq41Kf4bHodOjowrQQKBgBKl5MIUG7rYb+Ucqfk2ahUZvqnKFyjRbOXTBKDl5bjuhwo2jfQpZF2ob2XalPQwtEktXWXIhvH8phTBO1xr6U0jfWSWDJXvdE9o/2rMHF7+HE5O0Q38byG3Zns7FNoULJtOFEosMq6+gCYGnm74rYKAN4llE45pDrwtRBhmqlDZAoGAEpnROFTn3TMsm4Ar/+1kRqntVyUdLy1TyCaUoyHQ0hgpel6e5Byebel4SeFuS1wr2eDF0PddTHciXHtGvBu9MSCVC1saZkPMgrNJKZ++Xt1sZpzjJFiiGc3OY/o6yyaC4PyRydTnJwJi1H7b17RhfLrW6+1/BU9PFvf2abj1p34=';

	public function setPublicKey($key)
	{
		$this->public_key = $key;
	}
	public function setPrivateKey($key)
	{
		$this->private_key = $key;
	}

	public function design($str)
	{
		$encrypted_data = $str;
		// 私钥及密码
		$private_key = $this->getPrivateKey();
		$passphrase = '';
		// 加载私钥
		$private_key = openssl_pkey_get_private($private_key, $passphrase);
		// 使用私钥进行解密
		$sensitive_data = '';
		openssl_private_decrypt(base64_decode($encrypted_data), $sensitive_data, $private_key);
		return $sensitive_data;
	}
	public function ensign($str)
	{
		$public_key = $this->getPublicKey();
		// 加载公钥
		$public_key = openssl_pkey_get_public($public_key);
		// 使用公钥进行加密
		$encrypted_data = '';
		openssl_public_encrypt($str, $encrypted_data, $public_key);
		return base64_encode($encrypted_data);
	}

	private function getPrivateKey()
	{
		return $this->getKey('private');
	}

	private function getPublicKey()
	{
		return $this->getKey('public');
	}

	private function getKey($type)
	{
		if($type=="private"){
			$key = $this->private_key;
			$key_name = "RSA PRIVATE";
		}else{
			$key = $this->public_key;
			$key_name = "PUBLIC";
		}

        $key_width = 64;

        $p_key = [];
        //如果私钥是 1行
        if( ! stripos( $key, "\n" )  ){
            $i = 0;
            while( $key_str = substr( $key , $i * $key_width , $key_width) ){
                $p_key[] = $key_str;
                $i ++ ;
            }
        }else{
            //echo '一行？';
        }

        $key = "-----BEGIN ".$key_name." KEY-----\n" . implode("\n", $p_key) ;
        $key = $key ."\n-----END ".$key_name." KEY-----";

        return $key;
	}
}