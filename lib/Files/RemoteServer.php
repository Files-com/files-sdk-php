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

  // string # Type of authentication method
  public function getAuthenticationMethod() {
    return $this->attributes['authentication_method'];
  }

  public function setAuthenticationMethod($value) {
    return $this->attributes['authentication_method'] = $value;
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

  // int64 # Max number of parallel connections.  Ignored for S3 connections (we will parallelize these as much as possible).
  public function getMaxConnections() {
    return $this->attributes['max_connections'];
  }

  public function setMaxConnections($value) {
    return $this->attributes['max_connections'] = $value;
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

  // string # Google Cloud Storage bucket name
  public function getGoogleCloudStorageBucket() {
    return $this->attributes['google_cloud_storage_bucket'];
  }

  public function setGoogleCloudStorageBucket($value) {
    return $this->attributes['google_cloud_storage_bucket'] = $value;
  }

  // string # Google Cloud Project ID
  public function getGoogleCloudStorageProjectId() {
    return $this->attributes['google_cloud_storage_project_id'];
  }

  public function setGoogleCloudStorageProjectId($value) {
    return $this->attributes['google_cloud_storage_project_id'] = $value;
  }

  // string # Backblaze B2 Cloud Storage S3 Endpoint
  public function getBackblazeB2S3Endpoint() {
    return $this->attributes['backblaze_b2_s3_endpoint'];
  }

  public function setBackblazeB2S3Endpoint($value) {
    return $this->attributes['backblaze_b2_s3_endpoint'] = $value;
  }

  // string # Backblaze B2 Cloud Storage Bucket name
  public function getBackblazeB2Bucket() {
    return $this->attributes['backblaze_b2_bucket'];
  }

  public function setBackblazeB2Bucket($value) {
    return $this->attributes['backblaze_b2_bucket'] = $value;
  }

  // string # Wasabi region
  public function getWasabiBucket() {
    return $this->attributes['wasabi_bucket'];
  }

  public function setWasabiBucket($value) {
    return $this->attributes['wasabi_bucket'] = $value;
  }

  // string # Wasabi Bucket name
  public function getWasabiRegion() {
    return $this->attributes['wasabi_region'];
  }

  public function setWasabiRegion($value) {
    return $this->attributes['wasabi_region'] = $value;
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

  // string # A JSON file that contains the private key. To generate see https://cloud.google.com/storage/docs/json_api/v1/how-tos/authorizing#APIKey
  public function getGoogleCloudStorageCredentialsJson() {
    return $this->attributes['google_cloud_storage_credentials_json'];
  }

  public function setGoogleCloudStorageCredentialsJson($value) {
    return $this->attributes['google_cloud_storage_credentials_json'] = $value;
  }

  // string # Wasabi access key.
  public function getWasabiAccessKey() {
    return $this->attributes['wasabi_access_key'];
  }

  public function setWasabiAccessKey($value) {
    return $this->attributes['wasabi_access_key'] = $value;
  }

  // string # Wasabi secret key.
  public function getWasabiSecretKey() {
    return $this->attributes['wasabi_secret_key'];
  }

  public function setWasabiSecretKey($value) {
    return $this->attributes['wasabi_secret_key'] = $value;
  }

  // string # Backblaze B2 Cloud Storage keyID.
  public function getBackblazeB2KeyId() {
    return $this->attributes['backblaze_b2_key_id'];
  }

  public function setBackblazeB2KeyId($value) {
    return $this->attributes['backblaze_b2_key_id'] = $value;
  }

  // string # Backblaze B2 Cloud Storage applicationKey.
  public function getBackblazeB2ApplicationKey() {
    return $this->attributes['backblaze_b2_application_key'];
  }

  public function setBackblazeB2ApplicationKey($value) {
    return $this->attributes['backblaze_b2_application_key'] = $value;
  }

  // Parameters:
  //   aws_access_key - string - AWS Access Key.
  //   aws_secret_key - string - AWS secret key.
  //   password - string - Password if needed.
  //   private_key - string - Private key if needed.
  //   google_cloud_storage_credentials_json - string - A JSON file that contains the private key. To generate see https://cloud.google.com/storage/docs/json_api/v1/how-tos/authorizing#APIKey
  //   wasabi_access_key - string - Wasabi access key.
  //   wasabi_secret_key - string - Wasabi secret key.
  //   backblaze_b2_key_id - string - Backblaze B2 Cloud Storage keyID.
  //   backblaze_b2_application_key - string - Backblaze B2 Cloud Storage applicationKey.
  //   hostname - string - Hostname or IP address
  //   name - string - Internal name for your reference
  //   max_connections - integer - Max number of parallel connections.  Ignored for S3 connections (we will parallelize these as much as possible).
  //   port - integer - Port for remote server.  Not needed for S3.
  //   s3_bucket - string - S3 bucket name
  //   s3_region - string - S3 region
  //   server_certificate - string - Remote server certificate
  //   server_type - string - Remote server type.
  //   ssl - string - Should we require SSL?
  //   username - string - Remote server username.  Not needed for S3 buckets.
  //   google_cloud_storage_bucket - string - Google Cloud Storage bucket name
  //   google_cloud_storage_project_id - string - Google Cloud Project ID
  //   backblaze_b2_bucket - string - Backblaze B2 Cloud Storage Bucket name
  //   backblaze_b2_s3_endpoint - string - Backblaze B2 Cloud Storage S3 Endpoint
  //   wasabi_bucket - string - Wasabi region
  //   wasabi_region - string - Wasabi Bucket name
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
    if ($params['password'] && !is_string($params['password'])) {
      throw new \InvalidArgumentException('Bad parameter: $password must be of type string; received ' . gettype($password));
    }
    if ($params['private_key'] && !is_string($params['private_key'])) {
      throw new \InvalidArgumentException('Bad parameter: $private_key must be of type string; received ' . gettype($private_key));
    }
    if ($params['google_cloud_storage_credentials_json'] && !is_string($params['google_cloud_storage_credentials_json'])) {
      throw new \InvalidArgumentException('Bad parameter: $google_cloud_storage_credentials_json must be of type string; received ' . gettype($google_cloud_storage_credentials_json));
    }
    if ($params['wasabi_access_key'] && !is_string($params['wasabi_access_key'])) {
      throw new \InvalidArgumentException('Bad parameter: $wasabi_access_key must be of type string; received ' . gettype($wasabi_access_key));
    }
    if ($params['wasabi_secret_key'] && !is_string($params['wasabi_secret_key'])) {
      throw new \InvalidArgumentException('Bad parameter: $wasabi_secret_key must be of type string; received ' . gettype($wasabi_secret_key));
    }
    if ($params['backblaze_b2_key_id'] && !is_string($params['backblaze_b2_key_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $backblaze_b2_key_id must be of type string; received ' . gettype($backblaze_b2_key_id));
    }
    if ($params['backblaze_b2_application_key'] && !is_string($params['backblaze_b2_application_key'])) {
      throw new \InvalidArgumentException('Bad parameter: $backblaze_b2_application_key must be of type string; received ' . gettype($backblaze_b2_application_key));
    }
    if ($params['hostname'] && !is_string($params['hostname'])) {
      throw new \InvalidArgumentException('Bad parameter: $hostname must be of type string; received ' . gettype($hostname));
    }
    if ($params['name'] && !is_string($params['name'])) {
      throw new \InvalidArgumentException('Bad parameter: $name must be of type string; received ' . gettype($name));
    }
    if ($params['max_connections'] && !is_int($params['max_connections'])) {
      throw new \InvalidArgumentException('Bad parameter: $max_connections must be of type int; received ' . gettype($max_connections));
    }
    if ($params['port'] && !is_int($params['port'])) {
      throw new \InvalidArgumentException('Bad parameter: $port must be of type int; received ' . gettype($port));
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
    if ($params['google_cloud_storage_bucket'] && !is_string($params['google_cloud_storage_bucket'])) {
      throw new \InvalidArgumentException('Bad parameter: $google_cloud_storage_bucket must be of type string; received ' . gettype($google_cloud_storage_bucket));
    }
    if ($params['google_cloud_storage_project_id'] && !is_string($params['google_cloud_storage_project_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $google_cloud_storage_project_id must be of type string; received ' . gettype($google_cloud_storage_project_id));
    }
    if ($params['backblaze_b2_bucket'] && !is_string($params['backblaze_b2_bucket'])) {
      throw new \InvalidArgumentException('Bad parameter: $backblaze_b2_bucket must be of type string; received ' . gettype($backblaze_b2_bucket));
    }
    if ($params['backblaze_b2_s3_endpoint'] && !is_string($params['backblaze_b2_s3_endpoint'])) {
      throw new \InvalidArgumentException('Bad parameter: $backblaze_b2_s3_endpoint must be of type string; received ' . gettype($backblaze_b2_s3_endpoint));
    }
    if ($params['wasabi_bucket'] && !is_string($params['wasabi_bucket'])) {
      throw new \InvalidArgumentException('Bad parameter: $wasabi_bucket must be of type string; received ' . gettype($wasabi_bucket));
    }
    if ($params['wasabi_region'] && !is_string($params['wasabi_region'])) {
      throw new \InvalidArgumentException('Bad parameter: $wasabi_region must be of type string; received ' . gettype($wasabi_region));
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
  //   password - string - Password if needed.
  //   private_key - string - Private key if needed.
  //   google_cloud_storage_credentials_json - string - A JSON file that contains the private key. To generate see https://cloud.google.com/storage/docs/json_api/v1/how-tos/authorizing#APIKey
  //   wasabi_access_key - string - Wasabi access key.
  //   wasabi_secret_key - string - Wasabi secret key.
  //   backblaze_b2_key_id - string - Backblaze B2 Cloud Storage keyID.
  //   backblaze_b2_application_key - string - Backblaze B2 Cloud Storage applicationKey.
  //   hostname - string - Hostname or IP address
  //   name - string - Internal name for your reference
  //   max_connections - integer - Max number of parallel connections.  Ignored for S3 connections (we will parallelize these as much as possible).
  //   port - integer - Port for remote server.  Not needed for S3.
  //   s3_bucket - string - S3 bucket name
  //   s3_region - string - S3 region
  //   server_certificate - string - Remote server certificate
  //   server_type - string - Remote server type.
  //   ssl - string - Should we require SSL?
  //   username - string - Remote server username.  Not needed for S3 buckets.
  //   google_cloud_storage_bucket - string - Google Cloud Storage bucket name
  //   google_cloud_storage_project_id - string - Google Cloud Project ID
  //   backblaze_b2_bucket - string - Backblaze B2 Cloud Storage Bucket name
  //   backblaze_b2_s3_endpoint - string - Backblaze B2 Cloud Storage S3 Endpoint
  //   wasabi_bucket - string - Wasabi region
  //   wasabi_region - string - Wasabi Bucket name
  public static function create($params = [], $options = []) {
    if ($params['aws_access_key'] && !is_string($params['aws_access_key'])) {
      throw new \InvalidArgumentException('Bad parameter: $aws_access_key must be of type string; received ' . gettype($aws_access_key));
    }

    if ($params['aws_secret_key'] && !is_string($params['aws_secret_key'])) {
      throw new \InvalidArgumentException('Bad parameter: $aws_secret_key must be of type string; received ' . gettype($aws_secret_key));
    }

    if ($params['password'] && !is_string($params['password'])) {
      throw new \InvalidArgumentException('Bad parameter: $password must be of type string; received ' . gettype($password));
    }

    if ($params['private_key'] && !is_string($params['private_key'])) {
      throw new \InvalidArgumentException('Bad parameter: $private_key must be of type string; received ' . gettype($private_key));
    }

    if ($params['google_cloud_storage_credentials_json'] && !is_string($params['google_cloud_storage_credentials_json'])) {
      throw new \InvalidArgumentException('Bad parameter: $google_cloud_storage_credentials_json must be of type string; received ' . gettype($google_cloud_storage_credentials_json));
    }

    if ($params['wasabi_access_key'] && !is_string($params['wasabi_access_key'])) {
      throw new \InvalidArgumentException('Bad parameter: $wasabi_access_key must be of type string; received ' . gettype($wasabi_access_key));
    }

    if ($params['wasabi_secret_key'] && !is_string($params['wasabi_secret_key'])) {
      throw new \InvalidArgumentException('Bad parameter: $wasabi_secret_key must be of type string; received ' . gettype($wasabi_secret_key));
    }

    if ($params['backblaze_b2_key_id'] && !is_string($params['backblaze_b2_key_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $backblaze_b2_key_id must be of type string; received ' . gettype($backblaze_b2_key_id));
    }

    if ($params['backblaze_b2_application_key'] && !is_string($params['backblaze_b2_application_key'])) {
      throw new \InvalidArgumentException('Bad parameter: $backblaze_b2_application_key must be of type string; received ' . gettype($backblaze_b2_application_key));
    }

    if ($params['hostname'] && !is_string($params['hostname'])) {
      throw new \InvalidArgumentException('Bad parameter: $hostname must be of type string; received ' . gettype($hostname));
    }

    if ($params['name'] && !is_string($params['name'])) {
      throw new \InvalidArgumentException('Bad parameter: $name must be of type string; received ' . gettype($name));
    }

    if ($params['max_connections'] && !is_int($params['max_connections'])) {
      throw new \InvalidArgumentException('Bad parameter: $max_connections must be of type int; received ' . gettype($max_connections));
    }

    if ($params['port'] && !is_int($params['port'])) {
      throw new \InvalidArgumentException('Bad parameter: $port must be of type int; received ' . gettype($port));
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

    if ($params['google_cloud_storage_bucket'] && !is_string($params['google_cloud_storage_bucket'])) {
      throw new \InvalidArgumentException('Bad parameter: $google_cloud_storage_bucket must be of type string; received ' . gettype($google_cloud_storage_bucket));
    }

    if ($params['google_cloud_storage_project_id'] && !is_string($params['google_cloud_storage_project_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $google_cloud_storage_project_id must be of type string; received ' . gettype($google_cloud_storage_project_id));
    }

    if ($params['backblaze_b2_bucket'] && !is_string($params['backblaze_b2_bucket'])) {
      throw new \InvalidArgumentException('Bad parameter: $backblaze_b2_bucket must be of type string; received ' . gettype($backblaze_b2_bucket));
    }

    if ($params['backblaze_b2_s3_endpoint'] && !is_string($params['backblaze_b2_s3_endpoint'])) {
      throw new \InvalidArgumentException('Bad parameter: $backblaze_b2_s3_endpoint must be of type string; received ' . gettype($backblaze_b2_s3_endpoint));
    }

    if ($params['wasabi_bucket'] && !is_string($params['wasabi_bucket'])) {
      throw new \InvalidArgumentException('Bad parameter: $wasabi_bucket must be of type string; received ' . gettype($wasabi_bucket));
    }

    if ($params['wasabi_region'] && !is_string($params['wasabi_region'])) {
      throw new \InvalidArgumentException('Bad parameter: $wasabi_region must be of type string; received ' . gettype($wasabi_region));
    }

    $response = Api::sendRequest('/remote_servers', 'POST', $params);

    return new RemoteServer((array)$response->data, $options);
  }
}
