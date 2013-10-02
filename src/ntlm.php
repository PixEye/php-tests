<?php
/**
 * NTLM = NT LAN Manager (Microsoft Windows)
 * $Id$
 */

require_once 'lib/my-functions.php'; // for getUserAgent() & getOS()

/**
 * http://www.developpez.net/forums/d744317/webmasters-developpement-web/flashflex/flex/ntlm-flex/
 */
class Ntlm
{
  public function getLogin()
  {
    $ua = getUserAgent();
    $os = getOS();
    if (''!=$os) $ua.= " / $os";
    $file = basename(__FILE__);
    $log = "($ua) pass in file ".$file.' at line ';

    $headers = apache_request_headers();	// Get client header

    if (isSet($_SERVER['HTTP_VIA']))
    { // NTLM does not go through a proxy :(
      $isOk = error_log($log.__LINE__);
      $result = 'Proxy bypass!';
    }
    elseIf(!isSet($headers['Authorization']))
    {						//si l'entete autorisation est inexistante
      $isOk = error_log($log.__LINE__);
      header( 'HTTP/1.0 401 Unauthorized' );	//envoi au client le mode d'identification
      header( 'WWW-Authenticate: NTLM' );	//dans notre cas le NTLM
      exit;					//on quitte
    }

    if(isset($headers['Authorization']))	//dans le cas d'une authorisation (identification)
    {
      if(substr($headers['Authorization'], 0, 5) == 'NTLM ')
      {	// on verifie que le client soit en NTLM

        $chaine=$headers['Authorization'];
        $chaine=substr($chaine, 5);		// recuperation du base64-encoded type1 message
        $chained64=base64_decode($chaine);	// decodage base64 dans $chained64

        if(ord($chained64{8}) == 1)
        {
          // octet indiquant l'etape du processus d'identification (etape 3)

          // verification du drapeau NTLM "0xb2" a l'offset 13 dans le message type-1-message (comp ie 5.5+) :
          //if (ord($chained64[13]) != 178)
          //{
          //	echo 'NTLM Flag error!';
          //	exit;
          //}

          $retAuth = 'NTLMSSP'.chr(000).chr(002).chr(000).chr(000).chr(000).chr(000).chr(000).chr(000);
          $retAuth .= chr(000).chr(040).chr(000).chr(000).chr(000).chr(001).chr(130).chr(000).chr(000);
          $retAuth .= chr(000).chr(002).chr(002).chr(002).chr(000).chr(000).chr(000).chr(000).chr(000);
          $retAuth .= chr(000).chr(000).chr(000).chr(000).chr(000).chr(000).chr(000);

          $retAuth64 =base64_encode($retAuth);		 // encode en base64
          $retAuth64 = trim($retAuth64);		 // enleve les espaces de debut et de fin
          $isOk = error_log($log.__LINE__);
          header( 'HTTP/1.0 401 Unauthorized' );	 // envoi le nouveau header
          header( "WWW-Authenticate: NTLM $retAuth64" ); // avec l'identification supplementaire
          exit;
        }

        if(ord($chained64{8}) == 3)
        {
          // octet indiquant l'etape du processus d'identification (etape 5)

          // on recupere le domaine
          $lenght_domain = (ord($chained64[31])*256 + ord($chained64[30])); // longueur du domaine
          $offset_domain = (ord($chained64[33])*256 + ord($chained64[32])); // position du domaine
          // decoupage du domaine :
          $domain = str_replace("\0", '', substr($chained64, $offset_domain, $lenght_domain));

          //le login
          $lenght_login = (ord($chained64[39])*256 + ord($chained64[38])); // longueur du login
          $offset_login = (ord($chained64[41])*256 + ord($chained64[40])); // position du login
          // decoupage du login :
          $login = str_replace("\0", '', substr($chained64, $offset_login, $lenght_login));

          if (!isSet($login))
          {
            $isOk = error_log($log.__LINE__);
            throw new Error('Erreur');
          }

          // stockage des donnees dans des variable de session
          //$_SESSION['login']=$login;
          //$url.='portail/traitements/traitement_login.php';
          //header('Location: ' .$url);

          $result = $login;
          $isOk = error_log($log.__LINE__." login='$login'");
        }
      }
    }
    return ($result);
  }
}

// Main (in order to test):
if (isSet($_SERVER['PHP_AUTH_USER']))
  $login = $_SERVER['PHP_AUTH_USER'];
else
{
  try {
    $ntml = new Ntlm;
    $login = $ntml->getLogin();
  }
  catch(Exception $e) {
    echo 'Caught exception: ', $e->getMessage(), PHP_EOL;
    exit;
  }
}
// Display the result:
$login = trim($login);
$title = 'NTML';
if (''!=$login) $title = "$login - $title";
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $title?></title>
  </head>
  <body>
    <pre>Login r&eacute;cup&eacute;r&eacute; = '<?php echo $login?>'</pre>
  </body>
</html>
