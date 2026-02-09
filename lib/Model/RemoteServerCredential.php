<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class RemoteServerCredential
 *
 * @package Files
 */
class RemoteServerCredential
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
    // int64 # Remote Server Credential ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
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
    // string # Remote server type.  Remote Server Credentials are only valid for a single type of Remote Server.
    public function getServerType()
    {
        return @$this->attributes['server_type'];
    }

    public function setServerType($value)
    {
        return $this->attributes['server_type'] = $value;
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
    // string # AWS IAM Role ARN for AssumeRole authentication.
    public function getS3AssumeRoleArn()
    {
        return @$this->attributes['s3_assume_role_arn'];
    }

    public function setS3AssumeRoleArn($value)
    {
        return $this->attributes['s3_assume_role_arn'] = $value;
    }
    // int64 # Session duration in seconds for AssumeRole authentication (900-43200).
    public function getS3AssumeRoleDurationSeconds()
    {
        return @$this->attributes['s3_assume_role_duration_seconds'];
    }

    public function setS3AssumeRoleDurationSeconds($value)
    {
        return $this->attributes['s3_assume_role_duration_seconds'] = $value;
    }
    // string # External ID for AssumeRole authentication.
    public function getS3AssumeRoleExternalId()
    {
        return @$this->attributes['s3_assume_role_external_id'];
    }

    public function setS3AssumeRoleExternalId($value)
    {
        return $this->attributes['s3_assume_role_external_id'] = $value;
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
    // string # Wasabi: Access Key.
    public function getWasabiAccessKey()
    {
        return @$this->attributes['wasabi_access_key'];
    }

    public function setWasabiAccessKey($value)
    {
        return $this->attributes['wasabi_access_key'] = $value;
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
    // string # Filebase: Access Key.
    public function getFilebaseAccessKey()
    {
        return @$this->attributes['filebase_access_key'];
    }

    public function setFilebaseAccessKey($value)
    {
        return $this->attributes['filebase_access_key'] = $value;
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
    // string # Linode: Access Key
    public function getLinodeAccessKey()
    {
        return @$this->attributes['linode_access_key'];
    }

    public function setLinodeAccessKey($value)
    {
        return $this->attributes['linode_access_key'] = $value;
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

    // Parameters:
    //   name - string - Internal name for your reference
    //   description - string - Internal description for your reference
    //   server_type - string - Remote server type.  Remote Server Credentials are only valid for a single type of Remote Server.
    //   aws_access_key - string - AWS Access Key.
    //   s3_assume_role_arn - string - AWS IAM Role ARN for AssumeRole authentication.
    //   s3_assume_role_duration_seconds - int64 - Session duration in seconds for AssumeRole authentication (900-43200).
    //   cloudflare_access_key - string - Cloudflare: Access Key.
    //   filebase_access_key - string - Filebase: Access Key.
    //   google_cloud_storage_s3_compatible_access_key - string - Google Cloud Storage: S3-compatible Access Key.
    //   linode_access_key - string - Linode: Access Key
    //   s3_compatible_access_key - string - S3-compatible: Access Key
    //   username - string - Remote server username.
    //   wasabi_access_key - string - Wasabi: Access Key.
    //   password - string - Password, if needed.
    //   private_key - string - Private key, if needed.
    //   private_key_passphrase - string - Passphrase for private key if needed.
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

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['description'] && !is_string(@$params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (@$params['server_type'] && !is_string(@$params['server_type'])) {
            throw new \Files\Exception\InvalidParameterException('$server_type must be of type string; received ' . gettype(@$params['server_type']));
        }

        if (@$params['aws_access_key'] && !is_string(@$params['aws_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$aws_access_key must be of type string; received ' . gettype(@$params['aws_access_key']));
        }

        if (@$params['s3_assume_role_arn'] && !is_string(@$params['s3_assume_role_arn'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_assume_role_arn must be of type string; received ' . gettype(@$params['s3_assume_role_arn']));
        }

        if (@$params['s3_assume_role_duration_seconds'] && !is_int(@$params['s3_assume_role_duration_seconds'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_assume_role_duration_seconds must be of type int; received ' . gettype(@$params['s3_assume_role_duration_seconds']));
        }

        if (@$params['cloudflare_access_key'] && !is_string(@$params['cloudflare_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_access_key must be of type string; received ' . gettype(@$params['cloudflare_access_key']));
        }

        if (@$params['filebase_access_key'] && !is_string(@$params['filebase_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$filebase_access_key must be of type string; received ' . gettype(@$params['filebase_access_key']));
        }

        if (@$params['google_cloud_storage_s3_compatible_access_key'] && !is_string(@$params['google_cloud_storage_s3_compatible_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_s3_compatible_access_key must be of type string; received ' . gettype(@$params['google_cloud_storage_s3_compatible_access_key']));
        }

        if (@$params['linode_access_key'] && !is_string(@$params['linode_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_access_key must be of type string; received ' . gettype(@$params['linode_access_key']));
        }

        if (@$params['s3_compatible_access_key'] && !is_string(@$params['s3_compatible_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_access_key must be of type string; received ' . gettype(@$params['s3_compatible_access_key']));
        }

        if (@$params['username'] && !is_string(@$params['username'])) {
            throw new \Files\Exception\InvalidParameterException('$username must be of type string; received ' . gettype(@$params['username']));
        }

        if (@$params['wasabi_access_key'] && !is_string(@$params['wasabi_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_access_key must be of type string; received ' . gettype(@$params['wasabi_access_key']));
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

        $response = Api::sendRequest('/remote_server_credentials/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new RemoteServerCredential((array) (@$response->data ?: []), $this->options);
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

        $response = Api::sendRequest('/remote_server_credentials/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id` and `id`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `workspace_id` and `name`. Valid field combinations are `[ workspace_id, name ]`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `name`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/remote_server_credentials', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new RemoteServerCredential((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Remote Server Credential ID.
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

        $response = Api::sendRequest('/remote_server_credentials/' . @$params['id'] . '', 'GET', $params, $options);

        return new RemoteServerCredential((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   name - string - Internal name for your reference
    //   description - string - Internal description for your reference
    //   server_type - string - Remote server type.  Remote Server Credentials are only valid for a single type of Remote Server.
    //   aws_access_key - string - AWS Access Key.
    //   s3_assume_role_arn - string - AWS IAM Role ARN for AssumeRole authentication.
    //   s3_assume_role_duration_seconds - int64 - Session duration in seconds for AssumeRole authentication (900-43200).
    //   cloudflare_access_key - string - Cloudflare: Access Key.
    //   filebase_access_key - string - Filebase: Access Key.
    //   google_cloud_storage_s3_compatible_access_key - string - Google Cloud Storage: S3-compatible Access Key.
    //   linode_access_key - string - Linode: Access Key
    //   s3_compatible_access_key - string - S3-compatible: Access Key
    //   username - string - Remote server username.
    //   wasabi_access_key - string - Wasabi: Access Key.
    //   password - string - Password, if needed.
    //   private_key - string - Private key, if needed.
    //   private_key_passphrase - string - Passphrase for private key if needed.
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
    //   workspace_id - int64 - Workspace ID (0 for default workspace)
    public static function create($params = [], $options = [])
    {
        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['description'] && !is_string(@$params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (@$params['server_type'] && !is_string(@$params['server_type'])) {
            throw new \Files\Exception\InvalidParameterException('$server_type must be of type string; received ' . gettype(@$params['server_type']));
        }

        if (@$params['aws_access_key'] && !is_string(@$params['aws_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$aws_access_key must be of type string; received ' . gettype(@$params['aws_access_key']));
        }

        if (@$params['s3_assume_role_arn'] && !is_string(@$params['s3_assume_role_arn'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_assume_role_arn must be of type string; received ' . gettype(@$params['s3_assume_role_arn']));
        }

        if (@$params['s3_assume_role_duration_seconds'] && !is_int(@$params['s3_assume_role_duration_seconds'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_assume_role_duration_seconds must be of type int; received ' . gettype(@$params['s3_assume_role_duration_seconds']));
        }

        if (@$params['cloudflare_access_key'] && !is_string(@$params['cloudflare_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$cloudflare_access_key must be of type string; received ' . gettype(@$params['cloudflare_access_key']));
        }

        if (@$params['filebase_access_key'] && !is_string(@$params['filebase_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$filebase_access_key must be of type string; received ' . gettype(@$params['filebase_access_key']));
        }

        if (@$params['google_cloud_storage_s3_compatible_access_key'] && !is_string(@$params['google_cloud_storage_s3_compatible_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$google_cloud_storage_s3_compatible_access_key must be of type string; received ' . gettype(@$params['google_cloud_storage_s3_compatible_access_key']));
        }

        if (@$params['linode_access_key'] && !is_string(@$params['linode_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$linode_access_key must be of type string; received ' . gettype(@$params['linode_access_key']));
        }

        if (@$params['s3_compatible_access_key'] && !is_string(@$params['s3_compatible_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_access_key must be of type string; received ' . gettype(@$params['s3_compatible_access_key']));
        }

        if (@$params['username'] && !is_string(@$params['username'])) {
            throw new \Files\Exception\InvalidParameterException('$username must be of type string; received ' . gettype(@$params['username']));
        }

        if (@$params['wasabi_access_key'] && !is_string(@$params['wasabi_access_key'])) {
            throw new \Files\Exception\InvalidParameterException('$wasabi_access_key must be of type string; received ' . gettype(@$params['wasabi_access_key']));
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

        if (@$params['workspace_id'] && !is_int(@$params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/remote_server_credentials', 'POST', $params, $options);

        return new RemoteServerCredential((array) (@$response->data ?: []), $options);
    }
}
