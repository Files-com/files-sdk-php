<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A RemoteServer is a specific type of Behavior called `remote_server_sync`.
 *
 * @property int|null $id Remote Server ID
 * @property bool|null $disabled If true, this Remote Server is disabled. Updating it clears this flag, except for retired Agent v1 records, which remain disabled.
 * @property string|null $authentication_method Type of authentication method to use
 * @property string|null $hostname Hostname or IP address. For Agent Remote Servers, the Agent reports the hostname it connects from at each registration, replacing the stored value. This field is not included in the generated Agent configuration file. Setting it on Create or Update does not change the running Agent.
 * @property string|null $remote_home_path Initial home folder on remote server
 * @property string|null $upload_staging_path Upload staging path. Applies to SFTP only. If a path is provided here, files will first be uploaded to this path on the remote folder and the moved into the final correct path via an SFTP move command. This is required by some remote MFT systems to emulate atomic uploads, which are otherwise not supoprted by SFTP.
 * @property bool|null $allow_relative_paths Allow relative paths in SFTP. If true, paths will not be forced to be absolute, allowing operations relative to the user's home directory.
 * @property string|null $name Internal name for your reference
 * @property string|null $description Internal description for your reference
 * @property int|null $port Port for remote server. For Agent Remote Servers, the Agent reports its downstream proxy port at each registration, replacing the stored value. This field is not included in the generated Agent configuration file. Setting it on Create or Update does not change the running Agent.
 * @property string|null $buffer_uploads If set to always, uploads to this server will be uploaded first to Files.com before being sent to the remote server. This can improve performance in certain access patterns, such as high-latency connections. It will cause data to be temporarily stored in Files.com. If set to auto, we will perform this optimization if we believe it to be a benefit in a given situation.
 * @property int|null $max_connections Max number of parallel connections. Ignored for S3 connections (we will parallelize these as much as possible).
 * @property bool|null $pin_to_site_region If true, we will ensure that all communications with this remote server are made through the primary region of the site. This setting can also be overridden by a site-wide setting which will force it to true.
 * @property string|null $pinned_region If set, all communications with this remote server are made through the provided region.
 * @property int|null $remote_server_credential_id ID of Remote Server Credential, if applicable.
 * @property string|null $s3_bucket S3 bucket name
 * @property string|null $s3_region S3 region
 * @property string|null $s3_kms_key_id ARN of the AWS KMS key that encrypts files written to the bucket (SSE-KMS). Leave blank unless your bucket requires a specific key.
 * @property string|null $aws_access_key AWS Access Key.
 * @property string|null $s3_assume_role_arn AWS IAM Role ARN for AssumeRole authentication.
 * @property int|null $s3_assume_role_duration_seconds Session duration in seconds for AssumeRole authentication (900-43200).
 * @property string|null $s3_assume_role_external_id External ID for AssumeRole authentication.
 * @property string|null $server_certificate Remote server certificate
 * @property string|null $server_host_key Pinned SSH host key or OpenSSH host certificate for SFTP. If omitted, Files.com detects and stores a host key, preferring plain keys over certificates. With `server_certificate=require_match` (the default), the server must present the exact pinned key or certificate and prove it holds the matching private key. A pinned certificate is compared in full, so renewal can require updating `server_host_key` even when its underlying key is unchanged. Files.com does not check certificate CA signatures, principals, or validity periods. Certificate expiration alone does not end the pin. Update `server_host_key` to replace the pin.
 * @property string|null $server_type Remote server type.
 * @property bool|null $site_admin_access Owner's preference for administrator access to this user-owned Remote Server. Null uses the Site policy's default. Only the owner may change it when the Site policy allows user choice.
 * @property bool|null $effective_site_admin_access Whether Site and Workspace Administrators can access this Remote Server under the current Site policy.
 * @property int|null $user_id ID of the Integration-Centric user who owns this personal Remote Server, if any. Connections created by Site and Workspace Administrators have no owner.
 * @property int|null $workspace_id Workspace ID (0 for default workspace)
 * @property string|null $ssl Should we require SSL?
 * @property string|null $username Remote server username.
 * @property string|null $google_cloud_storage_bucket Google Cloud Storage: Bucket Name
 * @property string|null $google_cloud_storage_authentication_method Google Cloud Storage: Authentication method. Can be json, hmac, or oauth.
 * @property string|null $google_cloud_storage_oauth_scope Google Cloud Storage: OAuth scope. Can be https://www.googleapis.com/auth/devstorage.read_only or https://www.googleapis.com/auth/devstorage.read_write.
 * @property string|null $google_cloud_storage_project_id Google Cloud Storage: Project ID
 * @property string|null $google_cloud_storage_s3_compatible_access_key Google Cloud Storage: S3-compatible Access Key.
 * @property string|null $backblaze_b2_s3_endpoint Backblaze B2 Cloud Storage: S3 Endpoint
 * @property string|null $backblaze_b2_bucket Backblaze B2 Cloud Storage: Bucket name
 * @property string|null $wasabi_bucket Wasabi: Bucket name
 * @property string|null $wasabi_region Wasabi: Region
 * @property string|null $wasabi_access_key Wasabi: Access Key.
 * @property string|null $auth_status Either `in_setup` or `complete`
 * @property string|null $auth_account_name Describes the authorized account
 * @property string|null $one_drive_account_type OneDrive: Either personal or business_other account types
 * @property string|null $sharepoint_tenant_id SharePoint: Microsoft Entra tenant ID for app-only authentication.
 * @property string|null $sharepoint_client_id SharePoint: Microsoft Entra application client ID for app-only authentication.
 * @property bool|null $sharepoint_app_authentication SharePoint: If true, this remote server uses Microsoft Entra app-only authentication.
 * @property string|null $sharepoint_app_credential_type SharePoint: App-only credential type. Either secret or certificate.
 * @property string|null $sharepoint_site_url SharePoint: Site URL to scope app-only authentication to a single site. Leave blank to browse all sites.
 * @property string|null $azure_blob_storage_account Azure Blob Storage: Account name
 * @property string|null $azure_blob_storage_container Azure Blob Storage: Container name
 * @property bool|null $azure_blob_storage_hierarchical_namespace Azure Blob Storage: Does the storage account has hierarchical namespace feature enabled?
 * @property string|null $azure_blob_storage_dns_suffix Azure Blob Storage: Custom DNS suffix
 * @property string|null $azure_files_storage_account Azure Files: Storage Account name
 * @property string|null $azure_files_storage_share_name Azure Files: Storage Share name
 * @property string|null $azure_files_storage_dns_suffix Azure Files: Custom DNS suffix
 * @property string|null $s3_compatible_bucket S3-compatible: Bucket name
 * @property string|null $s3_compatible_endpoint S3-compatible: endpoint
 * @property string|null $s3_compatible_region S3-compatible: region
 * @property bool|null $s3_compatible_virtual_hosted_style S3-compatible: If true, use virtual-hosted-style URLs instead of path-style URLs
 * @property string|null $s3_compatible_access_key S3-compatible: Access Key
 * @property bool|null $enable_dedicated_ips `true` if remote server only accepts connections from dedicated IPs
 * @property int|null $custom_domain_id Custom Domain ID whose dedicated IP addresses are selected when this Remote Server uses dedicated IPs. Must be available to this Remote Server's workspace. Requires enable_dedicated_ips and cannot be combined with an outbound Agent. Set to null to use the site's default dedicated IPs.
 * @property string[]|null $outbound_ip_addresses Current eligible public IP addresses for the selected Custom Domain. Any address in this list may originate a connection. Empty when no domain is selected or its configuration is unavailable. Only included in responses for a single Remote Server.
 * @property string|null $files_agent_permission_set Agent file permissions: `read_only`, `write_only`, or `read_write`. Files.com writes this value as `permission_set` in the configuration file generated for this Remote Server. Setting it on Create or Update changes the generated file, not a running Agent. The Agent enforces the permissions in its local configuration file, controlled by the host's system administrator, and reports them at registration, replacing the stored value.
 * @property string|null $files_agent_root Agent local root path. Files.com writes this value as `root` in the configuration file generated for this Remote Server. Setting it on Create or Update changes the generated file, not a running Agent. The Agent enforces the root in its local configuration file, controlled by the host's system administrator, and reports it at registration, replacing the stored value.
 * @property string|null $files_agent_version Files.com Agent version reported by the Agent at each registration, replacing the stored value. This field is not included in the generated Agent configuration file. Setting it on Create or Update does not update the running Agent.
 * @property bool|null $files_agent_up_to_date If true, the Files Agent is up to date.
 * @property string|null $files_agent_latest_version Latest available Files Agent version
 * @property bool|null $files_agent_supports_push_updates Files Agent supports receiving push updates
 * @property bool|null $direct_transfer_available Whether the Files Agent Proxy recently validated a direct transfer connection. `true` means a direct connection was recently validated (actual availability can vary by client network), `false` means direct transfers are enabled but not currently available, and `null` means direct transfers are disabled or unsupported. Only provided for a connected Files Agent when showing a single Remote Server.
 * @property int|null $outbound_agent_id Route traffic to outbound on a files-agent
 * @property string|null $filebase_bucket Filebase: Bucket name
 * @property string|null $filebase_access_key Filebase: Access Key.
 * @property string|null $files_api_key_prefix Files.com direct link: paired API key prefix.
 * @property string|null $cloudflare_bucket Cloudflare: Bucket name
 * @property string|null $cloudflare_access_key Cloudflare: Access Key.
 * @property string|null $cloudflare_endpoint Cloudflare: endpoint
 * @property bool|null $dropbox_teams Dropbox: If true, list Team folders in root?
 * @property string|null $linode_bucket Linode: Bucket name
 * @property string|null $linode_access_key Linode: Access Key
 * @property string|null $linode_region Linode: region
 * @property bool|null $supports_versioning If true, this remote server supports file versioning. This value is determined automatically by Files.com.
 * @property string|null $password Password, if needed.
 * @property string|null $private_key Private key, if needed.
 * @property string|null $private_key_passphrase Passphrase for private key if needed.
 * @property bool|null $reset_authentication Reset authenticated account?
 * @property string|null $sharepoint_client_certificate SharePoint: PEM-encoded certificate and unencrypted private key for app-only authentication.
 * @property string|null $sharepoint_client_secret SharePoint: Microsoft Entra application client secret for app-only authentication.
 * @property string|null $ssl_certificate SSL client certificate.
 * @property string|null $aws_secret_key AWS: secret key.
 * @property string|null $azure_blob_storage_access_key Azure Blob Storage: Access Key
 * @property string|null $azure_blob_storage_sas_token Azure Blob Storage: Shared Access Signature (SAS) token
 * @property string|null $azure_files_storage_access_key Azure File Storage: Access Key
 * @property string|null $azure_files_storage_sas_token Azure File Storage: Shared Access Signature (SAS) token
 * @property string|null $backblaze_b2_application_key Backblaze B2 Cloud Storage: applicationKey
 * @property string|null $backblaze_b2_key_id Backblaze B2 Cloud Storage: keyID
 * @property string|null $cloudflare_secret_key Cloudflare: Secret Key
 * @property string|null $filebase_secret_key Filebase: Secret Key
 * @property string|null $google_cloud_storage_credentials_json Google Cloud Storage: JSON file that contains the private key. To generate see https://cloud.google.com/storage/docs/json_api/v1/how-tos/authorizing#APIKey
 * @property string|null $google_cloud_storage_s3_compatible_secret_key Google Cloud Storage: S3-compatible secret key
 * @property string|null $linode_secret_key Linode: Secret Key
 * @property string|null $s3_compatible_secret_key S3-compatible: Secret Key
 * @property string|null $wasabi_secret_key Wasabi: Secret Key
 * @property string|null $files_api_key Files.com direct link: API key used once to pair the remote server.
 * @package Files
 */
class RemoteServer
{
    private $attributes = [];
    private $options = [];
    private static $static_mapped_functions = [
        'list' => 'all',
    ];

    /**
     * Build a model from API attributes and retain its request options.
     *
     * @param array $attributes Initial model attributes, keyed by property name.
     * @param array $options Request options used by this model's operations.
     */
    public function __construct($attributes = [], $options = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[str_replace('?', '', $key)] = $value;
        }

        $this->options = $options;
    }

    /**
     * Set a model attribute locally; call save() to persist supported changes.
     *
     * @param string $name Attribute name.
     * @param mixed $value Attribute value.
     * @return void
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Read a model attribute.
     *
     * @param string $name Attribute name.
     * @return mixed The attribute value, or null when it is not set.
     */
    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * Dispatch a legacy static alias, including list() to all().
     *
     * @param string $name Alias name.
     * @param array $arguments Arguments passed to the canonical method.
     * @return mixed The canonical method's result, or null for an unknown alias.
     */
    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, array_keys(self::$static_mapped_functions))) {
            $method = self::$static_mapped_functions[$name];
            if (method_exists(__CLASS__, $method)) {
                return @self::$method(...$arguments);
            }
        }
    }

    /**
     * Whether this model has a nonempty primary key.
     *
     * @return bool
     */
    public function isLoaded()
    {
        return !!@$this->attributes['id'];
    }
    /**
     * Remote Server ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Remote Server ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * If true, this Remote Server is disabled. Updating it clears this flag, except for retired Agent v1 records, which remain disabled.
     *
     * @return bool|null
     */
    public function getDisabled()
    {
        return isset($this->attributes['disabled']) ? $this->attributes['disabled'] : null;
    }

    /**
     * If true, this Remote Server is disabled. Updating it clears this flag, except for retired Agent v1 records, which remain disabled.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDisabled($value)
    {
        return $this->attributes['disabled'] = $value;
    }
    /**
     * Type of authentication method to use
     *
     * @return string|null
     */
    public function getAuthenticationMethod()
    {
        return isset($this->attributes['authentication_method']) ? $this->attributes['authentication_method'] : null;
    }

    /**
     * Type of authentication method to use
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAuthenticationMethod($value)
    {
        return $this->attributes['authentication_method'] = $value;
    }
    /**
     * Hostname or IP address. For Agent Remote Servers, the Agent reports the hostname it connects from at each registration, replacing the stored value. This field is not included in the generated Agent configuration file. Setting it on Create or Update does not change the running Agent.
     *
     * @return string|null
     */
    public function getHostname()
    {
        return isset($this->attributes['hostname']) ? $this->attributes['hostname'] : null;
    }

    /**
     * Hostname or IP address. For Agent Remote Servers, the Agent reports the hostname it connects from at each registration, replacing the stored value. This field is not included in the generated Agent configuration file. Setting it on Create or Update does not change the running Agent.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setHostname($value)
    {
        return $this->attributes['hostname'] = $value;
    }
    /**
     * Initial home folder on remote server
     *
     * @return string|null
     */
    public function getRemoteHomePath()
    {
        return isset($this->attributes['remote_home_path']) ? $this->attributes['remote_home_path'] : null;
    }

    /**
     * Initial home folder on remote server
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setRemoteHomePath($value)
    {
        return $this->attributes['remote_home_path'] = $value;
    }
    /**
     * Upload staging path.  Applies to SFTP only.  If a path is provided here, files will first be uploaded to this path on the remote folder and the moved into the final correct path via an SFTP move command.  This is required by some remote MFT systems to emulate atomic uploads, which are otherwise not supoprted by SFTP.
     *
     * @return string|null
     */
    public function getUploadStagingPath()
    {
        return isset($this->attributes['upload_staging_path']) ? $this->attributes['upload_staging_path'] : null;
    }

    /**
     * Upload staging path.  Applies to SFTP only.  If a path is provided here, files will first be uploaded to this path on the remote folder and the moved into the final correct path via an SFTP move command.  This is required by some remote MFT systems to emulate atomic uploads, which are otherwise not supoprted by SFTP.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUploadStagingPath($value)
    {
        return $this->attributes['upload_staging_path'] = $value;
    }
    /**
     * Allow relative paths in SFTP. If true, paths will not be forced to be absolute, allowing operations relative to the user's home directory.
     *
     * @return bool|null
     */
    public function getAllowRelativePaths()
    {
        return isset($this->attributes['allow_relative_paths']) ? $this->attributes['allow_relative_paths'] : null;
    }

    /**
     * Allow relative paths in SFTP. If true, paths will not be forced to be absolute, allowing operations relative to the user's home directory.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setAllowRelativePaths($value)
    {
        return $this->attributes['allow_relative_paths'] = $value;
    }
    /**
     * Internal name for your reference
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Internal name for your reference
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Internal description for your reference
     *
     * @return string|null
     */
    public function getDescription()
    {
        return isset($this->attributes['description']) ? $this->attributes['description'] : null;
    }

    /**
     * Internal description for your reference
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    /**
     * Port for remote server. For Agent Remote Servers, the Agent reports its downstream proxy port at each registration, replacing the stored value. This field is not included in the generated Agent configuration file. Setting it on Create or Update does not change the running Agent.
     *
     * @return int|null
     */
    public function getPort()
    {
        return isset($this->attributes['port']) ? $this->attributes['port'] : null;
    }

    /**
     * Port for remote server. For Agent Remote Servers, the Agent reports its downstream proxy port at each registration, replacing the stored value. This field is not included in the generated Agent configuration file. Setting it on Create or Update does not change the running Agent.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setPort($value)
    {
        return $this->attributes['port'] = $value;
    }
    /**
     * If set to always, uploads to this server will be uploaded first to Files.com before being sent to the remote server. This can improve performance in certain access patterns, such as high-latency connections.  It will cause data to be temporarily stored in Files.com. If set to auto, we will perform this optimization if we believe it to be a benefit in a given situation.
     *
     * @return string|null
     */
    public function getBufferUploads()
    {
        return isset($this->attributes['buffer_uploads']) ? $this->attributes['buffer_uploads'] : null;
    }

    /**
     * If set to always, uploads to this server will be uploaded first to Files.com before being sent to the remote server. This can improve performance in certain access patterns, such as high-latency connections.  It will cause data to be temporarily stored in Files.com. If set to auto, we will perform this optimization if we believe it to be a benefit in a given situation.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setBufferUploads($value)
    {
        return $this->attributes['buffer_uploads'] = $value;
    }
    /**
     * Max number of parallel connections.  Ignored for S3 connections (we will parallelize these as much as possible).
     *
     * @return int|null
     */
    public function getMaxConnections()
    {
        return isset($this->attributes['max_connections']) ? $this->attributes['max_connections'] : null;
    }

    /**
     * Max number of parallel connections.  Ignored for S3 connections (we will parallelize these as much as possible).
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setMaxConnections($value)
    {
        return $this->attributes['max_connections'] = $value;
    }
    /**
     * If true, we will ensure that all communications with this remote server are made through the primary region of the site.  This setting can also be overridden by a site-wide setting which will force it to true.
     *
     * @return bool|null
     */
    public function getPinToSiteRegion()
    {
        return isset($this->attributes['pin_to_site_region']) ? $this->attributes['pin_to_site_region'] : null;
    }

    /**
     * If true, we will ensure that all communications with this remote server are made through the primary region of the site.  This setting can also be overridden by a site-wide setting which will force it to true.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setPinToSiteRegion($value)
    {
        return $this->attributes['pin_to_site_region'] = $value;
    }
    /**
     * If set, all communications with this remote server are made through the provided region.
     *
     * @return string|null
     */
    public function getPinnedRegion()
    {
        return isset($this->attributes['pinned_region']) ? $this->attributes['pinned_region'] : null;
    }

    /**
     * If set, all communications with this remote server are made through the provided region.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPinnedRegion($value)
    {
        return $this->attributes['pinned_region'] = $value;
    }
    /**
     * ID of Remote Server Credential, if applicable.
     *
     * @return int|null
     */
    public function getRemoteServerCredentialId()
    {
        return isset($this->attributes['remote_server_credential_id']) ? $this->attributes['remote_server_credential_id'] : null;
    }

    /**
     * ID of Remote Server Credential, if applicable.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setRemoteServerCredentialId($value)
    {
        return $this->attributes['remote_server_credential_id'] = $value;
    }
    /**
     * S3 bucket name
     *
     * @return string|null
     */
    public function getS3Bucket()
    {
        return isset($this->attributes['s3_bucket']) ? $this->attributes['s3_bucket'] : null;
    }

    /**
     * S3 bucket name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setS3Bucket($value)
    {
        return $this->attributes['s3_bucket'] = $value;
    }
    /**
     * S3 region
     *
     * @return string|null
     */
    public function getS3Region()
    {
        return isset($this->attributes['s3_region']) ? $this->attributes['s3_region'] : null;
    }

    /**
     * S3 region
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setS3Region($value)
    {
        return $this->attributes['s3_region'] = $value;
    }
    /**
     * ARN of the AWS KMS key that encrypts files written to the bucket (SSE-KMS). Leave blank unless your bucket requires a specific key.
     *
     * @return string|null
     */
    public function getS3KmsKeyId()
    {
        return isset($this->attributes['s3_kms_key_id']) ? $this->attributes['s3_kms_key_id'] : null;
    }

    /**
     * ARN of the AWS KMS key that encrypts files written to the bucket (SSE-KMS). Leave blank unless your bucket requires a specific key.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setS3KmsKeyId($value)
    {
        return $this->attributes['s3_kms_key_id'] = $value;
    }
    /**
     * AWS Access Key.
     *
     * @return string|null
     */
    public function getAwsAccessKey()
    {
        return isset($this->attributes['aws_access_key']) ? $this->attributes['aws_access_key'] : null;
    }

    /**
     * AWS Access Key.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAwsAccessKey($value)
    {
        return $this->attributes['aws_access_key'] = $value;
    }
    /**
     * AWS IAM Role ARN for AssumeRole authentication.
     *
     * @return string|null
     */
    public function getS3AssumeRoleArn()
    {
        return isset($this->attributes['s3_assume_role_arn']) ? $this->attributes['s3_assume_role_arn'] : null;
    }

    /**
     * AWS IAM Role ARN for AssumeRole authentication.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setS3AssumeRoleArn($value)
    {
        return $this->attributes['s3_assume_role_arn'] = $value;
    }
    /**
     * Session duration in seconds for AssumeRole authentication (900-43200).
     *
     * @return int|null
     */
    public function getS3AssumeRoleDurationSeconds()
    {
        return isset($this->attributes['s3_assume_role_duration_seconds']) ? $this->attributes['s3_assume_role_duration_seconds'] : null;
    }

    /**
     * Session duration in seconds for AssumeRole authentication (900-43200).
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setS3AssumeRoleDurationSeconds($value)
    {
        return $this->attributes['s3_assume_role_duration_seconds'] = $value;
    }
    /**
     * External ID for AssumeRole authentication.
     *
     * @return string|null
     */
    public function getS3AssumeRoleExternalId()
    {
        return isset($this->attributes['s3_assume_role_external_id']) ? $this->attributes['s3_assume_role_external_id'] : null;
    }

    /**
     * External ID for AssumeRole authentication.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setS3AssumeRoleExternalId($value)
    {
        return $this->attributes['s3_assume_role_external_id'] = $value;
    }
    /**
     * Remote server certificate
     *
     * @return string|null
     */
    public function getServerCertificate()
    {
        return isset($this->attributes['server_certificate']) ? $this->attributes['server_certificate'] : null;
    }

    /**
     * Remote server certificate
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setServerCertificate($value)
    {
        return $this->attributes['server_certificate'] = $value;
    }
    /**
     * Pinned SSH host key or OpenSSH host certificate for SFTP. If omitted, Files.com detects and stores a host key, preferring plain keys over certificates. With `server_certificate=require_match` (the default), the server must present the exact pinned key or certificate and prove it holds the matching private key. A pinned certificate is compared in full, so renewal can require updating `server_host_key` even when its underlying key is unchanged. Files.com does not check certificate CA signatures, principals, or validity periods. Certificate expiration alone does not end the pin. Update `server_host_key` to replace the pin.
     *
     * @return string|null
     */
    public function getServerHostKey()
    {
        return isset($this->attributes['server_host_key']) ? $this->attributes['server_host_key'] : null;
    }

    /**
     * Pinned SSH host key or OpenSSH host certificate for SFTP. If omitted, Files.com detects and stores a host key, preferring plain keys over certificates. With `server_certificate=require_match` (the default), the server must present the exact pinned key or certificate and prove it holds the matching private key. A pinned certificate is compared in full, so renewal can require updating `server_host_key` even when its underlying key is unchanged. Files.com does not check certificate CA signatures, principals, or validity periods. Certificate expiration alone does not end the pin. Update `server_host_key` to replace the pin.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setServerHostKey($value)
    {
        return $this->attributes['server_host_key'] = $value;
    }
    /**
     * Remote server type.
     *
     * @return string|null
     */
    public function getServerType()
    {
        return isset($this->attributes['server_type']) ? $this->attributes['server_type'] : null;
    }

    /**
     * Remote server type.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setServerType($value)
    {
        return $this->attributes['server_type'] = $value;
    }
    /**
     * Owner's preference for administrator access to this user-owned Remote Server. Null uses the Site policy's default. Only the owner may change it when the Site policy allows user choice.
     *
     * @return bool|null
     */
    public function getSiteAdminAccess()
    {
        return isset($this->attributes['site_admin_access']) ? $this->attributes['site_admin_access'] : null;
    }

    /**
     * Owner's preference for administrator access to this user-owned Remote Server. Null uses the Site policy's default. Only the owner may change it when the Site policy allows user choice.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSiteAdminAccess($value)
    {
        return $this->attributes['site_admin_access'] = $value;
    }
    /**
     * Whether Site and Workspace Administrators can access this Remote Server under the current Site policy.
     *
     * @return bool|null
     */
    public function getEffectiveSiteAdminAccess()
    {
        return isset($this->attributes['effective_site_admin_access']) ? $this->attributes['effective_site_admin_access'] : null;
    }

    /**
     * Whether Site and Workspace Administrators can access this Remote Server under the current Site policy.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setEffectiveSiteAdminAccess($value)
    {
        return $this->attributes['effective_site_admin_access'] = $value;
    }
    /**
     * ID of the Integration-Centric user who owns this personal Remote Server, if any. Connections created by Site and Workspace Administrators have no owner.
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }

    /**
     * ID of the Integration-Centric user who owns this personal Remote Server, if any. Connections created by Site and Workspace Administrators have no owner.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    /**
     * Workspace ID (0 for default workspace)
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * Workspace ID (0 for default workspace)
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * Should we require SSL?
     *
     * @return string|null
     */
    public function getSsl()
    {
        return isset($this->attributes['ssl']) ? $this->attributes['ssl'] : null;
    }

    /**
     * Should we require SSL?
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSsl($value)
    {
        return $this->attributes['ssl'] = $value;
    }
    /**
     * Remote server username.
     *
     * @return string|null
     */
    public function getUsername()
    {
        return isset($this->attributes['username']) ? $this->attributes['username'] : null;
    }

    /**
     * Remote server username.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUsername($value)
    {
        return $this->attributes['username'] = $value;
    }
    /**
     * Google Cloud Storage: Bucket Name
     *
     * @return string|null
     */
    public function getGoogleCloudStorageBucket()
    {
        return isset($this->attributes['google_cloud_storage_bucket']) ? $this->attributes['google_cloud_storage_bucket'] : null;
    }

    /**
     * Google Cloud Storage: Bucket Name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGoogleCloudStorageBucket($value)
    {
        return $this->attributes['google_cloud_storage_bucket'] = $value;
    }
    /**
     * Google Cloud Storage: Authentication method. Can be json, hmac, or oauth.
     *
     * @return string|null
     */
    public function getGoogleCloudStorageAuthenticationMethod()
    {
        return isset($this->attributes['google_cloud_storage_authentication_method']) ? $this->attributes['google_cloud_storage_authentication_method'] : null;
    }

    /**
     * Google Cloud Storage: Authentication method. Can be json, hmac, or oauth.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGoogleCloudStorageAuthenticationMethod($value)
    {
        return $this->attributes['google_cloud_storage_authentication_method'] = $value;
    }
    /**
     * Google Cloud Storage: OAuth scope. Can be https://www.googleapis.com/auth/devstorage.read_only or https://www.googleapis.com/auth/devstorage.read_write.
     *
     * @return string|null
     */
    public function getGoogleCloudStorageOauthScope()
    {
        return isset($this->attributes['google_cloud_storage_oauth_scope']) ? $this->attributes['google_cloud_storage_oauth_scope'] : null;
    }

    /**
     * Google Cloud Storage: OAuth scope. Can be https://www.googleapis.com/auth/devstorage.read_only or https://www.googleapis.com/auth/devstorage.read_write.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGoogleCloudStorageOauthScope($value)
    {
        return $this->attributes['google_cloud_storage_oauth_scope'] = $value;
    }
    /**
     * Google Cloud Storage: Project ID
     *
     * @return string|null
     */
    public function getGoogleCloudStorageProjectId()
    {
        return isset($this->attributes['google_cloud_storage_project_id']) ? $this->attributes['google_cloud_storage_project_id'] : null;
    }

    /**
     * Google Cloud Storage: Project ID
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGoogleCloudStorageProjectId($value)
    {
        return $this->attributes['google_cloud_storage_project_id'] = $value;
    }
    /**
     * Google Cloud Storage: S3-compatible Access Key.
     *
     * @return string|null
     */
    public function getGoogleCloudStorageS3CompatibleAccessKey()
    {
        return isset($this->attributes['google_cloud_storage_s3_compatible_access_key']) ? $this->attributes['google_cloud_storage_s3_compatible_access_key'] : null;
    }

    /**
     * Google Cloud Storage: S3-compatible Access Key.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGoogleCloudStorageS3CompatibleAccessKey($value)
    {
        return $this->attributes['google_cloud_storage_s3_compatible_access_key'] = $value;
    }
    /**
     * Backblaze B2 Cloud Storage: S3 Endpoint
     *
     * @return string|null
     */
    public function getBackblazeB2S3Endpoint()
    {
        return isset($this->attributes['backblaze_b2_s3_endpoint']) ? $this->attributes['backblaze_b2_s3_endpoint'] : null;
    }

    /**
     * Backblaze B2 Cloud Storage: S3 Endpoint
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setBackblazeB2S3Endpoint($value)
    {
        return $this->attributes['backblaze_b2_s3_endpoint'] = $value;
    }
    /**
     * Backblaze B2 Cloud Storage: Bucket name
     *
     * @return string|null
     */
    public function getBackblazeB2Bucket()
    {
        return isset($this->attributes['backblaze_b2_bucket']) ? $this->attributes['backblaze_b2_bucket'] : null;
    }

    /**
     * Backblaze B2 Cloud Storage: Bucket name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setBackblazeB2Bucket($value)
    {
        return $this->attributes['backblaze_b2_bucket'] = $value;
    }
    /**
     * Wasabi: Bucket name
     *
     * @return string|null
     */
    public function getWasabiBucket()
    {
        return isset($this->attributes['wasabi_bucket']) ? $this->attributes['wasabi_bucket'] : null;
    }

    /**
     * Wasabi: Bucket name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setWasabiBucket($value)
    {
        return $this->attributes['wasabi_bucket'] = $value;
    }
    /**
     * Wasabi: Region
     *
     * @return string|null
     */
    public function getWasabiRegion()
    {
        return isset($this->attributes['wasabi_region']) ? $this->attributes['wasabi_region'] : null;
    }

    /**
     * Wasabi: Region
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setWasabiRegion($value)
    {
        return $this->attributes['wasabi_region'] = $value;
    }
    /**
     * Wasabi: Access Key.
     *
     * @return string|null
     */
    public function getWasabiAccessKey()
    {
        return isset($this->attributes['wasabi_access_key']) ? $this->attributes['wasabi_access_key'] : null;
    }

    /**
     * Wasabi: Access Key.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setWasabiAccessKey($value)
    {
        return $this->attributes['wasabi_access_key'] = $value;
    }
    /**
     * Either `in_setup` or `complete`
     *
     * @return string|null
     */
    public function getAuthStatus()
    {
        return isset($this->attributes['auth_status']) ? $this->attributes['auth_status'] : null;
    }

    /**
     * Either `in_setup` or `complete`
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAuthStatus($value)
    {
        return $this->attributes['auth_status'] = $value;
    }
    /**
     * Describes the authorized account
     *
     * @return string|null
     */
    public function getAuthAccountName()
    {
        return isset($this->attributes['auth_account_name']) ? $this->attributes['auth_account_name'] : null;
    }

    /**
     * Describes the authorized account
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAuthAccountName($value)
    {
        return $this->attributes['auth_account_name'] = $value;
    }
    /**
     * OneDrive: Either personal or business_other account types
     *
     * @return string|null
     */
    public function getOneDriveAccountType()
    {
        return isset($this->attributes['one_drive_account_type']) ? $this->attributes['one_drive_account_type'] : null;
    }

    /**
     * OneDrive: Either personal or business_other account types
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setOneDriveAccountType($value)
    {
        return $this->attributes['one_drive_account_type'] = $value;
    }
    /**
     * SharePoint: Microsoft Entra tenant ID for app-only authentication.
     *
     * @return string|null
     */
    public function getSharepointTenantId()
    {
        return isset($this->attributes['sharepoint_tenant_id']) ? $this->attributes['sharepoint_tenant_id'] : null;
    }

    /**
     * SharePoint: Microsoft Entra tenant ID for app-only authentication.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSharepointTenantId($value)
    {
        return $this->attributes['sharepoint_tenant_id'] = $value;
    }
    /**
     * SharePoint: Microsoft Entra application client ID for app-only authentication.
     *
     * @return string|null
     */
    public function getSharepointClientId()
    {
        return isset($this->attributes['sharepoint_client_id']) ? $this->attributes['sharepoint_client_id'] : null;
    }

    /**
     * SharePoint: Microsoft Entra application client ID for app-only authentication.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSharepointClientId($value)
    {
        return $this->attributes['sharepoint_client_id'] = $value;
    }
    /**
     * SharePoint: If true, this remote server uses Microsoft Entra app-only authentication.
     *
     * @return bool|null
     */
    public function getSharepointAppAuthentication()
    {
        return isset($this->attributes['sharepoint_app_authentication']) ? $this->attributes['sharepoint_app_authentication'] : null;
    }

    /**
     * SharePoint: If true, this remote server uses Microsoft Entra app-only authentication.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSharepointAppAuthentication($value)
    {
        return $this->attributes['sharepoint_app_authentication'] = $value;
    }
    /**
     * SharePoint: App-only credential type. Either secret or certificate.
     *
     * @return string|null
     */
    public function getSharepointAppCredentialType()
    {
        return isset($this->attributes['sharepoint_app_credential_type']) ? $this->attributes['sharepoint_app_credential_type'] : null;
    }

    /**
     * SharePoint: App-only credential type. Either secret or certificate.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSharepointAppCredentialType($value)
    {
        return $this->attributes['sharepoint_app_credential_type'] = $value;
    }
    /**
     * SharePoint: Site URL to scope app-only authentication to a single site. Leave blank to browse all sites.
     *
     * @return string|null
     */
    public function getSharepointSiteUrl()
    {
        return isset($this->attributes['sharepoint_site_url']) ? $this->attributes['sharepoint_site_url'] : null;
    }

    /**
     * SharePoint: Site URL to scope app-only authentication to a single site. Leave blank to browse all sites.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSharepointSiteUrl($value)
    {
        return $this->attributes['sharepoint_site_url'] = $value;
    }
    /**
     * Azure Blob Storage: Account name
     *
     * @return string|null
     */
    public function getAzureBlobStorageAccount()
    {
        return isset($this->attributes['azure_blob_storage_account']) ? $this->attributes['azure_blob_storage_account'] : null;
    }

    /**
     * Azure Blob Storage: Account name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAzureBlobStorageAccount($value)
    {
        return $this->attributes['azure_blob_storage_account'] = $value;
    }
    /**
     * Azure Blob Storage: Container name
     *
     * @return string|null
     */
    public function getAzureBlobStorageContainer()
    {
        return isset($this->attributes['azure_blob_storage_container']) ? $this->attributes['azure_blob_storage_container'] : null;
    }

    /**
     * Azure Blob Storage: Container name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAzureBlobStorageContainer($value)
    {
        return $this->attributes['azure_blob_storage_container'] = $value;
    }
    /**
     * Azure Blob Storage: Does the storage account has hierarchical namespace feature enabled?
     *
     * @return bool|null
     */
    public function getAzureBlobStorageHierarchicalNamespace()
    {
        return isset($this->attributes['azure_blob_storage_hierarchical_namespace']) ? $this->attributes['azure_blob_storage_hierarchical_namespace'] : null;
    }

    /**
     * Azure Blob Storage: Does the storage account has hierarchical namespace feature enabled?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setAzureBlobStorageHierarchicalNamespace($value)
    {
        return $this->attributes['azure_blob_storage_hierarchical_namespace'] = $value;
    }
    /**
     * Azure Blob Storage: Custom DNS suffix
     *
     * @return string|null
     */
    public function getAzureBlobStorageDnsSuffix()
    {
        return isset($this->attributes['azure_blob_storage_dns_suffix']) ? $this->attributes['azure_blob_storage_dns_suffix'] : null;
    }

    /**
     * Azure Blob Storage: Custom DNS suffix
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAzureBlobStorageDnsSuffix($value)
    {
        return $this->attributes['azure_blob_storage_dns_suffix'] = $value;
    }
    /**
     * Azure Files: Storage Account name
     *
     * @return string|null
     */
    public function getAzureFilesStorageAccount()
    {
        return isset($this->attributes['azure_files_storage_account']) ? $this->attributes['azure_files_storage_account'] : null;
    }

    /**
     * Azure Files: Storage Account name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAzureFilesStorageAccount($value)
    {
        return $this->attributes['azure_files_storage_account'] = $value;
    }
    /**
     * Azure Files:  Storage Share name
     *
     * @return string|null
     */
    public function getAzureFilesStorageShareName()
    {
        return isset($this->attributes['azure_files_storage_share_name']) ? $this->attributes['azure_files_storage_share_name'] : null;
    }

    /**
     * Azure Files:  Storage Share name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAzureFilesStorageShareName($value)
    {
        return $this->attributes['azure_files_storage_share_name'] = $value;
    }
    /**
     * Azure Files: Custom DNS suffix
     *
     * @return string|null
     */
    public function getAzureFilesStorageDnsSuffix()
    {
        return isset($this->attributes['azure_files_storage_dns_suffix']) ? $this->attributes['azure_files_storage_dns_suffix'] : null;
    }

    /**
     * Azure Files: Custom DNS suffix
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAzureFilesStorageDnsSuffix($value)
    {
        return $this->attributes['azure_files_storage_dns_suffix'] = $value;
    }
    /**
     * S3-compatible: Bucket name
     *
     * @return string|null
     */
    public function getS3CompatibleBucket()
    {
        return isset($this->attributes['s3_compatible_bucket']) ? $this->attributes['s3_compatible_bucket'] : null;
    }

    /**
     * S3-compatible: Bucket name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setS3CompatibleBucket($value)
    {
        return $this->attributes['s3_compatible_bucket'] = $value;
    }
    /**
     * S3-compatible: endpoint
     *
     * @return string|null
     */
    public function getS3CompatibleEndpoint()
    {
        return isset($this->attributes['s3_compatible_endpoint']) ? $this->attributes['s3_compatible_endpoint'] : null;
    }

    /**
     * S3-compatible: endpoint
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setS3CompatibleEndpoint($value)
    {
        return $this->attributes['s3_compatible_endpoint'] = $value;
    }
    /**
     * S3-compatible: region
     *
     * @return string|null
     */
    public function getS3CompatibleRegion()
    {
        return isset($this->attributes['s3_compatible_region']) ? $this->attributes['s3_compatible_region'] : null;
    }

    /**
     * S3-compatible: region
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setS3CompatibleRegion($value)
    {
        return $this->attributes['s3_compatible_region'] = $value;
    }
    /**
     * S3-compatible: If true, use virtual-hosted-style URLs instead of path-style URLs
     *
     * @return bool|null
     */
    public function getS3CompatibleVirtualHostedStyle()
    {
        return isset($this->attributes['s3_compatible_virtual_hosted_style']) ? $this->attributes['s3_compatible_virtual_hosted_style'] : null;
    }

    /**
     * S3-compatible: If true, use virtual-hosted-style URLs instead of path-style URLs
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setS3CompatibleVirtualHostedStyle($value)
    {
        return $this->attributes['s3_compatible_virtual_hosted_style'] = $value;
    }
    /**
     * S3-compatible: Access Key
     *
     * @return string|null
     */
    public function getS3CompatibleAccessKey()
    {
        return isset($this->attributes['s3_compatible_access_key']) ? $this->attributes['s3_compatible_access_key'] : null;
    }

    /**
     * S3-compatible: Access Key
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setS3CompatibleAccessKey($value)
    {
        return $this->attributes['s3_compatible_access_key'] = $value;
    }
    /**
     * `true` if remote server only accepts connections from dedicated IPs
     *
     * @return bool|null
     */
    public function getEnableDedicatedIps()
    {
        return isset($this->attributes['enable_dedicated_ips']) ? $this->attributes['enable_dedicated_ips'] : null;
    }

    /**
     * `true` if remote server only accepts connections from dedicated IPs
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setEnableDedicatedIps($value)
    {
        return $this->attributes['enable_dedicated_ips'] = $value;
    }
    /**
     * Custom Domain ID whose dedicated IP addresses are selected when this Remote Server uses dedicated IPs. Must be available to this Remote Server's workspace. Requires enable_dedicated_ips and cannot be combined with an outbound Agent. Set to null to use the site's default dedicated IPs.
     *
     * @return int|null
     */
    public function getCustomDomainId()
    {
        return isset($this->attributes['custom_domain_id']) ? $this->attributes['custom_domain_id'] : null;
    }

    /**
     * Custom Domain ID whose dedicated IP addresses are selected when this Remote Server uses dedicated IPs. Must be available to this Remote Server's workspace. Requires enable_dedicated_ips and cannot be combined with an outbound Agent. Set to null to use the site's default dedicated IPs.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setCustomDomainId($value)
    {
        return $this->attributes['custom_domain_id'] = $value;
    }
    /**
     * Current eligible public IP addresses for the selected Custom Domain. Any address in this list may originate a connection. Empty when no domain is selected or its configuration is unavailable. Only included in responses for a single Remote Server.
     *
     * @return string[]|null
     */
    public function getOutboundIpAddresses()
    {
        return isset($this->attributes['outbound_ip_addresses']) ? $this->attributes['outbound_ip_addresses'] : null;
    }

    /**
     * Current eligible public IP addresses for the selected Custom Domain. Any address in this list may originate a connection. Empty when no domain is selected or its configuration is unavailable. Only included in responses for a single Remote Server.
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setOutboundIpAddresses($value)
    {
        return $this->attributes['outbound_ip_addresses'] = $value;
    }
    /**
     * Agent file permissions: `read_only`, `write_only`, or `read_write`. Files.com writes this value as `permission_set` in the configuration file generated for this Remote Server. Setting it on Create or Update changes the generated file, not a running Agent. The Agent enforces the permissions in its local configuration file, controlled by the host's system administrator, and reports them at registration, replacing the stored value.
     *
     * @return string|null
     */
    public function getFilesAgentPermissionSet()
    {
        return isset($this->attributes['files_agent_permission_set']) ? $this->attributes['files_agent_permission_set'] : null;
    }

    /**
     * Agent file permissions: `read_only`, `write_only`, or `read_write`. Files.com writes this value as `permission_set` in the configuration file generated for this Remote Server. Setting it on Create or Update changes the generated file, not a running Agent. The Agent enforces the permissions in its local configuration file, controlled by the host's system administrator, and reports them at registration, replacing the stored value.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFilesAgentPermissionSet($value)
    {
        return $this->attributes['files_agent_permission_set'] = $value;
    }
    /**
     * Agent local root path. Files.com writes this value as `root` in the configuration file generated for this Remote Server. Setting it on Create or Update changes the generated file, not a running Agent. The Agent enforces the root in its local configuration file, controlled by the host's system administrator, and reports it at registration, replacing the stored value.
     *
     * @return string|null
     */
    public function getFilesAgentRoot()
    {
        return isset($this->attributes['files_agent_root']) ? $this->attributes['files_agent_root'] : null;
    }

    /**
     * Agent local root path. Files.com writes this value as `root` in the configuration file generated for this Remote Server. Setting it on Create or Update changes the generated file, not a running Agent. The Agent enforces the root in its local configuration file, controlled by the host's system administrator, and reports it at registration, replacing the stored value.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFilesAgentRoot($value)
    {
        return $this->attributes['files_agent_root'] = $value;
    }
    /**
     * Files.com Agent version reported by the Agent at each registration, replacing the stored value. This field is not included in the generated Agent configuration file. Setting it on Create or Update does not update the running Agent.
     *
     * @return string|null
     */
    public function getFilesAgentVersion()
    {
        return isset($this->attributes['files_agent_version']) ? $this->attributes['files_agent_version'] : null;
    }

    /**
     * Files.com Agent version reported by the Agent at each registration, replacing the stored value. This field is not included in the generated Agent configuration file. Setting it on Create or Update does not update the running Agent.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFilesAgentVersion($value)
    {
        return $this->attributes['files_agent_version'] = $value;
    }
    /**
     * If true, the Files Agent is up to date.
     *
     * @return bool|null
     */
    public function getFilesAgentUpToDate()
    {
        return isset($this->attributes['files_agent_up_to_date']) ? $this->attributes['files_agent_up_to_date'] : null;
    }

    /**
     * If true, the Files Agent is up to date.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setFilesAgentUpToDate($value)
    {
        return $this->attributes['files_agent_up_to_date'] = $value;
    }
    /**
     * Latest available Files Agent version
     *
     * @return string|null
     */
    public function getFilesAgentLatestVersion()
    {
        return isset($this->attributes['files_agent_latest_version']) ? $this->attributes['files_agent_latest_version'] : null;
    }

    /**
     * Latest available Files Agent version
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFilesAgentLatestVersion($value)
    {
        return $this->attributes['files_agent_latest_version'] = $value;
    }
    /**
     * Files Agent supports receiving push updates
     *
     * @return bool|null
     */
    public function getFilesAgentSupportsPushUpdates()
    {
        return isset($this->attributes['files_agent_supports_push_updates']) ? $this->attributes['files_agent_supports_push_updates'] : null;
    }

    /**
     * Files Agent supports receiving push updates
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setFilesAgentSupportsPushUpdates($value)
    {
        return $this->attributes['files_agent_supports_push_updates'] = $value;
    }
    /**
     * Whether the Files Agent Proxy recently validated a direct transfer connection. `true` means a direct connection was recently validated (actual availability can vary by client network), `false` means direct transfers are enabled but not currently available, and `null` means direct transfers are disabled or unsupported. Only provided for a connected Files Agent when showing a single Remote Server.
     *
     * @return bool|null
     */
    public function getDirectTransferAvailable()
    {
        return isset($this->attributes['direct_transfer_available']) ? $this->attributes['direct_transfer_available'] : null;
    }

    /**
     * Whether the Files Agent Proxy recently validated a direct transfer connection. `true` means a direct connection was recently validated (actual availability can vary by client network), `false` means direct transfers are enabled but not currently available, and `null` means direct transfers are disabled or unsupported. Only provided for a connected Files Agent when showing a single Remote Server.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDirectTransferAvailable($value)
    {
        return $this->attributes['direct_transfer_available'] = $value;
    }
    /**
     * Route traffic to outbound on a files-agent
     *
     * @return int|null
     */
    public function getOutboundAgentId()
    {
        return isset($this->attributes['outbound_agent_id']) ? $this->attributes['outbound_agent_id'] : null;
    }

    /**
     * Route traffic to outbound on a files-agent
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setOutboundAgentId($value)
    {
        return $this->attributes['outbound_agent_id'] = $value;
    }
    /**
     * Filebase: Bucket name
     *
     * @return string|null
     */
    public function getFilebaseBucket()
    {
        return isset($this->attributes['filebase_bucket']) ? $this->attributes['filebase_bucket'] : null;
    }

    /**
     * Filebase: Bucket name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFilebaseBucket($value)
    {
        return $this->attributes['filebase_bucket'] = $value;
    }
    /**
     * Filebase: Access Key.
     *
     * @return string|null
     */
    public function getFilebaseAccessKey()
    {
        return isset($this->attributes['filebase_access_key']) ? $this->attributes['filebase_access_key'] : null;
    }

    /**
     * Filebase: Access Key.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFilebaseAccessKey($value)
    {
        return $this->attributes['filebase_access_key'] = $value;
    }
    /**
     * Files.com direct link: paired API key prefix.
     *
     * @return string|null
     */
    public function getFilesApiKeyPrefix()
    {
        return isset($this->attributes['files_api_key_prefix']) ? $this->attributes['files_api_key_prefix'] : null;
    }

    /**
     * Files.com direct link: paired API key prefix.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFilesApiKeyPrefix($value)
    {
        return $this->attributes['files_api_key_prefix'] = $value;
    }
    /**
     * Cloudflare: Bucket name
     *
     * @return string|null
     */
    public function getCloudflareBucket()
    {
        return isset($this->attributes['cloudflare_bucket']) ? $this->attributes['cloudflare_bucket'] : null;
    }

    /**
     * Cloudflare: Bucket name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCloudflareBucket($value)
    {
        return $this->attributes['cloudflare_bucket'] = $value;
    }
    /**
     * Cloudflare: Access Key.
     *
     * @return string|null
     */
    public function getCloudflareAccessKey()
    {
        return isset($this->attributes['cloudflare_access_key']) ? $this->attributes['cloudflare_access_key'] : null;
    }

    /**
     * Cloudflare: Access Key.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCloudflareAccessKey($value)
    {
        return $this->attributes['cloudflare_access_key'] = $value;
    }
    /**
     * Cloudflare: endpoint
     *
     * @return string|null
     */
    public function getCloudflareEndpoint()
    {
        return isset($this->attributes['cloudflare_endpoint']) ? $this->attributes['cloudflare_endpoint'] : null;
    }

    /**
     * Cloudflare: endpoint
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCloudflareEndpoint($value)
    {
        return $this->attributes['cloudflare_endpoint'] = $value;
    }
    /**
     * Dropbox: If true, list Team folders in root?
     *
     * @return bool|null
     */
    public function getDropboxTeams()
    {
        return isset($this->attributes['dropbox_teams']) ? $this->attributes['dropbox_teams'] : null;
    }

    /**
     * Dropbox: If true, list Team folders in root?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDropboxTeams($value)
    {
        return $this->attributes['dropbox_teams'] = $value;
    }
    /**
     * Linode: Bucket name
     *
     * @return string|null
     */
    public function getLinodeBucket()
    {
        return isset($this->attributes['linode_bucket']) ? $this->attributes['linode_bucket'] : null;
    }

    /**
     * Linode: Bucket name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLinodeBucket($value)
    {
        return $this->attributes['linode_bucket'] = $value;
    }
    /**
     * Linode: Access Key
     *
     * @return string|null
     */
    public function getLinodeAccessKey()
    {
        return isset($this->attributes['linode_access_key']) ? $this->attributes['linode_access_key'] : null;
    }

    /**
     * Linode: Access Key
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLinodeAccessKey($value)
    {
        return $this->attributes['linode_access_key'] = $value;
    }
    /**
     * Linode: region
     *
     * @return string|null
     */
    public function getLinodeRegion()
    {
        return isset($this->attributes['linode_region']) ? $this->attributes['linode_region'] : null;
    }

    /**
     * Linode: region
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLinodeRegion($value)
    {
        return $this->attributes['linode_region'] = $value;
    }
    /**
     * If true, this remote server supports file versioning. This value is determined automatically by Files.com.
     *
     * @return bool|null
     */
    public function getSupportsVersioning()
    {
        return isset($this->attributes['supports_versioning']) ? $this->attributes['supports_versioning'] : null;
    }

    /**
     * If true, this remote server supports file versioning. This value is determined automatically by Files.com.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSupportsVersioning($value)
    {
        return $this->attributes['supports_versioning'] = $value;
    }
    /**
     * Password, if needed.
     *
     * @return string|null
     */
    public function getPassword()
    {
        return isset($this->attributes['password']) ? $this->attributes['password'] : null;
    }

    /**
     * Password, if needed.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPassword($value)
    {
        return $this->attributes['password'] = $value;
    }
    /**
     * Private key, if needed.
     *
     * @return string|null
     */
    public function getPrivateKey()
    {
        return isset($this->attributes['private_key']) ? $this->attributes['private_key'] : null;
    }

    /**
     * Private key, if needed.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPrivateKey($value)
    {
        return $this->attributes['private_key'] = $value;
    }
    /**
     * Passphrase for private key if needed.
     *
     * @return string|null
     */
    public function getPrivateKeyPassphrase()
    {
        return isset($this->attributes['private_key_passphrase']) ? $this->attributes['private_key_passphrase'] : null;
    }

    /**
     * Passphrase for private key if needed.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPrivateKeyPassphrase($value)
    {
        return $this->attributes['private_key_passphrase'] = $value;
    }
    /**
     * Reset authenticated account?
     *
     * @return bool|null
     */
    public function getResetAuthentication()
    {
        return isset($this->attributes['reset_authentication']) ? $this->attributes['reset_authentication'] : null;
    }

    /**
     * Reset authenticated account?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setResetAuthentication($value)
    {
        return $this->attributes['reset_authentication'] = $value;
    }
    /**
     * SharePoint: PEM-encoded certificate and unencrypted private key for app-only authentication.
     *
     * @return string|null
     */
    public function getSharepointClientCertificate()
    {
        return isset($this->attributes['sharepoint_client_certificate']) ? $this->attributes['sharepoint_client_certificate'] : null;
    }

    /**
     * SharePoint: PEM-encoded certificate and unencrypted private key for app-only authentication.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSharepointClientCertificate($value)
    {
        return $this->attributes['sharepoint_client_certificate'] = $value;
    }
    /**
     * SharePoint: Microsoft Entra application client secret for app-only authentication.
     *
     * @return string|null
     */
    public function getSharepointClientSecret()
    {
        return isset($this->attributes['sharepoint_client_secret']) ? $this->attributes['sharepoint_client_secret'] : null;
    }

    /**
     * SharePoint: Microsoft Entra application client secret for app-only authentication.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSharepointClientSecret($value)
    {
        return $this->attributes['sharepoint_client_secret'] = $value;
    }
    /**
     * SSL client certificate.
     *
     * @return string|null
     */
    public function getSslCertificate()
    {
        return isset($this->attributes['ssl_certificate']) ? $this->attributes['ssl_certificate'] : null;
    }

    /**
     * SSL client certificate.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSslCertificate($value)
    {
        return $this->attributes['ssl_certificate'] = $value;
    }
    /**
     * AWS: secret key.
     *
     * @return string|null
     */
    public function getAwsSecretKey()
    {
        return isset($this->attributes['aws_secret_key']) ? $this->attributes['aws_secret_key'] : null;
    }

    /**
     * AWS: secret key.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAwsSecretKey($value)
    {
        return $this->attributes['aws_secret_key'] = $value;
    }
    /**
     * Azure Blob Storage: Access Key
     *
     * @return string|null
     */
    public function getAzureBlobStorageAccessKey()
    {
        return isset($this->attributes['azure_blob_storage_access_key']) ? $this->attributes['azure_blob_storage_access_key'] : null;
    }

    /**
     * Azure Blob Storage: Access Key
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAzureBlobStorageAccessKey($value)
    {
        return $this->attributes['azure_blob_storage_access_key'] = $value;
    }
    /**
     * Azure Blob Storage: Shared Access Signature (SAS) token
     *
     * @return string|null
     */
    public function getAzureBlobStorageSasToken()
    {
        return isset($this->attributes['azure_blob_storage_sas_token']) ? $this->attributes['azure_blob_storage_sas_token'] : null;
    }

    /**
     * Azure Blob Storage: Shared Access Signature (SAS) token
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAzureBlobStorageSasToken($value)
    {
        return $this->attributes['azure_blob_storage_sas_token'] = $value;
    }
    /**
     * Azure File Storage: Access Key
     *
     * @return string|null
     */
    public function getAzureFilesStorageAccessKey()
    {
        return isset($this->attributes['azure_files_storage_access_key']) ? $this->attributes['azure_files_storage_access_key'] : null;
    }

    /**
     * Azure File Storage: Access Key
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAzureFilesStorageAccessKey($value)
    {
        return $this->attributes['azure_files_storage_access_key'] = $value;
    }
    /**
     * Azure File Storage: Shared Access Signature (SAS) token
     *
     * @return string|null
     */
    public function getAzureFilesStorageSasToken()
    {
        return isset($this->attributes['azure_files_storage_sas_token']) ? $this->attributes['azure_files_storage_sas_token'] : null;
    }

    /**
     * Azure File Storage: Shared Access Signature (SAS) token
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAzureFilesStorageSasToken($value)
    {
        return $this->attributes['azure_files_storage_sas_token'] = $value;
    }
    /**
     * Backblaze B2 Cloud Storage: applicationKey
     *
     * @return string|null
     */
    public function getBackblazeB2ApplicationKey()
    {
        return isset($this->attributes['backblaze_b2_application_key']) ? $this->attributes['backblaze_b2_application_key'] : null;
    }

    /**
     * Backblaze B2 Cloud Storage: applicationKey
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setBackblazeB2ApplicationKey($value)
    {
        return $this->attributes['backblaze_b2_application_key'] = $value;
    }
    /**
     * Backblaze B2 Cloud Storage: keyID
     *
     * @return string|null
     */
    public function getBackblazeB2KeyId()
    {
        return isset($this->attributes['backblaze_b2_key_id']) ? $this->attributes['backblaze_b2_key_id'] : null;
    }

    /**
     * Backblaze B2 Cloud Storage: keyID
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setBackblazeB2KeyId($value)
    {
        return $this->attributes['backblaze_b2_key_id'] = $value;
    }
    /**
     * Cloudflare: Secret Key
     *
     * @return string|null
     */
    public function getCloudflareSecretKey()
    {
        return isset($this->attributes['cloudflare_secret_key']) ? $this->attributes['cloudflare_secret_key'] : null;
    }

    /**
     * Cloudflare: Secret Key
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCloudflareSecretKey($value)
    {
        return $this->attributes['cloudflare_secret_key'] = $value;
    }
    /**
     * Filebase: Secret Key
     *
     * @return string|null
     */
    public function getFilebaseSecretKey()
    {
        return isset($this->attributes['filebase_secret_key']) ? $this->attributes['filebase_secret_key'] : null;
    }

    /**
     * Filebase: Secret Key
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFilebaseSecretKey($value)
    {
        return $this->attributes['filebase_secret_key'] = $value;
    }
    /**
     * Google Cloud Storage: JSON file that contains the private key. To generate see https://cloud.google.com/storage/docs/json_api/v1/how-tos/authorizing#APIKey
     *
     * @return string|null
     */
    public function getGoogleCloudStorageCredentialsJson()
    {
        return isset($this->attributes['google_cloud_storage_credentials_json']) ? $this->attributes['google_cloud_storage_credentials_json'] : null;
    }

    /**
     * Google Cloud Storage: JSON file that contains the private key. To generate see https://cloud.google.com/storage/docs/json_api/v1/how-tos/authorizing#APIKey
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGoogleCloudStorageCredentialsJson($value)
    {
        return $this->attributes['google_cloud_storage_credentials_json'] = $value;
    }
    /**
     * Google Cloud Storage: S3-compatible secret key
     *
     * @return string|null
     */
    public function getGoogleCloudStorageS3CompatibleSecretKey()
    {
        return isset($this->attributes['google_cloud_storage_s3_compatible_secret_key']) ? $this->attributes['google_cloud_storage_s3_compatible_secret_key'] : null;
    }

    /**
     * Google Cloud Storage: S3-compatible secret key
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGoogleCloudStorageS3CompatibleSecretKey($value)
    {
        return $this->attributes['google_cloud_storage_s3_compatible_secret_key'] = $value;
    }
    /**
     * Linode: Secret Key
     *
     * @return string|null
     */
    public function getLinodeSecretKey()
    {
        return isset($this->attributes['linode_secret_key']) ? $this->attributes['linode_secret_key'] : null;
    }

    /**
     * Linode: Secret Key
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLinodeSecretKey($value)
    {
        return $this->attributes['linode_secret_key'] = $value;
    }
    /**
     * S3-compatible: Secret Key
     *
     * @return string|null
     */
    public function getS3CompatibleSecretKey()
    {
        return isset($this->attributes['s3_compatible_secret_key']) ? $this->attributes['s3_compatible_secret_key'] : null;
    }

    /**
     * S3-compatible: Secret Key
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setS3CompatibleSecretKey($value)
    {
        return $this->attributes['s3_compatible_secret_key'] = $value;
    }
    /**
     * Wasabi: Secret Key
     *
     * @return string|null
     */
    public function getWasabiSecretKey()
    {
        return isset($this->attributes['wasabi_secret_key']) ? $this->attributes['wasabi_secret_key'] : null;
    }

    /**
     * Wasabi: Secret Key
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setWasabiSecretKey($value)
    {
        return $this->attributes['wasabi_secret_key'] = $value;
    }
    /**
     * Files.com direct link: API key used once to pair the remote server.
     *
     * @return string|null
     */
    public function getFilesApiKey()
    {
        return isset($this->attributes['files_api_key']) ? $this->attributes['files_api_key'] : null;
    }

    /**
     * Files.com direct link: API key used once to pair the remote server.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFilesApiKey($value)
    {
        return $this->attributes['files_api_key'] = $value;
    }

    /**
     * List Files.com Agent nodes
     *
     * GET /remote_servers/{id}/agent_nodes
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Remote Server ID.
     * @return \Files\Model\AgentNode
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function agentNodes($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['id'])) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/remote_servers/' . rawurlencode(strval(@$params['id'])) . '/agent_nodes', 'GET', $params, $this->options);
        return new AgentNode((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Push update to Files Agent
     *
     * POST /remote_servers/{id}/agent_push_update
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Remote Server ID.
     * @return \Files\Model\AgentPushUpdate
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function agentPushUpdate($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['id'])) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/remote_servers/' . rawurlencode(strval(@$params['id'])) . '/agent_push_update', 'POST', $params, $this->options);
        return new AgentPushUpdate((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Update Remote Server
     *
     * PATCH /remote_servers/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Remote Server ID.
     *     - `password` (string, optional): Password, if needed.
     *     - `private_key` (string, optional): Private key, if needed.
     *     - `private_key_passphrase` (string, optional): Passphrase for private key if needed.
     *     - `reset_authentication` (bool, optional): Reset authenticated account?
     *     - `sharepoint_client_certificate` (string, optional): SharePoint: PEM-encoded certificate and unencrypted private key for app-only authentication.
     *     - `sharepoint_client_secret` (string, optional): SharePoint: Microsoft Entra application client secret for app-only authentication.
     *     - `ssl_certificate` (string, optional): SSL client certificate.
     *     - `aws_secret_key` (string, optional): AWS: secret key.
     *     - `azure_blob_storage_access_key` (string, optional): Azure Blob Storage: Access Key
     *     - `azure_blob_storage_sas_token` (string, optional): Azure Blob Storage: Shared Access Signature (SAS) token
     *     - `azure_files_storage_access_key` (string, optional): Azure File Storage: Access Key
     *     - `azure_files_storage_sas_token` (string, optional): Azure File Storage: Shared Access Signature (SAS) token
     *     - `backblaze_b2_application_key` (string, optional): Backblaze B2 Cloud Storage: applicationKey
     *     - `backblaze_b2_key_id` (string, optional): Backblaze B2 Cloud Storage: keyID
     *     - `cloudflare_secret_key` (string, optional): Cloudflare: Secret Key
     *     - `filebase_secret_key` (string, optional): Filebase: Secret Key
     *     - `google_cloud_storage_credentials_json` (string, optional): Google Cloud Storage: JSON file that contains the private key. To generate see https://cloud.google.com/storage/docs/json_api/v1/how-tos/authorizing#APIKey
     *     - `google_cloud_storage_s3_compatible_secret_key` (string, optional): Google Cloud Storage: S3-compatible secret key
     *     - `linode_secret_key` (string, optional): Linode: Secret Key
     *     - `s3_compatible_secret_key` (string, optional): S3-compatible: Secret Key
     *     - `wasabi_secret_key` (string, optional): Wasabi: Secret Key
     *     - `allow_relative_paths` (bool, optional): Allow relative paths in SFTP. If true, paths will not be forced to be absolute, allowing operations relative to the user's home directory.
     *     - `aws_access_key` (string, optional): AWS Access Key.
     *     - `azure_blob_storage_account` (string, optional): Azure Blob Storage: Account name
     *     - `azure_blob_storage_container` (string, optional): Azure Blob Storage: Container name
     *     - `azure_blob_storage_dns_suffix` (string, optional): Azure Blob Storage: Custom DNS suffix
     *     - `azure_blob_storage_hierarchical_namespace` (bool, optional): Azure Blob Storage: Does the storage account has hierarchical namespace feature enabled?
     *     - `azure_files_storage_account` (string, optional): Azure Files: Storage Account name
     *     - `azure_files_storage_dns_suffix` (string, optional): Azure Files: Custom DNS suffix
     *     - `azure_files_storage_share_name` (string, optional): Azure Files:  Storage Share name
     *     - `backblaze_b2_bucket` (string, optional): Backblaze B2 Cloud Storage: Bucket name
     *     - `backblaze_b2_s3_endpoint` (string, optional): Backblaze B2 Cloud Storage: S3 Endpoint
     *     - `buffer_uploads` (string, optional): If set to always, uploads to this server will be uploaded first to Files.com before being sent to the remote server. This can improve performance in certain access patterns, such as high-latency connections.  It will cause data to be temporarily stored in Files.com. If set to auto, we will perform this optimization if we believe it to be a benefit in a given situation. Allowed values: auto, always, never.
     *     - `cloudflare_access_key` (string, optional): Cloudflare: Access Key.
     *     - `cloudflare_bucket` (string, optional): Cloudflare: Bucket name
     *     - `cloudflare_endpoint` (string, optional): Cloudflare: endpoint
     *     - `description` (string, optional): Internal description for your reference
     *     - `dropbox_teams` (bool, optional): Dropbox: If true, list Team folders in root?
     *     - `enable_dedicated_ips` (bool, optional): `true` if remote server only accepts connections from dedicated IPs
     *     - `filebase_access_key` (string, optional): Filebase: Access Key.
     *     - `filebase_bucket` (string, optional): Filebase: Bucket name
     *     - `files_api_key` (string, optional): Files.com direct link: API key used once to pair the remote server.
     *     - `files_agent_permission_set` (string, optional): Agent file permissions: `read_only`, `write_only`, or `read_write`. Files.com writes this value as `permission_set` in the configuration file generated for this Remote Server. Setting it on Create or Update changes the generated file, not a running Agent. The Agent enforces the permissions in its local configuration file, controlled by the host's system administrator, and reports them at registration, replacing the stored value. Allowed values: read_write, read_only, write_only.
     *     - `files_agent_root` (string, optional): Agent local root path. Files.com writes this value as `root` in the configuration file generated for this Remote Server. Setting it on Create or Update changes the generated file, not a running Agent. The Agent enforces the root in its local configuration file, controlled by the host's system administrator, and reports it at registration, replacing the stored value.
     *     - `files_agent_version` (string, optional): Files.com Agent version reported by the Agent at each registration, replacing the stored value. This field is not included in the generated Agent configuration file. Setting it on Create or Update does not update the running Agent.
     *     - `outbound_agent_id` (int, optional): Route traffic to outbound on a files-agent
     *     - `custom_domain_id` (int, optional): Custom Domain ID whose dedicated IP addresses are selected when this Remote Server uses dedicated IPs. Must be available to this Remote Server's workspace. Requires enable_dedicated_ips and cannot be combined with an outbound Agent. Set to null to use the site's default dedicated IPs.
     *     - `google_cloud_storage_authentication_method` (string, optional): Google Cloud Storage: Authentication method. Can be json, hmac, or oauth. Allowed values: json, hmac, oauth.
     *     - `google_cloud_storage_bucket` (string, optional): Google Cloud Storage: Bucket Name
     *     - `google_cloud_storage_oauth_scope` (string, optional): Google Cloud Storage: OAuth scope. Can be https://www.googleapis.com/auth/devstorage.read_only or https://www.googleapis.com/auth/devstorage.read_write.
     *     - `google_cloud_storage_project_id` (string, optional): Google Cloud Storage: Project ID
     *     - `google_cloud_storage_s3_compatible_access_key` (string, optional): Google Cloud Storage: S3-compatible Access Key.
     *     - `hostname` (string, optional): Hostname or IP address. For Agent Remote Servers, the Agent reports the hostname it connects from at each registration, replacing the stored value. This field is not included in the generated Agent configuration file. Setting it on Create or Update does not change the running Agent.
     *     - `linode_access_key` (string, optional): Linode: Access Key
     *     - `linode_bucket` (string, optional): Linode: Bucket name
     *     - `linode_region` (string, optional): Linode: region
     *     - `max_connections` (int, optional): Max number of parallel connections.  Ignored for S3 connections (we will parallelize these as much as possible).
     *     - `name` (string, optional): Internal name for your reference
     *     - `one_drive_account_type` (string, optional): OneDrive: Either personal or business_other account types Allowed values: personal, business_other.
     *     - `pin_to_site_region` (bool, optional): If true, we will ensure that all communications with this remote server are made through the primary region of the site.  This setting can also be overridden by a site-wide setting which will force it to true.
     *     - `port` (int, optional): Port for remote server. For Agent Remote Servers, the Agent reports its downstream proxy port at each registration, replacing the stored value. This field is not included in the generated Agent configuration file. Setting it on Create or Update does not change the running Agent.
     *     - `upload_staging_path` (string, optional): Upload staging path.  Applies to SFTP only.  If a path is provided here, files will first be uploaded to this path on the remote folder and the moved into the final correct path via an SFTP move command.  This is required by some remote MFT systems to emulate atomic uploads, which are otherwise not supoprted by SFTP.
     *     - `remote_server_credential_id` (int, optional): ID of Remote Server Credential, if applicable.
     *     - `s3_assume_role_arn` (string, optional): AWS IAM Role ARN for AssumeRole authentication.
     *     - `s3_assume_role_duration_seconds` (int, optional): Session duration in seconds for AssumeRole authentication (900-43200).
     *     - `s3_bucket` (string, optional): S3 bucket name
     *     - `s3_compatible_access_key` (string, optional): S3-compatible: Access Key
     *     - `s3_compatible_bucket` (string, optional): S3-compatible: Bucket name
     *     - `s3_compatible_endpoint` (string, optional): S3-compatible: endpoint
     *     - `s3_compatible_region` (string, optional): S3-compatible: region
     *     - `s3_compatible_virtual_hosted_style` (bool, optional): S3-compatible: If true, use virtual-hosted-style URLs instead of path-style URLs
     *     - `s3_kms_key_id` (string, optional): ARN of the AWS KMS key that encrypts files written to the bucket (SSE-KMS). Leave blank unless your bucket requires a specific key.
     *     - `s3_region` (string, optional): S3 region
     *     - `server_certificate` (string, optional): Remote server certificate Allowed values: require_match, allow_any.
     *     - `server_host_key` (string, optional): Pinned SSH host key or OpenSSH host certificate for SFTP. If omitted, Files.com detects and stores a host key, preferring plain keys over certificates. With `server_certificate=require_match` (the default), the server must present the exact pinned key or certificate and prove it holds the matching private key. A pinned certificate is compared in full, so renewal can require updating `server_host_key` even when its underlying key is unchanged. Files.com does not check certificate CA signatures, principals, or validity periods. Certificate expiration alone does not end the pin. Update `server_host_key` to replace the pin.
     *     - `server_type` (string, optional): Remote server type. Allowed values: ftp, sftp, s3, google_cloud_storage, webdav, wasabi, backblaze_b2, one_drive, box, dropbox, google_drive, azure, sharepoint, s3_compatible, azure_files, files_agent, filebase, cloudflare, linode, files_com.
     *     - `site_admin_access` (bool, optional): Owner's preference for administrator access to this user-owned Remote Server. Null uses the Site policy's default. Only the owner may change it when the Site policy allows user choice.
     *     - `sharepoint_client_id` (string, optional): SharePoint: Microsoft Entra application client ID for app-only authentication.
     *     - `sharepoint_site_url` (string, optional): SharePoint: Site URL to scope app-only authentication to a single site. Leave blank to browse all sites.
     *     - `sharepoint_tenant_id` (string, optional): SharePoint: Microsoft Entra tenant ID for app-only authentication.
     *     - `ssl` (string, optional): Should we require SSL? Allowed values: if_available, require, require_implicit, never.
     *     - `username` (string, optional): Remote server username.
     *     - `wasabi_access_key` (string, optional): Wasabi: Access Key.
     *     - `wasabi_bucket` (string, optional): Wasabi: Bucket name
     *     - `wasabi_region` (string, optional): Wasabi: Region
     * @return \Files\Model\RemoteServer
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function update($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['id'])) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (!empty($params['password']) && !is_string($params['password'])) {
            throw new \Files\Exception\InvalidParameterException('$password must be of type string; received ' . gettype(@$params['password']));
        }

        if (!empty($params['private_key']) && !is_string($params['private_key'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key must be of type string; received ' . gettype(@$params['private_key']));
        }

        if (!empty($params['private_key_passphrase']) && !is_string($params['private_key_passphrase'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key_passphrase must be of type string; received ' . gettype(@$params['private_key_passphrase']));
        }

        if (!empty($params['sharepoint_client_certificate']) && !is_string($params['sharepoint_client_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$sharepoint_client_certificate must be of type string; received ' . gettype(@$params['sharepoint_client_certificate']));
        }

        if (!empty($params['sharepoint_client_secret']) && !is_string($params['sharepoint_client_secret'])) {
            throw new \Files\Exception\InvalidParameterException('$sharepoint_client_secret must be of type string; received ' . gettype(@$params['sharepoint_client_secret']));
        }

        if (!empty($params['ssl_certificate']) && !is_string($params['ssl_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$ssl_certificate must be of type string; received ' . gettype(@$params['ssl_certificate']));
        }

        if (!empty($params['aws_secret_key']) && !is_string($params['aws_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$aws_secret_key must be of type string; received ' . gettype(@$params['aws_secret_key']));
        }

        if (!empty($params['azure_blob_storage_access_key']) && !is_string($params['azure_blob_storage_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_access_key must be of type string; received ' . gettype(@$params['azure_blob_storage_access_key']));
        }

        if (!empty($params['azure_blob_storage_sas_token']) && !is_string($params['azure_blob_storage_sas_token'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_sas_token must be of type string; received ' . gettype(@$params['azure_blob_storage_sas_token']));
        }

        if (!empty($params['azure_files_storage_access_key']) && !is_string($params['azure_files_storage_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_access_key must be of type string; received ' . gettype(@$params['azure_files_storage_access_key']));
        }

        if (!empty($params['azure_files_storage_sas_token']) && !is_string($params['azure_files_storage_sas_token'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_sas_token must be of type string; received ' . gettype(@$params['azure_files_storage_sas_token']));
        }

        if (!empty($params['backblaze_b2_application_key']) && !is_string($params['backblaze_b2_application_key'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_application_key must be of type string; received ' . gettype(@$params['backblaze_b2_application_key']));
        }

        if (!empty($params['backblaze_b2_key_id']) && !is_string($params['backblaze_b2_key_id'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_key_id must be of type string; received ' . gettype(@$params['backblaze_b2_key_id']));
        }

        if (!empty($params['cloudflare_secret_key']) && !is_string($params['cloudflare_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_secret_key must be of type string; received ' . gettype(@$params['cloudflare_secret_key']));
        }

        if (!empty($params['filebase_secret_key']) && !is_string($params['filebase_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$filebase_secret_key must be of type string; received ' . gettype(@$params['filebase_secret_key']));
        }

        if (!empty($params['google_cloud_storage_credentials_json']) && !is_string($params['google_cloud_storage_credentials_json'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_credentials_json must be of type string; received ' . gettype(@$params['google_cloud_storage_credentials_json']));
        }

        if (!empty($params['google_cloud_storage_s3_compatible_secret_key']) && !is_string($params['google_cloud_storage_s3_compatible_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_s3_compatible_secret_key must be of type string; received ' . gettype(@$params['google_cloud_storage_s3_compatible_secret_key']));
        }

        if (!empty($params['linode_secret_key']) && !is_string($params['linode_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_secret_key must be of type string; received ' . gettype(@$params['linode_secret_key']));
        }

        if (!empty($params['s3_compatible_secret_key']) && !is_string($params['s3_compatible_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_secret_key must be of type string; received ' . gettype(@$params['s3_compatible_secret_key']));
        }

        if (!empty($params['wasabi_secret_key']) && !is_string($params['wasabi_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_secret_key must be of type string; received ' . gettype(@$params['wasabi_secret_key']));
        }

        if (!empty($params['aws_access_key']) && !is_string($params['aws_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$aws_access_key must be of type string; received ' . gettype(@$params['aws_access_key']));
        }

        if (!empty($params['azure_blob_storage_account']) && !is_string($params['azure_blob_storage_account'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_account must be of type string; received ' . gettype(@$params['azure_blob_storage_account']));
        }

        if (!empty($params['azure_blob_storage_container']) && !is_string($params['azure_blob_storage_container'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_container must be of type string; received ' . gettype(@$params['azure_blob_storage_container']));
        }

        if (!empty($params['azure_blob_storage_dns_suffix']) && !is_string($params['azure_blob_storage_dns_suffix'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_dns_suffix must be of type string; received ' . gettype(@$params['azure_blob_storage_dns_suffix']));
        }

        if (!empty($params['azure_files_storage_account']) && !is_string($params['azure_files_storage_account'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_account must be of type string; received ' . gettype(@$params['azure_files_storage_account']));
        }

        if (!empty($params['azure_files_storage_dns_suffix']) && !is_string($params['azure_files_storage_dns_suffix'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_dns_suffix must be of type string; received ' . gettype(@$params['azure_files_storage_dns_suffix']));
        }

        if (!empty($params['azure_files_storage_share_name']) && !is_string($params['azure_files_storage_share_name'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_share_name must be of type string; received ' . gettype(@$params['azure_files_storage_share_name']));
        }

        if (!empty($params['backblaze_b2_bucket']) && !is_string($params['backblaze_b2_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_bucket must be of type string; received ' . gettype(@$params['backblaze_b2_bucket']));
        }

        if (!empty($params['backblaze_b2_s3_endpoint']) && !is_string($params['backblaze_b2_s3_endpoint'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_s3_endpoint must be of type string; received ' . gettype(@$params['backblaze_b2_s3_endpoint']));
        }

        if (!empty($params['buffer_uploads']) && !is_string($params['buffer_uploads'])) {
            throw new \Files\Exception\InvalidParameterException('$buffer_uploads must be of type string; received ' . gettype(@$params['buffer_uploads']));
        }

        if (!empty($params['cloudflare_access_key']) && !is_string($params['cloudflare_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_access_key must be of type string; received ' . gettype(@$params['cloudflare_access_key']));
        }

        if (!empty($params['cloudflare_bucket']) && !is_string($params['cloudflare_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_bucket must be of type string; received ' . gettype(@$params['cloudflare_bucket']));
        }

        if (!empty($params['cloudflare_endpoint']) && !is_string($params['cloudflare_endpoint'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_endpoint must be of type string; received ' . gettype(@$params['cloudflare_endpoint']));
        }

        if (!empty($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (!empty($params['filebase_access_key']) && !is_string($params['filebase_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$filebase_access_key must be of type string; received ' . gettype(@$params['filebase_access_key']));
        }

        if (!empty($params['filebase_bucket']) && !is_string($params['filebase_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$filebase_bucket must be of type string; received ' . gettype(@$params['filebase_bucket']));
        }

        if (!empty($params['files_api_key']) && !is_string($params['files_api_key'])) {
            throw new \Files\Exception\InvalidParameterException('$files_api_key must be of type string; received ' . gettype(@$params['files_api_key']));
        }

        if (!empty($params['files_agent_permission_set']) && !is_string($params['files_agent_permission_set'])) {
            throw new \Files\Exception\InvalidParameterException('$files_agent_permission_set must be of type string; received ' . gettype(@$params['files_agent_permission_set']));
        }

        if (!empty($params['files_agent_root']) && !is_string($params['files_agent_root'])) {
            throw new \Files\Exception\InvalidParameterException('$files_agent_root must be of type string; received ' . gettype(@$params['files_agent_root']));
        }

        if (!empty($params['files_agent_version']) && !is_string($params['files_agent_version'])) {
            throw new \Files\Exception\InvalidParameterException('$files_agent_version must be of type string; received ' . gettype(@$params['files_agent_version']));
        }

        if (!empty($params['outbound_agent_id']) && !is_int($params['outbound_agent_id'])) {
            throw new \Files\Exception\InvalidParameterException('$outbound_agent_id must be of type int; received ' . gettype(@$params['outbound_agent_id']));
        }

        if (!empty($params['custom_domain_id']) && !is_int($params['custom_domain_id'])) {
            throw new \Files\Exception\InvalidParameterException('$custom_domain_id must be of type int; received ' . gettype(@$params['custom_domain_id']));
        }

        if (!empty($params['google_cloud_storage_authentication_method']) && !is_string($params['google_cloud_storage_authentication_method'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_authentication_method must be of type string; received ' . gettype(@$params['google_cloud_storage_authentication_method']));
        }

        if (!empty($params['google_cloud_storage_bucket']) && !is_string($params['google_cloud_storage_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_bucket must be of type string; received ' . gettype(@$params['google_cloud_storage_bucket']));
        }

        if (!empty($params['google_cloud_storage_oauth_scope']) && !is_string($params['google_cloud_storage_oauth_scope'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_oauth_scope must be of type string; received ' . gettype(@$params['google_cloud_storage_oauth_scope']));
        }

        if (!empty($params['google_cloud_storage_project_id']) && !is_string($params['google_cloud_storage_project_id'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_project_id must be of type string; received ' . gettype(@$params['google_cloud_storage_project_id']));
        }

        if (!empty($params['google_cloud_storage_s3_compatible_access_key']) && !is_string($params['google_cloud_storage_s3_compatible_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_s3_compatible_access_key must be of type string; received ' . gettype(@$params['google_cloud_storage_s3_compatible_access_key']));
        }

        if (!empty($params['hostname']) && !is_string($params['hostname'])) {
            throw new \Files\Exception\InvalidParameterException('$hostname must be of type string; received ' . gettype(@$params['hostname']));
        }

        if (!empty($params['linode_access_key']) && !is_string($params['linode_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_access_key must be of type string; received ' . gettype(@$params['linode_access_key']));
        }

        if (!empty($params['linode_bucket']) && !is_string($params['linode_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_bucket must be of type string; received ' . gettype(@$params['linode_bucket']));
        }

        if (!empty($params['linode_region']) && !is_string($params['linode_region'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_region must be of type string; received ' . gettype(@$params['linode_region']));
        }

        if (!empty($params['max_connections']) && !is_int($params['max_connections'])) {
            throw new \Files\Exception\InvalidParameterException('$max_connections must be of type int; received ' . gettype(@$params['max_connections']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['one_drive_account_type']) && !is_string($params['one_drive_account_type'])) {
            throw new \Files\Exception\InvalidParameterException('$one_drive_account_type must be of type string; received ' . gettype(@$params['one_drive_account_type']));
        }

        if (!empty($params['port']) && !is_int($params['port'])) {
            throw new \Files\Exception\InvalidParameterException('$port must be of type int; received ' . gettype(@$params['port']));
        }

        if (!empty($params['upload_staging_path']) && !is_string($params['upload_staging_path'])) {
            throw new \Files\Exception\InvalidParameterException('$upload_staging_path must be of type string; received ' . gettype(@$params['upload_staging_path']));
        }

        if (!empty($params['remote_server_credential_id']) && !is_int($params['remote_server_credential_id'])) {
            throw new \Files\Exception\InvalidParameterException('$remote_server_credential_id must be of type int; received ' . gettype(@$params['remote_server_credential_id']));
        }

        if (!empty($params['s3_assume_role_arn']) && !is_string($params['s3_assume_role_arn'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_assume_role_arn must be of type string; received ' . gettype(@$params['s3_assume_role_arn']));
        }

        if (!empty($params['s3_assume_role_duration_seconds']) && !is_int($params['s3_assume_role_duration_seconds'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_assume_role_duration_seconds must be of type int; received ' . gettype(@$params['s3_assume_role_duration_seconds']));
        }

        if (!empty($params['s3_bucket']) && !is_string($params['s3_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_bucket must be of type string; received ' . gettype(@$params['s3_bucket']));
        }

        if (!empty($params['s3_compatible_access_key']) && !is_string($params['s3_compatible_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_access_key must be of type string; received ' . gettype(@$params['s3_compatible_access_key']));
        }

        if (!empty($params['s3_compatible_bucket']) && !is_string($params['s3_compatible_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_bucket must be of type string; received ' . gettype(@$params['s3_compatible_bucket']));
        }

        if (!empty($params['s3_compatible_endpoint']) && !is_string($params['s3_compatible_endpoint'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_endpoint must be of type string; received ' . gettype(@$params['s3_compatible_endpoint']));
        }

        if (!empty($params['s3_compatible_region']) && !is_string($params['s3_compatible_region'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_region must be of type string; received ' . gettype(@$params['s3_compatible_region']));
        }

        if (!empty($params['s3_kms_key_id']) && !is_string($params['s3_kms_key_id'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_kms_key_id must be of type string; received ' . gettype(@$params['s3_kms_key_id']));
        }

        if (!empty($params['s3_region']) && !is_string($params['s3_region'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_region must be of type string; received ' . gettype(@$params['s3_region']));
        }

        if (!empty($params['server_certificate']) && !is_string($params['server_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$server_certificate must be of type string; received ' . gettype(@$params['server_certificate']));
        }

        if (!empty($params['server_host_key']) && !is_string($params['server_host_key'])) {
            throw new \Files\Exception\InvalidParameterException('$server_host_key must be of type string; received ' . gettype(@$params['server_host_key']));
        }

        if (!empty($params['server_type']) && !is_string($params['server_type'])) {
            throw new \Files\Exception\InvalidParameterException('$server_type must be of type string; received ' . gettype(@$params['server_type']));
        }

        if (!empty($params['sharepoint_client_id']) && !is_string($params['sharepoint_client_id'])) {
            throw new \Files\Exception\InvalidParameterException('$sharepoint_client_id must be of type string; received ' . gettype(@$params['sharepoint_client_id']));
        }

        if (!empty($params['sharepoint_site_url']) && !is_string($params['sharepoint_site_url'])) {
            throw new \Files\Exception\InvalidParameterException('$sharepoint_site_url must be of type string; received ' . gettype(@$params['sharepoint_site_url']));
        }

        if (!empty($params['sharepoint_tenant_id']) && !is_string($params['sharepoint_tenant_id'])) {
            throw new \Files\Exception\InvalidParameterException('$sharepoint_tenant_id must be of type string; received ' . gettype(@$params['sharepoint_tenant_id']));
        }

        if (!empty($params['ssl']) && !is_string($params['ssl'])) {
            throw new \Files\Exception\InvalidParameterException('$ssl must be of type string; received ' . gettype(@$params['ssl']));
        }

        if (!empty($params['username']) && !is_string($params['username'])) {
            throw new \Files\Exception\InvalidParameterException('$username must be of type string; received ' . gettype(@$params['username']));
        }

        if (!empty($params['wasabi_access_key']) && !is_string($params['wasabi_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_access_key must be of type string; received ' . gettype(@$params['wasabi_access_key']));
        }

        if (!empty($params['wasabi_bucket']) && !is_string($params['wasabi_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_bucket must be of type string; received ' . gettype(@$params['wasabi_bucket']));
        }

        if (!empty($params['wasabi_region']) && !is_string($params['wasabi_region'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_region must be of type string; received ' . gettype(@$params['wasabi_region']));
        }

        $response = Api::sendRequest('/remote_servers/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new RemoteServer((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete Remote Server
     *
     * DELETE /remote_servers/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Remote Server ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function delete($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['id'])) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/remote_servers/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Remote Server
     *
     * DELETE /remote_servers/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Remote Server ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function destroy($params = [])
    {
        $this->delete($params);
        return;
    }

    /**
     * Persist this model's attributes and replace them with the API response.
     *
     * @return bool True after the operation succeeds.
     * @throws \Files\Exception\FilesException When saving fails or updates are unsupported.
     */
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


    /**
     * List Remote Servers
     *
     * GET /remote_servers
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `name`, `server_type`, `backblaze_b2_bucket`, `google_cloud_storage_bucket`, `wasabi_bucket`, `s3_bucket`, `azure_blob_storage_container`, `azure_files_storage_share_name`, `s3_compatible_bucket`, `filebase_bucket`, `cloudflare_bucket` or `linode_bucket`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `name`, `server_type`, `workspace_id`, `backblaze_b2_bucket`, `google_cloud_storage_bucket`, `wasabi_bucket`, `s3_bucket`, `azure_blob_storage_container`, `azure_files_storage_share_name`, `s3_compatible_bucket`, `filebase_bucket`, `cloudflare_bucket` or `linode_bucket`. Valid field combinations are `[ server_type, name ]`, `[ workspace_id, name ]`, `[ backblaze_b2_bucket, name ]`, `[ google_cloud_storage_bucket, name ]`, `[ wasabi_bucket, name ]`, `[ s3_bucket, name ]`, `[ azure_blob_storage_container, name ]`, `[ azure_files_storage_share_name, name ]`, `[ s3_compatible_bucket, name ]`, `[ filebase_bucket, name ]`, `[ cloudflare_bucket, name ]`, `[ linode_bucket, name ]`, `[ workspace_id, server_type ]` or `[ workspace_id, server_type, name ]`.
     *     - `filter_prefix` (array|object, optional): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `name`, `backblaze_b2_bucket`, `google_cloud_storage_bucket`, `wasabi_bucket`, `s3_bucket`, `azure_blob_storage_container`, `azure_files_storage_share_name`, `s3_compatible_bucket`, `filebase_bucket`, `cloudflare_bucket` or `linode_bucket`. Valid field combinations are `[ backblaze_b2_bucket, name ]`, `[ google_cloud_storage_bucket, name ]`, `[ wasabi_bucket, name ]`, `[ s3_bucket, name ]`, `[ azure_blob_storage_container, name ]`, `[ azure_files_storage_share_name, name ]`, `[ s3_compatible_bucket, name ]`, `[ filebase_bucket, name ]`, `[ cloudflare_bucket, name ]` or `[ linode_bucket, name ]`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\RemoteServer[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/remote_servers', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new RemoteServer((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show Remote Server
     *
     * GET /remote_servers/{id}
     *
     * @param int $id Remote Server ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\RemoteServer
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function find($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (empty($params['id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/remote_servers/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new RemoteServer((array) (@$response->data ?: []), $options);
    }
    /**
     * Show Remote Server
     *
     * GET /remote_servers/{id}
     *
     * Alias of find().
     *
     * @param int $id Remote Server ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\RemoteServer
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Download configuration file (required for some Remote Server integrations, such as the Files.com Agent)
     *
     * GET /remote_servers/{id}/configuration_file
     *
     * @param int $id Remote Server ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\RemoteServerConfigurationFile
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function findConfigurationFile($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (empty($params['id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/remote_servers/' . rawurlencode(strval(@$params['id'])) . '/configuration_file', 'GET', $params, $options);

        return new RemoteServerConfigurationFile((array) (@$response->data ?: []), $options);
    }

    /**
     * Create Remote Server
     *
     * POST /remote_servers
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `password` (string, optional): Password, if needed.
     *     - `private_key` (string, optional): Private key, if needed.
     *     - `private_key_passphrase` (string, optional): Passphrase for private key if needed.
     *     - `reset_authentication` (bool, optional): Reset authenticated account?
     *     - `sharepoint_client_certificate` (string, optional): SharePoint: PEM-encoded certificate and unencrypted private key for app-only authentication.
     *     - `sharepoint_client_secret` (string, optional): SharePoint: Microsoft Entra application client secret for app-only authentication.
     *     - `ssl_certificate` (string, optional): SSL client certificate.
     *     - `aws_secret_key` (string, optional): AWS: secret key.
     *     - `azure_blob_storage_access_key` (string, optional): Azure Blob Storage: Access Key
     *     - `azure_blob_storage_sas_token` (string, optional): Azure Blob Storage: Shared Access Signature (SAS) token
     *     - `azure_files_storage_access_key` (string, optional): Azure File Storage: Access Key
     *     - `azure_files_storage_sas_token` (string, optional): Azure File Storage: Shared Access Signature (SAS) token
     *     - `backblaze_b2_application_key` (string, optional): Backblaze B2 Cloud Storage: applicationKey
     *     - `backblaze_b2_key_id` (string, optional): Backblaze B2 Cloud Storage: keyID
     *     - `cloudflare_secret_key` (string, optional): Cloudflare: Secret Key
     *     - `filebase_secret_key` (string, optional): Filebase: Secret Key
     *     - `google_cloud_storage_credentials_json` (string, optional): Google Cloud Storage: JSON file that contains the private key. To generate see https://cloud.google.com/storage/docs/json_api/v1/how-tos/authorizing#APIKey
     *     - `google_cloud_storage_s3_compatible_secret_key` (string, optional): Google Cloud Storage: S3-compatible secret key
     *     - `linode_secret_key` (string, optional): Linode: Secret Key
     *     - `s3_compatible_secret_key` (string, optional): S3-compatible: Secret Key
     *     - `wasabi_secret_key` (string, optional): Wasabi: Secret Key
     *     - `allow_relative_paths` (bool, optional): Allow relative paths in SFTP. If true, paths will not be forced to be absolute, allowing operations relative to the user's home directory.
     *     - `aws_access_key` (string, optional): AWS Access Key.
     *     - `azure_blob_storage_account` (string, optional): Azure Blob Storage: Account name
     *     - `azure_blob_storage_container` (string, optional): Azure Blob Storage: Container name
     *     - `azure_blob_storage_dns_suffix` (string, optional): Azure Blob Storage: Custom DNS suffix
     *     - `azure_blob_storage_hierarchical_namespace` (bool, optional): Azure Blob Storage: Does the storage account has hierarchical namespace feature enabled?
     *     - `azure_files_storage_account` (string, optional): Azure Files: Storage Account name
     *     - `azure_files_storage_dns_suffix` (string, optional): Azure Files: Custom DNS suffix
     *     - `azure_files_storage_share_name` (string, optional): Azure Files:  Storage Share name
     *     - `backblaze_b2_bucket` (string, optional): Backblaze B2 Cloud Storage: Bucket name
     *     - `backblaze_b2_s3_endpoint` (string, optional): Backblaze B2 Cloud Storage: S3 Endpoint
     *     - `buffer_uploads` (string, optional): If set to always, uploads to this server will be uploaded first to Files.com before being sent to the remote server. This can improve performance in certain access patterns, such as high-latency connections.  It will cause data to be temporarily stored in Files.com. If set to auto, we will perform this optimization if we believe it to be a benefit in a given situation. Allowed values: auto, always, never.
     *     - `cloudflare_access_key` (string, optional): Cloudflare: Access Key.
     *     - `cloudflare_bucket` (string, optional): Cloudflare: Bucket name
     *     - `cloudflare_endpoint` (string, optional): Cloudflare: endpoint
     *     - `description` (string, optional): Internal description for your reference
     *     - `dropbox_teams` (bool, optional): Dropbox: If true, list Team folders in root?
     *     - `enable_dedicated_ips` (bool, optional): `true` if remote server only accepts connections from dedicated IPs
     *     - `filebase_access_key` (string, optional): Filebase: Access Key.
     *     - `filebase_bucket` (string, optional): Filebase: Bucket name
     *     - `files_api_key` (string, optional): Files.com direct link: API key used once to pair the remote server.
     *     - `files_agent_permission_set` (string, optional): Agent file permissions: `read_only`, `write_only`, or `read_write`. Files.com writes this value as `permission_set` in the configuration file generated for this Remote Server. Setting it on Create or Update changes the generated file, not a running Agent. The Agent enforces the permissions in its local configuration file, controlled by the host's system administrator, and reports them at registration, replacing the stored value. Allowed values: read_write, read_only, write_only.
     *     - `files_agent_root` (string, optional): Agent local root path. Files.com writes this value as `root` in the configuration file generated for this Remote Server. Setting it on Create or Update changes the generated file, not a running Agent. The Agent enforces the root in its local configuration file, controlled by the host's system administrator, and reports it at registration, replacing the stored value.
     *     - `files_agent_version` (string, optional): Files.com Agent version reported by the Agent at each registration, replacing the stored value. This field is not included in the generated Agent configuration file. Setting it on Create or Update does not update the running Agent.
     *     - `outbound_agent_id` (int, optional): Route traffic to outbound on a files-agent
     *     - `custom_domain_id` (int, optional): Custom Domain ID whose dedicated IP addresses are selected when this Remote Server uses dedicated IPs. Must be available to this Remote Server's workspace. Requires enable_dedicated_ips and cannot be combined with an outbound Agent. Set to null to use the site's default dedicated IPs.
     *     - `google_cloud_storage_authentication_method` (string, optional): Google Cloud Storage: Authentication method. Can be json, hmac, or oauth. Allowed values: json, hmac, oauth.
     *     - `google_cloud_storage_bucket` (string, optional): Google Cloud Storage: Bucket Name
     *     - `google_cloud_storage_oauth_scope` (string, optional): Google Cloud Storage: OAuth scope. Can be https://www.googleapis.com/auth/devstorage.read_only or https://www.googleapis.com/auth/devstorage.read_write.
     *     - `google_cloud_storage_project_id` (string, optional): Google Cloud Storage: Project ID
     *     - `google_cloud_storage_s3_compatible_access_key` (string, optional): Google Cloud Storage: S3-compatible Access Key.
     *     - `hostname` (string, optional): Hostname or IP address. For Agent Remote Servers, the Agent reports the hostname it connects from at each registration, replacing the stored value. This field is not included in the generated Agent configuration file. Setting it on Create or Update does not change the running Agent.
     *     - `linode_access_key` (string, optional): Linode: Access Key
     *     - `linode_bucket` (string, optional): Linode: Bucket name
     *     - `linode_region` (string, optional): Linode: region
     *     - `max_connections` (int, optional): Max number of parallel connections.  Ignored for S3 connections (we will parallelize these as much as possible).
     *     - `name` (string, optional): Internal name for your reference
     *     - `one_drive_account_type` (string, optional): OneDrive: Either personal or business_other account types Allowed values: personal, business_other.
     *     - `pin_to_site_region` (bool, optional): If true, we will ensure that all communications with this remote server are made through the primary region of the site.  This setting can also be overridden by a site-wide setting which will force it to true.
     *     - `port` (int, optional): Port for remote server. For Agent Remote Servers, the Agent reports its downstream proxy port at each registration, replacing the stored value. This field is not included in the generated Agent configuration file. Setting it on Create or Update does not change the running Agent.
     *     - `upload_staging_path` (string, optional): Upload staging path.  Applies to SFTP only.  If a path is provided here, files will first be uploaded to this path on the remote folder and the moved into the final correct path via an SFTP move command.  This is required by some remote MFT systems to emulate atomic uploads, which are otherwise not supoprted by SFTP.
     *     - `remote_server_credential_id` (int, optional): ID of Remote Server Credential, if applicable.
     *     - `s3_assume_role_arn` (string, optional): AWS IAM Role ARN for AssumeRole authentication.
     *     - `s3_assume_role_duration_seconds` (int, optional): Session duration in seconds for AssumeRole authentication (900-43200).
     *     - `s3_bucket` (string, optional): S3 bucket name
     *     - `s3_compatible_access_key` (string, optional): S3-compatible: Access Key
     *     - `s3_compatible_bucket` (string, optional): S3-compatible: Bucket name
     *     - `s3_compatible_endpoint` (string, optional): S3-compatible: endpoint
     *     - `s3_compatible_region` (string, optional): S3-compatible: region
     *     - `s3_compatible_virtual_hosted_style` (bool, optional): S3-compatible: If true, use virtual-hosted-style URLs instead of path-style URLs
     *     - `s3_kms_key_id` (string, optional): ARN of the AWS KMS key that encrypts files written to the bucket (SSE-KMS). Leave blank unless your bucket requires a specific key.
     *     - `s3_region` (string, optional): S3 region
     *     - `server_certificate` (string, optional): Remote server certificate Allowed values: require_match, allow_any.
     *     - `server_host_key` (string, optional): Pinned SSH host key or OpenSSH host certificate for SFTP. If omitted, Files.com detects and stores a host key, preferring plain keys over certificates. With `server_certificate=require_match` (the default), the server must present the exact pinned key or certificate and prove it holds the matching private key. A pinned certificate is compared in full, so renewal can require updating `server_host_key` even when its underlying key is unchanged. Files.com does not check certificate CA signatures, principals, or validity periods. Certificate expiration alone does not end the pin. Update `server_host_key` to replace the pin.
     *     - `server_type` (string, optional): Remote server type. Allowed values: ftp, sftp, s3, google_cloud_storage, webdav, wasabi, backblaze_b2, one_drive, box, dropbox, google_drive, azure, sharepoint, s3_compatible, azure_files, files_agent, filebase, cloudflare, linode, files_com.
     *     - `site_admin_access` (bool, optional): Owner's preference for administrator access to this user-owned Remote Server. Null uses the Site policy's default. Only the owner may change it when the Site policy allows user choice.
     *     - `sharepoint_client_id` (string, optional): SharePoint: Microsoft Entra application client ID for app-only authentication.
     *     - `sharepoint_site_url` (string, optional): SharePoint: Site URL to scope app-only authentication to a single site. Leave blank to browse all sites.
     *     - `sharepoint_tenant_id` (string, optional): SharePoint: Microsoft Entra tenant ID for app-only authentication.
     *     - `ssl` (string, optional): Should we require SSL? Allowed values: if_available, require, require_implicit, never.
     *     - `username` (string, optional): Remote server username.
     *     - `wasabi_access_key` (string, optional): Wasabi: Access Key.
     *     - `wasabi_bucket` (string, optional): Wasabi: Bucket name
     *     - `wasabi_region` (string, optional): Wasabi: Region
     *     - `workspace_id` (int, optional): Workspace ID (0 for default workspace)
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\RemoteServer
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['password']) && !is_string($params['password'])) {
            throw new \Files\Exception\InvalidParameterException('$password must be of type string; received ' . gettype(@$params['password']));
        }

        if (!empty($params['private_key']) && !is_string($params['private_key'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key must be of type string; received ' . gettype(@$params['private_key']));
        }

        if (!empty($params['private_key_passphrase']) && !is_string($params['private_key_passphrase'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key_passphrase must be of type string; received ' . gettype(@$params['private_key_passphrase']));
        }

        if (!empty($params['sharepoint_client_certificate']) && !is_string($params['sharepoint_client_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$sharepoint_client_certificate must be of type string; received ' . gettype(@$params['sharepoint_client_certificate']));
        }

        if (!empty($params['sharepoint_client_secret']) && !is_string($params['sharepoint_client_secret'])) {
            throw new \Files\Exception\InvalidParameterException('$sharepoint_client_secret must be of type string; received ' . gettype(@$params['sharepoint_client_secret']));
        }

        if (!empty($params['ssl_certificate']) && !is_string($params['ssl_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$ssl_certificate must be of type string; received ' . gettype(@$params['ssl_certificate']));
        }

        if (!empty($params['aws_secret_key']) && !is_string($params['aws_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$aws_secret_key must be of type string; received ' . gettype(@$params['aws_secret_key']));
        }

        if (!empty($params['azure_blob_storage_access_key']) && !is_string($params['azure_blob_storage_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_access_key must be of type string; received ' . gettype(@$params['azure_blob_storage_access_key']));
        }

        if (!empty($params['azure_blob_storage_sas_token']) && !is_string($params['azure_blob_storage_sas_token'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_sas_token must be of type string; received ' . gettype(@$params['azure_blob_storage_sas_token']));
        }

        if (!empty($params['azure_files_storage_access_key']) && !is_string($params['azure_files_storage_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_access_key must be of type string; received ' . gettype(@$params['azure_files_storage_access_key']));
        }

        if (!empty($params['azure_files_storage_sas_token']) && !is_string($params['azure_files_storage_sas_token'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_sas_token must be of type string; received ' . gettype(@$params['azure_files_storage_sas_token']));
        }

        if (!empty($params['backblaze_b2_application_key']) && !is_string($params['backblaze_b2_application_key'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_application_key must be of type string; received ' . gettype(@$params['backblaze_b2_application_key']));
        }

        if (!empty($params['backblaze_b2_key_id']) && !is_string($params['backblaze_b2_key_id'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_key_id must be of type string; received ' . gettype(@$params['backblaze_b2_key_id']));
        }

        if (!empty($params['cloudflare_secret_key']) && !is_string($params['cloudflare_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_secret_key must be of type string; received ' . gettype(@$params['cloudflare_secret_key']));
        }

        if (!empty($params['filebase_secret_key']) && !is_string($params['filebase_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$filebase_secret_key must be of type string; received ' . gettype(@$params['filebase_secret_key']));
        }

        if (!empty($params['google_cloud_storage_credentials_json']) && !is_string($params['google_cloud_storage_credentials_json'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_credentials_json must be of type string; received ' . gettype(@$params['google_cloud_storage_credentials_json']));
        }

        if (!empty($params['google_cloud_storage_s3_compatible_secret_key']) && !is_string($params['google_cloud_storage_s3_compatible_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_s3_compatible_secret_key must be of type string; received ' . gettype(@$params['google_cloud_storage_s3_compatible_secret_key']));
        }

        if (!empty($params['linode_secret_key']) && !is_string($params['linode_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_secret_key must be of type string; received ' . gettype(@$params['linode_secret_key']));
        }

        if (!empty($params['s3_compatible_secret_key']) && !is_string($params['s3_compatible_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_secret_key must be of type string; received ' . gettype(@$params['s3_compatible_secret_key']));
        }

        if (!empty($params['wasabi_secret_key']) && !is_string($params['wasabi_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_secret_key must be of type string; received ' . gettype(@$params['wasabi_secret_key']));
        }

        if (!empty($params['aws_access_key']) && !is_string($params['aws_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$aws_access_key must be of type string; received ' . gettype(@$params['aws_access_key']));
        }

        if (!empty($params['azure_blob_storage_account']) && !is_string($params['azure_blob_storage_account'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_account must be of type string; received ' . gettype(@$params['azure_blob_storage_account']));
        }

        if (!empty($params['azure_blob_storage_container']) && !is_string($params['azure_blob_storage_container'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_container must be of type string; received ' . gettype(@$params['azure_blob_storage_container']));
        }

        if (!empty($params['azure_blob_storage_dns_suffix']) && !is_string($params['azure_blob_storage_dns_suffix'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_dns_suffix must be of type string; received ' . gettype(@$params['azure_blob_storage_dns_suffix']));
        }

        if (!empty($params['azure_files_storage_account']) && !is_string($params['azure_files_storage_account'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_account must be of type string; received ' . gettype(@$params['azure_files_storage_account']));
        }

        if (!empty($params['azure_files_storage_dns_suffix']) && !is_string($params['azure_files_storage_dns_suffix'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_dns_suffix must be of type string; received ' . gettype(@$params['azure_files_storage_dns_suffix']));
        }

        if (!empty($params['azure_files_storage_share_name']) && !is_string($params['azure_files_storage_share_name'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_share_name must be of type string; received ' . gettype(@$params['azure_files_storage_share_name']));
        }

        if (!empty($params['backblaze_b2_bucket']) && !is_string($params['backblaze_b2_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_bucket must be of type string; received ' . gettype(@$params['backblaze_b2_bucket']));
        }

        if (!empty($params['backblaze_b2_s3_endpoint']) && !is_string($params['backblaze_b2_s3_endpoint'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_s3_endpoint must be of type string; received ' . gettype(@$params['backblaze_b2_s3_endpoint']));
        }

        if (!empty($params['buffer_uploads']) && !is_string($params['buffer_uploads'])) {
            throw new \Files\Exception\InvalidParameterException('$buffer_uploads must be of type string; received ' . gettype(@$params['buffer_uploads']));
        }

        if (!empty($params['cloudflare_access_key']) && !is_string($params['cloudflare_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_access_key must be of type string; received ' . gettype(@$params['cloudflare_access_key']));
        }

        if (!empty($params['cloudflare_bucket']) && !is_string($params['cloudflare_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_bucket must be of type string; received ' . gettype(@$params['cloudflare_bucket']));
        }

        if (!empty($params['cloudflare_endpoint']) && !is_string($params['cloudflare_endpoint'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_endpoint must be of type string; received ' . gettype(@$params['cloudflare_endpoint']));
        }

        if (!empty($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (!empty($params['filebase_access_key']) && !is_string($params['filebase_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$filebase_access_key must be of type string; received ' . gettype(@$params['filebase_access_key']));
        }

        if (!empty($params['filebase_bucket']) && !is_string($params['filebase_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$filebase_bucket must be of type string; received ' . gettype(@$params['filebase_bucket']));
        }

        if (!empty($params['files_api_key']) && !is_string($params['files_api_key'])) {
            throw new \Files\Exception\InvalidParameterException('$files_api_key must be of type string; received ' . gettype(@$params['files_api_key']));
        }

        if (!empty($params['files_agent_permission_set']) && !is_string($params['files_agent_permission_set'])) {
            throw new \Files\Exception\InvalidParameterException('$files_agent_permission_set must be of type string; received ' . gettype(@$params['files_agent_permission_set']));
        }

        if (!empty($params['files_agent_root']) && !is_string($params['files_agent_root'])) {
            throw new \Files\Exception\InvalidParameterException('$files_agent_root must be of type string; received ' . gettype(@$params['files_agent_root']));
        }

        if (!empty($params['files_agent_version']) && !is_string($params['files_agent_version'])) {
            throw new \Files\Exception\InvalidParameterException('$files_agent_version must be of type string; received ' . gettype(@$params['files_agent_version']));
        }

        if (!empty($params['outbound_agent_id']) && !is_int($params['outbound_agent_id'])) {
            throw new \Files\Exception\InvalidParameterException('$outbound_agent_id must be of type int; received ' . gettype(@$params['outbound_agent_id']));
        }

        if (!empty($params['custom_domain_id']) && !is_int($params['custom_domain_id'])) {
            throw new \Files\Exception\InvalidParameterException('$custom_domain_id must be of type int; received ' . gettype(@$params['custom_domain_id']));
        }

        if (!empty($params['google_cloud_storage_authentication_method']) && !is_string($params['google_cloud_storage_authentication_method'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_authentication_method must be of type string; received ' . gettype(@$params['google_cloud_storage_authentication_method']));
        }

        if (!empty($params['google_cloud_storage_bucket']) && !is_string($params['google_cloud_storage_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_bucket must be of type string; received ' . gettype(@$params['google_cloud_storage_bucket']));
        }

        if (!empty($params['google_cloud_storage_oauth_scope']) && !is_string($params['google_cloud_storage_oauth_scope'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_oauth_scope must be of type string; received ' . gettype(@$params['google_cloud_storage_oauth_scope']));
        }

        if (!empty($params['google_cloud_storage_project_id']) && !is_string($params['google_cloud_storage_project_id'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_project_id must be of type string; received ' . gettype(@$params['google_cloud_storage_project_id']));
        }

        if (!empty($params['google_cloud_storage_s3_compatible_access_key']) && !is_string($params['google_cloud_storage_s3_compatible_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_s3_compatible_access_key must be of type string; received ' . gettype(@$params['google_cloud_storage_s3_compatible_access_key']));
        }

        if (!empty($params['hostname']) && !is_string($params['hostname'])) {
            throw new \Files\Exception\InvalidParameterException('$hostname must be of type string; received ' . gettype(@$params['hostname']));
        }

        if (!empty($params['linode_access_key']) && !is_string($params['linode_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_access_key must be of type string; received ' . gettype(@$params['linode_access_key']));
        }

        if (!empty($params['linode_bucket']) && !is_string($params['linode_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_bucket must be of type string; received ' . gettype(@$params['linode_bucket']));
        }

        if (!empty($params['linode_region']) && !is_string($params['linode_region'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_region must be of type string; received ' . gettype(@$params['linode_region']));
        }

        if (!empty($params['max_connections']) && !is_int($params['max_connections'])) {
            throw new \Files\Exception\InvalidParameterException('$max_connections must be of type int; received ' . gettype(@$params['max_connections']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['one_drive_account_type']) && !is_string($params['one_drive_account_type'])) {
            throw new \Files\Exception\InvalidParameterException('$one_drive_account_type must be of type string; received ' . gettype(@$params['one_drive_account_type']));
        }

        if (!empty($params['port']) && !is_int($params['port'])) {
            throw new \Files\Exception\InvalidParameterException('$port must be of type int; received ' . gettype(@$params['port']));
        }

        if (!empty($params['upload_staging_path']) && !is_string($params['upload_staging_path'])) {
            throw new \Files\Exception\InvalidParameterException('$upload_staging_path must be of type string; received ' . gettype(@$params['upload_staging_path']));
        }

        if (!empty($params['remote_server_credential_id']) && !is_int($params['remote_server_credential_id'])) {
            throw new \Files\Exception\InvalidParameterException('$remote_server_credential_id must be of type int; received ' . gettype(@$params['remote_server_credential_id']));
        }

        if (!empty($params['s3_assume_role_arn']) && !is_string($params['s3_assume_role_arn'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_assume_role_arn must be of type string; received ' . gettype(@$params['s3_assume_role_arn']));
        }

        if (!empty($params['s3_assume_role_duration_seconds']) && !is_int($params['s3_assume_role_duration_seconds'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_assume_role_duration_seconds must be of type int; received ' . gettype(@$params['s3_assume_role_duration_seconds']));
        }

        if (!empty($params['s3_bucket']) && !is_string($params['s3_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_bucket must be of type string; received ' . gettype(@$params['s3_bucket']));
        }

        if (!empty($params['s3_compatible_access_key']) && !is_string($params['s3_compatible_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_access_key must be of type string; received ' . gettype(@$params['s3_compatible_access_key']));
        }

        if (!empty($params['s3_compatible_bucket']) && !is_string($params['s3_compatible_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_bucket must be of type string; received ' . gettype(@$params['s3_compatible_bucket']));
        }

        if (!empty($params['s3_compatible_endpoint']) && !is_string($params['s3_compatible_endpoint'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_endpoint must be of type string; received ' . gettype(@$params['s3_compatible_endpoint']));
        }

        if (!empty($params['s3_compatible_region']) && !is_string($params['s3_compatible_region'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_region must be of type string; received ' . gettype(@$params['s3_compatible_region']));
        }

        if (!empty($params['s3_kms_key_id']) && !is_string($params['s3_kms_key_id'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_kms_key_id must be of type string; received ' . gettype(@$params['s3_kms_key_id']));
        }

        if (!empty($params['s3_region']) && !is_string($params['s3_region'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_region must be of type string; received ' . gettype(@$params['s3_region']));
        }

        if (!empty($params['server_certificate']) && !is_string($params['server_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$server_certificate must be of type string; received ' . gettype(@$params['server_certificate']));
        }

        if (!empty($params['server_host_key']) && !is_string($params['server_host_key'])) {
            throw new \Files\Exception\InvalidParameterException('$server_host_key must be of type string; received ' . gettype(@$params['server_host_key']));
        }

        if (!empty($params['server_type']) && !is_string($params['server_type'])) {
            throw new \Files\Exception\InvalidParameterException('$server_type must be of type string; received ' . gettype(@$params['server_type']));
        }

        if (!empty($params['sharepoint_client_id']) && !is_string($params['sharepoint_client_id'])) {
            throw new \Files\Exception\InvalidParameterException('$sharepoint_client_id must be of type string; received ' . gettype(@$params['sharepoint_client_id']));
        }

        if (!empty($params['sharepoint_site_url']) && !is_string($params['sharepoint_site_url'])) {
            throw new \Files\Exception\InvalidParameterException('$sharepoint_site_url must be of type string; received ' . gettype(@$params['sharepoint_site_url']));
        }

        if (!empty($params['sharepoint_tenant_id']) && !is_string($params['sharepoint_tenant_id'])) {
            throw new \Files\Exception\InvalidParameterException('$sharepoint_tenant_id must be of type string; received ' . gettype(@$params['sharepoint_tenant_id']));
        }

        if (!empty($params['ssl']) && !is_string($params['ssl'])) {
            throw new \Files\Exception\InvalidParameterException('$ssl must be of type string; received ' . gettype(@$params['ssl']));
        }

        if (!empty($params['username']) && !is_string($params['username'])) {
            throw new \Files\Exception\InvalidParameterException('$username must be of type string; received ' . gettype(@$params['username']));
        }

        if (!empty($params['wasabi_access_key']) && !is_string($params['wasabi_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_access_key must be of type string; received ' . gettype(@$params['wasabi_access_key']));
        }

        if (!empty($params['wasabi_bucket']) && !is_string($params['wasabi_bucket'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_bucket must be of type string; received ' . gettype(@$params['wasabi_bucket']));
        }

        if (!empty($params['wasabi_region']) && !is_string($params['wasabi_region'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_region must be of type string; received ' . gettype(@$params['wasabi_region']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/remote_servers', 'POST', $params, $options);

        return new RemoteServer((array) (@$response->data ?: []), $options);
    }
}
