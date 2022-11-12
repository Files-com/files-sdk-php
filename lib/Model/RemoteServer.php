<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

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

  public function __set($name, $value) {
    $this->attributes[$name] = $value;
  }

  public function __get($name) {
    return @$this->attributes[$name];
  }

  public function isLoaded() {
    return !!@$this->attributes['id'];
  }

  // int64 # Remote server ID
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // boolean # If true, this server has been disabled due to failures.  Make any change or set disabled to false to clear this flag.
  public function getDisabled() {
    return @$this->attributes['disabled'];
  }

  public function setDisabled($value) {
    return $this->attributes['disabled'] = $value;
  }

  // string # Type of authentication method
  public function getAuthenticationMethod() {
    return @$this->attributes['authentication_method'];
  }

  public function setAuthenticationMethod($value) {
    return $this->attributes['authentication_method'] = $value;
  }

  // string # Hostname or IP address
  public function getHostname() {
    return @$this->attributes['hostname'];
  }

  public function setHostname($value) {
    return $this->attributes['hostname'] = $value;
  }

  // string # Initial home folder on remote server
  public function getRemoteHomePath() {
    return @$this->attributes['remote_home_path'];
  }

  public function setRemoteHomePath($value) {
    return $this->attributes['remote_home_path'] = $value;
  }

  // string # Internal name for your reference
  public function getName() {
    return @$this->attributes['name'];
  }

  public function setName($value) {
    return $this->attributes['name'] = $value;
  }

  // int64 # Port for remote server.  Not needed for S3.
  public function getPort() {
    return @$this->attributes['port'];
  }

  public function setPort($value) {
    return $this->attributes['port'] = $value;
  }

  // int64 # Max number of parallel connections.  Ignored for S3 connections (we will parallelize these as much as possible).
  public function getMaxConnections() {
    return @$this->attributes['max_connections'];
  }

  public function setMaxConnections($value) {
    return $this->attributes['max_connections'] = $value;
  }

  // boolean # If true, we will ensure that all communications with this remote server are made through the primary region of the site.  This setting can also be overridden by a sitewide setting which will force it to true.
  public function getPinToSiteRegion() {
    return @$this->attributes['pin_to_site_region'];
  }

  public function setPinToSiteRegion($value) {
    return $this->attributes['pin_to_site_region'] = $value;
  }

  // string # If set, all communciations with this remote server are made through the provided region.
  public function getPinnedRegion() {
    return @$this->attributes['pinned_region'];
  }

  public function setPinnedRegion($value) {
    return $this->attributes['pinned_region'] = $value;
  }

  // string # S3 bucket name
  public function getS3Bucket() {
    return @$this->attributes['s3_bucket'];
  }

  public function setS3Bucket($value) {
    return $this->attributes['s3_bucket'] = $value;
  }

  // string # S3 region
  public function getS3Region() {
    return @$this->attributes['s3_region'];
  }

  public function setS3Region($value) {
    return $this->attributes['s3_region'] = $value;
  }

  // string # AWS Access Key.
  public function getAwsAccessKey() {
    return @$this->attributes['aws_access_key'];
  }

  public function setAwsAccessKey($value) {
    return $this->attributes['aws_access_key'] = $value;
  }

  // string # Remote server certificate
  public function getServerCertificate() {
    return @$this->attributes['server_certificate'];
  }

  public function setServerCertificate($value) {
    return $this->attributes['server_certificate'] = $value;
  }

  // string # Remote server SSH Host Key. If provided, we will require that the server host key matches the provided key. Uses OpenSSH format similar to what would go into ~/.ssh/known_hosts
  public function getServerHostKey() {
    return @$this->attributes['server_host_key'];
  }

  public function setServerHostKey($value) {
    return $this->attributes['server_host_key'] = $value;
  }

  // string # Remote server type.
  public function getServerType() {
    return @$this->attributes['server_type'];
  }

  public function setServerType($value) {
    return $this->attributes['server_type'] = $value;
  }

  // string # Should we require SSL?
  public function getSsl() {
    return @$this->attributes['ssl'];
  }

  public function setSsl($value) {
    return $this->attributes['ssl'] = $value;
  }

  // string # Remote server username.  Not needed for S3 buckets.
  public function getUsername() {
    return @$this->attributes['username'];
  }

  public function setUsername($value) {
    return $this->attributes['username'] = $value;
  }

  // string # Google Cloud Storage bucket name
  public function getGoogleCloudStorageBucket() {
    return @$this->attributes['google_cloud_storage_bucket'];
  }

  public function setGoogleCloudStorageBucket($value) {
    return $this->attributes['google_cloud_storage_bucket'] = $value;
  }

  // string # Google Cloud Project ID
  public function getGoogleCloudStorageProjectId() {
    return @$this->attributes['google_cloud_storage_project_id'];
  }

  public function setGoogleCloudStorageProjectId($value) {
    return $this->attributes['google_cloud_storage_project_id'] = $value;
  }

  // string # Backblaze B2 Cloud Storage S3 Endpoint
  public function getBackblazeB2S3Endpoint() {
    return @$this->attributes['backblaze_b2_s3_endpoint'];
  }

  public function setBackblazeB2S3Endpoint($value) {
    return $this->attributes['backblaze_b2_s3_endpoint'] = $value;
  }

  // string # Backblaze B2 Cloud Storage Bucket name
  public function getBackblazeB2Bucket() {
    return @$this->attributes['backblaze_b2_bucket'];
  }

  public function setBackblazeB2Bucket($value) {
    return $this->attributes['backblaze_b2_bucket'] = $value;
  }

  // string # Wasabi Bucket name
  public function getWasabiBucket() {
    return @$this->attributes['wasabi_bucket'];
  }

  public function setWasabiBucket($value) {
    return $this->attributes['wasabi_bucket'] = $value;
  }

  // string # Wasabi region
  public function getWasabiRegion() {
    return @$this->attributes['wasabi_region'];
  }

  public function setWasabiRegion($value) {
    return $this->attributes['wasabi_region'] = $value;
  }

  // string # Wasabi access key.
  public function getWasabiAccessKey() {
    return @$this->attributes['wasabi_access_key'];
  }

  public function setWasabiAccessKey($value) {
    return $this->attributes['wasabi_access_key'] = $value;
  }

  // string # Rackspace username used to login to the Rackspace Cloud Control Panel.
  public function getRackspaceUsername() {
    return @$this->attributes['rackspace_username'];
  }

  public function setRackspaceUsername($value) {
    return $this->attributes['rackspace_username'] = $value;
  }

  // string # Three letter airport code for Rackspace region. See https://support.rackspace.com/how-to/about-regions/
  public function getRackspaceRegion() {
    return @$this->attributes['rackspace_region'];
  }

  public function setRackspaceRegion($value) {
    return $this->attributes['rackspace_region'] = $value;
  }

  // string # The name of the container (top level directory) where files will sync.
  public function getRackspaceContainer() {
    return @$this->attributes['rackspace_container'];
  }

  public function setRackspaceContainer($value) {
    return $this->attributes['rackspace_container'] = $value;
  }

  // string # Returns link to login with an Oauth provider
  public function getAuthSetupLink() {
    return @$this->attributes['auth_setup_link'];
  }

  public function setAuthSetupLink($value) {
    return $this->attributes['auth_setup_link'] = $value;
  }

  // string # Either `in_setup` or `complete`
  public function getAuthStatus() {
    return @$this->attributes['auth_status'];
  }

  public function setAuthStatus($value) {
    return $this->attributes['auth_status'] = $value;
  }

  // string # Describes the authorized account
  public function getAuthAccountName() {
    return @$this->attributes['auth_account_name'];
  }

  public function setAuthAccountName($value) {
    return $this->attributes['auth_account_name'] = $value;
  }

  // string # Either personal or business_other account types
  public function getOneDriveAccountType() {
    return @$this->attributes['one_drive_account_type'];
  }

  public function setOneDriveAccountType($value) {
    return $this->attributes['one_drive_account_type'] = $value;
  }

  // string # Azure Blob Storage Account name
  public function getAzureBlobStorageAccount() {
    return @$this->attributes['azure_blob_storage_account'];
  }

  public function setAzureBlobStorageAccount($value) {
    return $this->attributes['azure_blob_storage_account'] = $value;
  }

  // string # Shared Access Signature (SAS) token
  public function getAzureBlobStorageSasToken() {
    return @$this->attributes['azure_blob_storage_sas_token'];
  }

  public function setAzureBlobStorageSasToken($value) {
    return $this->attributes['azure_blob_storage_sas_token'] = $value;
  }

  // string # Azure Blob Storage Container name
  public function getAzureBlobStorageContainer() {
    return @$this->attributes['azure_blob_storage_container'];
  }

  public function setAzureBlobStorageContainer($value) {
    return $this->attributes['azure_blob_storage_container'] = $value;
  }

  // string # Azure File Storage Account name
  public function getAzureFilesStorageAccount() {
    return @$this->attributes['azure_files_storage_account'];
  }

  public function setAzureFilesStorageAccount($value) {
    return $this->attributes['azure_files_storage_account'] = $value;
  }

  // string # Shared Access Signature (SAS) token
  public function getAzureFilesStorageSasToken() {
    return @$this->attributes['azure_files_storage_sas_token'];
  }

  public function setAzureFilesStorageSasToken($value) {
    return $this->attributes['azure_files_storage_sas_token'] = $value;
  }

  // string # Azure File Storage Share name
  public function getAzureFilesStorageShareName() {
    return @$this->attributes['azure_files_storage_share_name'];
  }

  public function setAzureFilesStorageShareName($value) {
    return $this->attributes['azure_files_storage_share_name'] = $value;
  }

  // string # S3-compatible Bucket name
  public function getS3CompatibleBucket() {
    return @$this->attributes['s3_compatible_bucket'];
  }

  public function setS3CompatibleBucket($value) {
    return $this->attributes['s3_compatible_bucket'] = $value;
  }

  // string # S3-compatible endpoint
  public function getS3CompatibleEndpoint() {
    return @$this->attributes['s3_compatible_endpoint'];
  }

  public function setS3CompatibleEndpoint($value) {
    return $this->attributes['s3_compatible_endpoint'] = $value;
  }

  // string # S3-compatible endpoint
  public function getS3CompatibleRegion() {
    return @$this->attributes['s3_compatible_region'];
  }

  public function setS3CompatibleRegion($value) {
    return $this->attributes['s3_compatible_region'] = $value;
  }

  // string # S3-compatible Access Key.
  public function getS3CompatibleAccessKey() {
    return @$this->attributes['s3_compatible_access_key'];
  }

  public function setS3CompatibleAccessKey($value) {
    return $this->attributes['s3_compatible_access_key'] = $value;
  }

  // boolean # `true` if remote server only accepts connections from dedicated IPs
  public function getEnableDedicatedIps() {
    return @$this->attributes['enable_dedicated_ips'];
  }

  public function setEnableDedicatedIps($value) {
    return $this->attributes['enable_dedicated_ips'] = $value;
  }

  // string # Local permissions for files agent. read_only, write_only, or read_write
  public function getFilesAgentPermissionSet() {
    return @$this->attributes['files_agent_permission_set'];
  }

  public function setFilesAgentPermissionSet($value) {
    return $this->attributes['files_agent_permission_set'] = $value;
  }

  // string # Agent local root path
  public function getFilesAgentRoot() {
    return @$this->attributes['files_agent_root'];
  }

  public function setFilesAgentRoot($value) {
    return $this->attributes['files_agent_root'] = $value;
  }

  // string # Files Agent API Token
  public function getFilesAgentApiToken() {
    return @$this->attributes['files_agent_api_token'];
  }

  public function setFilesAgentApiToken($value) {
    return $this->attributes['files_agent_api_token'] = $value;
  }

  // string # AWS secret key.
  public function getAwsSecretKey() {
    return @$this->attributes['aws_secret_key'];
  }

  public function setAwsSecretKey($value) {
    return $this->attributes['aws_secret_key'] = $value;
  }

  // string # Password if needed.
  public function getPassword() {
    return @$this->attributes['password'];
  }

  public function setPassword($value) {
    return $this->attributes['password'] = $value;
  }

  // string # Private key if needed.
  public function getPrivateKey() {
    return @$this->attributes['private_key'];
  }

  public function setPrivateKey($value) {
    return $this->attributes['private_key'] = $value;
  }

  // string # Passphrase for private key if needed.
  public function getPrivateKeyPassphrase() {
    return @$this->attributes['private_key_passphrase'];
  }

  public function setPrivateKeyPassphrase($value) {
    return $this->attributes['private_key_passphrase'] = $value;
  }

  // string # SSL client certificate.
  public function getSslCertificate() {
    return @$this->attributes['ssl_certificate'];
  }

  public function setSslCertificate($value) {
    return $this->attributes['ssl_certificate'] = $value;
  }

  // string # A JSON file that contains the private key. To generate see https://cloud.google.com/storage/docs/json_api/v1/how-tos/authorizing#APIKey
  public function getGoogleCloudStorageCredentialsJson() {
    return @$this->attributes['google_cloud_storage_credentials_json'];
  }

  public function setGoogleCloudStorageCredentialsJson($value) {
    return $this->attributes['google_cloud_storage_credentials_json'] = $value;
  }

  // string # Wasabi secret key.
  public function getWasabiSecretKey() {
    return @$this->attributes['wasabi_secret_key'];
  }

  public function setWasabiSecretKey($value) {
    return $this->attributes['wasabi_secret_key'] = $value;
  }

  // string # Backblaze B2 Cloud Storage keyID.
  public function getBackblazeB2KeyId() {
    return @$this->attributes['backblaze_b2_key_id'];
  }

  public function setBackblazeB2KeyId($value) {
    return $this->attributes['backblaze_b2_key_id'] = $value;
  }

  // string # Backblaze B2 Cloud Storage applicationKey.
  public function getBackblazeB2ApplicationKey() {
    return @$this->attributes['backblaze_b2_application_key'];
  }

  public function setBackblazeB2ApplicationKey($value) {
    return $this->attributes['backblaze_b2_application_key'] = $value;
  }

  // string # Rackspace API key from the Rackspace Cloud Control Panel.
  public function getRackspaceApiKey() {
    return @$this->attributes['rackspace_api_key'];
  }

  public function setRackspaceApiKey($value) {
    return $this->attributes['rackspace_api_key'] = $value;
  }

  // boolean # Reset authenticated account
  public function getResetAuthentication() {
    return @$this->attributes['reset_authentication'];
  }

  public function setResetAuthentication($value) {
    return $this->attributes['reset_authentication'] = $value;
  }

  // string # Azure Blob Storage secret key.
  public function getAzureBlobStorageAccessKey() {
    return @$this->attributes['azure_blob_storage_access_key'];
  }

  public function setAzureBlobStorageAccessKey($value) {
    return $this->attributes['azure_blob_storage_access_key'] = $value;
  }

  // string # Azure File Storage access key.
  public function getAzureFilesStorageAccessKey() {
    return @$this->attributes['azure_files_storage_access_key'];
  }

  public function setAzureFilesStorageAccessKey($value) {
    return $this->attributes['azure_files_storage_access_key'] = $value;
  }

  // string # S3-compatible secret key
  public function getS3CompatibleSecretKey() {
    return @$this->attributes['s3_compatible_secret_key'];
  }

  public function setS3CompatibleSecretKey($value) {
    return $this->attributes['s3_compatible_secret_key'] = $value;
  }

  // Post local changes, check in, and download configuration file (used by some Remote Server integrations, such as the Files.com Agent)
  //
  // Parameters:
  //   api_token - string - Files Agent API Token
  //   permission_set - string -
  //   root - string - Agent local root path
  //   hostname - string
  //   port - int64 - Incoming port for files agent connections
  //   status - string - either running or shutdown
  //   config_version - string - agent config version
  //   private_key - string - private key
  //   public_key - string - public key
  //   server_host_key - string
  public function configurationFile($params = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    if (!@$params['id']) {
      if (@$this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: id');
      }
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    if (@$params['api_token'] && !is_string(@$params['api_token'])) {
      throw new \Files\InvalidParameterException('$api_token must be of type string; received ' . gettype($api_token));
    }

    if (@$params['permission_set'] && !is_string(@$params['permission_set'])) {
      throw new \Files\InvalidParameterException('$permission_set must be of type string; received ' . gettype($permission_set));
    }

    if (@$params['root'] && !is_string(@$params['root'])) {
      throw new \Files\InvalidParameterException('$root must be of type string; received ' . gettype($root));
    }

    if (@$params['hostname'] && !is_string(@$params['hostname'])) {
      throw new \Files\InvalidParameterException('$hostname must be of type string; received ' . gettype($hostname));
    }

    if (@$params['port'] && !is_int(@$params['port'])) {
      throw new \Files\InvalidParameterException('$port must be of type int; received ' . gettype($port));
    }

    if (@$params['status'] && !is_string(@$params['status'])) {
      throw new \Files\InvalidParameterException('$status must be of type string; received ' . gettype($status));
    }

    if (@$params['config_version'] && !is_string(@$params['config_version'])) {
      throw new \Files\InvalidParameterException('$config_version must be of type string; received ' . gettype($config_version));
    }

    if (@$params['private_key'] && !is_string(@$params['private_key'])) {
      throw new \Files\InvalidParameterException('$private_key must be of type string; received ' . gettype($private_key));
    }

    if (@$params['public_key'] && !is_string(@$params['public_key'])) {
      throw new \Files\InvalidParameterException('$public_key must be of type string; received ' . gettype($public_key));
    }

    if (@$params['server_host_key'] && !is_string(@$params['server_host_key'])) {
      throw new \Files\InvalidParameterException('$server_host_key must be of type string; received ' . gettype($server_host_key));
    }

    $response = Api::sendRequest('/remote_servers/' . @$params['id'] . '/configuration_file', 'POST', $params, $this->options);
    return $response->data;
  }

  // Parameters:
  //   aws_access_key - string - AWS Access Key.
  //   aws_secret_key - string - AWS secret key.
  //   password - string - Password if needed.
  //   private_key - string - Private key if needed.
  //   private_key_passphrase - string - Passphrase for private key if needed.
  //   ssl_certificate - string - SSL client certificate.
  //   google_cloud_storage_credentials_json - string - A JSON file that contains the private key. To generate see https://cloud.google.com/storage/docs/json_api/v1/how-tos/authorizing#APIKey
  //   wasabi_access_key - string - Wasabi access key.
  //   wasabi_secret_key - string - Wasabi secret key.
  //   backblaze_b2_key_id - string - Backblaze B2 Cloud Storage keyID.
  //   backblaze_b2_application_key - string - Backblaze B2 Cloud Storage applicationKey.
  //   rackspace_api_key - string - Rackspace API key from the Rackspace Cloud Control Panel.
  //   reset_authentication - boolean - Reset authenticated account
  //   azure_blob_storage_access_key - string - Azure Blob Storage secret key.
  //   azure_files_storage_access_key - string - Azure File Storage access key.
  //   hostname - string - Hostname or IP address
  //   name - string - Internal name for your reference
  //   max_connections - int64 - Max number of parallel connections.  Ignored for S3 connections (we will parallelize these as much as possible).
  //   pin_to_site_region - boolean - If true, we will ensure that all communications with this remote server are made through the primary region of the site.  This setting can also be overridden by a sitewide setting which will force it to true.
  //   port - int64 - Port for remote server.  Not needed for S3.
  //   s3_bucket - string - S3 bucket name
  //   s3_region - string - S3 region
  //   server_certificate - string - Remote server certificate
  //   server_host_key - string - Remote server SSH Host Key. If provided, we will require that the server host key matches the provided key. Uses OpenSSH format similar to what would go into ~/.ssh/known_hosts
  //   server_type - string - Remote server type.
  //   ssl - string - Should we require SSL?
  //   username - string - Remote server username.  Not needed for S3 buckets.
  //   google_cloud_storage_bucket - string - Google Cloud Storage bucket name
  //   google_cloud_storage_project_id - string - Google Cloud Project ID
  //   backblaze_b2_bucket - string - Backblaze B2 Cloud Storage Bucket name
  //   backblaze_b2_s3_endpoint - string - Backblaze B2 Cloud Storage S3 Endpoint
  //   wasabi_bucket - string - Wasabi Bucket name
  //   wasabi_region - string - Wasabi region
  //   rackspace_username - string - Rackspace username used to login to the Rackspace Cloud Control Panel.
  //   rackspace_region - string - Three letter airport code for Rackspace region. See https://support.rackspace.com/how-to/about-regions/
  //   rackspace_container - string - The name of the container (top level directory) where files will sync.
  //   one_drive_account_type - string - Either personal or business_other account types
  //   azure_blob_storage_account - string - Azure Blob Storage Account name
  //   azure_blob_storage_container - string - Azure Blob Storage Container name
  //   azure_blob_storage_sas_token - string - Shared Access Signature (SAS) token
  //   azure_files_storage_account - string - Azure File Storage Account name
  //   azure_files_storage_share_name - string - Azure File Storage Share name
  //   azure_files_storage_sas_token - string - Shared Access Signature (SAS) token
  //   s3_compatible_bucket - string - S3-compatible Bucket name
  //   s3_compatible_endpoint - string - S3-compatible endpoint
  //   s3_compatible_region - string - S3-compatible endpoint
  //   enable_dedicated_ips - boolean - `true` if remote server only accepts connections from dedicated IPs
  //   s3_compatible_access_key - string - S3-compatible Access Key.
  //   s3_compatible_secret_key - string - S3-compatible secret key
  //   files_agent_root - string - Agent local root path
  //   files_agent_permission_set - string - Local permissions for files agent. read_only, write_only, or read_write
  public function update($params = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    if (!@$params['id']) {
      if (@$this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: id');
      }
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    if (@$params['aws_access_key'] && !is_string(@$params['aws_access_key'])) {
      throw new \Files\InvalidParameterException('$aws_access_key must be of type string; received ' . gettype($aws_access_key));
    }

    if (@$params['aws_secret_key'] && !is_string(@$params['aws_secret_key'])) {
      throw new \Files\InvalidParameterException('$aws_secret_key must be of type string; received ' . gettype($aws_secret_key));
    }

    if (@$params['password'] && !is_string(@$params['password'])) {
      throw new \Files\InvalidParameterException('$password must be of type string; received ' . gettype($password));
    }

    if (@$params['private_key'] && !is_string(@$params['private_key'])) {
      throw new \Files\InvalidParameterException('$private_key must be of type string; received ' . gettype($private_key));
    }

    if (@$params['private_key_passphrase'] && !is_string(@$params['private_key_passphrase'])) {
      throw new \Files\InvalidParameterException('$private_key_passphrase must be of type string; received ' . gettype($private_key_passphrase));
    }

    if (@$params['ssl_certificate'] && !is_string(@$params['ssl_certificate'])) {
      throw new \Files\InvalidParameterException('$ssl_certificate must be of type string; received ' . gettype($ssl_certificate));
    }

    if (@$params['google_cloud_storage_credentials_json'] && !is_string(@$params['google_cloud_storage_credentials_json'])) {
      throw new \Files\InvalidParameterException('$google_cloud_storage_credentials_json must be of type string; received ' . gettype($google_cloud_storage_credentials_json));
    }

    if (@$params['wasabi_access_key'] && !is_string(@$params['wasabi_access_key'])) {
      throw new \Files\InvalidParameterException('$wasabi_access_key must be of type string; received ' . gettype($wasabi_access_key));
    }

    if (@$params['wasabi_secret_key'] && !is_string(@$params['wasabi_secret_key'])) {
      throw new \Files\InvalidParameterException('$wasabi_secret_key must be of type string; received ' . gettype($wasabi_secret_key));
    }

    if (@$params['backblaze_b2_key_id'] && !is_string(@$params['backblaze_b2_key_id'])) {
      throw new \Files\InvalidParameterException('$backblaze_b2_key_id must be of type string; received ' . gettype($backblaze_b2_key_id));
    }

    if (@$params['backblaze_b2_application_key'] && !is_string(@$params['backblaze_b2_application_key'])) {
      throw new \Files\InvalidParameterException('$backblaze_b2_application_key must be of type string; received ' . gettype($backblaze_b2_application_key));
    }

    if (@$params['rackspace_api_key'] && !is_string(@$params['rackspace_api_key'])) {
      throw new \Files\InvalidParameterException('$rackspace_api_key must be of type string; received ' . gettype($rackspace_api_key));
    }

    if (@$params['azure_blob_storage_access_key'] && !is_string(@$params['azure_blob_storage_access_key'])) {
      throw new \Files\InvalidParameterException('$azure_blob_storage_access_key must be of type string; received ' . gettype($azure_blob_storage_access_key));
    }

    if (@$params['azure_files_storage_access_key'] && !is_string(@$params['azure_files_storage_access_key'])) {
      throw new \Files\InvalidParameterException('$azure_files_storage_access_key must be of type string; received ' . gettype($azure_files_storage_access_key));
    }

    if (@$params['hostname'] && !is_string(@$params['hostname'])) {
      throw new \Files\InvalidParameterException('$hostname must be of type string; received ' . gettype($hostname));
    }

    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype($name));
    }

    if (@$params['max_connections'] && !is_int(@$params['max_connections'])) {
      throw new \Files\InvalidParameterException('$max_connections must be of type int; received ' . gettype($max_connections));
    }

    if (@$params['port'] && !is_int(@$params['port'])) {
      throw new \Files\InvalidParameterException('$port must be of type int; received ' . gettype($port));
    }

    if (@$params['s3_bucket'] && !is_string(@$params['s3_bucket'])) {
      throw new \Files\InvalidParameterException('$s3_bucket must be of type string; received ' . gettype($s3_bucket));
    }

    if (@$params['s3_region'] && !is_string(@$params['s3_region'])) {
      throw new \Files\InvalidParameterException('$s3_region must be of type string; received ' . gettype($s3_region));
    }

    if (@$params['server_certificate'] && !is_string(@$params['server_certificate'])) {
      throw new \Files\InvalidParameterException('$server_certificate must be of type string; received ' . gettype($server_certificate));
    }

    if (@$params['server_host_key'] && !is_string(@$params['server_host_key'])) {
      throw new \Files\InvalidParameterException('$server_host_key must be of type string; received ' . gettype($server_host_key));
    }

    if (@$params['server_type'] && !is_string(@$params['server_type'])) {
      throw new \Files\InvalidParameterException('$server_type must be of type string; received ' . gettype($server_type));
    }

    if (@$params['ssl'] && !is_string(@$params['ssl'])) {
      throw new \Files\InvalidParameterException('$ssl must be of type string; received ' . gettype($ssl));
    }

    if (@$params['username'] && !is_string(@$params['username'])) {
      throw new \Files\InvalidParameterException('$username must be of type string; received ' . gettype($username));
    }

    if (@$params['google_cloud_storage_bucket'] && !is_string(@$params['google_cloud_storage_bucket'])) {
      throw new \Files\InvalidParameterException('$google_cloud_storage_bucket must be of type string; received ' . gettype($google_cloud_storage_bucket));
    }

    if (@$params['google_cloud_storage_project_id'] && !is_string(@$params['google_cloud_storage_project_id'])) {
      throw new \Files\InvalidParameterException('$google_cloud_storage_project_id must be of type string; received ' . gettype($google_cloud_storage_project_id));
    }

    if (@$params['backblaze_b2_bucket'] && !is_string(@$params['backblaze_b2_bucket'])) {
      throw new \Files\InvalidParameterException('$backblaze_b2_bucket must be of type string; received ' . gettype($backblaze_b2_bucket));
    }

    if (@$params['backblaze_b2_s3_endpoint'] && !is_string(@$params['backblaze_b2_s3_endpoint'])) {
      throw new \Files\InvalidParameterException('$backblaze_b2_s3_endpoint must be of type string; received ' . gettype($backblaze_b2_s3_endpoint));
    }

    if (@$params['wasabi_bucket'] && !is_string(@$params['wasabi_bucket'])) {
      throw new \Files\InvalidParameterException('$wasabi_bucket must be of type string; received ' . gettype($wasabi_bucket));
    }

    if (@$params['wasabi_region'] && !is_string(@$params['wasabi_region'])) {
      throw new \Files\InvalidParameterException('$wasabi_region must be of type string; received ' . gettype($wasabi_region));
    }

    if (@$params['rackspace_username'] && !is_string(@$params['rackspace_username'])) {
      throw new \Files\InvalidParameterException('$rackspace_username must be of type string; received ' . gettype($rackspace_username));
    }

    if (@$params['rackspace_region'] && !is_string(@$params['rackspace_region'])) {
      throw new \Files\InvalidParameterException('$rackspace_region must be of type string; received ' . gettype($rackspace_region));
    }

    if (@$params['rackspace_container'] && !is_string(@$params['rackspace_container'])) {
      throw new \Files\InvalidParameterException('$rackspace_container must be of type string; received ' . gettype($rackspace_container));
    }

    if (@$params['one_drive_account_type'] && !is_string(@$params['one_drive_account_type'])) {
      throw new \Files\InvalidParameterException('$one_drive_account_type must be of type string; received ' . gettype($one_drive_account_type));
    }

    if (@$params['azure_blob_storage_account'] && !is_string(@$params['azure_blob_storage_account'])) {
      throw new \Files\InvalidParameterException('$azure_blob_storage_account must be of type string; received ' . gettype($azure_blob_storage_account));
    }

    if (@$params['azure_blob_storage_container'] && !is_string(@$params['azure_blob_storage_container'])) {
      throw new \Files\InvalidParameterException('$azure_blob_storage_container must be of type string; received ' . gettype($azure_blob_storage_container));
    }

    if (@$params['azure_blob_storage_sas_token'] && !is_string(@$params['azure_blob_storage_sas_token'])) {
      throw new \Files\InvalidParameterException('$azure_blob_storage_sas_token must be of type string; received ' . gettype($azure_blob_storage_sas_token));
    }

    if (@$params['azure_files_storage_account'] && !is_string(@$params['azure_files_storage_account'])) {
      throw new \Files\InvalidParameterException('$azure_files_storage_account must be of type string; received ' . gettype($azure_files_storage_account));
    }

    if (@$params['azure_files_storage_share_name'] && !is_string(@$params['azure_files_storage_share_name'])) {
      throw new \Files\InvalidParameterException('$azure_files_storage_share_name must be of type string; received ' . gettype($azure_files_storage_share_name));
    }

    if (@$params['azure_files_storage_sas_token'] && !is_string(@$params['azure_files_storage_sas_token'])) {
      throw new \Files\InvalidParameterException('$azure_files_storage_sas_token must be of type string; received ' . gettype($azure_files_storage_sas_token));
    }

    if (@$params['s3_compatible_bucket'] && !is_string(@$params['s3_compatible_bucket'])) {
      throw new \Files\InvalidParameterException('$s3_compatible_bucket must be of type string; received ' . gettype($s3_compatible_bucket));
    }

    if (@$params['s3_compatible_endpoint'] && !is_string(@$params['s3_compatible_endpoint'])) {
      throw new \Files\InvalidParameterException('$s3_compatible_endpoint must be of type string; received ' . gettype($s3_compatible_endpoint));
    }

    if (@$params['s3_compatible_region'] && !is_string(@$params['s3_compatible_region'])) {
      throw new \Files\InvalidParameterException('$s3_compatible_region must be of type string; received ' . gettype($s3_compatible_region));
    }

    if (@$params['s3_compatible_access_key'] && !is_string(@$params['s3_compatible_access_key'])) {
      throw new \Files\InvalidParameterException('$s3_compatible_access_key must be of type string; received ' . gettype($s3_compatible_access_key));
    }

    if (@$params['s3_compatible_secret_key'] && !is_string(@$params['s3_compatible_secret_key'])) {
      throw new \Files\InvalidParameterException('$s3_compatible_secret_key must be of type string; received ' . gettype($s3_compatible_secret_key));
    }

    if (@$params['files_agent_root'] && !is_string(@$params['files_agent_root'])) {
      throw new \Files\InvalidParameterException('$files_agent_root must be of type string; received ' . gettype($files_agent_root));
    }

    if (@$params['files_agent_permission_set'] && !is_string(@$params['files_agent_permission_set'])) {
      throw new \Files\InvalidParameterException('$files_agent_permission_set must be of type string; received ' . gettype($files_agent_permission_set));
    }

    $response = Api::sendRequest('/remote_servers/' . @$params['id'] . '', 'PATCH', $params, $this->options);
    return $response->data;
  }

  public function delete($params = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    if (!@$params['id']) {
      if (@$this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: id');
      }
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/remote_servers/' . @$params['id'] . '', 'DELETE', $params, $this->options);
    return $response->data;
  }

  public function destroy($params = []) {
    return $this->delete($params);
  }

  public function save() {
      if (@$this->attributes['id']) {
        return $this->update($this->attributes);
      } else {
        $new_obj = self::create($this->attributes, $this->options);
        $this->attributes = $new_obj->attributes;
        return true;
      }
  }

  // Parameters:
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via either the X-Files-Cursor-Next header or the X-Files-Cursor-Prev header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  public static function list($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    $response = Api::sendRequest('/remote_servers', 'GET', $params, $options);

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
  //   id (required) - int64 - Remote Server ID.
  public static function find($id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $id;

    if (!@$params['id']) {
      throw new \Files\MissingParameterException('Parameter missing: id');
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/remote_servers/' . @$params['id'] . '', 'GET', $params, $options);

    return new RemoteServer((array)(@$response->data ?: []), $options);
  }

  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }

  // Parameters:
  //   id (required) - int64 - Remote Server ID.
  public static function findConfigurationFile($id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $id;

    if (!@$params['id']) {
      throw new \Files\MissingParameterException('Parameter missing: id');
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/remote_servers/' . @$params['id'] . '/configuration_file', 'GET', $params, $options);

    return new RemoteServerConfigurationFile((array)(@$response->data ?: []), $options);
  }

  // Parameters:
  //   aws_access_key - string - AWS Access Key.
  //   aws_secret_key - string - AWS secret key.
  //   password - string - Password if needed.
  //   private_key - string - Private key if needed.
  //   private_key_passphrase - string - Passphrase for private key if needed.
  //   ssl_certificate - string - SSL client certificate.
  //   google_cloud_storage_credentials_json - string - A JSON file that contains the private key. To generate see https://cloud.google.com/storage/docs/json_api/v1/how-tos/authorizing#APIKey
  //   wasabi_access_key - string - Wasabi access key.
  //   wasabi_secret_key - string - Wasabi secret key.
  //   backblaze_b2_key_id - string - Backblaze B2 Cloud Storage keyID.
  //   backblaze_b2_application_key - string - Backblaze B2 Cloud Storage applicationKey.
  //   rackspace_api_key - string - Rackspace API key from the Rackspace Cloud Control Panel.
  //   reset_authentication - boolean - Reset authenticated account
  //   azure_blob_storage_access_key - string - Azure Blob Storage secret key.
  //   azure_files_storage_access_key - string - Azure File Storage access key.
  //   hostname - string - Hostname or IP address
  //   name - string - Internal name for your reference
  //   max_connections - int64 - Max number of parallel connections.  Ignored for S3 connections (we will parallelize these as much as possible).
  //   pin_to_site_region - boolean - If true, we will ensure that all communications with this remote server are made through the primary region of the site.  This setting can also be overridden by a sitewide setting which will force it to true.
  //   port - int64 - Port for remote server.  Not needed for S3.
  //   s3_bucket - string - S3 bucket name
  //   s3_region - string - S3 region
  //   server_certificate - string - Remote server certificate
  //   server_host_key - string - Remote server SSH Host Key. If provided, we will require that the server host key matches the provided key. Uses OpenSSH format similar to what would go into ~/.ssh/known_hosts
  //   server_type - string - Remote server type.
  //   ssl - string - Should we require SSL?
  //   username - string - Remote server username.  Not needed for S3 buckets.
  //   google_cloud_storage_bucket - string - Google Cloud Storage bucket name
  //   google_cloud_storage_project_id - string - Google Cloud Project ID
  //   backblaze_b2_bucket - string - Backblaze B2 Cloud Storage Bucket name
  //   backblaze_b2_s3_endpoint - string - Backblaze B2 Cloud Storage S3 Endpoint
  //   wasabi_bucket - string - Wasabi Bucket name
  //   wasabi_region - string - Wasabi region
  //   rackspace_username - string - Rackspace username used to login to the Rackspace Cloud Control Panel.
  //   rackspace_region - string - Three letter airport code for Rackspace region. See https://support.rackspace.com/how-to/about-regions/
  //   rackspace_container - string - The name of the container (top level directory) where files will sync.
  //   one_drive_account_type - string - Either personal or business_other account types
  //   azure_blob_storage_account - string - Azure Blob Storage Account name
  //   azure_blob_storage_container - string - Azure Blob Storage Container name
  //   azure_blob_storage_sas_token - string - Shared Access Signature (SAS) token
  //   azure_files_storage_account - string - Azure File Storage Account name
  //   azure_files_storage_share_name - string - Azure File Storage Share name
  //   azure_files_storage_sas_token - string - Shared Access Signature (SAS) token
  //   s3_compatible_bucket - string - S3-compatible Bucket name
  //   s3_compatible_endpoint - string - S3-compatible endpoint
  //   s3_compatible_region - string - S3-compatible endpoint
  //   enable_dedicated_ips - boolean - `true` if remote server only accepts connections from dedicated IPs
  //   s3_compatible_access_key - string - S3-compatible Access Key.
  //   s3_compatible_secret_key - string - S3-compatible secret key
  //   files_agent_root - string - Agent local root path
  //   files_agent_permission_set - string - Local permissions for files agent. read_only, write_only, or read_write
  public static function create($params = [], $options = []) {
    if (@$params['aws_access_key'] && !is_string(@$params['aws_access_key'])) {
      throw new \Files\InvalidParameterException('$aws_access_key must be of type string; received ' . gettype($aws_access_key));
    }

    if (@$params['aws_secret_key'] && !is_string(@$params['aws_secret_key'])) {
      throw new \Files\InvalidParameterException('$aws_secret_key must be of type string; received ' . gettype($aws_secret_key));
    }

    if (@$params['password'] && !is_string(@$params['password'])) {
      throw new \Files\InvalidParameterException('$password must be of type string; received ' . gettype($password));
    }

    if (@$params['private_key'] && !is_string(@$params['private_key'])) {
      throw new \Files\InvalidParameterException('$private_key must be of type string; received ' . gettype($private_key));
    }

    if (@$params['private_key_passphrase'] && !is_string(@$params['private_key_passphrase'])) {
      throw new \Files\InvalidParameterException('$private_key_passphrase must be of type string; received ' . gettype($private_key_passphrase));
    }

    if (@$params['ssl_certificate'] && !is_string(@$params['ssl_certificate'])) {
      throw new \Files\InvalidParameterException('$ssl_certificate must be of type string; received ' . gettype($ssl_certificate));
    }

    if (@$params['google_cloud_storage_credentials_json'] && !is_string(@$params['google_cloud_storage_credentials_json'])) {
      throw new \Files\InvalidParameterException('$google_cloud_storage_credentials_json must be of type string; received ' . gettype($google_cloud_storage_credentials_json));
    }

    if (@$params['wasabi_access_key'] && !is_string(@$params['wasabi_access_key'])) {
      throw new \Files\InvalidParameterException('$wasabi_access_key must be of type string; received ' . gettype($wasabi_access_key));
    }

    if (@$params['wasabi_secret_key'] && !is_string(@$params['wasabi_secret_key'])) {
      throw new \Files\InvalidParameterException('$wasabi_secret_key must be of type string; received ' . gettype($wasabi_secret_key));
    }

    if (@$params['backblaze_b2_key_id'] && !is_string(@$params['backblaze_b2_key_id'])) {
      throw new \Files\InvalidParameterException('$backblaze_b2_key_id must be of type string; received ' . gettype($backblaze_b2_key_id));
    }

    if (@$params['backblaze_b2_application_key'] && !is_string(@$params['backblaze_b2_application_key'])) {
      throw new \Files\InvalidParameterException('$backblaze_b2_application_key must be of type string; received ' . gettype($backblaze_b2_application_key));
    }

    if (@$params['rackspace_api_key'] && !is_string(@$params['rackspace_api_key'])) {
      throw new \Files\InvalidParameterException('$rackspace_api_key must be of type string; received ' . gettype($rackspace_api_key));
    }

    if (@$params['azure_blob_storage_access_key'] && !is_string(@$params['azure_blob_storage_access_key'])) {
      throw new \Files\InvalidParameterException('$azure_blob_storage_access_key must be of type string; received ' . gettype($azure_blob_storage_access_key));
    }

    if (@$params['azure_files_storage_access_key'] && !is_string(@$params['azure_files_storage_access_key'])) {
      throw new \Files\InvalidParameterException('$azure_files_storage_access_key must be of type string; received ' . gettype($azure_files_storage_access_key));
    }

    if (@$params['hostname'] && !is_string(@$params['hostname'])) {
      throw new \Files\InvalidParameterException('$hostname must be of type string; received ' . gettype($hostname));
    }

    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype($name));
    }

    if (@$params['max_connections'] && !is_int(@$params['max_connections'])) {
      throw new \Files\InvalidParameterException('$max_connections must be of type int; received ' . gettype($max_connections));
    }

    if (@$params['port'] && !is_int(@$params['port'])) {
      throw new \Files\InvalidParameterException('$port must be of type int; received ' . gettype($port));
    }

    if (@$params['s3_bucket'] && !is_string(@$params['s3_bucket'])) {
      throw new \Files\InvalidParameterException('$s3_bucket must be of type string; received ' . gettype($s3_bucket));
    }

    if (@$params['s3_region'] && !is_string(@$params['s3_region'])) {
      throw new \Files\InvalidParameterException('$s3_region must be of type string; received ' . gettype($s3_region));
    }

    if (@$params['server_certificate'] && !is_string(@$params['server_certificate'])) {
      throw new \Files\InvalidParameterException('$server_certificate must be of type string; received ' . gettype($server_certificate));
    }

    if (@$params['server_host_key'] && !is_string(@$params['server_host_key'])) {
      throw new \Files\InvalidParameterException('$server_host_key must be of type string; received ' . gettype($server_host_key));
    }

    if (@$params['server_type'] && !is_string(@$params['server_type'])) {
      throw new \Files\InvalidParameterException('$server_type must be of type string; received ' . gettype($server_type));
    }

    if (@$params['ssl'] && !is_string(@$params['ssl'])) {
      throw new \Files\InvalidParameterException('$ssl must be of type string; received ' . gettype($ssl));
    }

    if (@$params['username'] && !is_string(@$params['username'])) {
      throw new \Files\InvalidParameterException('$username must be of type string; received ' . gettype($username));
    }

    if (@$params['google_cloud_storage_bucket'] && !is_string(@$params['google_cloud_storage_bucket'])) {
      throw new \Files\InvalidParameterException('$google_cloud_storage_bucket must be of type string; received ' . gettype($google_cloud_storage_bucket));
    }

    if (@$params['google_cloud_storage_project_id'] && !is_string(@$params['google_cloud_storage_project_id'])) {
      throw new \Files\InvalidParameterException('$google_cloud_storage_project_id must be of type string; received ' . gettype($google_cloud_storage_project_id));
    }

    if (@$params['backblaze_b2_bucket'] && !is_string(@$params['backblaze_b2_bucket'])) {
      throw new \Files\InvalidParameterException('$backblaze_b2_bucket must be of type string; received ' . gettype($backblaze_b2_bucket));
    }

    if (@$params['backblaze_b2_s3_endpoint'] && !is_string(@$params['backblaze_b2_s3_endpoint'])) {
      throw new \Files\InvalidParameterException('$backblaze_b2_s3_endpoint must be of type string; received ' . gettype($backblaze_b2_s3_endpoint));
    }

    if (@$params['wasabi_bucket'] && !is_string(@$params['wasabi_bucket'])) {
      throw new \Files\InvalidParameterException('$wasabi_bucket must be of type string; received ' . gettype($wasabi_bucket));
    }

    if (@$params['wasabi_region'] && !is_string(@$params['wasabi_region'])) {
      throw new \Files\InvalidParameterException('$wasabi_region must be of type string; received ' . gettype($wasabi_region));
    }

    if (@$params['rackspace_username'] && !is_string(@$params['rackspace_username'])) {
      throw new \Files\InvalidParameterException('$rackspace_username must be of type string; received ' . gettype($rackspace_username));
    }

    if (@$params['rackspace_region'] && !is_string(@$params['rackspace_region'])) {
      throw new \Files\InvalidParameterException('$rackspace_region must be of type string; received ' . gettype($rackspace_region));
    }

    if (@$params['rackspace_container'] && !is_string(@$params['rackspace_container'])) {
      throw new \Files\InvalidParameterException('$rackspace_container must be of type string; received ' . gettype($rackspace_container));
    }

    if (@$params['one_drive_account_type'] && !is_string(@$params['one_drive_account_type'])) {
      throw new \Files\InvalidParameterException('$one_drive_account_type must be of type string; received ' . gettype($one_drive_account_type));
    }

    if (@$params['azure_blob_storage_account'] && !is_string(@$params['azure_blob_storage_account'])) {
      throw new \Files\InvalidParameterException('$azure_blob_storage_account must be of type string; received ' . gettype($azure_blob_storage_account));
    }

    if (@$params['azure_blob_storage_container'] && !is_string(@$params['azure_blob_storage_container'])) {
      throw new \Files\InvalidParameterException('$azure_blob_storage_container must be of type string; received ' . gettype($azure_blob_storage_container));
    }

    if (@$params['azure_blob_storage_sas_token'] && !is_string(@$params['azure_blob_storage_sas_token'])) {
      throw new \Files\InvalidParameterException('$azure_blob_storage_sas_token must be of type string; received ' . gettype($azure_blob_storage_sas_token));
    }

    if (@$params['azure_files_storage_account'] && !is_string(@$params['azure_files_storage_account'])) {
      throw new \Files\InvalidParameterException('$azure_files_storage_account must be of type string; received ' . gettype($azure_files_storage_account));
    }

    if (@$params['azure_files_storage_share_name'] && !is_string(@$params['azure_files_storage_share_name'])) {
      throw new \Files\InvalidParameterException('$azure_files_storage_share_name must be of type string; received ' . gettype($azure_files_storage_share_name));
    }

    if (@$params['azure_files_storage_sas_token'] && !is_string(@$params['azure_files_storage_sas_token'])) {
      throw new \Files\InvalidParameterException('$azure_files_storage_sas_token must be of type string; received ' . gettype($azure_files_storage_sas_token));
    }

    if (@$params['s3_compatible_bucket'] && !is_string(@$params['s3_compatible_bucket'])) {
      throw new \Files\InvalidParameterException('$s3_compatible_bucket must be of type string; received ' . gettype($s3_compatible_bucket));
    }

    if (@$params['s3_compatible_endpoint'] && !is_string(@$params['s3_compatible_endpoint'])) {
      throw new \Files\InvalidParameterException('$s3_compatible_endpoint must be of type string; received ' . gettype($s3_compatible_endpoint));
    }

    if (@$params['s3_compatible_region'] && !is_string(@$params['s3_compatible_region'])) {
      throw new \Files\InvalidParameterException('$s3_compatible_region must be of type string; received ' . gettype($s3_compatible_region));
    }

    if (@$params['s3_compatible_access_key'] && !is_string(@$params['s3_compatible_access_key'])) {
      throw new \Files\InvalidParameterException('$s3_compatible_access_key must be of type string; received ' . gettype($s3_compatible_access_key));
    }

    if (@$params['s3_compatible_secret_key'] && !is_string(@$params['s3_compatible_secret_key'])) {
      throw new \Files\InvalidParameterException('$s3_compatible_secret_key must be of type string; received ' . gettype($s3_compatible_secret_key));
    }

    if (@$params['files_agent_root'] && !is_string(@$params['files_agent_root'])) {
      throw new \Files\InvalidParameterException('$files_agent_root must be of type string; received ' . gettype($files_agent_root));
    }

    if (@$params['files_agent_permission_set'] && !is_string(@$params['files_agent_permission_set'])) {
      throw new \Files\InvalidParameterException('$files_agent_permission_set must be of type string; received ' . gettype($files_agent_permission_set));
    }

    $response = Api::sendRequest('/remote_servers', 'POST', $params, $options);

    return new RemoteServer((array)(@$response->data ?: []), $options);
  }
}
