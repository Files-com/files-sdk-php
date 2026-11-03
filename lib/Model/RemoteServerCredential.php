<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A RemoteServerCredential is a way to store a credential for Remote Servers in a centralized vault and then reference it from Remote Server definitions.
 *
 * @property int|null $id Remote Server Credential ID
 * @property int|null $workspace_id Workspace ID (0 for default workspace)
 * @property string|null $name Internal name for your reference
 * @property string|null $description Internal description for your reference
 * @property string|null $server_type Remote server type. Remote Server Credentials are only valid for a single type of Remote Server.
 * @property string|null $aws_access_key AWS Access Key.
 * @property string|null $s3_assume_role_arn AWS IAM Role ARN for AssumeRole authentication.
 * @property int|null $s3_assume_role_duration_seconds Session duration in seconds for AssumeRole authentication (900-43200).
 * @property string|null $s3_assume_role_external_id External ID for AssumeRole authentication.
 * @property string|null $google_cloud_storage_s3_compatible_access_key Google Cloud Storage: S3-compatible Access Key.
 * @property string|null $wasabi_access_key Wasabi: Access Key.
 * @property string|null $s3_compatible_access_key S3-compatible: Access Key
 * @property string|null $filebase_access_key Filebase: Access Key.
 * @property string|null $cloudflare_access_key Cloudflare: Access Key.
 * @property string|null $linode_access_key Linode: Access Key
 * @property string|null $sharepoint_tenant_id SharePoint: Microsoft Entra tenant ID for app-only authentication.
 * @property string|null $sharepoint_client_id SharePoint: Microsoft Entra application client ID for app-only authentication.
 * @property string|null $sharepoint_app_credential_type SharePoint: App-only credential type. Either secret or certificate.
 * @property string|null $username Remote server username.
 * @property string|null $password Password, if needed.
 * @property string|null $private_key Private key, if needed.
 * @property string|null $private_key_passphrase Passphrase for private key if needed.
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
 * @property string|null $sharepoint_client_certificate SharePoint: PEM-encoded certificate and unencrypted private key for app-only authentication.
 * @property string|null $sharepoint_client_secret SharePoint: Microsoft Entra application client secret for app-only authentication.
 * @property string|null $wasabi_secret_key Wasabi: Secret Key
 * @property int|null $copy_values_from_credential_id ID of Remote Server Credential to copy omitted values from.
 * @package Files
 */
class RemoteServerCredential
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
     * Remote Server Credential ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Remote Server Credential ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
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
     * Remote server type.  Remote Server Credentials are only valid for a single type of Remote Server.
     *
     * @return string|null
     */
    public function getServerType()
    {
        return isset($this->attributes['server_type']) ? $this->attributes['server_type'] : null;
    }

    /**
     * Remote server type.  Remote Server Credentials are only valid for a single type of Remote Server.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setServerType($value)
    {
        return $this->attributes['server_type'] = $value;
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
     * ID of Remote Server Credential to copy omitted values from.
     *
     * @return int|null
     */
    public function getCopyValuesFromCredentialId()
    {
        return isset($this->attributes['copy_values_from_credential_id']) ? $this->attributes['copy_values_from_credential_id'] : null;
    }

    /**
     * ID of Remote Server Credential to copy omitted values from.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setCopyValuesFromCredentialId($value)
    {
        return $this->attributes['copy_values_from_credential_id'] = $value;
    }

    // Saves the changes, then refreshes this object with the saved attributes, which it also returns as a separate
    // RemoteServerCredential with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update Remote Server Credential
     *
     * PATCH /remote_server_credentials/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Remote Server Credential ID.
     *     - `name` (string, optional): Internal name for your reference
     *     - `description` (string, optional): Internal description for your reference
     *     - `server_type` (string, optional): Remote server type.  Remote Server Credentials are only valid for a single type of Remote Server. Allowed values: ftp, sftp, s3, google_cloud_storage, webdav, wasabi, backblaze_b2, one_drive, box, dropbox, google_drive, azure, sharepoint, s3_compatible, azure_files, files_agent, filebase, cloudflare, linode, files_com.
     *     - `aws_access_key` (string, optional): AWS Access Key.
     *     - `s3_assume_role_arn` (string, optional): AWS IAM Role ARN for AssumeRole authentication.
     *     - `s3_assume_role_duration_seconds` (int, optional): Session duration in seconds for AssumeRole authentication (900-43200).
     *     - `cloudflare_access_key` (string, optional): Cloudflare: Access Key.
     *     - `filebase_access_key` (string, optional): Filebase: Access Key.
     *     - `google_cloud_storage_s3_compatible_access_key` (string, optional): Google Cloud Storage: S3-compatible Access Key.
     *     - `linode_access_key` (string, optional): Linode: Access Key
     *     - `s3_compatible_access_key` (string, optional): S3-compatible: Access Key
     *     - `sharepoint_client_id` (string, optional): SharePoint: Microsoft Entra application client ID for app-only authentication.
     *     - `sharepoint_tenant_id` (string, optional): SharePoint: Microsoft Entra tenant ID for app-only authentication.
     *     - `username` (string, optional): Remote server username.
     *     - `wasabi_access_key` (string, optional): Wasabi: Access Key.
     *     - `password` (string, optional): Password, if needed.
     *     - `private_key` (string, optional): Private key, if needed.
     *     - `private_key_passphrase` (string, optional): Passphrase for private key if needed.
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
     *     - `sharepoint_client_certificate` (string, optional): SharePoint: PEM-encoded certificate and unencrypted private key for app-only authentication.
     *     - `sharepoint_client_secret` (string, optional): SharePoint: Microsoft Entra application client secret for app-only authentication.
     *     - `wasabi_secret_key` (string, optional): Wasabi: Secret Key
     * @return \Files\Model\RemoteServerCredential
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

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (isset($params['server_type']) && !is_string($params['server_type'])) {
            throw new \Files\Exception\InvalidParameterException('$server_type must be of type string; received ' . gettype(@$params['server_type']));
        }

        if (isset($params['aws_access_key']) && !is_string($params['aws_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$aws_access_key must be of type string; received ' . gettype(@$params['aws_access_key']));
        }

        if (isset($params['s3_assume_role_arn']) && !is_string($params['s3_assume_role_arn'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_assume_role_arn must be of type string; received ' . gettype(@$params['s3_assume_role_arn']));
        }

        if (isset($params['s3_assume_role_duration_seconds']) && !is_int($params['s3_assume_role_duration_seconds'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_assume_role_duration_seconds must be of type int; received ' . gettype(@$params['s3_assume_role_duration_seconds']));
        }

        if (isset($params['cloudflare_access_key']) && !is_string($params['cloudflare_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_access_key must be of type string; received ' . gettype(@$params['cloudflare_access_key']));
        }

        if (isset($params['filebase_access_key']) && !is_string($params['filebase_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$filebase_access_key must be of type string; received ' . gettype(@$params['filebase_access_key']));
        }

        if (isset($params['google_cloud_storage_s3_compatible_access_key']) && !is_string($params['google_cloud_storage_s3_compatible_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_s3_compatible_access_key must be of type string; received ' . gettype(@$params['google_cloud_storage_s3_compatible_access_key']));
        }

        if (isset($params['linode_access_key']) && !is_string($params['linode_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_access_key must be of type string; received ' . gettype(@$params['linode_access_key']));
        }

        if (isset($params['s3_compatible_access_key']) && !is_string($params['s3_compatible_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_access_key must be of type string; received ' . gettype(@$params['s3_compatible_access_key']));
        }

        if (isset($params['sharepoint_client_id']) && !is_string($params['sharepoint_client_id'])) {
            throw new \Files\Exception\InvalidParameterException('$sharepoint_client_id must be of type string; received ' . gettype(@$params['sharepoint_client_id']));
        }

        if (isset($params['sharepoint_tenant_id']) && !is_string($params['sharepoint_tenant_id'])) {
            throw new \Files\Exception\InvalidParameterException('$sharepoint_tenant_id must be of type string; received ' . gettype(@$params['sharepoint_tenant_id']));
        }

        if (isset($params['username']) && !is_string($params['username'])) {
            throw new \Files\Exception\InvalidParameterException('$username must be of type string; received ' . gettype(@$params['username']));
        }

        if (isset($params['wasabi_access_key']) && !is_string($params['wasabi_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_access_key must be of type string; received ' . gettype(@$params['wasabi_access_key']));
        }

        if (isset($params['password']) && !is_string($params['password'])) {
            throw new \Files\Exception\InvalidParameterException('$password must be of type string; received ' . gettype(@$params['password']));
        }

        if (isset($params['private_key']) && !is_string($params['private_key'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key must be of type string; received ' . gettype(@$params['private_key']));
        }

        if (isset($params['private_key_passphrase']) && !is_string($params['private_key_passphrase'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key_passphrase must be of type string; received ' . gettype(@$params['private_key_passphrase']));
        }

        if (isset($params['aws_secret_key']) && !is_string($params['aws_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$aws_secret_key must be of type string; received ' . gettype(@$params['aws_secret_key']));
        }

        if (isset($params['azure_blob_storage_access_key']) && !is_string($params['azure_blob_storage_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_access_key must be of type string; received ' . gettype(@$params['azure_blob_storage_access_key']));
        }

        if (isset($params['azure_blob_storage_sas_token']) && !is_string($params['azure_blob_storage_sas_token'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_sas_token must be of type string; received ' . gettype(@$params['azure_blob_storage_sas_token']));
        }

        if (isset($params['azure_files_storage_access_key']) && !is_string($params['azure_files_storage_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_access_key must be of type string; received ' . gettype(@$params['azure_files_storage_access_key']));
        }

        if (isset($params['azure_files_storage_sas_token']) && !is_string($params['azure_files_storage_sas_token'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_sas_token must be of type string; received ' . gettype(@$params['azure_files_storage_sas_token']));
        }

        if (isset($params['backblaze_b2_application_key']) && !is_string($params['backblaze_b2_application_key'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_application_key must be of type string; received ' . gettype(@$params['backblaze_b2_application_key']));
        }

        if (isset($params['backblaze_b2_key_id']) && !is_string($params['backblaze_b2_key_id'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_key_id must be of type string; received ' . gettype(@$params['backblaze_b2_key_id']));
        }

        if (isset($params['cloudflare_secret_key']) && !is_string($params['cloudflare_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_secret_key must be of type string; received ' . gettype(@$params['cloudflare_secret_key']));
        }

        if (isset($params['filebase_secret_key']) && !is_string($params['filebase_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$filebase_secret_key must be of type string; received ' . gettype(@$params['filebase_secret_key']));
        }

        if (isset($params['google_cloud_storage_credentials_json']) && !is_string($params['google_cloud_storage_credentials_json'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_credentials_json must be of type string; received ' . gettype(@$params['google_cloud_storage_credentials_json']));
        }

        if (isset($params['google_cloud_storage_s3_compatible_secret_key']) && !is_string($params['google_cloud_storage_s3_compatible_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_s3_compatible_secret_key must be of type string; received ' . gettype(@$params['google_cloud_storage_s3_compatible_secret_key']));
        }

        if (isset($params['linode_secret_key']) && !is_string($params['linode_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_secret_key must be of type string; received ' . gettype(@$params['linode_secret_key']));
        }

        if (isset($params['s3_compatible_secret_key']) && !is_string($params['s3_compatible_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_secret_key must be of type string; received ' . gettype(@$params['s3_compatible_secret_key']));
        }

        if (isset($params['sharepoint_client_certificate']) && !is_string($params['sharepoint_client_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$sharepoint_client_certificate must be of type string; received ' . gettype(@$params['sharepoint_client_certificate']));
        }

        if (isset($params['sharepoint_client_secret']) && !is_string($params['sharepoint_client_secret'])) {
            throw new \Files\Exception\InvalidParameterException('$sharepoint_client_secret must be of type string; received ' . gettype(@$params['sharepoint_client_secret']));
        }

        if (isset($params['wasabi_secret_key']) && !is_string($params['wasabi_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_secret_key must be of type string; received ' . gettype(@$params['wasabi_secret_key']));
        }

        $response = Api::sendRequest('/remote_server_credentials/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        $updated = new RemoteServerCredential(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete Remote Server Credential
     *
     * DELETE /remote_server_credentials/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Remote Server Credential ID.
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

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/remote_server_credentials/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Remote Server Credential
     *
     * DELETE /remote_server_credentials/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Remote Server Credential ID.
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
     * List Remote Server Credentials
     *
     * GET /remote_server_credentials
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id` and `name`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `workspace_id` and `name`. Valid field combinations are `[ workspace_id, name ]`.
     *     - `filter_prefix` (array|object, optional): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `name`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\RemoteServerCredential[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/remote_server_credentials', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new RemoteServerCredential(Api::modelAttributes($obj, $response->status), $options);
        }

        return $return_array;
    }

    /**
     * Fetches one page of all()'s results, with the same parameters and options,
     * whatever Files::$autoPaginate is. Iterate the page, step with nextPage(), or read every result lazily with
     * autoPagingIterator(). A 'cursor' parameter starts from a page saved with getNextCursor().
     *
     * @return \Files\Page
     */
    public static function listPage($params = [], $options = [])
    {
        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        return \Files\Page::fetch('/remote_server_credentials', $params, $options, RemoteServerCredential::class);
    }

    /**
     * Show Remote Server Credential
     *
     * GET /remote_server_credentials/{id}
     *
     * @param int $id Remote Server Credential ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\RemoteServerCredential
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

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/remote_server_credentials/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new RemoteServerCredential(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Remote Server Credential
     *
     * GET /remote_server_credentials/{id}
     *
     * Alias of find().
     *
     * @param int $id Remote Server Credential ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\RemoteServerCredential
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Remote Server Credential
     *
     * POST /remote_server_credentials
     *
     * @param array $params Request parameters.
     *     - `name` (string, optional): Internal name for your reference
     *     - `description` (string, optional): Internal description for your reference
     *     - `server_type` (string, optional): Remote server type.  Remote Server Credentials are only valid for a single type of Remote Server. Allowed values: ftp, sftp, s3, google_cloud_storage, webdav, wasabi, backblaze_b2, one_drive, box, dropbox, google_drive, azure, sharepoint, s3_compatible, azure_files, files_agent, filebase, cloudflare, linode, files_com.
     *     - `aws_access_key` (string, optional): AWS Access Key.
     *     - `s3_assume_role_arn` (string, optional): AWS IAM Role ARN for AssumeRole authentication.
     *     - `s3_assume_role_duration_seconds` (int, optional): Session duration in seconds for AssumeRole authentication (900-43200).
     *     - `cloudflare_access_key` (string, optional): Cloudflare: Access Key.
     *     - `filebase_access_key` (string, optional): Filebase: Access Key.
     *     - `google_cloud_storage_s3_compatible_access_key` (string, optional): Google Cloud Storage: S3-compatible Access Key.
     *     - `linode_access_key` (string, optional): Linode: Access Key
     *     - `s3_compatible_access_key` (string, optional): S3-compatible: Access Key
     *     - `sharepoint_client_id` (string, optional): SharePoint: Microsoft Entra application client ID for app-only authentication.
     *     - `sharepoint_tenant_id` (string, optional): SharePoint: Microsoft Entra tenant ID for app-only authentication.
     *     - `username` (string, optional): Remote server username.
     *     - `wasabi_access_key` (string, optional): Wasabi: Access Key.
     *     - `password` (string, optional): Password, if needed.
     *     - `private_key` (string, optional): Private key, if needed.
     *     - `private_key_passphrase` (string, optional): Passphrase for private key if needed.
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
     *     - `sharepoint_client_certificate` (string, optional): SharePoint: PEM-encoded certificate and unencrypted private key for app-only authentication.
     *     - `sharepoint_client_secret` (string, optional): SharePoint: Microsoft Entra application client secret for app-only authentication.
     *     - `wasabi_secret_key` (string, optional): Wasabi: Secret Key
     *     - `workspace_id` (int, optional): Workspace ID (0 for default workspace)
     *     - `copy_values_from_credential_id` (int, optional): ID of Remote Server Credential to copy omitted values from.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\RemoteServerCredential
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (isset($params['server_type']) && !is_string($params['server_type'])) {
            throw new \Files\Exception\InvalidParameterException('$server_type must be of type string; received ' . gettype(@$params['server_type']));
        }

        if (isset($params['aws_access_key']) && !is_string($params['aws_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$aws_access_key must be of type string; received ' . gettype(@$params['aws_access_key']));
        }

        if (isset($params['s3_assume_role_arn']) && !is_string($params['s3_assume_role_arn'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_assume_role_arn must be of type string; received ' . gettype(@$params['s3_assume_role_arn']));
        }

        if (isset($params['s3_assume_role_duration_seconds']) && !is_int($params['s3_assume_role_duration_seconds'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_assume_role_duration_seconds must be of type int; received ' . gettype(@$params['s3_assume_role_duration_seconds']));
        }

        if (isset($params['cloudflare_access_key']) && !is_string($params['cloudflare_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_access_key must be of type string; received ' . gettype(@$params['cloudflare_access_key']));
        }

        if (isset($params['filebase_access_key']) && !is_string($params['filebase_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$filebase_access_key must be of type string; received ' . gettype(@$params['filebase_access_key']));
        }

        if (isset($params['google_cloud_storage_s3_compatible_access_key']) && !is_string($params['google_cloud_storage_s3_compatible_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_s3_compatible_access_key must be of type string; received ' . gettype(@$params['google_cloud_storage_s3_compatible_access_key']));
        }

        if (isset($params['linode_access_key']) && !is_string($params['linode_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_access_key must be of type string; received ' . gettype(@$params['linode_access_key']));
        }

        if (isset($params['s3_compatible_access_key']) && !is_string($params['s3_compatible_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_access_key must be of type string; received ' . gettype(@$params['s3_compatible_access_key']));
        }

        if (isset($params['sharepoint_client_id']) && !is_string($params['sharepoint_client_id'])) {
            throw new \Files\Exception\InvalidParameterException('$sharepoint_client_id must be of type string; received ' . gettype(@$params['sharepoint_client_id']));
        }

        if (isset($params['sharepoint_tenant_id']) && !is_string($params['sharepoint_tenant_id'])) {
            throw new \Files\Exception\InvalidParameterException('$sharepoint_tenant_id must be of type string; received ' . gettype(@$params['sharepoint_tenant_id']));
        }

        if (isset($params['username']) && !is_string($params['username'])) {
            throw new \Files\Exception\InvalidParameterException('$username must be of type string; received ' . gettype(@$params['username']));
        }

        if (isset($params['wasabi_access_key']) && !is_string($params['wasabi_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_access_key must be of type string; received ' . gettype(@$params['wasabi_access_key']));
        }

        if (isset($params['password']) && !is_string($params['password'])) {
            throw new \Files\Exception\InvalidParameterException('$password must be of type string; received ' . gettype(@$params['password']));
        }

        if (isset($params['private_key']) && !is_string($params['private_key'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key must be of type string; received ' . gettype(@$params['private_key']));
        }

        if (isset($params['private_key_passphrase']) && !is_string($params['private_key_passphrase'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key_passphrase must be of type string; received ' . gettype(@$params['private_key_passphrase']));
        }

        if (isset($params['aws_secret_key']) && !is_string($params['aws_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$aws_secret_key must be of type string; received ' . gettype(@$params['aws_secret_key']));
        }

        if (isset($params['azure_blob_storage_access_key']) && !is_string($params['azure_blob_storage_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_access_key must be of type string; received ' . gettype(@$params['azure_blob_storage_access_key']));
        }

        if (isset($params['azure_blob_storage_sas_token']) && !is_string($params['azure_blob_storage_sas_token'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_blob_storage_sas_token must be of type string; received ' . gettype(@$params['azure_blob_storage_sas_token']));
        }

        if (isset($params['azure_files_storage_access_key']) && !is_string($params['azure_files_storage_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_access_key must be of type string; received ' . gettype(@$params['azure_files_storage_access_key']));
        }

        if (isset($params['azure_files_storage_sas_token']) && !is_string($params['azure_files_storage_sas_token'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_files_storage_sas_token must be of type string; received ' . gettype(@$params['azure_files_storage_sas_token']));
        }

        if (isset($params['backblaze_b2_application_key']) && !is_string($params['backblaze_b2_application_key'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_application_key must be of type string; received ' . gettype(@$params['backblaze_b2_application_key']));
        }

        if (isset($params['backblaze_b2_key_id']) && !is_string($params['backblaze_b2_key_id'])) {
            throw new \Files\Exception\InvalidParameterException('$backblaze_b2_key_id must be of type string; received ' . gettype(@$params['backblaze_b2_key_id']));
        }

        if (isset($params['cloudflare_secret_key']) && !is_string($params['cloudflare_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_secret_key must be of type string; received ' . gettype(@$params['cloudflare_secret_key']));
        }

        if (isset($params['filebase_secret_key']) && !is_string($params['filebase_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$filebase_secret_key must be of type string; received ' . gettype(@$params['filebase_secret_key']));
        }

        if (isset($params['google_cloud_storage_credentials_json']) && !is_string($params['google_cloud_storage_credentials_json'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_credentials_json must be of type string; received ' . gettype(@$params['google_cloud_storage_credentials_json']));
        }

        if (isset($params['google_cloud_storage_s3_compatible_secret_key']) && !is_string($params['google_cloud_storage_s3_compatible_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_s3_compatible_secret_key must be of type string; received ' . gettype(@$params['google_cloud_storage_s3_compatible_secret_key']));
        }

        if (isset($params['linode_secret_key']) && !is_string($params['linode_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_secret_key must be of type string; received ' . gettype(@$params['linode_secret_key']));
        }

        if (isset($params['s3_compatible_secret_key']) && !is_string($params['s3_compatible_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_secret_key must be of type string; received ' . gettype(@$params['s3_compatible_secret_key']));
        }

        if (isset($params['sharepoint_client_certificate']) && !is_string($params['sharepoint_client_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$sharepoint_client_certificate must be of type string; received ' . gettype(@$params['sharepoint_client_certificate']));
        }

        if (isset($params['sharepoint_client_secret']) && !is_string($params['sharepoint_client_secret'])) {
            throw new \Files\Exception\InvalidParameterException('$sharepoint_client_secret must be of type string; received ' . gettype(@$params['sharepoint_client_secret']));
        }

        if (isset($params['wasabi_secret_key']) && !is_string($params['wasabi_secret_key'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_secret_key must be of type string; received ' . gettype(@$params['wasabi_secret_key']));
        }

        if (isset($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (isset($params['copy_values_from_credential_id']) && !is_int($params['copy_values_from_credential_id'])) {
            throw new \Files\Exception\InvalidParameterException('$copy_values_from_credential_id must be of type int; received ' . gettype(@$params['copy_values_from_credential_id']));
        }

        $response = Api::sendRequest('/remote_server_credentials', 'POST', $params, $options);

        return new RemoteServerCredential(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
