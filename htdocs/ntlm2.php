<?php
/**
 * A child of SoapClient with support for NTLM proxy authentication
 * @author Meltir <meltir@meltir.com>
 * @link http://www.php.net/manual/en/soapclient.soapclient.php#97029
 */

class NTLM_SoapClient extends SoapClient
{

  public function __construct($wsdl, $options = array())
  {
    if (!isSet($options['proxy_login']) || !isSet($options['proxy_password']))
      throw new Exception('Login and password required for NTLM authentication!');

    $this->proxy_login = $options['proxy_login'];
    $this->proxy_password = $options['proxy_password'];
    $this->proxy_host = (!isSet($options['proxy_host']) ? 'localhost' : $options['proxy_host']);
    $this->proxy_port = (!isSet($options['proxy_port']) ? 8080 : $options['proxy_port']);
    parent::__construct($wsdl, $options);
  }

  /**
   * Call a url using curl with ntlm auth
   *
   * @param string $url
   * @param string $data
   * @return string
   * @throws SoapFault on curl connection error
   */
  protected function callCurl($url, $data)
  {
    $handle   = curl_init();
    curl_setopt($handle, CURLOPT_HEADER, false);
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_FAILONERROR, true);
    curl_setopt($handle, CURLOPT_HTTPHEADER, Array('PHP SOAP-NTLM Client') );
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
    curl_setopt($handle, CURLOPT_PROXYUSERPWD, $this->proxy_login.':'.$this->proxy_password);
    curl_setopt($handle, CURLOPT_PROXY, $this->proxy_host.':'.$this->proxy_port);
    curl_setopt($handle, CURLOPT_PROXYAUTH, CURLAUTH_NTLM);
    $response = curl_exec($handle);
    if (!isSet($response))
      throw new SoapFault('CURL error: '.curl_error($handle), curl_errno($handle));

    curl_close($handle);
    return $response;
  }

  public function __doRequest($request, $location, $action, $version, $one_way = 0)
  {
    return $this->callCurl($location, $request);
  }
}

// main:
try {
  $ntml = new NTLM_SoapClient('');
  print('ok');
}
catch(Exception $e) {
  echo 'Caught exception: ', $e->getMessage(), PHP_EOL;
}
