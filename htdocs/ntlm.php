<?php
/**
 * NTLM = NT LAN Manager (Microsoft Windows)
 * $Id$
 */

require_once 'Include/functions.php'; // for getUserAgent() & getOS()

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
    {
      $isOk = error_log($log.__LINE__);
      header( 'HTTP/1.0 401 Unauthorized' );	// Send auth mode to the browser...
      header( 'WWW-Authenticate: NTLM' );	// NTLM in our case.
      exit;					// Then quit.
    }

    if(isSet($headers['Authorization']))
    {
      if(substr($headers['Authorization'], 0, 5) == 'NTLM ')
      {	// Check that the browser uses NTML

        $auth_str=$headers['Authorization'];
        $auth_str=substr($auth_str, 5);		// Get base64-encoded type1 message
        $chained64=base64_decode($auth_str);

        if(ord($chained64{8}) == 1)
        {
	  // byte containing the step of the auth process (step 3)

          // Check NTLM flag "0xb2" at offset 13 in the type-1 message (IE 5.5+ compat) :
          # if (ord($chained64[13]) != 178)
          # {
          # 	echo 'NTLM Flag error!';
          # 	exit;
          # }

          $retAuth = 'NTLMSSP'.chr(000).chr(002).chr(000).chr(000).chr(000).chr(000).chr(000).chr(000);
          $retAuth.= chr(000).chr(040).chr(000).chr(000).chr(000).chr(001).chr(130).chr(000).chr(000);
          $retAuth.= chr(000).chr(002).chr(002).chr(002).chr(000).chr(000).chr(000).chr(000).chr(000);
          $retAuth.= chr(000).chr(000).chr(000).chr(000).chr(000).chr(000).chr(000);

          $retAuth64 = base64_encode($retAuth);
          $retAuth64 = trim($retAuth64);		 // strip useless spaces
          $isOk = error_log($log.__LINE__);
          header( 'HTTP/1.0 401 Unauthorized' );	 // send new header
          header( "WWW-Authenticate: NTLM $retAuth64" ); // adding the ID
          exit;
        }

        if(ord($chained64{8}) == 3)
        {
          // octet indiquant l'etape du processus d'identification (etape 5)

          // Get the domaine
          $lenght_domain = (ord($chained64[31])*256 + ord($chained64[30])); // length of domain
          $offset_domain = (ord($chained64[33])*256 + ord($chained64[32])); // position of domain
          // Make the domain:
          $domain = str_replace("\0", '', substr($chained64, $offset_domain, $lenght_domain));

          // Get the login
          $lenght_login = (ord($chained64[39])*256 + ord($chained64[38])); // length of login
          $offset_login = (ord($chained64[41])*256 + ord($chained64[40])); // position du login
          // Make the login:
          $login = str_replace("\0", '', substr($chained64, $offset_login, $lenght_login));

          if (!isSet($login))
          {
            $isOk = error_log($log.__LINE__);
            throw new Error('Erreur');
          }

          // Save data in session vars:
          # $_SESSION['login']=$login;
          # $url.='portail/traitements/traitement_login.php';
          # header('Location: ' .$url);

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
    <pre>Got login = '<?php echo $login?>'</pre>
  </body>
</html>
