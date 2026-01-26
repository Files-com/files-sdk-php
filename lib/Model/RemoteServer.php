<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class RemoteServer
 *
 * @package Files
 */
class RemoteServer
{
    private $attributes = [];
    private $options = [];
    private static $static_mapped_functions = [
        'list' => 'all',
    ];

    public function __construct($attributes = [], $options = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[str_replace('?', '', $key)] = $value;
        }

        $this->options = $options;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __get($name)
    {
        return @$this->attributes[$name];
    }

    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, array_keys(self::$static_mapped_functions))) {
            $method = self::$static_mapped_functions[$name];
            if (method_exists(__CLASS__, $method)) {
                return @self::$method(...$arguments);
            }
        }
    }

    public function isLoaded()
    {
        return !!@$this->attributes['id'];
    }
    // int64 # Remote Server ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // boolean # If true, this Remote Server has been disabled due to failures.  Make any change or set disabled to false to clear this flag.
    public function getDisabled()
    {
        return @$this->attributes['disabled'];
    }

    public function setDisabled($value)
    {
        return $this->attributes['disabled'] = $value;
    }
    // string # Type of authentication method to use
    public function getAuthenticationMethod()
    {
        return @$this->attributes['authentication_method'];
    }

    public function setAuthenticationMethod($value)
    {
        return $this->attributes['authentication_method'] = $value;
    }
    // string # Hostname or IP address
    public function getHostname()
    {
        return @$this->attributes['hostname'];
    }

    public function setHostname($value)
    {
        return $this->attributes['hostname'] = $value;
    }
    // string # Initial home folder on remote server
    public function getRemoteHomePath()
    {
        return @$this->attributes['remote_home_path'];
    }

    public function setRemoteHomePath($value)
    {
        return $this->attributes['remote_home_path'] = $value;
    }
    // string # Upload staging path.  Applies to SFTP only.  If a path is provided here, files will first be uploaded to this path on the remote folder and the moved into the final correct path via an SFTP move command.  This is required by some remote MFT systems to emulate atomic uploads, which are otherwise not supoprted by SFTP.
    public function getUploadStagingPath()
    {
        return @$this->attributes['upload_staging_path'];
    }

    public function setUploadStagingPath($value)
    {
        return $this->attributes['upload_staging_path'] = $value;
    }
    // boolean # Allow relative paths in SFTP. If true, paths will not be forced to be absolute, allowing operations relative to the user's home directory.
    public function getAllowRelativePaths()
    {
        return @$this->attributes['allow_relative_paths'];
    }

    public function setAllowRelativePaths($value)
    {
        return $this->attributes['allow_relative_paths'] = $value;
    }
    // string # Internal name for your reference
    public function getName()
    {
        return @$this->attributes['name'];
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // string # Internal description for your reference
    public function getDescription()
    {
        return @$this->attributes['description'];
    }

    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    // int64 # Port for remote server.
    public function getPort()
    {
        return @$this->attributes['port'];
    }

    public function setPort($value)
    {
        return $this->attributes['port'] = $value;
    }
    // string # If set to always, uploads to this server will be uploaded first to Files.com before being sent to the remote server. This can improve performance in certain access patterns, such as high-latency connections.  It will cause data to be temporarily stored in Files.com. If set to auto, we will perform this optimization if we believe it to be a benefit in a given situation.
    public function getBufferUploads()
    {
        return @$this->attributes['buffer_uploads'];
    }

    public function setBufferUploads($value)
    {
        return $this->attributes['buffer_uploads'] = $value;
    }
    // int64 # Max number of parallel connections.  Ignored for S3 connections (we will parallelize these as much as possible).
    public function getMaxConnections()
    {
        return @$this->attributes['max_connections'];
    }

    public function setMaxConnections($value)
    {
        return $this->attributes['max_connections'] = $value;
    }
    // boolean # If true, we will ensure that all communications with this remote server are made through the primary region of the site.  This setting can also be overridden by a site-wide setting which will force it to true.
    public function getPinToSiteRegion()
    {
        return @$this->attributes['pin_to_site_region'];
    }

    public function setPinToSiteRegion($value)
    {
        return $this->attributes['pin_to_site_region'] = $value;
    }
    // string # If set, all communications with this remote server are made through the provided region.
    public function getPinnedRegion()
    {
        return @$this->attributes['pinned_region'];
    }

    public function setPinnedRegion($value)
    {
        return $this->attributes['pinned_region'] = $value;
    }
    // int64 # ID of Remote Server Credential, if applicable.
    public function getRemoteServerCredentialId()
    {
        return @$this->attributes['remote_server_credential_id'];
    }

    public function setRemoteServerCredentialId($value)
    {
        return $this->attributes['remote_server_credential_id'] = $value;
    }
    // string # S3 bucket name
    public function getS3Bucket()
    {
        return @$this->attributes['s3_bucket'];
    }

    public function setS3Bucket($value)
    {
        return $this->attributes['s3_bucket'] = $value;
    }
    // string # S3 region
    public function getS3Region()
    {
        return @$this->attributes['s3_region'];
    }

    public function setS3Region($value)
    {
        return $this->attributes['s3_region'] = $value;
    }
    // string # AWS Access Key.
    public function getAwsAccessKey()
    {
        return @$this->attributes['aws_access_key'];
    }

    public function setAwsAccessKey($value)
    {
        return $this->attributes['aws_access_key'] = $value;
    }
    // string # Remote server certificate
    public function getServerCertificate()
    {
        return @$this->attributes['server_certificate'];
    }

    public function setServerCertificate($value)
    {
        return $this->attributes['server_certificate'] = $value;
    }
    // string # Remote server SSH Host Key. If provided, we will require that the server host key matches the provided key. Uses OpenSSH format similar to what would go into ~/.ssh/known_hosts
    public function getServerHostKey()
    {
        return @$this->attributes['server_host_key'];
    }

    public function setServerHostKey($value)
    {
        return $this->attributes['server_host_key'] = $value;
    }
    // string # Remote server type.
    public function getServerType()
    {
        return @$this->attributes['server_type'];
    }

    public function setServerType($value)
    {
        return $this->attributes['server_type'] = $value;
    }
    // int64 # Workspace ID (0 for default workspace)
    public function getWorkspaceId()
    {
        return @$this->attributes['workspace_id'];
    }

    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    // string # Should we require SSL?
    public function getSsl()
    {
        return @$this->attributes['ssl'];
    }

    public function setSsl($value)
    {
        return $this->attributes['ssl'] = $value;
    }
    // string # Remote server username.
    public function getUsername()
    {
        return @$this->attributes['username'];
    }

    public function setUsername($value)
    {
        return $this->attributes['username'] = $value;
    }
    // string # Google Cloud Storage: Bucket Name
    public function getGoogleCloudStorageBucket()
    {
        return @$this->attributes['google_cloud_storage_bucket'];
    }

    public function setGoogleCloudStorageBucket($value)
    {
        return $this->attributes['google_cloud_storage_bucket'] = $value;
    }
    // string # Google Cloud Storage: Project ID
    public function getGoogleCloudStorageProjectId()
    {
        return @$this->attributes['google_cloud_storage_project_id'];
    }

    public function setGoogleCloudStorageProjectId($value)
    {
        return $this->attributes['google_cloud_storage_project_id'] = $value;
    }
    // string # Google Cloud Storage: S3-compatible Access Key.
    public function getGoogleCloudStorageS3CompatibleAccessKey()
    {
        return @$this->attributes['google_cloud_storage_s3_compatible_access_key'];
    }

    public function setGoogleCloudStorageS3CompatibleAccessKey($value)
    {
        return $this->attributes['google_cloud_storage_s3_compatible_access_key'] = $value;
    }
    // string # Backblaze B2 Cloud Storage: S3 Endpoint
    public function getBackblazeB2S3Endpoint()
    {
        return @$this->attributes['backblaze_b2_s3_endpoint'];
    }

    public function setBackblazeB2S3Endpoint($value)
    {
        return $this->attributes['backblaze_b2_s3_endpoint'] = $value;
    }
    // string # Backblaze B2 Cloud Storage: Bucket name
    public function getBackblazeB2Bucket()
    {
        return @$this->attributes['backblaze_b2_bucket'];
    }

    public function setBackblazeB2Bucket($value)
    {
        return $this->attributes['backblaze_b2_bucket'] = $value;
    }
    // string # Wasabi: Bucket name
    public function getWasabiBucket()
    {
        return @$this->attributes['wasabi_bucket'];
    }

    public function setWasabiBucket($value)
    {
        return $this->attributes['wasabi_bucket'] = $value;
    }
    // string # Wasabi: Region
    public function getWasabiRegion()
    {
        return @$this->attributes['wasabi_region'];
    }

    public function setWasabiRegion($value)
    {
        return $this->attributes['wasabi_region'] = $value;
    }
    // string # Wasabi: Access Key.
    public function getWasabiAccessKey()
    {
        return @$this->attributes['wasabi_access_key'];
    }

    public function setWasabiAccessKey($value)
    {
        return $this->attributes['wasabi_access_key'] = $value;
    }
    // string # Either `in_setup` or `complete`
    public function getAuthStatus()
    {
        return @$this->attributes['auth_status'];
    }

    public function setAuthStatus($value)
    {
        return $this->attributes['auth_status'] = $value;
    }
    // string # Describes the authorized account
    public function getAuthAccountName()
    {
        return @$this->attributes['auth_account_name'];
    }

    public function setAuthAccountName($value)
    {
        return $this->attributes['auth_account_name'] = $value;
    }
    // string # OneDrive: Either personal or business_other account types
    public function getOneDriveAccountType()
    {
        return @$this->attributes['one_drive_account_type'];
    }

    public function setOneDriveAccountType($value)
    {
        return $this->attributes['one_drive_account_type'] = $value;
    }
    // string # Azure Blob Storage: Account name
    public function getAzureBlobStorageAccount()
    {
        return @$this->attributes['azure_blob_storage_account'];
    }

    public function setAzureBlobStorageAccount($value)
    {
        return $this->attributes['azure_blob_storage_account'] = $value;
    }
    // string # Azure Blob Storage: Container name
    public function getAzureBlobStorageContainer()
    {
        return @$this->attributes['azure_blob_storage_container'];
    }

    public function setAzureBlobStorageContainer($value)
    {
        return $this->attributes['azure_blob_storage_container'] = $value;
    }
    // boolean # Azure Blob Storage: Does the storage account has hierarchical namespace feature enabled?
    public function getAzureBlobStorageHierarchicalNamespace()
    {
        return @$this->attributes['azure_blob_storage_hierarchical_namespace'];
    }

    public function setAzureBlobStorageHierarchicalNamespace($value)
    {
        return $this->attributes['azure_blob_storage_hierarchical_namespace'] = $value;
    }
    // string # Azure Blob Storage: Custom DNS suffix
    public function getAzureBlobStorageDnsSuffix()
    {
        return @$this->attributes['azure_blob_storage_dns_suffix'];
    }

    public function setAzureBlobStorageDnsSuffix($value)
    {
        return $this->attributes['azure_blob_storage_dns_suffix'] = $value;
    }
    // string # Azure Files: Storage Account name
    public function getAzureFilesStorageAccount()
    {
        return @$this->attributes['azure_files_storage_account'];
    }

    public function setAzureFilesStorageAccount($value)
    {
        return $this->attributes['azure_files_storage_account'] = $value;
    }
    // string # Azure Files:  Storage Share name
    public function getAzureFilesStorageShareName()
    {
        return @$this->attributes['azure_files_storage_share_name'];
    }

    public function setAzureFilesStorageShareName($value)
    {
        return $this->attributes['azure_files_storage_share_name'] = $value;
    }
    // string # Azure Files: Custom DNS suffix
    public function getAzureFilesStorageDnsSuffix()
    {
        return @$this->attributes['azure_files_storage_dns_suffix'];
    }

    public function setAzureFilesStorageDnsSuffix($value)
    {
        return $this->attributes['azure_files_storage_dns_suffix'] = $value;
    }
    // string # S3-compatible: Bucket name
    public function getS3CompatibleBucket()
    {
        return @$this->attributes['s3_compatible_bucket'];
    }

    public function setS3CompatibleBucket($value)
    {
        return $this->attributes['s3_compatible_bucket'] = $value;
    }
    // string # S3-compatible: endpoint
    public function getS3CompatibleEndpoint()
    {
        return @$this->attributes['s3_compatible_endpoint'];
    }

    public function setS3CompatibleEndpoint($value)
    {
        return $this->attributes['s3_compatible_endpoint'] = $value;
    }
    // string # S3-compatible: region
    public function getS3CompatibleRegion()
    {
        return @$this->attributes['s3_compatible_region'];
    }

    public function setS3CompatibleRegion($value)
    {
        return $this->attributes['s3_compatible_region'] = $value;
    }
    // string # S3-compatible: Access Key
    public function getS3CompatibleAccessKey()
    {
        return @$this->attributes['s3_compatible_access_key'];
    }

    public function setS3CompatibleAccessKey($value)
    {
        return $this->attributes['s3_compatible_access_key'] = $value;
    }
    // boolean # `true` if remote server only accepts connections from dedicated IPs
    public function getEnableDedicatedIps()
    {
        return @$this->attributes['enable_dedicated_ips'];
    }

    public function setEnableDedicatedIps($value)
    {
        return $this->attributes['enable_dedicated_ips'] = $value;
    }
    // string # Local permissions for files agent. read_only, write_only, or read_write
    public function getFilesAgentPermissionSet()
    {
        return @$this->attributes['files_agent_permission_set'];
    }

    public function setFilesAgentPermissionSet($value)
    {
        return $this->attributes['files_agent_permission_set'] = $value;
    }
    // string # Agent local root path
    public function getFilesAgentRoot()
    {
        return @$this->attributes['files_agent_root'];
    }

    public function setFilesAgentRoot($value)
    {
        return $this->attributes['files_agent_root'] = $value;
    }
    // string # Files Agent API Token
    public function getFilesAgentApiToken()
    {
        return @$this->attributes['files_agent_api_token'];
    }

    public function setFilesAgentApiToken($value)
    {
        return $this->attributes['files_agent_api_token'] = $value;
    }
    // string # Files Agent version
    public function getFilesAgentVersion()
    {
        return @$this->attributes['files_agent_version'];
    }

    public function setFilesAgentVersion($value)
    {
        return $this->attributes['files_agent_version'] = $value;
    }
    // boolean # If true, the Files Agent is up to date.
    public function getFilesAgentUpToDate()
    {
        return @$this->attributes['files_agent_up_to_date'];
    }

    public function setFilesAgentUpToDate($value)
    {
        return $this->attributes['files_agent_up_to_date'] = $value;
    }
    // string # Latest available Files Agent version
    public function getFilesAgentLatestVersion()
    {
        return @$this->attributes['files_agent_latest_version'];
    }

    public function setFilesAgentLatestVersion($value)
    {
        return $this->attributes['files_agent_latest_version'] = $value;
    }
    // boolean # Files Agent supports receiving push updates
    public function getFilesAgentSupportsPushUpdates()
    {
        return @$this->attributes['files_agent_supports_push_updates'];
    }

    public function setFilesAgentSupportsPushUpdates($value)
    {
        return $this->attributes['files_agent_supports_push_updates'] = $value;
    }
    // int64 # Route traffic to outbound on a files-agent
    public function getOutboundAgentId()
    {
        return @$this->attributes['outbound_agent_id'];
    }

    public function setOutboundAgentId($value)
    {
        return $this->attributes['outbound_agent_id'] = $value;
    }
    // string # Filebase: Bucket name
    public function getFilebaseBucket()
    {
        return @$this->attributes['filebase_bucket'];
    }

    public function setFilebaseBucket($value)
    {
        return $this->attributes['filebase_bucket'] = $value;
    }
    // string # Filebase: Access Key.
    public function getFilebaseAccessKey()
    {
        return @$this->attributes['filebase_access_key'];
    }

    public function setFilebaseAccessKey($value)
    {
        return $this->attributes['filebase_access_key'] = $value;
    }
    // string # Cloudflare: Bucket name
    public function getCloudflareBucket()
    {
        return @$this->attributes['cloudflare_bucket'];
    }

    public function setCloudflareBucket($value)
    {
        return $this->attributes['cloudflare_bucket'] = $value;
    }
    // string # Cloudflare: Access Key.
    public function getCloudflareAccessKey()
    {
        return @$this->attributes['cloudflare_access_key'];
    }

    public function setCloudflareAccessKey($value)
    {
        return $this->attributes['cloudflare_access_key'] = $value;
    }
    // string # Cloudflare: endpoint
    public function getCloudflareEndpoint()
    {
        return @$this->attributes['cloudflare_endpoint'];
    }

    public function setCloudflareEndpoint($value)
    {
        return $this->attributes['cloudflare_endpoint'] = $value;
    }
    // boolean # Dropbox: If true, list Team folders in root?
    public function getDropboxTeams()
    {
        return @$this->attributes['dropbox_teams'];
    }

    public function setDropboxTeams($value)
    {
        return $this->attributes['dropbox_teams'] = $value;
    }
    // string # Linode: Bucket name
    public function getLinodeBucket()
    {
        return @$this->attributes['linode_bucket'];
    }

    public function setLinodeBucket($value)
    {
        return $this->attributes['linode_bucket'] = $value;
    }
    // string # Linode: Access Key
    public function getLinodeAccessKey()
    {
        return @$this->attributes['linode_access_key'];
    }

    public function setLinodeAccessKey($value)
    {
        return $this->attributes['linode_access_key'] = $value;
    }
    // string # Linode: region
    public function getLinodeRegion()
    {
        return @$this->attributes['linode_region'];
    }

    public function setLinodeRegion($value)
    {
        return $this->attributes['linode_region'] = $value;
    }
    // boolean # If true, this remote server supports file versioning. This value is determined automatically by Files.com.
    public function getSupportsVersioning()
    {
        return @$this->attributes['supports_versioning'];
    }

    public function setSupportsVersioning($value)
    {
        return $this->attributes['supports_versioning'] = $value;
    }
    // string # Password, if needed.
    public function getPassword()
    {
        return @$this->attributes['password'];
    }

    public function setPassword($value)
    {
        return $this->attributes['password'] = $value;
    }
    // string # Private key, if needed.
    public function getPrivateKey()
    {
        return @$this->attributes['private_key'];
    }

    public function setPrivateKey($value)
    {
        return $this->attributes['private_key'] = $value;
    }
    // string # Passphrase for private key if needed.
    public function getPrivateKeyPassphrase()
    {
        return @$this->attributes['private_key_passphrase'];
    }

    public function setPrivateKeyPassphrase($value)
    {
        return $this->attributes['private_key_passphrase'] = $value;
    }
    // boolean # Reset authenticated account?
    public function getResetAuthentication()
    {
        return @$this->attributes['reset_authentication'];
    }

    public function setResetAuthentication($value)
    {
        return $this->attributes['reset_authentication'] = $value;
    }
    // string # SSL client certificate.
    public function getSslCertificate()
    {
        return @$this->attributes['ssl_certificate'];
    }

    public function setSslCertificate($value)
    {
        return $this->attributes['ssl_certificate'] = $value;
    }
    // string # AWS: secret key.
    public function getAwsSecretKey()
    {
        return @$this->attributes['aws_secret_key'];
    }

    public function setAwsSecretKey($value)
    {
        return $this->attributes['aws_secret_key'] = $value;
    }
    // string # Azure Blob Storage: Access Key
    public function getAzureBlobStorageAccessKey()
    {
        return @$this->attributes['azure_blob_storage_access_key'];
    }

    public function setAzureBlobStorageAccessKey($value)
    {
        return $this->attributes['azure_blob_storage_access_key'] = $value;
    }
    // string # Azure Blob Storage: Shared Access Signature (SAS) token
    public function getAzureBlobStorageSasToken()
    {
        return @$this->attributes['azure_blob_storage_sas_token'];
    }

    public function setAzureBlobStorageSasToken($value)
    {
        return $this->attributes['azure_blob_storage_sas_token'] = $value;
    }
    // string # Azure File Storage: Access Key
    public function getAzureFilesStorageAccessKey()
    {
        return @$this->attributes['azure_files_storage_access_key'];
    }

    public function setAzureFilesStorageAccessKey($value)
    {
        return $this->attributes['azure_files_storage_access_key'] = $value;
    }
    // string # Azure File Storage: Shared Access Signature (SAS) token
    public function getAzureFilesStorageSasToken()
    {
        return @$this->attributes['azure_files_storage_sas_token'];
    }

    public function setAzureFilesStorageSasToken($value)
    {
        return $this->attributes['azure_files_storage_sas_token'] = $value;
    }
    // string # Backblaze B2 Cloud Storage: applicationKey
    public function getBackblazeB2ApplicationKey()
    {
        return @$this->attributes['backblaze_b2_application_key'];
    }

    public function setBackblazeB2ApplicationKey($value)
    {
        return $this->attributes['backblaze_b2_application_key'] = $value;
    }
    // string # Backblaze B2 Cloud Storage: keyID
    public function getBackblazeB2KeyId()
    {
        return @$this->attributes['backblaze_b2_key_id'];
    }

    public function setBackblazeB2KeyId($value)
    {
        return $this->attributes['backblaze_b2_key_id'] = $value;
    }
    // string # Cloudflare: Secret Key
    public function getCloudflareSecretKey()
    {
        return @$this->attributes['cloudflare_secret_key'];
    }

    public function setCloudflareSecretKey($value)
    {
        return $this->attributes['cloudflare_secret_key'] = $value;
    }
    // string # Filebase: Secret Key
    public function getFilebaseSecretKey()
    {
        return @$this->attributes['filebase_secret_key'];
    }

    public function setFilebaseSecretKey($value)
    {
        return $this->attributes['filebase_secret_key'] = $value;
    }
    // string # Google Cloud Storage: JSON file that contains the private key. To generate see https://cloud.google.com/storage/docs/json_api/v1/how-tos/authorizing#APIKey
    public function getGoogleCloudStorageCredentialsJson()
    {
        return @$this->attributes['google_cloud_storage_credentials_json'];
    }

    public function setGoogleCloudStorageCredentialsJson($value)
    {
        return $this->attributes['google_cloud_storage_credentials_json'] = $value;
    }
    // string # Google Cloud Storage: S3-compatible secret key
    public function getGoogleCloudStorageS3CompatibleSecretKey()
    {
        return @$this->attributes['google_cloud_storage_s3_compatible_secret_key'];
    }

    public function setGoogleCloudStorageS3CompatibleSecretKey($value)
    {
        return $this->attributes['google_cloud_storage_s3_compatible_secret_key'] = $value;
    }
    // string # Linode: Secret Key
    public function getLinodeSecretKey()
    {
        return @$this->attributes['linode_secret_key'];
    }

    public function setLinodeSecretKey($value)
    {
        return $this->attributes['linode_secret_key'] = $value;
    }
    // string # S3-compatible: Secret Key
    public function getS3CompatibleSecretKey()
    {
        return @$this->attributes['s3_compatible_secret_key'];
    }

    public function setS3CompatibleSecretKey($value)
    {
        return $this->attributes['s3_compatible_secret_key'] = $value;
    }
    // string # Wasabi: Secret Key
    public function getWasabiSecretKey()
    {
        return @$this->attributes['wasabi_secret_key'];
    }

    public function setWasabiSecretKey($value)
    {
        return $this->attributes['wasabi_secret_key'] = $value;
    }

    // Push update to Files Agent
    public function agentPushUpdate($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['id']) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/remote_servers/' . @$params['id'] . '/agent_push_update', 'POST', $params, $this->options);
        return new AgentPushUpdate((array) (@$response->data ?: []), $this->options);
    }

    // Post local changes, check in, and download configuration file (used by some Remote Server integrations, such as the Files.com Agent)
    //
    // Parameters:
    //   api_token - string - Files Agent API Token
    //   permission_set - string - The permission set for the agent ['read_write', 'read_only', 'write_only']
    //   root - string - The root directory for the agent
    //   hostname - string
    //   port - int64 - Incoming port for files agent connections
    //   status - string - either running or shutdown
    //   config_version - string - agent config version
    //   private_key - string - The private key for the agent
    //   public_key - string - public key
    //   server_host_key - string
    //   subdomain - string - Files.com subdomain site name
    public function configurationFile($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['id']) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (@$params['api_token'] && !is_string(@$params['api_token'])) {
            throw new \Files\Exception\InvalidParameterException('$api_token must be of type string; received ' . gettype(@$params['api_token']));
        }

        if (@$params['permission_set'] && !is_string(@$params['permission_set'])) {
            throw new \Files\Exception\InvalidParameterException('$permission_set must be of type string; received ' . gettype(@$params['permission_set']));
        }

        if (@$params['root'] && !is_string(@$params['root'])) {
            throw new \Files\Exception\InvalidParameterException('$root must be of type string; received ' . gettype(@$params['root']));
        }

        if (@$params['hostname'] && !is_string(@$params['hostname'])) {
            throw new \Files\Exception\InvalidParameterException('$hostname must be of type string; received ' . gettype(@$params['hostname']));
        }

        if (@$params['port'] && !is_int(@$params['port'])) {
            throw new \Files\Exception\InvalidParameterException('$port must be of type int; received ' . gettype(@$params['port']));
        }

        if (@$params['status'] && !is_string(@$params['status'])) {
            throw new \Files\Exception\InvalidParameterException('$status must be of type string; received ' . gettype(@$params['status']));
        }

        if (@$params['config_version'] && !is_string(@$params['config_version'])) {
            throw new \Files\Exception\InvalidParameterException('$config_version must be of type string; received ' . gettype(@$params['config_version']));
        }

        if (@$params['private_key'] && !is_string(@$params['private_key'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key must be of type string; received ' . gettype(@$params['private_key']));
        }

        if (@$params['public_key'] && !is_string(@$params['public_key'])) {
            throw new \Files\Exception\InvalidParameterException('$public_key must be of type string; received ' . gettype(@$params['public_key']));
        }

        if (@$params['server_host_key'] && !is_string(@$params['server_host_key'])) {
            throw new \Files\Exception\InvalidParameterException('$server_host_key must be of type string; received ' . gettype(@$params['server_host_key']));
        }

        if (@$params['subdomain'] && !is_string(@$params['subdomain'])) {
            throw new \Files\Exception\InvalidParameterException('$subdomain must be of type string; received ' . gettype(@$params['subdomain']));
        }

        $response = Api::sendRequest('/remote_servers/' . @$params['id'] . '/configuration_file', 'POST', $params, $this->options);
        return new RemoteServerConfigurationFile((array) (@$response->data ?: []), $this->options);
    }

    // Parameters:
    //   password - string - Password, if needed.
    //   private_key - string - Private key, if needed.
    //   private_key_passphrase - string - Passphrase for private key if needed.
    //   reset_authentication - boolean - Reset authenticated account?
    //   ssl_certificate - string - SSL client certificate.
    //   aws_secret_key - string - AWS: secret key.
    //   azure_blob_storage_access_key - string - Azure Blob Storage: Access Key
    //   azure_blob_storage_sas_token - string - Azure Blob Storage: Shared Access Signature (SAS) token
    //   azure_files_storage_access_key - string - Azure File Storage: Access Key
    //   azure_files_storage_sas_token - string - Azure File Storage: Shared Access Signature (SAS) token
    //   backblaze_b2_application_key - string - Backblaze B2 Cloud Storage: applicationKey
    //   backblaze_b2_key_id - string - Backblaze B2 Cloud Storage: keyID
    //   cloudflare_secret_key - string - Cloudflare: Secret Key
    //   filebase_secret_key - string - Filebase: Secret Key
    //   google_cloud_storage_credentials_json - string - Google Cloud Storage: JSON file that contains the private key. To generate see https://cloud.google.com/storage/docs/json_api/v1/how-tos/authorizing#APIKey
    //   google_cloud_storage_s3_compatible_secret_key - string - Google Cloud Storage: S3-compatible secret key
    //   linode_secret_key - string - Linode: Secret Key
    //   s3_compatible_secret_key - string - S3-compatible: Secret Key
    //   wasabi_secret_key - string - Wasabi: Secret Key
    //   allow_relative_paths - boolean - Allow relative paths in SFTP. If true, paths will not be forced to be absolute, allowing operations relative to the user's home directory.
    //   aws_access_key - string - AWS Access Key.
    //   azure_blob_storage_account - string - Azure Blob Storage: Account name
    //   azure_blob_storage_container - string - Azure Blob Storage: Container name
    //   azure_blob_storage_dns_suffix - string - Azure Blob Storage: Custom DNS suffix
    //   azure_blob_storage_hierarchical_namespace - boolean - Azure Blob Storage: Does the storage account has hierarchical namespace feature enabled?
    //   azure_files_storage_account - string - Azure Files: Storage Account name
    //   azure_files_storage_dns_suffix - string - Azure Files: Custom DNS suffix
    //   azure_files_storage_share_name - string - Azure Files:  Storage Share name
    //   backblaze_b2_bucket - string - Backblaze B2 Cloud Storage: Bucket name
    //   backblaze_b2_s3_endpoint - string - Backblaze B2 Cloud Storage: S3 Endpoint
    //   buffer_uploads - string - If set to always, uploads to this server will be uploaded first to Files.com before being sent to the remote server. This can improve performance in certain access patterns, such as high-latency connections.  It will cause data to be temporarily stored in Files.com. If set to auto, we will perform this optimization if we believe it to be a benefit in a given situation.
    //   cloudflare_access_key - string - Cloudflare: Access Key.
    //   cloudflare_bucket - string - Cloudflare: Bucket name
    //   cloudflare_endpoint - string - Cloudflare: endpoint
    //   description - string - Internal description for your reference
    //   dropbox_teams - boolean - Dropbox: If true, list Team folders in root?
    //   enable_dedicated_ips - boolean - `true` if remote server only accepts connections from dedicated IPs
    //   filebase_access_key - string - Filebase: Access Key.
    //   filebase_bucket - string - Filebase: Bucket name
    //   files_agent_permission_set - string - Local permissions for files agent. read_only, write_only, or read_write
    //   files_agent_root - string - Agent local root path
    //   files_agent_version - string - Files Agent version
    //   outbound_agent_id - int64 - Route traffic to outbound on a files-agent
    //   google_cloud_storage_bucket - string - Google Cloud Storage: Bucket Name
    //   google_cloud_storage_project_id - string - Google Cloud Storage: Project ID
    //   google_cloud_storage_s3_compatible_access_key - string - Google Cloud Storage: S3-compatible Access Key.
    //   hostname - string - Hostname or IP address
    //   linode_access_key - string - Linode: Access Key
    //   linode_bucket - string - Linode: Bucket name
    //   linode_region - string - Linode: region
    //   max_connections - int64 - Max number of parallel connections.  Ignored for S3 connections (we will parallelize these as much as possible).
    //   name - string - Internal name for your reference
    //   one_drive_account_type - string - OneDrive: Either personal or business_other account types
    //   pin_to_site_region - boolean - If true, we will ensure that all communications with this remote server are made through the primary region of the site.  This setting can also be overridden by a site-wide setting which will force it to true.
    //   port - int64 - Port for remote server.
    //   upload_staging_path - string - Upload staging path.  Applies to SFTP only.  If a path is provided here, files will first be uploaded to this path on the remote folder and the moved into the final correct path via an SFTP move command.  This is required by some remote MFT systems to emulate atomic uploads, which are otherwise not supoprted by SFTP.
    //   remote_server_credential_id - int64 - ID of Remote Server Credential, if applicable.
    //   s3_bucket - string - S3 bucket name
    //   s3_compatible_access_key - string - S3-compatible: Access Key
    //   s3_compatible_bucket - string - S3-compatible: Bucket name
    //   s3_compatible_endpoint - string - S3-compatible: endpoint
    //   s3_compatible_region - string - S3-compatible: region
    //   s3_region - string - S3 region
    //   server_certificate - string - Remote server certificate
    //   server_host_key - string - Remote server SSH Host Key. If provided, we will require that the server host key matches the provided key. Uses OpenSSH format similar to what would go into ~/.ssh/known_hosts
    //   server_type - string - Remote server type.
    //   ssl - string - Should we require SSL?
    //   username - string - Remote server username.
    //   wasabi_access_key - string - Wasabi: Access Key.
    //   wasabi_bucket - string - Wasabi: Bucket name
    //   wasabi_region - string - Wasabi: Region
    public function update($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['id']) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (@$params['password'] && !is_string(@$params['password'])) {
            throw new \Files\Exception\InvalidParameterException('$password must be of type string; received ' . gettype(@$params['password']));
        }

        if (@$params['private_key'] && !is_string(@$params['private_key'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key must be of type string; received ' . gettype(@$params['private_key']));
        }

        if (@$params['private_key_passphrase'] && !is_string(@$params['private_key_passphrase'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key_passphrase must be of type string; received ' . gettype(@$params['private_key_passphrase']));
        }

        if (@$params['ssl_certificate'] && !is_string(@$params['ssl_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$ssl_certificate must be of type string; received ' . gettype(@$params['ssl_certificate']));
        }

        if (@$params['aws_secret_key'] && !is_string(@$params['aws_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$aws_secret_key must be of type string; received ' . gettype(@$params['aws_secret_key']));
        }

        if (@$params['azure_blob_storage_access_key'] && !is_string(@$params['azure_blob_storage_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_access_key must be of type string; received ' . gettype(@$params['azure_blob_storage_access_key']));
        }

        if (@$params['azure_blob_storage_sas_token'] && !is_string(@$params['azure_blob_storage_sas_token'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_sas_token must be of type string; received ' . gettype(@$params['azure_blob_storage_sas_token']));
        }

        if (@$params['azure_files_storage_access_key'] && !is_string(@$params['azure_files_storage_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_access_key must be of type string; received ' . gettype(@$params['azure_files_storage_access_key']));
        }

        if (@$params['azure_files_storage_sas_token'] && !is_string(@$params['azure_files_storage_sas_token'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_sas_token must be of type string; received ' . gettype(@$params['azure_files_storage_sas_token']));
        }

        if (@$params['backblaze_b2_application_key'] && !is_string(@$params['backblaze_b2_application_key'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_application_key must be of type string; received ' . gettype(@$params['backblaze_b2_application_key']));
        }

        if (@$params['backblaze_b2_key_id'] && !is_string(@$params['backblaze_b2_key_id'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_key_id must be of type string; received ' . gettype(@$params['backblaze_b2_key_id']));
        }

        if (@$params['cloudflare_secret_key'] && !is_string(@$params['cloudflare_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_secret_key must be of type string; received ' . gettype(@$params['cloudflare_secret_key']));
        }

        if (@$params['filebase_secret_key'] && !is_string(@$params['filebase_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$filebase_secret_key must be of type string; received ' . gettype(@$params['filebase_secret_key']));
        }

        if (@$params['google_cloud_storage_credentials_json'] && !is_string(@$params['google_cloud_storage_credentials_json'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_credentials_json must be of type string; received ' . gettype(@$params['google_cloud_storage_credentials_json']));
        }

        if (@$params['google_cloud_storage_s3_compatible_secret_key'] && !is_string(@$params['google_cloud_storage_s3_compatible_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_s3_compatible_secret_key must be of type string; received ' . gettype(@$params['google_cloud_storage_s3_compatible_secret_key']));
        }

        if (@$params['linode_secret_key'] && !is_string(@$params['linode_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_secret_key must be of type string; received ' . gettype(@$params['linode_secret_key']));
        }

        if (@$params['s3_compatible_secret_key'] && !is_string(@$params['s3_compatible_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_secret_key must be of type string; received ' . gettype(@$params['s3_compatible_secret_key']));
        }

        if (@$params['wasabi_secret_key'] && !is_string(@$params['wasabi_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_secret_key must be of type string; received ' . gettype(@$params['wasabi_secret_key']));
        }

        if (@$params['aws_access_key'] && !is_string(@$params['aws_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$aws_access_key must be of type string; received ' . gettype(@$params['aws_access_key']));
        }

        if (@$params['azure_blob_storage_account'] && !is_string(@$params['azure_blob_storage_account'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_account must be of type string; received ' . gettype(@$params['azure_blob_storage_account']));
        }

        if (@$params['azure_blob_storage_container'] && !is_string(@$params['azure_blob_storage_container'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_container must be of type string; received ' . gettype(@$params['azure_blob_storage_container']));
        }

        if (@$params['azure_blob_storage_dns_suffix'] && !is_string(@$params['azure_blob_storage_dns_suffix'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_dns_suffix must be of type string; received ' . gettype(@$params['azure_blob_storage_dns_suffix']));
        }

        if (@$params['azure_files_storage_account'] && !is_string(@$params['azure_files_storage_account'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_account must be of type string; received ' . gettype(@$params['azure_files_storage_account']));
        }

        if (@$params['azure_files_storage_dns_suffix'] && !is_string(@$params['azure_files_storage_dns_suffix'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_dns_suffix must be of type string; received ' . gettype(@$params['azure_files_storage_dns_suffix']));
        }

        if (@$params['azure_files_storage_share_name'] && !is_string(@$params['azure_files_storage_share_name'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_share_name must be of type string; received ' . gettype(@$params['azure_files_storage_share_name']));
        }

        if (@$params['backblaze_b2_bucket'] && !is_string(@$params['backblaze_b2_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_bucket must be of type string; received ' . gettype(@$params['backblaze_b2_bucket']));
        }

        if (@$params['backblaze_b2_s3_endpoint'] && !is_string(@$params['backblaze_b2_s3_endpoint'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_s3_endpoint must be of type string; received ' . gettype(@$params['backblaze_b2_s3_endpoint']));
        }

        if (@$params['buffer_uploads'] && !is_string(@$params['buffer_uploads'])) {
            throw new \Files\Exception\InvalidParameterException('$buffer_uploads must be of type string; received ' . gettype(@$params['buffer_uploads']));
        }

        if (@$params['cloudflare_access_key'] && !is_string(@$params['cloudflare_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_access_key must be of type string; received ' . gettype(@$params['cloudflare_access_key']));
        }

        if (@$params['cloudflare_bucket'] && !is_string(@$params['cloudflare_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_bucket must be of type string; received ' . gettype(@$params['cloudflare_bucket']));
        }

        if (@$params['cloudflare_endpoint'] && !is_string(@$params['cloudflare_endpoint'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_endpoint must be of type string; received ' . gettype(@$params['cloudflare_endpoint']));
        }

        if (@$params['description'] && !is_string(@$params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (@$params['filebase_access_key'] && !is_string(@$params['filebase_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$filebase_access_key must be of type string; received ' . gettype(@$params['filebase_access_key']));
        }

        if (@$params['filebase_bucket'] && !is_string(@$params['filebase_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$filebase_bucket must be of type string; received ' . gettype(@$params['filebase_bucket']));
        }

        if (@$params['files_agent_permission_set'] && !is_string(@$params['files_agent_permission_set'])) {
            throw new \Files\Exception\InvalidParameterException('$files_agent_permission_set must be of type string; received ' . gettype(@$params['files_agent_permission_set']));
        }

        if (@$params['files_agent_root'] && !is_string(@$params['files_agent_root'])) {
            throw new \Files\Exception\InvalidParameterException('$files_agent_root must be of type string; received ' . gettype(@$params['files_agent_root']));
        }

        if (@$params['files_agent_version'] && !is_string(@$params['files_agent_version'])) {
            throw new \Files\Exception\InvalidParameterException('$files_agent_version must be of type string; received ' . gettype(@$params['files_agent_version']));
        }

        if (@$params['outbound_agent_id'] && !is_int(@$params['outbound_agent_id'])) {
            throw new \Files\Exception\InvalidParameterException('$outbound_agent_id must be of type int; received ' . gettype(@$params['outbound_agent_id']));
        }

        if (@$params['google_cloud_storage_bucket'] && !is_string(@$params['google_cloud_storage_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_bucket must be of type string; received ' . gettype(@$params['google_cloud_storage_bucket']));
        }

        if (@$params['google_cloud_storage_project_id'] && !is_string(@$params['google_cloud_storage_project_id'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_project_id must be of type string; received ' . gettype(@$params['google_cloud_storage_project_id']));
        }

        if (@$params['google_cloud_storage_s3_compatible_access_key'] && !is_string(@$params['google_cloud_storage_s3_compatible_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_s3_compatible_access_key must be of type string; received ' . gettype(@$params['google_cloud_storage_s3_compatible_access_key']));
        }

        if (@$params['hostname'] && !is_string(@$params['hostname'])) {
            throw new \Files\Exception\InvalidParameterException('$hostname must be of type string; received ' . gettype(@$params['hostname']));
        }

        if (@$params['linode_access_key'] && !is_string(@$params['linode_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_access_key must be of type string; received ' . gettype(@$params['linode_access_key']));
        }

        if (@$params['linode_bucket'] && !is_string(@$params['linode_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_bucket must be of type string; received ' . gettype(@$params['linode_bucket']));
        }

        if (@$params['linode_region'] && !is_string(@$params['linode_region'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_region must be of type string; received ' . gettype(@$params['linode_region']));
        }

        if (@$params['max_connections'] && !is_int(@$params['max_connections'])) {
            throw new \Files\Exception\InvalidParameterException('$max_connections must be of type int; received ' . gettype(@$params['max_connections']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['one_drive_account_type'] && !is_string(@$params['one_drive_account_type'])) {
            throw new \Files\Exception\InvalidParameterException('$one_drive_account_type must be of type string; received ' . gettype(@$params['one_drive_account_type']));
        }

        if (@$params['port'] && !is_int(@$params['port'])) {
            throw new \Files\Exception\InvalidParameterException('$port must be of type int; received ' . gettype(@$params['port']));
        }

        if (@$params['upload_staging_path'] && !is_string(@$params['upload_staging_path'])) {
            throw new \Files\Exception\InvalidParameterException('$upload_staging_path must be of type string; received ' . gettype(@$params['upload_staging_path']));
        }

        if (@$params['remote_server_credential_id'] && !is_int(@$params['remote_server_credential_id'])) {
            throw new \Files\Exception\InvalidParameterException('$remote_server_credential_id must be of type int; received ' . gettype(@$params['remote_server_credential_id']));
        }

        if (@$params['s3_bucket'] && !is_string(@$params['s3_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_bucket must be of type string; received ' . gettype(@$params['s3_bucket']));
        }

        if (@$params['s3_compatible_access_key'] && !is_string(@$params['s3_compatible_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_access_key must be of type string; received ' . gettype(@$params['s3_compatible_access_key']));
        }

        if (@$params['s3_compatible_bucket'] && !is_string(@$params['s3_compatible_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_bucket must be of type string; received ' . gettype(@$params['s3_compatible_bucket']));
        }

        if (@$params['s3_compatible_endpoint'] && !is_string(@$params['s3_compatible_endpoint'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_endpoint must be of type string; received ' . gettype(@$params['s3_compatible_endpoint']));
        }

        if (@$params['s3_compatible_region'] && !is_string(@$params['s3_compatible_region'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_region must be of type string; received ' . gettype(@$params['s3_compatible_region']));
        }

        if (@$params['s3_region'] && !is_string(@$params['s3_region'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_region must be of type string; received ' . gettype(@$params['s3_region']));
        }

        if (@$params['server_certificate'] && !is_string(@$params['server_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$server_certificate must be of type string; received ' . gettype(@$params['server_certificate']));
        }

        if (@$params['server_host_key'] && !is_string(@$params['server_host_key'])) {
            throw new \Files\Exception\InvalidParameterException('$server_host_key must be of type string; received ' . gettype(@$params['server_host_key']));
        }

        if (@$params['server_type'] && !is_string(@$params['server_type'])) {
            throw new \Files\Exception\InvalidParameterException('$server_type must be of type string; received ' . gettype(@$params['server_type']));
        }

        if (@$params['ssl'] && !is_string(@$params['ssl'])) {
            throw new \Files\Exception\InvalidParameterException('$ssl must be of type string; received ' . gettype(@$params['ssl']));
        }

        if (@$params['username'] && !is_string(@$params['username'])) {
            throw new \Files\Exception\InvalidParameterException('$username must be of type string; received ' . gettype(@$params['username']));
        }

        if (@$params['wasabi_access_key'] && !is_string(@$params['wasabi_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_access_key must be of type string; received ' . gettype(@$params['wasabi_access_key']));
        }

        if (@$params['wasabi_bucket'] && !is_string(@$params['wasabi_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_bucket must be of type string; received ' . gettype(@$params['wasabi_bucket']));
        }

        if (@$params['wasabi_region'] && !is_string(@$params['wasabi_region'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_region must be of type string; received ' . gettype(@$params['wasabi_region']));
        }

        $response = Api::sendRequest('/remote_servers/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new RemoteServer((array) (@$response->data ?: []), $this->options);
    }

    public function delete($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['id']) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/remote_servers/' . @$params['id'] . '', 'DELETE', $params, $this->options);
        return;
    }

    public function destroy($params = [])
    {
        $this->delete($params);
        return;
    }

    public function save()
    {
        if (@$this->attributes['id']) {
            $new_obj = $this->update($this->attributes);
            $this->attributes = $new_obj->attributes;
            return true;
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `name`, `server_type`, `backblaze_b2_bucket`, `google_cloud_storage_bucket`, `wasabi_bucket`, `s3_bucket`, `azure_blob_storage_container`, `azure_files_storage_share_name`, `s3_compatible_bucket`, `filebase_bucket`, `cloudflare_bucket` or `linode_bucket`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `name`, `server_type`, `workspace_id`, `backblaze_b2_bucket`, `google_cloud_storage_bucket`, `wasabi_bucket`, `s3_bucket`, `azure_blob_storage_container`, `azure_files_storage_share_name`, `s3_compatible_bucket`, `filebase_bucket`, `cloudflare_bucket` or `linode_bucket`. Valid field combinations are `[ server_type, name ]`, `[ workspace_id, name ]`, `[ backblaze_b2_bucket, name ]`, `[ google_cloud_storage_bucket, name ]`, `[ wasabi_bucket, name ]`, `[ s3_bucket, name ]`, `[ azure_blob_storage_container, name ]`, `[ azure_files_storage_share_name, name ]`, `[ s3_compatible_bucket, name ]`, `[ filebase_bucket, name ]`, `[ cloudflare_bucket, name ]`, `[ linode_bucket, name ]`, `[ workspace_id, server_type ]` or `[ workspace_id, server_type, name ]`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `name`, `backblaze_b2_bucket`, `google_cloud_storage_bucket`, `wasabi_bucket`, `s3_bucket`, `azure_blob_storage_container`, `azure_files_storage_share_name`, `s3_compatible_bucket`, `filebase_bucket`, `cloudflare_bucket` or `linode_bucket`. Valid field combinations are `[ backblaze_b2_bucket, name ]`, `[ google_cloud_storage_bucket, name ]`, `[ wasabi_bucket, name ]`, `[ s3_bucket, name ]`, `[ azure_blob_storage_container, name ]`, `[ azure_files_storage_share_name, name ]`, `[ s3_compatible_bucket, name ]`, `[ filebase_bucket, name ]`, `[ cloudflare_bucket, name ]` or `[ linode_bucket, name ]`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/remote_servers', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new RemoteServer((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Remote Server ID.
    public static function find($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (!@$params['id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/remote_servers/' . @$params['id'] . '', 'GET', $params, $options);

        return new RemoteServer((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   id (required) - int64 - Remote Server ID.
    public static function findConfigurationFile($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (!@$params['id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/remote_servers/' . @$params['id'] . '/configuration_file', 'GET', $params, $options);

        return new RemoteServerConfigurationFile((array) (@$response->data ?: []), $options);
    }

    // Parameters:
    //   password - string - Password, if needed.
    //   private_key - string - Private key, if needed.
    //   private_key_passphrase - string - Passphrase for private key if needed.
    //   reset_authentication - boolean - Reset authenticated account?
    //   ssl_certificate - string - SSL client certificate.
    //   aws_secret_key - string - AWS: secret key.
    //   azure_blob_storage_access_key - string - Azure Blob Storage: Access Key
    //   azure_blob_storage_sas_token - string - Azure Blob Storage: Shared Access Signature (SAS) token
    //   azure_files_storage_access_key - string - Azure File Storage: Access Key
    //   azure_files_storage_sas_token - string - Azure File Storage: Shared Access Signature (SAS) token
    //   backblaze_b2_application_key - string - Backblaze B2 Cloud Storage: applicationKey
    //   backblaze_b2_key_id - string - Backblaze B2 Cloud Storage: keyID
    //   cloudflare_secret_key - string - Cloudflare: Secret Key
    //   filebase_secret_key - string - Filebase: Secret Key
    //   google_cloud_storage_credentials_json - string - Google Cloud Storage: JSON file that contains the private key. To generate see https://cloud.google.com/storage/docs/json_api/v1/how-tos/authorizing#APIKey
    //   google_cloud_storage_s3_compatible_secret_key - string - Google Cloud Storage: S3-compatible secret key
    //   linode_secret_key - string - Linode: Secret Key
    //   s3_compatible_secret_key - string - S3-compatible: Secret Key
    //   wasabi_secret_key - string - Wasabi: Secret Key
    //   allow_relative_paths - boolean - Allow relative paths in SFTP. If true, paths will not be forced to be absolute, allowing operations relative to the user's home directory.
    //   aws_access_key - string - AWS Access Key.
    //   azure_blob_storage_account - string - Azure Blob Storage: Account name
    //   azure_blob_storage_container - string - Azure Blob Storage: Container name
    //   azure_blob_storage_dns_suffix - string - Azure Blob Storage: Custom DNS suffix
    //   azure_blob_storage_hierarchical_namespace - boolean - Azure Blob Storage: Does the storage account has hierarchical namespace feature enabled?
    //   azure_files_storage_account - string - Azure Files: Storage Account name
    //   azure_files_storage_dns_suffix - string - Azure Files: Custom DNS suffix
    //   azure_files_storage_share_name - string - Azure Files:  Storage Share name
    //   backblaze_b2_bucket - string - Backblaze B2 Cloud Storage: Bucket name
    //   backblaze_b2_s3_endpoint - string - Backblaze B2 Cloud Storage: S3 Endpoint
    //   buffer_uploads - string - If set to always, uploads to this server will be uploaded first to Files.com before being sent to the remote server. This can improve performance in certain access patterns, such as high-latency connections.  It will cause data to be temporarily stored in Files.com. If set to auto, we will perform this optimization if we believe it to be a benefit in a given situation.
    //   cloudflare_access_key - string - Cloudflare: Access Key.
    //   cloudflare_bucket - string - Cloudflare: Bucket name
    //   cloudflare_endpoint - string - Cloudflare: endpoint
    //   description - string - Internal description for your reference
    //   dropbox_teams - boolean - Dropbox: If true, list Team folders in root?
    //   enable_dedicated_ips - boolean - `true` if remote server only accepts connections from dedicated IPs
    //   filebase_access_key - string - Filebase: Access Key.
    //   filebase_bucket - string - Filebase: Bucket name
    //   files_agent_permission_set - string - Local permissions for files agent. read_only, write_only, or read_write
    //   files_agent_root - string - Agent local root path
    //   files_agent_version - string - Files Agent version
    //   outbound_agent_id - int64 - Route traffic to outbound on a files-agent
    //   google_cloud_storage_bucket - string - Google Cloud Storage: Bucket Name
    //   google_cloud_storage_project_id - string - Google Cloud Storage: Project ID
    //   google_cloud_storage_s3_compatible_access_key - string - Google Cloud Storage: S3-compatible Access Key.
    //   hostname - string - Hostname or IP address
    //   linode_access_key - string - Linode: Access Key
    //   linode_bucket - string - Linode: Bucket name
    //   linode_region - string - Linode: region
    //   max_connections - int64 - Max number of parallel connections.  Ignored for S3 connections (we will parallelize these as much as possible).
    //   name - string - Internal name for your reference
    //   one_drive_account_type - string - OneDrive: Either personal or business_other account types
    //   pin_to_site_region - boolean - If true, we will ensure that all communications with this remote server are made through the primary region of the site.  This setting can also be overridden by a site-wide setting which will force it to true.
    //   port - int64 - Port for remote server.
    //   upload_staging_path - string - Upload staging path.  Applies to SFTP only.  If a path is provided here, files will first be uploaded to this path on the remote folder and the moved into the final correct path via an SFTP move command.  This is required by some remote MFT systems to emulate atomic uploads, which are otherwise not supoprted by SFTP.
    //   remote_server_credential_id - int64 - ID of Remote Server Credential, if applicable.
    //   s3_bucket - string - S3 bucket name
    //   s3_compatible_access_key - string - S3-compatible: Access Key
    //   s3_compatible_bucket - string - S3-compatible: Bucket name
    //   s3_compatible_endpoint - string - S3-compatible: endpoint
    //   s3_compatible_region - string - S3-compatible: region
    //   s3_region - string - S3 region
    //   server_certificate - string - Remote server certificate
    //   server_host_key - string - Remote server SSH Host Key. If provided, we will require that the server host key matches the provided key. Uses OpenSSH format similar to what would go into ~/.ssh/known_hosts
    //   server_type - string - Remote server type.
    //   ssl - string - Should we require SSL?
    //   username - string - Remote server username.
    //   wasabi_access_key - string - Wasabi: Access Key.
    //   wasabi_bucket - string - Wasabi: Bucket name
    //   wasabi_region - string - Wasabi: Region
    //   workspace_id - int64 - Workspace ID (0 for default workspace)
    public static function create($params = [], $options = [])
    {
        if (@$params['password'] && !is_string(@$params['password'])) {
            throw new \Files\Exception\InvalidParameterException('$password must be of type string; received ' . gettype(@$params['password']));
        }

        if (@$params['private_key'] && !is_string(@$params['private_key'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key must be of type string; received ' . gettype(@$params['private_key']));
        }

        if (@$params['private_key_passphrase'] && !is_string(@$params['private_key_passphrase'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key_passphrase must be of type string; received ' . gettype(@$params['private_key_passphrase']));
        }

        if (@$params['ssl_certificate'] && !is_string(@$params['ssl_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$ssl_certificate must be of type string; received ' . gettype(@$params['ssl_certificate']));
        }

        if (@$params['aws_secret_key'] && !is_string(@$params['aws_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$aws_secret_key must be of type string; received ' . gettype(@$params['aws_secret_key']));
        }

        if (@$params['azure_blob_storage_access_key'] && !is_string(@$params['azure_blob_storage_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_access_key must be of type string; received ' . gettype(@$params['azure_blob_storage_access_key']));
        }

        if (@$params['azure_blob_storage_sas_token'] && !is_string(@$params['azure_blob_storage_sas_token'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_sas_token must be of type string; received ' . gettype(@$params['azure_blob_storage_sas_token']));
        }

        if (@$params['azure_files_storage_access_key'] && !is_string(@$params['azure_files_storage_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_access_key must be of type string; received ' . gettype(@$params['azure_files_storage_access_key']));
        }

        if (@$params['azure_files_storage_sas_token'] && !is_string(@$params['azure_files_storage_sas_token'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_sas_token must be of type string; received ' . gettype(@$params['azure_files_storage_sas_token']));
        }

        if (@$params['backblaze_b2_application_key'] && !is_string(@$params['backblaze_b2_application_key'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_application_key must be of type string; received ' . gettype(@$params['backblaze_b2_application_key']));
        }

        if (@$params['backblaze_b2_key_id'] && !is_string(@$params['backblaze_b2_key_id'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_key_id must be of type string; received ' . gettype(@$params['backblaze_b2_key_id']));
        }

        if (@$params['cloudflare_secret_key'] && !is_string(@$params['cloudflare_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_secret_key must be of type string; received ' . gettype(@$params['cloudflare_secret_key']));
        }

        if (@$params['filebase_secret_key'] && !is_string(@$params['filebase_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$filebase_secret_key must be of type string; received ' . gettype(@$params['filebase_secret_key']));
        }

        if (@$params['google_cloud_storage_credentials_json'] && !is_string(@$params['google_cloud_storage_credentials_json'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_credentials_json must be of type string; received ' . gettype(@$params['google_cloud_storage_credentials_json']));
        }

        if (@$params['google_cloud_storage_s3_compatible_secret_key'] && !is_string(@$params['google_cloud_storage_s3_compatible_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_s3_compatible_secret_key must be of type string; received ' . gettype(@$params['google_cloud_storage_s3_compatible_secret_key']));
        }

        if (@$params['linode_secret_key'] && !is_string(@$params['linode_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_secret_key must be of type string; received ' . gettype(@$params['linode_secret_key']));
        }

        if (@$params['s3_compatible_secret_key'] && !is_string(@$params['s3_compatible_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_secret_key must be of type string; received ' . gettype(@$params['s3_compatible_secret_key']));
        }

        if (@$params['wasabi_secret_key'] && !is_string(@$params['wasabi_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_secret_key must be of type string; received ' . gettype(@$params['wasabi_secret_key']));
        }

        if (@$params['aws_access_key'] && !is_string(@$params['aws_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$aws_access_key must be of type string; received ' . gettype(@$params['aws_access_key']));
        }

        if (@$params['azure_blob_storage_account'] && !is_string(@$params['azure_blob_storage_account'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_account must be of type string; received ' . gettype(@$params['azure_blob_storage_account']));
        }

        if (@$params['azure_blob_storage_container'] && !is_string(@$params['azure_blob_storage_container'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_container must be of type string; received ' . gettype(@$params['azure_blob_storage_container']));
        }

        if (@$params['azure_blob_storage_dns_suffix'] && !is_string(@$params['azure_blob_storage_dns_suffix'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_dns_suffix must be of type string; received ' . gettype(@$params['azure_blob_storage_dns_suffix']));
        }

        if (@$params['azure_files_storage_account'] && !is_string(@$params['azure_files_storage_account'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_account must be of type string; received ' . gettype(@$params['azure_files_storage_account']));
        }

        if (@$params['azure_files_storage_dns_suffix'] && !is_string(@$params['azure_files_storage_dns_suffix'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_dns_suffix must be of type string; received ' . gettype(@$params['azure_files_storage_dns_suffix']));
        }

        if (@$params['azure_files_storage_share_name'] && !is_string(@$params['azure_files_storage_share_name'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_share_name must be of type string; received ' . gettype(@$params['azure_files_storage_share_name']));
        }

        if (@$params['backblaze_b2_bucket'] && !is_string(@$params['backblaze_b2_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_bucket must be of type string; received ' . gettype(@$params['backblaze_b2_bucket']));
        }

        if (@$params['backblaze_b2_s3_endpoint'] && !is_string(@$params['backblaze_b2_s3_endpoint'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_s3_endpoint must be of type string; received ' . gettype(@$params['backblaze_b2_s3_endpoint']));
        }

        if (@$params['buffer_uploads'] && !is_string(@$params['buffer_uploads'])) {
            throw new \Files\Exception\InvalidParameterException('$buffer_uploads must be of type string; received ' . gettype(@$params['buffer_uploads']));
        }

        if (@$params['cloudflare_access_key'] && !is_string(@$params['cloudflare_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_access_key must be of type string; received ' . gettype(@$params['cloudflare_access_key']));
        }

        if (@$params['cloudflare_bucket'] && !is_string(@$params['cloudflare_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_bucket must be of type string; received ' . gettype(@$params['cloudflare_bucket']));
        }

        if (@$params['cloudflare_endpoint'] && !is_string(@$params['cloudflare_endpoint'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_endpoint must be of type string; received ' . gettype(@$params['cloudflare_endpoint']));
        }

        if (@$params['description'] && !is_string(@$params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (@$params['filebase_access_key'] && !is_string(@$params['filebase_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$filebase_access_key must be of type string; received ' . gettype(@$params['filebase_access_key']));
        }

        if (@$params['filebase_bucket'] && !is_string(@$params['filebase_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$filebase_bucket must be of type string; received ' . gettype(@$params['filebase_bucket']));
        }

        if (@$params['files_agent_permission_set'] && !is_string(@$params['files_agent_permission_set'])) {
            throw new \Files\Exception\InvalidParameterException('$files_agent_permission_set must be of type string; received ' . gettype(@$params['files_agent_permission_set']));
        }

        if (@$params['files_agent_root'] && !is_string(@$params['files_agent_root'])) {
            throw new \Files\Exception\InvalidParameterException('$files_agent_root must be of type string; received ' . gettype(@$params['files_agent_root']));
        }

        if (@$params['files_agent_version'] && !is_string(@$params['files_agent_version'])) {
            throw new \Files\Exception\InvalidParameterException('$files_agent_version must be of type string; received ' . gettype(@$params['files_agent_version']));
        }

        if (@$params['outbound_agent_id'] && !is_int(@$params['outbound_agent_id'])) {
            throw new \Files\Exception\InvalidParameterException('$outbound_agent_id must be of type int; received ' . gettype(@$params['outbound_agent_id']));
        }

        if (@$params['google_cloud_storage_bucket'] && !is_string(@$params['google_cloud_storage_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_bucket must be of type string; received ' . gettype(@$params['google_cloud_storage_bucket']));
        }

        if (@$params['google_cloud_storage_project_id'] && !is_string(@$params['google_cloud_storage_project_id'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_project_id must be of type string; received ' . gettype(@$params['google_cloud_storage_project_id']));
        }

        if (@$params['google_cloud_storage_s3_compatible_access_key'] && !is_string(@$params['google_cloud_storage_s3_compatible_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_s3_compatible_access_key must be of type string; received ' . gettype(@$params['google_cloud_storage_s3_compatible_access_key']));
        }

        if (@$params['hostname'] && !is_string(@$params['hostname'])) {
            throw new \Files\Exception\InvalidParameterException('$hostname must be of type string; received ' . gettype(@$params['hostname']));
        }

        if (@$params['linode_access_key'] && !is_string(@$params['linode_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_access_key must be of type string; received ' . gettype(@$params['linode_access_key']));
        }

        if (@$params['linode_bucket'] && !is_string(@$params['linode_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_bucket must be of type string; received ' . gettype(@$params['linode_bucket']));
        }

        if (@$params['linode_region'] && !is_string(@$params['linode_region'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_region must be of type string; received ' . gettype(@$params['linode_region']));
        }

        if (@$params['max_connections'] && !is_int(@$params['max_connections'])) {
            throw new \Files\Exception\InvalidParameterException('$max_connections must be of type int; received ' . gettype(@$params['max_connections']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['one_drive_account_type'] && !is_string(@$params['one_drive_account_type'])) {
            throw new \Files\Exception\InvalidParameterException('$one_drive_account_type must be of type string; received ' . gettype(@$params['one_drive_account_type']));
        }

        if (@$params['port'] && !is_int(@$params['port'])) {
            throw new \Files\Exception\InvalidParameterException('$port must be of type int; received ' . gettype(@$params['port']));
        }

        if (@$params['upload_staging_path'] && !is_string(@$params['upload_staging_path'])) {
            throw new \Files\Exception\InvalidParameterException('$upload_staging_path must be of type string; received ' . gettype(@$params['upload_staging_path']));
        }

        if (@$params['remote_server_credential_id'] && !is_int(@$params['remote_server_credential_id'])) {
            throw new \Files\Exception\InvalidParameterException('$remote_server_credential_id must be of type int; received ' . gettype(@$params['remote_server_credential_id']));
        }

        if (@$params['s3_bucket'] && !is_string(@$params['s3_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_bucket must be of type string; received ' . gettype(@$params['s3_bucket']));
        }

        if (@$params['s3_compatible_access_key'] && !is_string(@$params['s3_compatible_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_access_key must be of type string; received ' . gettype(@$params['s3_compatible_access_key']));
        }

        if (@$params['s3_compatible_bucket'] && !is_string(@$params['s3_compatible_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_bucket must be of type string; received ' . gettype(@$params['s3_compatible_bucket']));
        }

        if (@$params['s3_compatible_endpoint'] && !is_string(@$params['s3_compatible_endpoint'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_endpoint must be of type string; received ' . gettype(@$params['s3_compatible_endpoint']));
        }

        if (@$params['s3_compatible_region'] && !is_string(@$params['s3_compatible_region'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_region must be of type string; received ' . gettype(@$params['s3_compatible_region']));
        }

        if (@$params['s3_region'] && !is_string(@$params['s3_region'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_region must be of type string; received ' . gettype(@$params['s3_region']));
        }

        if (@$params['server_certificate'] && !is_string(@$params['server_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$server_certificate must be of type string; received ' . gettype(@$params['server_certificate']));
        }

        if (@$params['server_host_key'] && !is_string(@$params['server_host_key'])) {
            throw new \Files\Exception\InvalidParameterException('$server_host_key must be of type string; received ' . gettype(@$params['server_host_key']));
        }

        if (@$params['server_type'] && !is_string(@$params['server_type'])) {
            throw new \Files\Exception\InvalidParameterException('$server_type must be of type string; received ' . gettype(@$params['server_type']));
        }

        if (@$params['ssl'] && !is_string(@$params['ssl'])) {
            throw new \Files\Exception\InvalidParameterException('$ssl must be of type string; received ' . gettype(@$params['ssl']));
        }

        if (@$params['username'] && !is_string(@$params['username'])) {
            throw new \Files\Exception\InvalidParameterException('$username must be of type string; received ' . gettype(@$params['username']));
        }

        if (@$params['wasabi_access_key'] && !is_string(@$params['wasabi_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_access_key must be of type string; received ' . gettype(@$params['wasabi_access_key']));
        }

        if (@$params['wasabi_bucket'] && !is_string(@$params['wasabi_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_bucket must be of type string; received ' . gettype(@$params['wasabi_bucket']));
        }

        if (@$params['wasabi_region'] && !is_string(@$params['wasabi_region'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_region must be of type string; received ' . gettype(@$params['wasabi_region']));
        }

        if (@$params['workspace_id'] && !is_int(@$params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/remote_servers', 'POST', $params, $options);

        return new RemoteServer((array) (@$response->data ?: []), $options);
    }
}
