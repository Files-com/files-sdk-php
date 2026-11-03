<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A SIEM HTTP Destination defines where Files.com sends the log types you select.
 *
 * @property int|null $id SIEM HTTP Destination ID
 * @property string|null $name Name for this Destination
 * @property string|null $destination_type Destination Type
 * @property string|null $destination_url Destination Url
 * @property string|null $file_destination_path Applicable only for destination type: file. Destination folder path on Files.com.
 * @property string|null $file_format Applicable only for destination type: file. Generated file format.
 * @property int|null $file_interval_minutes Applicable only for destination type: file. Interval, in minutes, between file deliveries.
 * @property object|null $additional_headers Additional HTTP Headers included in calls to the destination URL
 * @property bool|null $sending_active Whether this SIEM HTTP Destination is currently being sent to or not
 * @property string|null $generic_payload_type Applicable only for destination type: generic. Indicates the type of HTTP body. Can be json_newline or json_array. json_newline is multiple log entries as JSON separated by newlines. json_array is a single JSON array containing multiple log entries as JSON.
 * @property string|null $splunk_token_masked Applicable only for destination types: splunk, splunk_compatible. Authentication token for the destination.
 * @property string|null $crowdstrike_token_masked Applicable only for destination type: crowdstrike. Authentication token provided by Crowdstrike.
 * @property string|null $azure_dcr_immutable_id Applicable only for destination types: azure, azure_legacy. Immutable ID of the Data Collection Rule.
 * @property string|null $azure_stream_name Applicable only for destination type: azure. Name of the stream in the DCR that represents the destination table.
 * @property string|null $azure_oauth_client_credentials_tenant_id Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Tenant ID.
 * @property string|null $azure_oauth_client_credentials_client_id Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Client ID.
 * @property string|null $azure_oauth_client_credentials_client_secret_masked Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Client Secret.
 * @property string|null $qradar_username Applicable only for destination type: qradar. Basic auth username provided by QRadar.
 * @property string|null $qradar_password_masked Applicable only for destination type: qradar. Basic auth password provided by QRadar.
 * @property string|null $solar_winds_token_masked Applicable only for destination type: solar_winds. Authentication token provided by Solar Winds.
 * @property string|null $new_relic_api_key_masked Applicable only for destination type: new_relic. API key provided by New Relic.
 * @property string|null $datadog_api_key_masked Applicable only for destination type: datadog. API key provided by Datadog.
 * @property bool|null $action_send_enabled Whether or not sending is enabled for action logs.
 * @property int|null $action_entries_sent Number of log entries sent for the lifetime of this destination.
 * @property bool|null $sftp_action_send_enabled Whether or not sending is enabled for sftp_action logs.
 * @property int|null $sftp_action_entries_sent Number of log entries sent for the lifetime of this destination.
 * @property bool|null $ftp_action_send_enabled Whether or not sending is enabled for ftp_action logs.
 * @property int|null $ftp_action_entries_sent Number of log entries sent for the lifetime of this destination.
 * @property bool|null $web_dav_action_send_enabled Whether or not sending is enabled for web_dav_action logs.
 * @property int|null $web_dav_action_entries_sent Number of log entries sent for the lifetime of this destination.
 * @property bool|null $sync_send_enabled Whether or not sending is enabled for sync logs.
 * @property int|null $sync_entries_sent Number of log entries sent for the lifetime of this destination.
 * @property bool|null $outbound_connection_send_enabled Whether or not sending is enabled for outbound_connection logs.
 * @property int|null $outbound_connection_entries_sent Number of log entries sent for the lifetime of this destination.
 * @property bool|null $automation_send_enabled Whether or not sending is enabled for automation logs.
 * @property int|null $automation_entries_sent Number of log entries sent for the lifetime of this destination.
 * @property bool|null $api_request_send_enabled Whether or not sending is enabled for api_request logs.
 * @property int|null $api_request_entries_sent Number of log entries sent for the lifetime of this destination.
 * @property bool|null $public_hosting_request_send_enabled Whether or not sending is enabled for public_hosting_request logs.
 * @property int|null $public_hosting_request_entries_sent Number of log entries sent for the lifetime of this destination.
 * @property bool|null $email_send_enabled Whether or not sending is enabled for email logs.
 * @property int|null $email_entries_sent Number of log entries sent for the lifetime of this destination.
 * @property bool|null $exavault_api_request_send_enabled Whether or not sending is enabled for exavault_api_request logs.
 * @property int|null $exavault_api_request_entries_sent Number of log entries sent for the lifetime of this destination.
 * @property bool|null $settings_change_send_enabled Whether or not sending is enabled for settings_change logs.
 * @property int|null $settings_change_entries_sent Number of log entries sent for the lifetime of this destination.
 * @property string|null $last_http_call_target_type Type of URL that was last called. Can be `destination_url` or `azure_oauth_client_credentials_url`
 * @property bool|null $last_http_call_success Was the last HTTP call made successful?
 * @property int|null $last_http_call_response_code Last HTTP Call Response Code
 * @property string|null $last_http_call_response_body Last HTTP Call Response Body. Large responses are truncated.
 * @property string|null $last_http_call_error_message Last HTTP Call Error Message if applicable
 * @property string|null $last_http_call_time Time of Last HTTP Call
 * @property int|null $last_http_call_duration_ms Duration of the last HTTP Call in milliseconds
 * @property string|null $most_recent_http_call_success_time Time of Most Recent Successful HTTP Call
 * @property string|null $connection_test_entry Connection Test Entry
 * @property string|null $splunk_token Applicable only for destination types: splunk, splunk_compatible. Authentication token for the destination.
 * @property string|null $crowdstrike_token Applicable only for destination type: crowdstrike. Authentication token provided by Crowdstrike.
 * @property string|null $azure_oauth_client_credentials_client_secret Applicable only for destination type: azure. Client Credentials OAuth Client Secret.
 * @property string|null $qradar_password Applicable only for destination type: qradar. Basic auth password provided by QRadar.
 * @property string|null $solar_winds_token Applicable only for destination type: solar_winds. Authentication token provided by Solar Winds.
 * @property string|null $new_relic_api_key Applicable only for destination type: new_relic. API key provided by New Relic.
 * @property string|null $datadog_api_key Applicable only for destination type: datadog. API key provided by Datadog.
 * @package Files
 */
class SiemHttpDestination
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
     * SIEM HTTP Destination ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * SIEM HTTP Destination ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Name for this Destination
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Name for this Destination
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Destination Type
     *
     * @return string|null
     */
    public function getDestinationType()
    {
        return isset($this->attributes['destination_type']) ? $this->attributes['destination_type'] : null;
    }

    /**
     * Destination Type
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDestinationType($value)
    {
        return $this->attributes['destination_type'] = $value;
    }
    /**
     * Destination Url
     *
     * @return string|null
     */
    public function getDestinationUrl()
    {
        return isset($this->attributes['destination_url']) ? $this->attributes['destination_url'] : null;
    }

    /**
     * Destination Url
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDestinationUrl($value)
    {
        return $this->attributes['destination_url'] = $value;
    }
    /**
     * Applicable only for destination type: file. Destination folder path on Files.com.
     *
     * @return string|null
     */
    public function getFileDestinationPath()
    {
        return isset($this->attributes['file_destination_path']) ? $this->attributes['file_destination_path'] : null;
    }

    /**
     * Applicable only for destination type: file. Destination folder path on Files.com.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFileDestinationPath($value)
    {
        return $this->attributes['file_destination_path'] = $value;
    }
    /**
     * Applicable only for destination type: file. Generated file format.
     *
     * @return string|null
     */
    public function getFileFormat()
    {
        return isset($this->attributes['file_format']) ? $this->attributes['file_format'] : null;
    }

    /**
     * Applicable only for destination type: file. Generated file format.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFileFormat($value)
    {
        return $this->attributes['file_format'] = $value;
    }
    /**
     * Applicable only for destination type: file. Interval, in minutes, between file deliveries.
     *
     * @return int|null
     */
    public function getFileIntervalMinutes()
    {
        return isset($this->attributes['file_interval_minutes']) ? $this->attributes['file_interval_minutes'] : null;
    }

    /**
     * Applicable only for destination type: file. Interval, in minutes, between file deliveries.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setFileIntervalMinutes($value)
    {
        return $this->attributes['file_interval_minutes'] = $value;
    }
    /**
     * Additional HTTP Headers included in calls to the destination URL
     *
     * @return object|null
     */
    public function getAdditionalHeaders()
    {
        return isset($this->attributes['additional_headers']) ? $this->attributes['additional_headers'] : null;
    }

    /**
     * Additional HTTP Headers included in calls to the destination URL
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setAdditionalHeaders($value)
    {
        return $this->attributes['additional_headers'] = $value;
    }
    /**
     * Whether this SIEM HTTP Destination is currently being sent to or not
     *
     * @return bool|null
     */
    public function getSendingActive()
    {
        return isset($this->attributes['sending_active']) ? $this->attributes['sending_active'] : null;
    }

    /**
     * Whether this SIEM HTTP Destination is currently being sent to or not
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSendingActive($value)
    {
        return $this->attributes['sending_active'] = $value;
    }
    /**
     * Applicable only for destination type: generic. Indicates the type of HTTP body. Can be json_newline or json_array. json_newline is multiple log entries as JSON separated by newlines. json_array is a single JSON array containing multiple log entries as JSON.
     *
     * @return string|null
     */
    public function getGenericPayloadType()
    {
        return isset($this->attributes['generic_payload_type']) ? $this->attributes['generic_payload_type'] : null;
    }

    /**
     * Applicable only for destination type: generic. Indicates the type of HTTP body. Can be json_newline or json_array. json_newline is multiple log entries as JSON separated by newlines. json_array is a single JSON array containing multiple log entries as JSON.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGenericPayloadType($value)
    {
        return $this->attributes['generic_payload_type'] = $value;
    }
    /**
     * Applicable only for destination types: splunk, splunk_compatible. Authentication token for the destination.
     *
     * @return string|null
     */
    public function getSplunkTokenMasked()
    {
        return isset($this->attributes['splunk_token_masked']) ? $this->attributes['splunk_token_masked'] : null;
    }

    /**
     * Applicable only for destination types: splunk, splunk_compatible. Authentication token for the destination.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSplunkTokenMasked($value)
    {
        return $this->attributes['splunk_token_masked'] = $value;
    }
    /**
     * Applicable only for destination type: crowdstrike. Authentication token provided by Crowdstrike.
     *
     * @return string|null
     */
    public function getCrowdstrikeTokenMasked()
    {
        return isset($this->attributes['crowdstrike_token_masked']) ? $this->attributes['crowdstrike_token_masked'] : null;
    }

    /**
     * Applicable only for destination type: crowdstrike. Authentication token provided by Crowdstrike.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCrowdstrikeTokenMasked($value)
    {
        return $this->attributes['crowdstrike_token_masked'] = $value;
    }
    /**
     * Applicable only for destination types: azure, azure_legacy. Immutable ID of the Data Collection Rule.
     *
     * @return string|null
     */
    public function getAzureDcrImmutableId()
    {
        return isset($this->attributes['azure_dcr_immutable_id']) ? $this->attributes['azure_dcr_immutable_id'] : null;
    }

    /**
     * Applicable only for destination types: azure, azure_legacy. Immutable ID of the Data Collection Rule.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAzureDcrImmutableId($value)
    {
        return $this->attributes['azure_dcr_immutable_id'] = $value;
    }
    /**
     * Applicable only for destination type: azure. Name of the stream in the DCR that represents the destination table.
     *
     * @return string|null
     */
    public function getAzureStreamName()
    {
        return isset($this->attributes['azure_stream_name']) ? $this->attributes['azure_stream_name'] : null;
    }

    /**
     * Applicable only for destination type: azure. Name of the stream in the DCR that represents the destination table.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAzureStreamName($value)
    {
        return $this->attributes['azure_stream_name'] = $value;
    }
    /**
     * Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Tenant ID.
     *
     * @return string|null
     */
    public function getAzureOauthClientCredentialsTenantId()
    {
        return isset($this->attributes['azure_oauth_client_credentials_tenant_id']) ? $this->attributes['azure_oauth_client_credentials_tenant_id'] : null;
    }

    /**
     * Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Tenant ID.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAzureOauthClientCredentialsTenantId($value)
    {
        return $this->attributes['azure_oauth_client_credentials_tenant_id'] = $value;
    }
    /**
     * Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Client ID.
     *
     * @return string|null
     */
    public function getAzureOauthClientCredentialsClientId()
    {
        return isset($this->attributes['azure_oauth_client_credentials_client_id']) ? $this->attributes['azure_oauth_client_credentials_client_id'] : null;
    }

    /**
     * Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Client ID.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAzureOauthClientCredentialsClientId($value)
    {
        return $this->attributes['azure_oauth_client_credentials_client_id'] = $value;
    }
    /**
     * Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Client Secret.
     *
     * @return string|null
     */
    public function getAzureOauthClientCredentialsClientSecretMasked()
    {
        return isset($this->attributes['azure_oauth_client_credentials_client_secret_masked']) ? $this->attributes['azure_oauth_client_credentials_client_secret_masked'] : null;
    }

    /**
     * Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Client Secret.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAzureOauthClientCredentialsClientSecretMasked($value)
    {
        return $this->attributes['azure_oauth_client_credentials_client_secret_masked'] = $value;
    }
    /**
     * Applicable only for destination type: qradar. Basic auth username provided by QRadar.
     *
     * @return string|null
     */
    public function getQradarUsername()
    {
        return isset($this->attributes['qradar_username']) ? $this->attributes['qradar_username'] : null;
    }

    /**
     * Applicable only for destination type: qradar. Basic auth username provided by QRadar.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setQradarUsername($value)
    {
        return $this->attributes['qradar_username'] = $value;
    }
    /**
     * Applicable only for destination type: qradar. Basic auth password provided by QRadar.
     *
     * @return string|null
     */
    public function getQradarPasswordMasked()
    {
        return isset($this->attributes['qradar_password_masked']) ? $this->attributes['qradar_password_masked'] : null;
    }

    /**
     * Applicable only for destination type: qradar. Basic auth password provided by QRadar.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setQradarPasswordMasked($value)
    {
        return $this->attributes['qradar_password_masked'] = $value;
    }
    /**
     * Applicable only for destination type: solar_winds. Authentication token provided by Solar Winds.
     *
     * @return string|null
     */
    public function getSolarWindsTokenMasked()
    {
        return isset($this->attributes['solar_winds_token_masked']) ? $this->attributes['solar_winds_token_masked'] : null;
    }

    /**
     * Applicable only for destination type: solar_winds. Authentication token provided by Solar Winds.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSolarWindsTokenMasked($value)
    {
        return $this->attributes['solar_winds_token_masked'] = $value;
    }
    /**
     * Applicable only for destination type: new_relic. API key provided by New Relic.
     *
     * @return string|null
     */
    public function getNewRelicApiKeyMasked()
    {
        return isset($this->attributes['new_relic_api_key_masked']) ? $this->attributes['new_relic_api_key_masked'] : null;
    }

    /**
     * Applicable only for destination type: new_relic. API key provided by New Relic.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setNewRelicApiKeyMasked($value)
    {
        return $this->attributes['new_relic_api_key_masked'] = $value;
    }
    /**
     * Applicable only for destination type: datadog. API key provided by Datadog.
     *
     * @return string|null
     */
    public function getDatadogApiKeyMasked()
    {
        return isset($this->attributes['datadog_api_key_masked']) ? $this->attributes['datadog_api_key_masked'] : null;
    }

    /**
     * Applicable only for destination type: datadog. API key provided by Datadog.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDatadogApiKeyMasked($value)
    {
        return $this->attributes['datadog_api_key_masked'] = $value;
    }
    /**
     * Whether or not sending is enabled for action logs.
     *
     * @return bool|null
     */
    public function getActionSendEnabled()
    {
        return isset($this->attributes['action_send_enabled']) ? $this->attributes['action_send_enabled'] : null;
    }

    /**
     * Whether or not sending is enabled for action logs.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setActionSendEnabled($value)
    {
        return $this->attributes['action_send_enabled'] = $value;
    }
    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @return int|null
     */
    public function getActionEntriesSent()
    {
        return isset($this->attributes['action_entries_sent']) ? $this->attributes['action_entries_sent'] : null;
    }

    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setActionEntriesSent($value)
    {
        return $this->attributes['action_entries_sent'] = $value;
    }
    /**
     * Whether or not sending is enabled for sftp_action logs.
     *
     * @return bool|null
     */
    public function getSftpActionSendEnabled()
    {
        return isset($this->attributes['sftp_action_send_enabled']) ? $this->attributes['sftp_action_send_enabled'] : null;
    }

    /**
     * Whether or not sending is enabled for sftp_action logs.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSftpActionSendEnabled($value)
    {
        return $this->attributes['sftp_action_send_enabled'] = $value;
    }
    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @return int|null
     */
    public function getSftpActionEntriesSent()
    {
        return isset($this->attributes['sftp_action_entries_sent']) ? $this->attributes['sftp_action_entries_sent'] : null;
    }

    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setSftpActionEntriesSent($value)
    {
        return $this->attributes['sftp_action_entries_sent'] = $value;
    }
    /**
     * Whether or not sending is enabled for ftp_action logs.
     *
     * @return bool|null
     */
    public function getFtpActionSendEnabled()
    {
        return isset($this->attributes['ftp_action_send_enabled']) ? $this->attributes['ftp_action_send_enabled'] : null;
    }

    /**
     * Whether or not sending is enabled for ftp_action logs.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setFtpActionSendEnabled($value)
    {
        return $this->attributes['ftp_action_send_enabled'] = $value;
    }
    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @return int|null
     */
    public function getFtpActionEntriesSent()
    {
        return isset($this->attributes['ftp_action_entries_sent']) ? $this->attributes['ftp_action_entries_sent'] : null;
    }

    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setFtpActionEntriesSent($value)
    {
        return $this->attributes['ftp_action_entries_sent'] = $value;
    }
    /**
     * Whether or not sending is enabled for web_dav_action logs.
     *
     * @return bool|null
     */
    public function getWebDavActionSendEnabled()
    {
        return isset($this->attributes['web_dav_action_send_enabled']) ? $this->attributes['web_dav_action_send_enabled'] : null;
    }

    /**
     * Whether or not sending is enabled for web_dav_action logs.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setWebDavActionSendEnabled($value)
    {
        return $this->attributes['web_dav_action_send_enabled'] = $value;
    }
    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @return int|null
     */
    public function getWebDavActionEntriesSent()
    {
        return isset($this->attributes['web_dav_action_entries_sent']) ? $this->attributes['web_dav_action_entries_sent'] : null;
    }

    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWebDavActionEntriesSent($value)
    {
        return $this->attributes['web_dav_action_entries_sent'] = $value;
    }
    /**
     * Whether or not sending is enabled for sync logs.
     *
     * @return bool|null
     */
    public function getSyncSendEnabled()
    {
        return isset($this->attributes['sync_send_enabled']) ? $this->attributes['sync_send_enabled'] : null;
    }

    /**
     * Whether or not sending is enabled for sync logs.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSyncSendEnabled($value)
    {
        return $this->attributes['sync_send_enabled'] = $value;
    }
    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @return int|null
     */
    public function getSyncEntriesSent()
    {
        return isset($this->attributes['sync_entries_sent']) ? $this->attributes['sync_entries_sent'] : null;
    }

    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setSyncEntriesSent($value)
    {
        return $this->attributes['sync_entries_sent'] = $value;
    }
    /**
     * Whether or not sending is enabled for outbound_connection logs.
     *
     * @return bool|null
     */
    public function getOutboundConnectionSendEnabled()
    {
        return isset($this->attributes['outbound_connection_send_enabled']) ? $this->attributes['outbound_connection_send_enabled'] : null;
    }

    /**
     * Whether or not sending is enabled for outbound_connection logs.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setOutboundConnectionSendEnabled($value)
    {
        return $this->attributes['outbound_connection_send_enabled'] = $value;
    }
    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @return int|null
     */
    public function getOutboundConnectionEntriesSent()
    {
        return isset($this->attributes['outbound_connection_entries_sent']) ? $this->attributes['outbound_connection_entries_sent'] : null;
    }

    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setOutboundConnectionEntriesSent($value)
    {
        return $this->attributes['outbound_connection_entries_sent'] = $value;
    }
    /**
     * Whether or not sending is enabled for automation logs.
     *
     * @return bool|null
     */
    public function getAutomationSendEnabled()
    {
        return isset($this->attributes['automation_send_enabled']) ? $this->attributes['automation_send_enabled'] : null;
    }

    /**
     * Whether or not sending is enabled for automation logs.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setAutomationSendEnabled($value)
    {
        return $this->attributes['automation_send_enabled'] = $value;
    }
    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @return int|null
     */
    public function getAutomationEntriesSent()
    {
        return isset($this->attributes['automation_entries_sent']) ? $this->attributes['automation_entries_sent'] : null;
    }

    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setAutomationEntriesSent($value)
    {
        return $this->attributes['automation_entries_sent'] = $value;
    }
    /**
     * Whether or not sending is enabled for api_request logs.
     *
     * @return bool|null
     */
    public function getApiRequestSendEnabled()
    {
        return isset($this->attributes['api_request_send_enabled']) ? $this->attributes['api_request_send_enabled'] : null;
    }

    /**
     * Whether or not sending is enabled for api_request logs.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setApiRequestSendEnabled($value)
    {
        return $this->attributes['api_request_send_enabled'] = $value;
    }
    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @return int|null
     */
    public function getApiRequestEntriesSent()
    {
        return isset($this->attributes['api_request_entries_sent']) ? $this->attributes['api_request_entries_sent'] : null;
    }

    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setApiRequestEntriesSent($value)
    {
        return $this->attributes['api_request_entries_sent'] = $value;
    }
    /**
     * Whether or not sending is enabled for public_hosting_request logs.
     *
     * @return bool|null
     */
    public function getPublicHostingRequestSendEnabled()
    {
        return isset($this->attributes['public_hosting_request_send_enabled']) ? $this->attributes['public_hosting_request_send_enabled'] : null;
    }

    /**
     * Whether or not sending is enabled for public_hosting_request logs.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setPublicHostingRequestSendEnabled($value)
    {
        return $this->attributes['public_hosting_request_send_enabled'] = $value;
    }
    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @return int|null
     */
    public function getPublicHostingRequestEntriesSent()
    {
        return isset($this->attributes['public_hosting_request_entries_sent']) ? $this->attributes['public_hosting_request_entries_sent'] : null;
    }

    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setPublicHostingRequestEntriesSent($value)
    {
        return $this->attributes['public_hosting_request_entries_sent'] = $value;
    }
    /**
     * Whether or not sending is enabled for email logs.
     *
     * @return bool|null
     */
    public function getEmailSendEnabled()
    {
        return isset($this->attributes['email_send_enabled']) ? $this->attributes['email_send_enabled'] : null;
    }

    /**
     * Whether or not sending is enabled for email logs.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setEmailSendEnabled($value)
    {
        return $this->attributes['email_send_enabled'] = $value;
    }
    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @return int|null
     */
    public function getEmailEntriesSent()
    {
        return isset($this->attributes['email_entries_sent']) ? $this->attributes['email_entries_sent'] : null;
    }

    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setEmailEntriesSent($value)
    {
        return $this->attributes['email_entries_sent'] = $value;
    }
    /**
     * Whether or not sending is enabled for exavault_api_request logs.
     *
     * @return bool|null
     */
    public function getExavaultApiRequestSendEnabled()
    {
        return isset($this->attributes['exavault_api_request_send_enabled']) ? $this->attributes['exavault_api_request_send_enabled'] : null;
    }

    /**
     * Whether or not sending is enabled for exavault_api_request logs.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setExavaultApiRequestSendEnabled($value)
    {
        return $this->attributes['exavault_api_request_send_enabled'] = $value;
    }
    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @return int|null
     */
    public function getExavaultApiRequestEntriesSent()
    {
        return isset($this->attributes['exavault_api_request_entries_sent']) ? $this->attributes['exavault_api_request_entries_sent'] : null;
    }

    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setExavaultApiRequestEntriesSent($value)
    {
        return $this->attributes['exavault_api_request_entries_sent'] = $value;
    }
    /**
     * Whether or not sending is enabled for settings_change logs.
     *
     * @return bool|null
     */
    public function getSettingsChangeSendEnabled()
    {
        return isset($this->attributes['settings_change_send_enabled']) ? $this->attributes['settings_change_send_enabled'] : null;
    }

    /**
     * Whether or not sending is enabled for settings_change logs.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSettingsChangeSendEnabled($value)
    {
        return $this->attributes['settings_change_send_enabled'] = $value;
    }
    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @return int|null
     */
    public function getSettingsChangeEntriesSent()
    {
        return isset($this->attributes['settings_change_entries_sent']) ? $this->attributes['settings_change_entries_sent'] : null;
    }

    /**
     * Number of log entries sent for the lifetime of this destination.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setSettingsChangeEntriesSent($value)
    {
        return $this->attributes['settings_change_entries_sent'] = $value;
    }
    /**
     * Type of URL that was last called. Can be `destination_url` or `azure_oauth_client_credentials_url`
     *
     * @return string|null
     */
    public function getLastHttpCallTargetType()
    {
        return isset($this->attributes['last_http_call_target_type']) ? $this->attributes['last_http_call_target_type'] : null;
    }

    /**
     * Type of URL that was last called. Can be `destination_url` or `azure_oauth_client_credentials_url`
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastHttpCallTargetType($value)
    {
        return $this->attributes['last_http_call_target_type'] = $value;
    }
    /**
     * Was the last HTTP call made successful?
     *
     * @return bool|null
     */
    public function getLastHttpCallSuccess()
    {
        return isset($this->attributes['last_http_call_success']) ? $this->attributes['last_http_call_success'] : null;
    }

    /**
     * Was the last HTTP call made successful?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setLastHttpCallSuccess($value)
    {
        return $this->attributes['last_http_call_success'] = $value;
    }
    /**
     * Last HTTP Call Response Code
     *
     * @return int|null
     */
    public function getLastHttpCallResponseCode()
    {
        return isset($this->attributes['last_http_call_response_code']) ? $this->attributes['last_http_call_response_code'] : null;
    }

    /**
     * Last HTTP Call Response Code
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setLastHttpCallResponseCode($value)
    {
        return $this->attributes['last_http_call_response_code'] = $value;
    }
    /**
     * Last HTTP Call Response Body. Large responses are truncated.
     *
     * @return string|null
     */
    public function getLastHttpCallResponseBody()
    {
        return isset($this->attributes['last_http_call_response_body']) ? $this->attributes['last_http_call_response_body'] : null;
    }

    /**
     * Last HTTP Call Response Body. Large responses are truncated.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastHttpCallResponseBody($value)
    {
        return $this->attributes['last_http_call_response_body'] = $value;
    }
    /**
     * Last HTTP Call Error Message if applicable
     *
     * @return string|null
     */
    public function getLastHttpCallErrorMessage()
    {
        return isset($this->attributes['last_http_call_error_message']) ? $this->attributes['last_http_call_error_message'] : null;
    }

    /**
     * Last HTTP Call Error Message if applicable
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastHttpCallErrorMessage($value)
    {
        return $this->attributes['last_http_call_error_message'] = $value;
    }
    /**
     * Time of Last HTTP Call
     *
     * @return string|null
     */
    public function getLastHttpCallTime()
    {
        return isset($this->attributes['last_http_call_time']) ? $this->attributes['last_http_call_time'] : null;
    }

    /**
     * Time of Last HTTP Call
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastHttpCallTime($value)
    {
        return $this->attributes['last_http_call_time'] = $value;
    }
    /**
     * Duration of the last HTTP Call in milliseconds
     *
     * @return int|null
     */
    public function getLastHttpCallDurationMs()
    {
        return isset($this->attributes['last_http_call_duration_ms']) ? $this->attributes['last_http_call_duration_ms'] : null;
    }

    /**
     * Duration of the last HTTP Call in milliseconds
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setLastHttpCallDurationMs($value)
    {
        return $this->attributes['last_http_call_duration_ms'] = $value;
    }
    /**
     * Time of Most Recent Successful HTTP Call
     *
     * @return string|null
     */
    public function getMostRecentHttpCallSuccessTime()
    {
        return isset($this->attributes['most_recent_http_call_success_time']) ? $this->attributes['most_recent_http_call_success_time'] : null;
    }

    /**
     * Time of Most Recent Successful HTTP Call
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setMostRecentHttpCallSuccessTime($value)
    {
        return $this->attributes['most_recent_http_call_success_time'] = $value;
    }
    /**
     * Connection Test Entry
     *
     * @return string|null
     */
    public function getConnectionTestEntry()
    {
        return isset($this->attributes['connection_test_entry']) ? $this->attributes['connection_test_entry'] : null;
    }

    /**
     * Connection Test Entry
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setConnectionTestEntry($value)
    {
        return $this->attributes['connection_test_entry'] = $value;
    }
    /**
     * Applicable only for destination types: splunk, splunk_compatible. Authentication token for the destination.
     *
     * @return string|null
     */
    public function getSplunkToken()
    {
        return isset($this->attributes['splunk_token']) ? $this->attributes['splunk_token'] : null;
    }

    /**
     * Applicable only for destination types: splunk, splunk_compatible. Authentication token for the destination.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSplunkToken($value)
    {
        return $this->attributes['splunk_token'] = $value;
    }
    /**
     * Applicable only for destination type: crowdstrike. Authentication token provided by Crowdstrike.
     *
     * @return string|null
     */
    public function getCrowdstrikeToken()
    {
        return isset($this->attributes['crowdstrike_token']) ? $this->attributes['crowdstrike_token'] : null;
    }

    /**
     * Applicable only for destination type: crowdstrike. Authentication token provided by Crowdstrike.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCrowdstrikeToken($value)
    {
        return $this->attributes['crowdstrike_token'] = $value;
    }
    /**
     * Applicable only for destination type: azure. Client Credentials OAuth Client Secret.
     *
     * @return string|null
     */
    public function getAzureOauthClientCredentialsClientSecret()
    {
        return isset($this->attributes['azure_oauth_client_credentials_client_secret']) ? $this->attributes['azure_oauth_client_credentials_client_secret'] : null;
    }

    /**
     * Applicable only for destination type: azure. Client Credentials OAuth Client Secret.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAzureOauthClientCredentialsClientSecret($value)
    {
        return $this->attributes['azure_oauth_client_credentials_client_secret'] = $value;
    }
    /**
     * Applicable only for destination type: qradar. Basic auth password provided by QRadar.
     *
     * @return string|null
     */
    public function getQradarPassword()
    {
        return isset($this->attributes['qradar_password']) ? $this->attributes['qradar_password'] : null;
    }

    /**
     * Applicable only for destination type: qradar. Basic auth password provided by QRadar.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setQradarPassword($value)
    {
        return $this->attributes['qradar_password'] = $value;
    }
    /**
     * Applicable only for destination type: solar_winds. Authentication token provided by Solar Winds.
     *
     * @return string|null
     */
    public function getSolarWindsToken()
    {
        return isset($this->attributes['solar_winds_token']) ? $this->attributes['solar_winds_token'] : null;
    }

    /**
     * Applicable only for destination type: solar_winds. Authentication token provided by Solar Winds.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSolarWindsToken($value)
    {
        return $this->attributes['solar_winds_token'] = $value;
    }
    /**
     * Applicable only for destination type: new_relic. API key provided by New Relic.
     *
     * @return string|null
     */
    public function getNewRelicApiKey()
    {
        return isset($this->attributes['new_relic_api_key']) ? $this->attributes['new_relic_api_key'] : null;
    }

    /**
     * Applicable only for destination type: new_relic. API key provided by New Relic.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setNewRelicApiKey($value)
    {
        return $this->attributes['new_relic_api_key'] = $value;
    }
    /**
     * Applicable only for destination type: datadog. API key provided by Datadog.
     *
     * @return string|null
     */
    public function getDatadogApiKey()
    {
        return isset($this->attributes['datadog_api_key']) ? $this->attributes['datadog_api_key'] : null;
    }

    /**
     * Applicable only for destination type: datadog. API key provided by Datadog.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDatadogApiKey($value)
    {
        return $this->attributes['datadog_api_key'] = $value;
    }

    // Saves the changes, then refreshes this object with the saved attributes, which it also returns as a separate
    // SiemHttpDestination with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update SIEM HTTP Destination
     *
     * PATCH /siem_http_destinations/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Siem Http Destination ID.
     *     - `name` (string, optional): Name for this Destination
     *     - `additional_headers` (array|object, optional): Additional HTTP Headers included in calls to the destination URL
     *     - `sending_active` (bool, optional): Whether this SIEM HTTP Destination is currently being sent to or not
     *     - `generic_payload_type` (string, optional): Applicable only for destination type: generic. Indicates the type of HTTP body. Can be json_newline or json_array. json_newline is multiple log entries as JSON separated by newlines. json_array is a single JSON array containing multiple log entries as JSON. Allowed values: json_newline, json_array.
     *     - `file_destination_path` (string, optional): Applicable only for destination type: file. Destination folder path on Files.com.
     *     - `file_format` (string, optional): Applicable only for destination type: file. Generated file format. Allowed values: json, csv.
     *     - `file_interval_minutes` (int, optional): Applicable only for destination type: file. Interval, in minutes, between file deliveries. Valid values are 5, 10, 15, 20, 30, 60, 90, 180, 240, 360.
     *     - `splunk_token` (string, optional): Applicable only for destination types: splunk, splunk_compatible. Authentication token for the destination.
     *     - `crowdstrike_token` (string, optional): Applicable only for destination type: crowdstrike. Authentication token provided by Crowdstrike.
     *     - `azure_dcr_immutable_id` (string, optional): Applicable only for destination types: azure, azure_legacy. Immutable ID of the Data Collection Rule.
     *     - `azure_stream_name` (string, optional): Applicable only for destination type: azure. Name of the stream in the DCR that represents the destination table.
     *     - `azure_oauth_client_credentials_tenant_id` (string, optional): Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Tenant ID.
     *     - `azure_oauth_client_credentials_client_id` (string, optional): Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Client ID.
     *     - `azure_oauth_client_credentials_client_secret` (string, optional): Applicable only for destination type: azure. Client Credentials OAuth Client Secret.
     *     - `qradar_username` (string, optional): Applicable only for destination type: qradar. Basic auth username provided by QRadar.
     *     - `qradar_password` (string, optional): Applicable only for destination type: qradar. Basic auth password provided by QRadar.
     *     - `solar_winds_token` (string, optional): Applicable only for destination type: solar_winds. Authentication token provided by Solar Winds.
     *     - `new_relic_api_key` (string, optional): Applicable only for destination type: new_relic. API key provided by New Relic.
     *     - `datadog_api_key` (string, optional): Applicable only for destination type: datadog. API key provided by Datadog.
     *     - `action_send_enabled` (bool, optional): Whether or not sending is enabled for action logs.
     *     - `sftp_action_send_enabled` (bool, optional): Whether or not sending is enabled for sftp_action logs.
     *     - `ftp_action_send_enabled` (bool, optional): Whether or not sending is enabled for ftp_action logs.
     *     - `web_dav_action_send_enabled` (bool, optional): Whether or not sending is enabled for web_dav_action logs.
     *     - `sync_send_enabled` (bool, optional): Whether or not sending is enabled for sync logs.
     *     - `outbound_connection_send_enabled` (bool, optional): Whether or not sending is enabled for outbound_connection logs.
     *     - `automation_send_enabled` (bool, optional): Whether or not sending is enabled for automation logs.
     *     - `api_request_send_enabled` (bool, optional): Whether or not sending is enabled for api_request logs.
     *     - `public_hosting_request_send_enabled` (bool, optional): Whether or not sending is enabled for public_hosting_request logs.
     *     - `email_send_enabled` (bool, optional): Whether or not sending is enabled for email logs.
     *     - `exavault_api_request_send_enabled` (bool, optional): Whether or not sending is enabled for exavault_api_request logs.
     *     - `settings_change_send_enabled` (bool, optional): Whether or not sending is enabled for settings_change logs.
     *     - `destination_type` (string, optional): Destination Type Allowed values: generic, splunk, azure_legacy, qradar, sumo, rapid7, solar_winds, new_relic, datadog, azure, file, crowdstrike, splunk_compatible.
     *     - `destination_url` (string, optional): Destination Url
     * @return \Files\Model\SiemHttpDestination
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

        if (isset($params['sending_active']) && !is_scalar($params['sending_active'])) {
            throw new \Files\Exception\InvalidParameterException('$sending_active must be of type boolean; received ' . gettype(@$params['sending_active']));
        }

        if (isset($params['generic_payload_type']) && !is_string($params['generic_payload_type'])) {
            throw new \Files\Exception\InvalidParameterException('$generic_payload_type must be of type string; received ' . gettype(@$params['generic_payload_type']));
        }

        if (isset($params['file_destination_path']) && !is_string($params['file_destination_path'])) {
            throw new \Files\Exception\InvalidParameterException('$file_destination_path must be of type string; received ' . gettype(@$params['file_destination_path']));
        }

        if (isset($params['file_format']) && !is_string($params['file_format'])) {
            throw new \Files\Exception\InvalidParameterException('$file_format must be of type string; received ' . gettype(@$params['file_format']));
        }

        if (isset($params['file_interval_minutes']) && !is_int($params['file_interval_minutes'])) {
            throw new \Files\Exception\InvalidParameterException('$file_interval_minutes must be of type int; received ' . gettype(@$params['file_interval_minutes']));
        }

        if (isset($params['splunk_token']) && !is_string($params['splunk_token'])) {
            throw new \Files\Exception\InvalidParameterException('$splunk_token must be of type string; received ' . gettype(@$params['splunk_token']));
        }

        if (isset($params['crowdstrike_token']) && !is_string($params['crowdstrike_token'])) {
            throw new \Files\Exception\InvalidParameterException('$crowdstrike_token must be of type string; received ' . gettype(@$params['crowdstrike_token']));
        }

        if (isset($params['azure_dcr_immutable_id']) && !is_string($params['azure_dcr_immutable_id'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_dcr_immutable_id must be of type string; received ' . gettype(@$params['azure_dcr_immutable_id']));
        }

        if (isset($params['azure_stream_name']) && !is_string($params['azure_stream_name'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_stream_name must be of type string; received ' . gettype(@$params['azure_stream_name']));
        }

        if (isset($params['azure_oauth_client_credentials_tenant_id']) && !is_string($params['azure_oauth_client_credentials_tenant_id'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_oauth_client_credentials_tenant_id must be of type string; received ' . gettype(@$params['azure_oauth_client_credentials_tenant_id']));
        }

        if (isset($params['azure_oauth_client_credentials_client_id']) && !is_string($params['azure_oauth_client_credentials_client_id'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_oauth_client_credentials_client_id must be of type string; received ' . gettype(@$params['azure_oauth_client_credentials_client_id']));
        }

        if (isset($params['azure_oauth_client_credentials_client_secret']) && !is_string($params['azure_oauth_client_credentials_client_secret'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_oauth_client_credentials_client_secret must be of type string; received ' . gettype(@$params['azure_oauth_client_credentials_client_secret']));
        }

        if (isset($params['qradar_username']) && !is_string($params['qradar_username'])) {
            throw new \Files\Exception\InvalidParameterException('$qradar_username must be of type string; received ' . gettype(@$params['qradar_username']));
        }

        if (isset($params['qradar_password']) && !is_string($params['qradar_password'])) {
            throw new \Files\Exception\InvalidParameterException('$qradar_password must be of type string; received ' . gettype(@$params['qradar_password']));
        }

        if (isset($params['solar_winds_token']) && !is_string($params['solar_winds_token'])) {
            throw new \Files\Exception\InvalidParameterException('$solar_winds_token must be of type string; received ' . gettype(@$params['solar_winds_token']));
        }

        if (isset($params['new_relic_api_key']) && !is_string($params['new_relic_api_key'])) {
            throw new \Files\Exception\InvalidParameterException('$new_relic_api_key must be of type string; received ' . gettype(@$params['new_relic_api_key']));
        }

        if (isset($params['datadog_api_key']) && !is_string($params['datadog_api_key'])) {
            throw new \Files\Exception\InvalidParameterException('$datadog_api_key must be of type string; received ' . gettype(@$params['datadog_api_key']));
        }

        if (isset($params['action_send_enabled']) && !is_scalar($params['action_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$action_send_enabled must be of type boolean; received ' . gettype(@$params['action_send_enabled']));
        }

        if (isset($params['sftp_action_send_enabled']) && !is_scalar($params['sftp_action_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$sftp_action_send_enabled must be of type boolean; received ' . gettype(@$params['sftp_action_send_enabled']));
        }

        if (isset($params['ftp_action_send_enabled']) && !is_scalar($params['ftp_action_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$ftp_action_send_enabled must be of type boolean; received ' . gettype(@$params['ftp_action_send_enabled']));
        }

        if (isset($params['web_dav_action_send_enabled']) && !is_scalar($params['web_dav_action_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$web_dav_action_send_enabled must be of type boolean; received ' . gettype(@$params['web_dav_action_send_enabled']));
        }

        if (isset($params['sync_send_enabled']) && !is_scalar($params['sync_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$sync_send_enabled must be of type boolean; received ' . gettype(@$params['sync_send_enabled']));
        }

        if (isset($params['outbound_connection_send_enabled']) && !is_scalar($params['outbound_connection_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$outbound_connection_send_enabled must be of type boolean; received ' . gettype(@$params['outbound_connection_send_enabled']));
        }

        if (isset($params['automation_send_enabled']) && !is_scalar($params['automation_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$automation_send_enabled must be of type boolean; received ' . gettype(@$params['automation_send_enabled']));
        }

        if (isset($params['api_request_send_enabled']) && !is_scalar($params['api_request_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$api_request_send_enabled must be of type boolean; received ' . gettype(@$params['api_request_send_enabled']));
        }

        if (isset($params['public_hosting_request_send_enabled']) && !is_scalar($params['public_hosting_request_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$public_hosting_request_send_enabled must be of type boolean; received ' . gettype(@$params['public_hosting_request_send_enabled']));
        }

        if (isset($params['email_send_enabled']) && !is_scalar($params['email_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$email_send_enabled must be of type boolean; received ' . gettype(@$params['email_send_enabled']));
        }

        if (isset($params['exavault_api_request_send_enabled']) && !is_scalar($params['exavault_api_request_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$exavault_api_request_send_enabled must be of type boolean; received ' . gettype(@$params['exavault_api_request_send_enabled']));
        }

        if (isset($params['settings_change_send_enabled']) && !is_scalar($params['settings_change_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$settings_change_send_enabled must be of type boolean; received ' . gettype(@$params['settings_change_send_enabled']));
        }

        if (isset($params['destination_type']) && !is_string($params['destination_type'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_type must be of type string; received ' . gettype(@$params['destination_type']));
        }

        if (isset($params['destination_url']) && !is_string($params['destination_url'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_url must be of type string; received ' . gettype(@$params['destination_url']));
        }

        $response = Api::sendRequest('/siem_http_destinations/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        $updated = new SiemHttpDestination(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete SIEM HTTP Destination
     *
     * DELETE /siem_http_destinations/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Siem Http Destination ID.
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

        $response = Api::sendRequest('/siem_http_destinations/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete SIEM HTTP Destination
     *
     * DELETE /siem_http_destinations/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Siem Http Destination ID.
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
     * List SIEM HTTP Destinations
     *
     * GET /siem_http_destinations
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\SiemHttpDestination[]
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

        $response = Api::sendRequest('/siem_http_destinations', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new SiemHttpDestination(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/siem_http_destinations', $params, $options, SiemHttpDestination::class);
    }

    /**
     * Show SIEM HTTP Destination
     *
     * GET /siem_http_destinations/{id}
     *
     * @param int $id Siem Http Destination ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\SiemHttpDestination
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

        $response = Api::sendRequest('/siem_http_destinations/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new SiemHttpDestination(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show SIEM HTTP Destination
     *
     * GET /siem_http_destinations/{id}
     *
     * Alias of find().
     *
     * @param int $id Siem Http Destination ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\SiemHttpDestination
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create SIEM HTTP Destination
     *
     * POST /siem_http_destinations
     *
     * @param array $params Request parameters.
     *     - `name` (string, optional): Name for this Destination
     *     - `additional_headers` (array|object, optional): Additional HTTP Headers included in calls to the destination URL
     *     - `sending_active` (bool, optional): Whether this SIEM HTTP Destination is currently being sent to or not
     *     - `generic_payload_type` (string, optional): Applicable only for destination type: generic. Indicates the type of HTTP body. Can be json_newline or json_array. json_newline is multiple log entries as JSON separated by newlines. json_array is a single JSON array containing multiple log entries as JSON. Allowed values: json_newline, json_array.
     *     - `file_destination_path` (string, optional): Applicable only for destination type: file. Destination folder path on Files.com.
     *     - `file_format` (string, optional): Applicable only for destination type: file. Generated file format. Allowed values: json, csv.
     *     - `file_interval_minutes` (int, optional): Applicable only for destination type: file. Interval, in minutes, between file deliveries. Valid values are 5, 10, 15, 20, 30, 60, 90, 180, 240, 360.
     *     - `splunk_token` (string, optional): Applicable only for destination types: splunk, splunk_compatible. Authentication token for the destination.
     *     - `crowdstrike_token` (string, optional): Applicable only for destination type: crowdstrike. Authentication token provided by Crowdstrike.
     *     - `azure_dcr_immutable_id` (string, optional): Applicable only for destination types: azure, azure_legacy. Immutable ID of the Data Collection Rule.
     *     - `azure_stream_name` (string, optional): Applicable only for destination type: azure. Name of the stream in the DCR that represents the destination table.
     *     - `azure_oauth_client_credentials_tenant_id` (string, optional): Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Tenant ID.
     *     - `azure_oauth_client_credentials_client_id` (string, optional): Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Client ID.
     *     - `azure_oauth_client_credentials_client_secret` (string, optional): Applicable only for destination type: azure. Client Credentials OAuth Client Secret.
     *     - `qradar_username` (string, optional): Applicable only for destination type: qradar. Basic auth username provided by QRadar.
     *     - `qradar_password` (string, optional): Applicable only for destination type: qradar. Basic auth password provided by QRadar.
     *     - `solar_winds_token` (string, optional): Applicable only for destination type: solar_winds. Authentication token provided by Solar Winds.
     *     - `new_relic_api_key` (string, optional): Applicable only for destination type: new_relic. API key provided by New Relic.
     *     - `datadog_api_key` (string, optional): Applicable only for destination type: datadog. API key provided by Datadog.
     *     - `action_send_enabled` (bool, optional): Whether or not sending is enabled for action logs.
     *     - `sftp_action_send_enabled` (bool, optional): Whether or not sending is enabled for sftp_action logs.
     *     - `ftp_action_send_enabled` (bool, optional): Whether or not sending is enabled for ftp_action logs.
     *     - `web_dav_action_send_enabled` (bool, optional): Whether or not sending is enabled for web_dav_action logs.
     *     - `sync_send_enabled` (bool, optional): Whether or not sending is enabled for sync logs.
     *     - `outbound_connection_send_enabled` (bool, optional): Whether or not sending is enabled for outbound_connection logs.
     *     - `automation_send_enabled` (bool, optional): Whether or not sending is enabled for automation logs.
     *     - `api_request_send_enabled` (bool, optional): Whether or not sending is enabled for api_request logs.
     *     - `public_hosting_request_send_enabled` (bool, optional): Whether or not sending is enabled for public_hosting_request logs.
     *     - `email_send_enabled` (bool, optional): Whether or not sending is enabled for email logs.
     *     - `exavault_api_request_send_enabled` (bool, optional): Whether or not sending is enabled for exavault_api_request logs.
     *     - `settings_change_send_enabled` (bool, optional): Whether or not sending is enabled for settings_change logs.
     *     - `destination_type` (string, required): Destination Type Allowed values: generic, splunk, azure_legacy, qradar, sumo, rapid7, solar_winds, new_relic, datadog, azure, file, crowdstrike, splunk_compatible.
     *     - `destination_url` (string, optional): Destination Url
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\SiemHttpDestination
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['destination_type'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: destination_type');
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['sending_active']) && !is_scalar($params['sending_active'])) {
            throw new \Files\Exception\InvalidParameterException('$sending_active must be of type boolean; received ' . gettype(@$params['sending_active']));
        }

        if (isset($params['generic_payload_type']) && !is_string($params['generic_payload_type'])) {
            throw new \Files\Exception\InvalidParameterException('$generic_payload_type must be of type string; received ' . gettype(@$params['generic_payload_type']));
        }

        if (isset($params['file_destination_path']) && !is_string($params['file_destination_path'])) {
            throw new \Files\Exception\InvalidParameterException('$file_destination_path must be of type string; received ' . gettype(@$params['file_destination_path']));
        }

        if (isset($params['file_format']) && !is_string($params['file_format'])) {
            throw new \Files\Exception\InvalidParameterException('$file_format must be of type string; received ' . gettype(@$params['file_format']));
        }

        if (isset($params['file_interval_minutes']) && !is_int($params['file_interval_minutes'])) {
            throw new \Files\Exception\InvalidParameterException('$file_interval_minutes must be of type int; received ' . gettype(@$params['file_interval_minutes']));
        }

        if (isset($params['splunk_token']) && !is_string($params['splunk_token'])) {
            throw new \Files\Exception\InvalidParameterException('$splunk_token must be of type string; received ' . gettype(@$params['splunk_token']));
        }

        if (isset($params['crowdstrike_token']) && !is_string($params['crowdstrike_token'])) {
            throw new \Files\Exception\InvalidParameterException('$crowdstrike_token must be of type string; received ' . gettype(@$params['crowdstrike_token']));
        }

        if (isset($params['azure_dcr_immutable_id']) && !is_string($params['azure_dcr_immutable_id'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_dcr_immutable_id must be of type string; received ' . gettype(@$params['azure_dcr_immutable_id']));
        }

        if (isset($params['azure_stream_name']) && !is_string($params['azure_stream_name'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_stream_name must be of type string; received ' . gettype(@$params['azure_stream_name']));
        }

        if (isset($params['azure_oauth_client_credentials_tenant_id']) && !is_string($params['azure_oauth_client_credentials_tenant_id'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_oauth_client_credentials_tenant_id must be of type string; received ' . gettype(@$params['azure_oauth_client_credentials_tenant_id']));
        }

        if (isset($params['azure_oauth_client_credentials_client_id']) && !is_string($params['azure_oauth_client_credentials_client_id'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_oauth_client_credentials_client_id must be of type string; received ' . gettype(@$params['azure_oauth_client_credentials_client_id']));
        }

        if (isset($params['azure_oauth_client_credentials_client_secret']) && !is_string($params['azure_oauth_client_credentials_client_secret'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_oauth_client_credentials_client_secret must be of type string; received ' . gettype(@$params['azure_oauth_client_credentials_client_secret']));
        }

        if (isset($params['qradar_username']) && !is_string($params['qradar_username'])) {
            throw new \Files\Exception\InvalidParameterException('$qradar_username must be of type string; received ' . gettype(@$params['qradar_username']));
        }

        if (isset($params['qradar_password']) && !is_string($params['qradar_password'])) {
            throw new \Files\Exception\InvalidParameterException('$qradar_password must be of type string; received ' . gettype(@$params['qradar_password']));
        }

        if (isset($params['solar_winds_token']) && !is_string($params['solar_winds_token'])) {
            throw new \Files\Exception\InvalidParameterException('$solar_winds_token must be of type string; received ' . gettype(@$params['solar_winds_token']));
        }

        if (isset($params['new_relic_api_key']) && !is_string($params['new_relic_api_key'])) {
            throw new \Files\Exception\InvalidParameterException('$new_relic_api_key must be of type string; received ' . gettype(@$params['new_relic_api_key']));
        }

        if (isset($params['datadog_api_key']) && !is_string($params['datadog_api_key'])) {
            throw new \Files\Exception\InvalidParameterException('$datadog_api_key must be of type string; received ' . gettype(@$params['datadog_api_key']));
        }

        if (isset($params['action_send_enabled']) && !is_scalar($params['action_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$action_send_enabled must be of type boolean; received ' . gettype(@$params['action_send_enabled']));
        }

        if (isset($params['sftp_action_send_enabled']) && !is_scalar($params['sftp_action_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$sftp_action_send_enabled must be of type boolean; received ' . gettype(@$params['sftp_action_send_enabled']));
        }

        if (isset($params['ftp_action_send_enabled']) && !is_scalar($params['ftp_action_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$ftp_action_send_enabled must be of type boolean; received ' . gettype(@$params['ftp_action_send_enabled']));
        }

        if (isset($params['web_dav_action_send_enabled']) && !is_scalar($params['web_dav_action_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$web_dav_action_send_enabled must be of type boolean; received ' . gettype(@$params['web_dav_action_send_enabled']));
        }

        if (isset($params['sync_send_enabled']) && !is_scalar($params['sync_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$sync_send_enabled must be of type boolean; received ' . gettype(@$params['sync_send_enabled']));
        }

        if (isset($params['outbound_connection_send_enabled']) && !is_scalar($params['outbound_connection_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$outbound_connection_send_enabled must be of type boolean; received ' . gettype(@$params['outbound_connection_send_enabled']));
        }

        if (isset($params['automation_send_enabled']) && !is_scalar($params['automation_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$automation_send_enabled must be of type boolean; received ' . gettype(@$params['automation_send_enabled']));
        }

        if (isset($params['api_request_send_enabled']) && !is_scalar($params['api_request_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$api_request_send_enabled must be of type boolean; received ' . gettype(@$params['api_request_send_enabled']));
        }

        if (isset($params['public_hosting_request_send_enabled']) && !is_scalar($params['public_hosting_request_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$public_hosting_request_send_enabled must be of type boolean; received ' . gettype(@$params['public_hosting_request_send_enabled']));
        }

        if (isset($params['email_send_enabled']) && !is_scalar($params['email_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$email_send_enabled must be of type boolean; received ' . gettype(@$params['email_send_enabled']));
        }

        if (isset($params['exavault_api_request_send_enabled']) && !is_scalar($params['exavault_api_request_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$exavault_api_request_send_enabled must be of type boolean; received ' . gettype(@$params['exavault_api_request_send_enabled']));
        }

        if (isset($params['settings_change_send_enabled']) && !is_scalar($params['settings_change_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$settings_change_send_enabled must be of type boolean; received ' . gettype(@$params['settings_change_send_enabled']));
        }

        if (isset($params['destination_type']) && !is_string($params['destination_type'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_type must be of type string; received ' . gettype(@$params['destination_type']));
        }

        if (isset($params['destination_url']) && !is_string($params['destination_url'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_url must be of type string; received ' . gettype(@$params['destination_url']));
        }

        $response = Api::sendRequest('/siem_http_destinations', 'POST', $params, $options);

        return new SiemHttpDestination(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }

    /**
     * send_test_entry SIEM HTTP Destination
     *
     * POST /siem_http_destinations/send_test_entry
     *
     * @param array $params Request parameters.
     *     - `siem_http_destination_id` (int, optional): SIEM HTTP Destination ID
     *     - `destination_type` (string, optional): Destination Type Allowed values: generic, splunk, azure_legacy, qradar, sumo, rapid7, solar_winds, new_relic, datadog, azure, file, crowdstrike, splunk_compatible.
     *     - `destination_url` (string, optional): Destination Url
     *     - `name` (string, optional): Name for this Destination
     *     - `additional_headers` (array|object, optional): Additional HTTP Headers included in calls to the destination URL
     *     - `sending_active` (bool, optional): Whether this SIEM HTTP Destination is currently being sent to or not
     *     - `generic_payload_type` (string, optional): Applicable only for destination type: generic. Indicates the type of HTTP body. Can be json_newline or json_array. json_newline is multiple log entries as JSON separated by newlines. json_array is a single JSON array containing multiple log entries as JSON. Allowed values: json_newline, json_array.
     *     - `file_destination_path` (string, optional): Applicable only for destination type: file. Destination folder path on Files.com.
     *     - `file_format` (string, optional): Applicable only for destination type: file. Generated file format. Allowed values: json, csv.
     *     - `file_interval_minutes` (int, optional): Applicable only for destination type: file. Interval, in minutes, between file deliveries. Valid values are 5, 10, 15, 20, 30, 60, 90, 180, 240, 360.
     *     - `splunk_token` (string, optional): Applicable only for destination types: splunk, splunk_compatible. Authentication token for the destination.
     *     - `crowdstrike_token` (string, optional): Applicable only for destination type: crowdstrike. Authentication token provided by Crowdstrike.
     *     - `azure_dcr_immutable_id` (string, optional): Applicable only for destination types: azure, azure_legacy. Immutable ID of the Data Collection Rule.
     *     - `azure_stream_name` (string, optional): Applicable only for destination type: azure. Name of the stream in the DCR that represents the destination table.
     *     - `azure_oauth_client_credentials_tenant_id` (string, optional): Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Tenant ID.
     *     - `azure_oauth_client_credentials_client_id` (string, optional): Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Client ID.
     *     - `azure_oauth_client_credentials_client_secret` (string, optional): Applicable only for destination type: azure. Client Credentials OAuth Client Secret.
     *     - `qradar_username` (string, optional): Applicable only for destination type: qradar. Basic auth username provided by QRadar.
     *     - `qradar_password` (string, optional): Applicable only for destination type: qradar. Basic auth password provided by QRadar.
     *     - `solar_winds_token` (string, optional): Applicable only for destination type: solar_winds. Authentication token provided by Solar Winds.
     *     - `new_relic_api_key` (string, optional): Applicable only for destination type: new_relic. API key provided by New Relic.
     *     - `datadog_api_key` (string, optional): Applicable only for destination type: datadog. API key provided by Datadog.
     *     - `action_send_enabled` (bool, optional): Whether or not sending is enabled for action logs.
     *     - `sftp_action_send_enabled` (bool, optional): Whether or not sending is enabled for sftp_action logs.
     *     - `ftp_action_send_enabled` (bool, optional): Whether or not sending is enabled for ftp_action logs.
     *     - `web_dav_action_send_enabled` (bool, optional): Whether or not sending is enabled for web_dav_action logs.
     *     - `sync_send_enabled` (bool, optional): Whether or not sending is enabled for sync logs.
     *     - `outbound_connection_send_enabled` (bool, optional): Whether or not sending is enabled for outbound_connection logs.
     *     - `automation_send_enabled` (bool, optional): Whether or not sending is enabled for automation logs.
     *     - `api_request_send_enabled` (bool, optional): Whether or not sending is enabled for api_request logs.
     *     - `public_hosting_request_send_enabled` (bool, optional): Whether or not sending is enabled for public_hosting_request logs.
     *     - `email_send_enabled` (bool, optional): Whether or not sending is enabled for email logs.
     *     - `exavault_api_request_send_enabled` (bool, optional): Whether or not sending is enabled for exavault_api_request logs.
     *     - `settings_change_send_enabled` (bool, optional): Whether or not sending is enabled for settings_change logs.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function sendTestEntry($params = [], $options = [])
    {
        if (isset($params['siem_http_destination_id']) && !is_int($params['siem_http_destination_id'])) {
            throw new \Files\Exception\InvalidParameterException('$siem_http_destination_id must be of type int; received ' . gettype(@$params['siem_http_destination_id']));
        }

        if (isset($params['destination_type']) && !is_string($params['destination_type'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_type must be of type string; received ' . gettype(@$params['destination_type']));
        }

        if (isset($params['destination_url']) && !is_string($params['destination_url'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_url must be of type string; received ' . gettype(@$params['destination_url']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['sending_active']) && !is_scalar($params['sending_active'])) {
            throw new \Files\Exception\InvalidParameterException('$sending_active must be of type boolean; received ' . gettype(@$params['sending_active']));
        }

        if (isset($params['generic_payload_type']) && !is_string($params['generic_payload_type'])) {
            throw new \Files\Exception\InvalidParameterException('$generic_payload_type must be of type string; received ' . gettype(@$params['generic_payload_type']));
        }

        if (isset($params['file_destination_path']) && !is_string($params['file_destination_path'])) {
            throw new \Files\Exception\InvalidParameterException('$file_destination_path must be of type string; received ' . gettype(@$params['file_destination_path']));
        }

        if (isset($params['file_format']) && !is_string($params['file_format'])) {
            throw new \Files\Exception\InvalidParameterException('$file_format must be of type string; received ' . gettype(@$params['file_format']));
        }

        if (isset($params['file_interval_minutes']) && !is_int($params['file_interval_minutes'])) {
            throw new \Files\Exception\InvalidParameterException('$file_interval_minutes must be of type int; received ' . gettype(@$params['file_interval_minutes']));
        }

        if (isset($params['splunk_token']) && !is_string($params['splunk_token'])) {
            throw new \Files\Exception\InvalidParameterException('$splunk_token must be of type string; received ' . gettype(@$params['splunk_token']));
        }

        if (isset($params['crowdstrike_token']) && !is_string($params['crowdstrike_token'])) {
            throw new \Files\Exception\InvalidParameterException('$crowdstrike_token must be of type string; received ' . gettype(@$params['crowdstrike_token']));
        }

        if (isset($params['azure_dcr_immutable_id']) && !is_string($params['azure_dcr_immutable_id'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_dcr_immutable_id must be of type string; received ' . gettype(@$params['azure_dcr_immutable_id']));
        }

        if (isset($params['azure_stream_name']) && !is_string($params['azure_stream_name'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_stream_name must be of type string; received ' . gettype(@$params['azure_stream_name']));
        }

        if (isset($params['azure_oauth_client_credentials_tenant_id']) && !is_string($params['azure_oauth_client_credentials_tenant_id'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_oauth_client_credentials_tenant_id must be of type string; received ' . gettype(@$params['azure_oauth_client_credentials_tenant_id']));
        }

        if (isset($params['azure_oauth_client_credentials_client_id']) && !is_string($params['azure_oauth_client_credentials_client_id'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_oauth_client_credentials_client_id must be of type string; received ' . gettype(@$params['azure_oauth_client_credentials_client_id']));
        }

        if (isset($params['azure_oauth_client_credentials_client_secret']) && !is_string($params['azure_oauth_client_credentials_client_secret'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_oauth_client_credentials_client_secret must be of type string; received ' . gettype(@$params['azure_oauth_client_credentials_client_secret']));
        }

        if (isset($params['qradar_username']) && !is_string($params['qradar_username'])) {
            throw new \Files\Exception\InvalidParameterException('$qradar_username must be of type string; received ' . gettype(@$params['qradar_username']));
        }

        if (isset($params['qradar_password']) && !is_string($params['qradar_password'])) {
            throw new \Files\Exception\InvalidParameterException('$qradar_password must be of type string; received ' . gettype(@$params['qradar_password']));
        }

        if (isset($params['solar_winds_token']) && !is_string($params['solar_winds_token'])) {
            throw new \Files\Exception\InvalidParameterException('$solar_winds_token must be of type string; received ' . gettype(@$params['solar_winds_token']));
        }

        if (isset($params['new_relic_api_key']) && !is_string($params['new_relic_api_key'])) {
            throw new \Files\Exception\InvalidParameterException('$new_relic_api_key must be of type string; received ' . gettype(@$params['new_relic_api_key']));
        }

        if (isset($params['datadog_api_key']) && !is_string($params['datadog_api_key'])) {
            throw new \Files\Exception\InvalidParameterException('$datadog_api_key must be of type string; received ' . gettype(@$params['datadog_api_key']));
        }

        if (isset($params['action_send_enabled']) && !is_scalar($params['action_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$action_send_enabled must be of type boolean; received ' . gettype(@$params['action_send_enabled']));
        }

        if (isset($params['sftp_action_send_enabled']) && !is_scalar($params['sftp_action_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$sftp_action_send_enabled must be of type boolean; received ' . gettype(@$params['sftp_action_send_enabled']));
        }

        if (isset($params['ftp_action_send_enabled']) && !is_scalar($params['ftp_action_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$ftp_action_send_enabled must be of type boolean; received ' . gettype(@$params['ftp_action_send_enabled']));
        }

        if (isset($params['web_dav_action_send_enabled']) && !is_scalar($params['web_dav_action_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$web_dav_action_send_enabled must be of type boolean; received ' . gettype(@$params['web_dav_action_send_enabled']));
        }

        if (isset($params['sync_send_enabled']) && !is_scalar($params['sync_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$sync_send_enabled must be of type boolean; received ' . gettype(@$params['sync_send_enabled']));
        }

        if (isset($params['outbound_connection_send_enabled']) && !is_scalar($params['outbound_connection_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$outbound_connection_send_enabled must be of type boolean; received ' . gettype(@$params['outbound_connection_send_enabled']));
        }

        if (isset($params['automation_send_enabled']) && !is_scalar($params['automation_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$automation_send_enabled must be of type boolean; received ' . gettype(@$params['automation_send_enabled']));
        }

        if (isset($params['api_request_send_enabled']) && !is_scalar($params['api_request_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$api_request_send_enabled must be of type boolean; received ' . gettype(@$params['api_request_send_enabled']));
        }

        if (isset($params['public_hosting_request_send_enabled']) && !is_scalar($params['public_hosting_request_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$public_hosting_request_send_enabled must be of type boolean; received ' . gettype(@$params['public_hosting_request_send_enabled']));
        }

        if (isset($params['email_send_enabled']) && !is_scalar($params['email_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$email_send_enabled must be of type boolean; received ' . gettype(@$params['email_send_enabled']));
        }

        if (isset($params['exavault_api_request_send_enabled']) && !is_scalar($params['exavault_api_request_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$exavault_api_request_send_enabled must be of type boolean; received ' . gettype(@$params['exavault_api_request_send_enabled']));
        }

        if (isset($params['settings_change_send_enabled']) && !is_scalar($params['settings_change_send_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$settings_change_send_enabled must be of type boolean; received ' . gettype(@$params['settings_change_send_enabled']));
        }

        $response = Api::sendRequest('/siem_http_destinations/send_test_entry', 'POST', $params, $options);

        return;
    }
}
