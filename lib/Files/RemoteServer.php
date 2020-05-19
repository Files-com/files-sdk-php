<?php

declare(strict_types=1);

namespace Files;

/**
 * Class RemoteServer
 *
 * @package Files
 */
class RemoteServer {
  private $attributes = [];
  private $options = [];

  function __construct($attributes = [], $options = []) {
    foreach ($attributes as $key => $value) {
      $this->attributes[str_replace('?', '', $key)] = $value;
    }

    $this->options = $options;
  }

  public function __get($name) {
    return $this->attributes[$name];
  }

  public function isLoaded() {
    return !!$this->attributes['id'];
  }

  // int64 # Remote server ID
  public function getId() {
    return $this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Hostname or IP address
  public function getHostname() {
    return $this->attributes['hostname'];
  }

  public function setHostname($value) {
    return $this->attributes['hostname'] = $value;
  }

  // string # Internal name for your reference
  public function getName() {
    return $this->attributes['name'];
  }

  public function setName($value) {
    return $this->attributes['name'] = $value;
  }

  // int64 # Port for remote server.  Not needed for S3.
  public function getPort() {
    return $this->attributes['port'];
  }

  public function setPort($value) {
    return $this->attributes['port'] = $value;
  }

  // string # S3 bucket name
  public function getS3Bucket() {
    return $this->attributes['s3_bucket'];
  }

  public function setS3Bucket($value) {
    return $this->attributes['s3_bucket'] = $value;
  }

  // string # S3 region
  public function getS3Region() {
    return $this->attributes['s3_region'];
  }

  public function setS3Region($value) {
    return $this->attributes['s3_region'] = $value;
  }

  // string # Remote server certificate
  public function getServerCertificate() {
    return $this->attributes['server_certificate'];
  }

  public function setServerCertificate($value) {
    return $this->attributes['server_certificate'] = $value;
  }

  // string # Remote server type.
  public function getServerType() {
    return $this->attributes['server_type'];
  }

  public function setServerType($value) {
    return $this->attributes['server_type'] = $value;
  }

  // string # Should we require SSL?
  public function getSsl() {
    return $this->attributes['ssl'];
  }

  public function setSsl($value) {
    return $this->attributes['ssl'] = $value;
  }

  // string # Remote server username.  Not needed for S3 buckets.
  public function getUsername() {
    return $this->attributes['username'];
  }

  public function setUsername($value) {
    return $this->attributes['username'] = $value;
  }

  // string # AWS Access Key.
  public function getAwsAccessKey() {
    return $this->attributes['aws_access_key'];
  }

  public function setAwsAccessKey($value) {
    return $this->attributes['aws_access_key'] = $value;
  }

  // string # AWS secret key.
  public function getAwsSecretKey() {
    return $this->attributes['aws_secret_key'];
  }

  public function setAwsSecretKey($value) {
    return $this->attributes['aws_secret_key'] = $value;
  }

  // string # Password if needed.
  public function getPassword() {
    return $this->attributes['password'];
  }

  public function setPassword($value) {
    return $this->attributes['password'] = $value;
  }

  // string # Private key if needed.
  public function getPrivateKey() {
    return $this->attributes['private_key'];
  }

  public function setPrivateKey($value) {
    return $this->attributes['private_key'] = $value;
  }

  // Parameters:
  //   aws_access_key - string - AWS Access Key.
  //   aws_secret_key - string - AWS secret key.
  //   hostname - string - Hostname.
  //   name - string - Internal reference name for server.
  //   password - string - Password if needed.
  //   port - string - Port.
  //   private_key - string - Private key if needed.
  //   s3_bucket - string - S3 bucket name.
  //   s3_region - string - S3 region.
  //   server_certificate - string - Certificate for this server.
  //   server_type - string - Type of server.  Can be ftp, sftp, or s3.
  //   ssl - string - SSL requirements.  Can be if_available, require, require_implicit, never.
  //   username - string - Server username if needed.
  public function update($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no ID');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if ($params['id'] && !is_int($params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }
    if ($params['aws_access_key'] && !is_string($params['aws_access_key'])) {
      throw new \InvalidArgumentException('Bad parameter: $aws_access_key must be of type string; received ' . gettype($aws_access_key));
    }
    if ($params['aws_secret_key'] && !is_string($params['aws_secret_key'])) {
      throw new \InvalidArgumentException('Bad parameter: $aws_secret_key must be of type string; received ' . gettype($aws_secret_key));
    }
    if ($params['hostname'] && !is_string($params['hostname'])) {
      throw new \InvalidArgumentException('Bad parameter: $hostname must be of type string; received ' . gettype($hostname));
    }
    if ($params['name'] && !is_string($params['name'])) {
      throw new \InvalidArgumentException('Bad parameter: $name must be of type string; received ' . gettype($name));
    }
    if ($params['password'] && !is_string($params['password'])) {
      throw new \InvalidArgumentException('Bad parameter: $password must be of type string; received ' . gettype($password));
    }
    if ($params['port'] && !is_string($params['port'])) {
      throw new \InvalidArgumentException('Bad parameter: $port must be of type string; received ' . gettype($port));
    }
    if ($params['private_key'] && !is_string($params['private_key'])) {
      throw new \InvalidArgumentException('Bad parameter: $private_key must be of type string; received ' . gettype($private_key));
    }
    if ($params['s3_bucket'] && !is_string($params['s3_bucket'])) {
      throw new \InvalidArgumentException('Bad parameter: $s3_bucket must be of type string; received ' . gettype($s3_bucket));
    }
    if ($params['s3_region'] && !is_string($params['s3_region'])) {
      throw new \InvalidArgumentException('Bad parameter: $s3_region must be of type string; received ' . gettype($s3_region));
    }
    if ($params['server_certificate'] && !is_string($params['server_certificate'])) {
      throw new \InvalidArgumentException('Bad parameter: $server_certificate must be of type string; received ' . gettype($server_certificate));
    }
    if ($params['server_type'] && !is_string($params['server_type'])) {
      throw new \InvalidArgumentException('Bad parameter: $server_type must be of type string; received ' . gettype($server_type));
    }
    if ($params['ssl'] && !is_string($params['ssl'])) {
      throw new \InvalidArgumentException('Bad parameter: $ssl must be of type string; received ' . gettype($ssl));
    }
    if ($params['username'] && !is_string($params['username'])) {
      throw new \InvalidArgumentException('Bad parameter: $username must be of type string; received ' . gettype($username));
    }

    if (!$params['id']) {
      if ($this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Error('Parameter missing: id');
      }
    }

    return Api::sendRequest('/remote_servers/' . $params['id'] . '', 'PATCH', $params);
  }

  public function delete($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no ID');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if ($params['id'] && !is_int($params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }

    if (!$params['id']) {
      if ($this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Error('Parameter missing: id');
      }
    }

    return Api::sendRequest('/remote_servers/' . $params['id'] . '', 'DELETE', $params);
  }

  public function destroy($params = []) {
    return $this->delete($params);
  }

  public function save() {
    if ($this->attributes['id']) {
      return $this->update($this->attributes);
    } else {
      $new_obj = self::create($this->attributes, $this->options);
      $this->attributes = $new_obj->attributes;
      return true;
    }
  }

  // Parameters:
  //   page - integer - Current page number.
  //   per_page - integer - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   action - string - Deprecated: If set to `count` returns a count of matching records rather than the records themselves.
  public static function list($params = [], $options = []) {
    if ($params['page'] && !is_int($params['page'])) {
      throw new \InvalidArgumentException('Bad parameter: $page must be of type int; received ' . gettype($page));
    }

    if ($params['per_page'] && !is_int($params['per_page'])) {
      throw new \InvalidArgumentException('Bad parameter: $per_page must be of type int; received ' . gettype($per_page));
    }

    if ($params['action'] && !is_string($params['action'])) {
      throw new \InvalidArgumentException('Bad parameter: $action must be of type string; received ' . gettype($action));
    }

    $response = Api::sendRequest('/remote_servers', 'GET', $params);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new RemoteServer((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }

  // Parameters:
  //   id (required) - integer - Remote Server ID.
  public static function find($id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $id;

    if (!$params['id']) {
      throw new \Error('Parameter missing: id');
    }

    if ($params['id'] && !is_int($params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/remote_servers/' . $params['id'] . '', 'GET', $params);

    return new RemoteServer((array)$response->data, $options);
  }

  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }

  // Parameters:
  //   aws_access_key - string - AWS Access Key.
  //   aws_secret_key - string - AWS secret key.
  //   hostname - string - Hostname.
  //   name - string - Internal reference name for server.
  //   password - string - Password if needed.
  //   port - string - Port.
  //   private_key - string - Private key if needed.
  //   s3_bucket - string - S3 bucket name.
  //   s3_region - string - S3 region.
  //   server_certificate - string - Certificate for this server.
  //   server_type - string - Type of server.  Can be ftp, sftp, or s3.
  //   ssl - string - SSL requirements.  Can be if_available, require, require_implicit, never.
  //   username - string - Server username if needed.
  public static function create($params = [], $options = []) {
    if ($params['aws_access_key'] && !is_string($params['aws_access_key'])) {
      throw new \InvalidArgumentException('Bad parameter: $aws_access_key must be of type string; received ' . gettype($aws_access_key));
    }

    if ($params['aws_secret_key'] && !is_string($params['aws_secret_key'])) {
      throw new \InvalidArgumentException('Bad parameter: $aws_secret_key must be of type string; received ' . gettype($aws_secret_key));
    }

    if ($params['hostname'] && !is_string($params['hostname'])) {
      throw new \InvalidArgumentException('Bad parameter: $hostname must be of type string; received ' . gettype($hostname));
    }

    if ($params['name'] && !is_string($params['name'])) {
      throw new \InvalidArgumentException('Bad parameter: $name must be of type string; received ' . gettype($name));
    }

    if ($params['password'] && !is_string($params['password'])) {
      throw new \InvalidArgumentException('Bad parameter: $password must be of type string; received ' . gettype($password));
    }

    if ($params['port'] && !is_string($params['port'])) {
      throw new \InvalidArgumentException('Bad parameter: $port must be of type string; received ' . gettype($port));
    }

    if ($params['private_key'] && !is_string($params['private_key'])) {
      throw new \InvalidArgumentException('Bad parameter: $private_key must be of type string; received ' . gettype($private_key));
    }

    if ($params['s3_bucket'] && !is_string($params['s3_bucket'])) {
      throw new \InvalidArgumentException('Bad parameter: $s3_bucket must be of type string; received ' . gettype($s3_bucket));
    }

    if ($params['s3_region'] && !is_string($params['s3_region'])) {
      throw new \InvalidArgumentException('Bad parameter: $s3_region must be of type string; received ' . gettype($s3_region));
    }

    if ($params['server_certificate'] && !is_string($params['server_certificate'])) {
      throw new \InvalidArgumentException('Bad parameter: $server_certificate must be of type string; received ' . gettype($server_certificate));
    }

    if ($params['server_type'] && !is_string($params['server_type'])) {
      throw new \InvalidArgumentException('Bad parameter: $server_type must be of type string; received ' . gettype($server_type));
    }

    if ($params['ssl'] && !is_string($params['ssl'])) {
      throw new \InvalidArgumentException('Bad parameter: $ssl must be of type string; received ' . gettype($ssl));
    }

    if ($params['username'] && !is_string($params['username'])) {
      throw new \InvalidArgumentException('Bad parameter: $username must be of type string; received ' . gettype($username));
    }

    $response = Api::sendRequest('/remote_servers', 'POST', $params);

    return new RemoteServer((array)$response->data, $options);
  }
}
