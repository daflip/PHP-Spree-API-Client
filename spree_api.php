<?
/*
 *  Simple High Performance Spree API Client for PHP
 *
 *  Author ✰  github.com/daflip ✰  
 *
 *  DateCreated ✰ Sat, 21/Dec/2013 @ 09:11
 *
 *     _______  __    _    __  __ ____  _     _____ ____  
 *    | ____\ \/ /   / \  |  \/  |  _ \| |   | ____/ ___| 
 *    |  _|  \  /   / _ \ | |\/| | |_) | |   |  _| \___ \ 
 *    | |___ /  \  / ___ \| |  | |  __/| |___| |___ ___) |
 *    |_____/_/\_\/_/   \_\_|  |_|_|   |_____|_____|____/ 
 *                                                        
 *   INIT YOUR API CLIENT:
 *  $api = new SpreeAPI;
 *  
 *   TAXONOMIES:
 *   get all taxonomies:
 *  print_r($api->taxonomies());
 *   get specific taxon (by ID):
 *  print_r($api->taxonomies(2));
 *  
 *   ORDERS:
 *   get all orders
 *  foreach ($api->orders()->orders as $order)
 *    print_r($order->order);
 *  get specific result page page
 *  foreach ($api->orders(array("page" => 2))->orders as $order)
 *   get details on specific order:
 *  print_r($api->orders('R285028844'));
 *  
 *   PRODUCTS:
 *   fetch all products
 *  foreach ($api->products()->products as $product)
 *    print_r($product->product->name);
 *   only show products in the taxon category id 2:
 *  print_r($api->products(array("q"=>array("taxons_id_eq" => 2))));
 *   only show products in the taxon category called 'QTK'
 *  $api->products(array("q" => array("taxons_name_eq" => "QTK")));
 */

class SpreeAPI {
  // the url of the spree store
  private $_api_endpoint = 'http://localhost:8080';
  // admin api token 
  private $_api_token    = '__YOUR_API_KEY_HERE___';
  // how long cached records are considered 'fresh' (in seconds):
  // set to 0 to disable caching of records but it's not recommended
  private $_cache_ttl    = 300;
  private $_last_error   = null;

  public function SpreeAPI() {
    $this->_api_endpoint = trim($this->_api_endpoint, "/ ");
    $this->_time_now     = time();
    $sCacheDir = dirname($this->cachePath("dummy"));
    if (!is_dir($sCacheDir))
      mkdir($sCacheDir);
    if (strlen($this->_api_token) <= 32)
      throw new Exception("Set your API token first doofus!");
  }

  public function __call($c, $a = array()) {
    if (preg_match('/^[a-z]+$/', $c))
      return $this->api_call($c, $a);
    throw new Exception("Unknown method call: $c");
  }

  protected function api_call($uri, $params = array()) {
    $sURL = $this->_api_endpoint."/api/$uri";
    $ttl  = $this->_cache_ttl;
    if (isset($params[0])) {
      // id passed?
      if (is_array($params[0])) {
        $params = $params[0];
        // add search param if it's available
        if (isset($params['q']) && in_array($uri, array("products","orders")))
          $sURL .= "/search";
      } else {
        $sURL .= "/".$params[0];
        // if accessing an individual item which isn't a product then 
        // reduce ttl to 90 seconds
        if ($uri != 'products')
          $ttl = 60;
      }
    }
    // if we have params, append them
    if (!empty($params))
      $sURL .= "?".http_build_query($params);
    $sCacheID = md5($sURL);
    if ($response = $this->isCached($sCacheID, $ttl)) {
      return json_decode($response);
    } else {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $sURL);
      curl_setopt($ch, CURLOPT_USERAGENT, "Envision Spree API");
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "X-Spree-Token: {$this->_api_token}",
      ));
      $response    = curl_exec($ch);
      $result_info = curl_getinfo($ch);
      // Check for a cURL connection error
      if (!curl_error($ch)) {
        // We hit a failure!
        curl_close($ch);
        if ($result_info['http_code'] == 200) {
          // cache the result if we have a ttl
          if ($ttl)
            $this->cacheResult($sCacheID, $response);
          return json_decode($response);
        } else {
          $this->setError("HTTP ".$result_info['http_code']);
        }
        try {
          $arrResult = json_decode($response);
          if (isset($arrResult->error))
            return $this->setError($arrResult->error);
        } catch (Exception $e) {
        }
      } else {
        $this->setError("Curl error: " . curl_error($ch));
        curl_close($ch);
      }
    }
    return false;
  }

  public function getError() {
    return $this->_last_error;
  }

  private function setError($sMessage) {
    $this->_last_error = $sMessage;
    return false;
  }

  private function cachePath($sCacheID) {
    return sys_get_temp_dir()."/.spree_cache/$sCacheID";
  }

  private function isCached($sCacheID, $ttl = -1) {
    if ($ttl < 0)
      $ttl = $this->_cache_ttl;
    if ($ttl == 0)
      return false;
    $sCachePath = $this->cachePath($sCacheID);
    if (file_exists($sCachePath) && (filemtime($sCachePath) > ($this->_time_now - $ttl)))
      return file_get_contents($sCachePath);
  }

  private function cacheResult($sCacheID, $sCacheData) {
    $sCachePath = $this->cachePath($sCacheID);
    if ($fp = fopen($sCachePath, "w")) { 
      fwrite($fp, $sCacheData);
      fclose($fp);
    }
    
  }


}


