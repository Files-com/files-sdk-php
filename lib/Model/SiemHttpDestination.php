<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class SiemHttpDestination
 *
 * @package Files
 */
class SiemHttpDestination
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
    // int64 # SIEM HTTP Destination ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # Name for this Destination
    public function getName()
    {
        return @$this->attributes['name'];
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // string # Destination Type
    public function getDestinationType()
    {
        return @$this->attributes['destination_type'];
    }

    public function setDestinationType($value)
    {
        return $this->attributes['destination_type'] = $value;
    }
    // string # Destination Url
    public function getDestinationUrl()
    {
        return @$this->attributes['destination_url'];
    }

    public function setDestinationUrl($value)
    {
        return $this->attributes['destination_url'] = $value;
    }
    // string # Applicable only for destination type: file. Destination folder path on Files.com.
    public function getFileDestinationPath()
    {
        return @$this->attributes['file_destination_path'];
    }

    public function setFileDestinationPath($value)
    {
        return $this->attributes['file_destination_path'] = $value;
    }
    // string # Applicable only for destination type: file. Generated file format.
    public function getFileFormat()
    {
        return @$this->attributes['file_format'];
    }

    public function setFileFormat($value)
    {
        return $this->attributes['file_format'] = $value;
    }
    // int64 # Applicable only for destination type: file. Interval, in minutes, between file deliveries.
    public function getFileIntervalMinutes()
    {
        return @$this->attributes['file_interval_minutes'];
    }

    public function setFileIntervalMinutes($value)
    {
        return $this->attributes['file_interval_minutes'] = $value;
    }
    // object # Additional HTTP Headers included in calls to the destination URL
    public function getAdditionalHeaders()
    {
        return @$this->attributes['additional_headers'];
    }

    public function setAdditionalHeaders($value)
    {
        return $this->attributes['additional_headers'] = $value;
    }
    // boolean # Whether this SIEM HTTP Destination is currently being sent to or not
    public function getSendingActive()
    {
        return @$this->attributes['sending_active'];
    }

    public function setSendingActive($value)
    {
        return $this->attributes['sending_active'] = $value;
    }
    // string # Applicable only for destination type: generic. Indicates the type of HTTP body. Can be json_newline or json_array. json_newline is multiple log entries as JSON separated by newlines. json_array is a single JSON array containing multiple log entries as JSON.
    public function getGenericPayloadType()
    {
        return @$this->attributes['generic_payload_type'];
    }

    public function setGenericPayloadType($value)
    {
        return $this->attributes['generic_payload_type'] = $value;
    }
    // string # Applicable only for destination type: splunk. Authentication token provided by Splunk.
    public function getSplunkTokenMasked()
    {
        return @$this->attributes['splunk_token_masked'];
    }

    public function setSplunkTokenMasked($value)
    {
        return $this->attributes['splunk_token_masked'] = $value;
    }
    // string # Applicable only for destination types: azure, azure_legacy. Immutable ID of the Data Collection Rule.
    public function getAzureDcrImmutableId()
    {
        return @$this->attributes['azure_dcr_immutable_id'];
    }

    public function setAzureDcrImmutableId($value)
    {
        return $this->attributes['azure_dcr_immutable_id'] = $value;
    }
    // string # Applicable only for destination type: azure. Name of the stream in the DCR that represents the destination table.
    public function getAzureStreamName()
    {
        return @$this->attributes['azure_stream_name'];
    }

    public function setAzureStreamName($value)
    {
        return $this->attributes['azure_stream_name'] = $value;
    }
    // string # Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Tenant ID.
    public function getAzureOauthClientCredentialsTenantId()
    {
        return @$this->attributes['azure_oauth_client_credentials_tenant_id'];
    }

    public function setAzureOauthClientCredentialsTenantId($value)
    {
        return $this->attributes['azure_oauth_client_credentials_tenant_id'] = $value;
    }
    // string # Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Client ID.
    public function getAzureOauthClientCredentialsClientId()
    {
        return @$this->attributes['azure_oauth_client_credentials_client_id'];
    }

    public function setAzureOauthClientCredentialsClientId($value)
    {
        return $this->attributes['azure_oauth_client_credentials_client_id'] = $value;
    }
    // string # Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Client Secret.
    public function getAzureOauthClientCredentialsClientSecretMasked()
    {
        return @$this->attributes['azure_oauth_client_credentials_client_secret_masked'];
    }

    public function setAzureOauthClientCredentialsClientSecretMasked($value)
    {
        return $this->attributes['azure_oauth_client_credentials_client_secret_masked'] = $value;
    }
    // string # Applicable only for destination type: qradar. Basic auth username provided by QRadar.
    public function getQradarUsername()
    {
        return @$this->attributes['qradar_username'];
    }

    public function setQradarUsername($value)
    {
        return $this->attributes['qradar_username'] = $value;
    }
    // string # Applicable only for destination type: qradar. Basic auth password provided by QRadar.
    public function getQradarPasswordMasked()
    {
        return @$this->attributes['qradar_password_masked'];
    }

    public function setQradarPasswordMasked($value)
    {
        return $this->attributes['qradar_password_masked'] = $value;
    }
    // string # Applicable only for destination type: solar_winds. Authentication token provided by Solar Winds.
    public function getSolarWindsTokenMasked()
    {
        return @$this->attributes['solar_winds_token_masked'];
    }

    public function setSolarWindsTokenMasked($value)
    {
        return $this->attributes['solar_winds_token_masked'] = $value;
    }
    // string # Applicable only for destination type: new_relic. API key provided by New Relic.
    public function getNewRelicApiKeyMasked()
    {
        return @$this->attributes['new_relic_api_key_masked'];
    }

    public function setNewRelicApiKeyMasked($value)
    {
        return $this->attributes['new_relic_api_key_masked'] = $value;
    }
    // string # Applicable only for destination type: datadog. API key provided by Datadog.
    public function getDatadogApiKeyMasked()
    {
        return @$this->attributes['datadog_api_key_masked'];
    }

    public function setDatadogApiKeyMasked($value)
    {
        return $this->attributes['datadog_api_key_masked'] = $value;
    }
    // boolean # Whether or not sending is enabled for sftp_action logs.
    public function getSftpActionSendEnabled()
    {
        return @$this->attributes['sftp_action_send_enabled'];
    }

    public function setSftpActionSendEnabled($value)
    {
        return $this->attributes['sftp_action_send_enabled'] = $value;
    }
    // int64 # Number of log entries sent for the lifetime of this destination.
    public function getSftpActionEntriesSent()
    {
        return @$this->attributes['sftp_action_entries_sent'];
    }

    public function setSftpActionEntriesSent($value)
    {
        return $this->attributes['sftp_action_entries_sent'] = $value;
    }
    // boolean # Whether or not sending is enabled for ftp_action logs.
    public function getFtpActionSendEnabled()
    {
        return @$this->attributes['ftp_action_send_enabled'];
    }

    public function setFtpActionSendEnabled($value)
    {
        return $this->attributes['ftp_action_send_enabled'] = $value;
    }
    // int64 # Number of log entries sent for the lifetime of this destination.
    public function getFtpActionEntriesSent()
    {
        return @$this->attributes['ftp_action_entries_sent'];
    }

    public function setFtpActionEntriesSent($value)
    {
        return $this->attributes['ftp_action_entries_sent'] = $value;
    }
    // boolean # Whether or not sending is enabled for web_dav_action logs.
    public function getWebDavActionSendEnabled()
    {
        return @$this->attributes['web_dav_action_send_enabled'];
    }

    public function setWebDavActionSendEnabled($value)
    {
        return $this->attributes['web_dav_action_send_enabled'] = $value;
    }
    // int64 # Number of log entries sent for the lifetime of this destination.
    public function getWebDavActionEntriesSent()
    {
        return @$this->attributes['web_dav_action_entries_sent'];
    }

    public function setWebDavActionEntriesSent($value)
    {
        return $this->attributes['web_dav_action_entries_sent'] = $value;
    }
    // boolean # Whether or not sending is enabled for sync logs.
    public function getSyncSendEnabled()
    {
        return @$this->attributes['sync_send_enabled'];
    }

    public function setSyncSendEnabled($value)
    {
        return $this->attributes['sync_send_enabled'] = $value;
    }
    // int64 # Number of log entries sent for the lifetime of this destination.
    public function getSyncEntriesSent()
    {
        return @$this->attributes['sync_entries_sent'];
    }

    public function setSyncEntriesSent($value)
    {
        return $this->attributes['sync_entries_sent'] = $value;
    }
    // boolean # Whether or not sending is enabled for outbound_connection logs.
    public function getOutboundConnectionSendEnabled()
    {
        return @$this->attributes['outbound_connection_send_enabled'];
    }

    public function setOutboundConnectionSendEnabled($value)
    {
        return $this->attributes['outbound_connection_send_enabled'] = $value;
    }
    // int64 # Number of log entries sent for the lifetime of this destination.
    public function getOutboundConnectionEntriesSent()
    {
        return @$this->attributes['outbound_connection_entries_sent'];
    }

    public function setOutboundConnectionEntriesSent($value)
    {
        return $this->attributes['outbound_connection_entries_sent'] = $value;
    }
    // boolean # Whether or not sending is enabled for automation logs.
    public function getAutomationSendEnabled()
    {
        return @$this->attributes['automation_send_enabled'];
    }

    public function setAutomationSendEnabled($value)
    {
        return $this->attributes['automation_send_enabled'] = $value;
    }
    // int64 # Number of log entries sent for the lifetime of this destination.
    public function getAutomationEntriesSent()
    {
        return @$this->attributes['automation_entries_sent'];
    }

    public function setAutomationEntriesSent($value)
    {
        return $this->attributes['automation_entries_sent'] = $value;
    }
    // boolean # Whether or not sending is enabled for api_request logs.
    public function getApiRequestSendEnabled()
    {
        return @$this->attributes['api_request_send_enabled'];
    }

    public function setApiRequestSendEnabled($value)
    {
        return $this->attributes['api_request_send_enabled'] = $value;
    }
    // int64 # Number of log entries sent for the lifetime of this destination.
    public function getApiRequestEntriesSent()
    {
        return @$this->attributes['api_request_entries_sent'];
    }

    public function setApiRequestEntriesSent($value)
    {
        return $this->attributes['api_request_entries_sent'] = $value;
    }
    // boolean # Whether or not sending is enabled for public_hosting_request logs.
    public function getPublicHostingRequestSendEnabled()
    {
        return @$this->attributes['public_hosting_request_send_enabled'];
    }

    public function setPublicHostingRequestSendEnabled($value)
    {
        return $this->attributes['public_hosting_request_send_enabled'] = $value;
    }
    // int64 # Number of log entries sent for the lifetime of this destination.
    public function getPublicHostingRequestEntriesSent()
    {
        return @$this->attributes['public_hosting_request_entries_sent'];
    }

    public function setPublicHostingRequestEntriesSent($value)
    {
        return $this->attributes['public_hosting_request_entries_sent'] = $value;
    }
    // boolean # Whether or not sending is enabled for email logs.
    public function getEmailSendEnabled()
    {
        return @$this->attributes['email_send_enabled'];
    }

    public function setEmailSendEnabled($value)
    {
        return $this->attributes['email_send_enabled'] = $value;
    }
    // int64 # Number of log entries sent for the lifetime of this destination.
    public function getEmailEntriesSent()
    {
        return @$this->attributes['email_entries_sent'];
    }

    public function setEmailEntriesSent($value)
    {
        return $this->attributes['email_entries_sent'] = $value;
    }
    // boolean # Whether or not sending is enabled for exavault_api_request logs.
    public function getExavaultApiRequestSendEnabled()
    {
        return @$this->attributes['exavault_api_request_send_enabled'];
    }

    public function setExavaultApiRequestSendEnabled($value)
    {
        return $this->attributes['exavault_api_request_send_enabled'] = $value;
    }
    // int64 # Number of log entries sent for the lifetime of this destination.
    public function getExavaultApiRequestEntriesSent()
    {
        return @$this->attributes['exavault_api_request_entries_sent'];
    }

    public function setExavaultApiRequestEntriesSent($value)
    {
        return $this->attributes['exavault_api_request_entries_sent'] = $value;
    }
    // boolean # Whether or not sending is enabled for settings_change logs.
    public function getSettingsChangeSendEnabled()
    {
        return @$this->attributes['settings_change_send_enabled'];
    }

    public function setSettingsChangeSendEnabled($value)
    {
        return $this->attributes['settings_change_send_enabled'] = $value;
    }
    // int64 # Number of log entries sent for the lifetime of this destination.
    public function getSettingsChangeEntriesSent()
    {
        return @$this->attributes['settings_change_entries_sent'];
    }

    public function setSettingsChangeEntriesSent($value)
    {
        return $this->attributes['settings_change_entries_sent'] = $value;
    }
    // string # Type of URL that was last called. Can be `destination_url` or `azure_oauth_client_credentials_url`
    public function getLastHttpCallTargetType()
    {
        return @$this->attributes['last_http_call_target_type'];
    }

    public function setLastHttpCallTargetType($value)
    {
        return $this->attributes['last_http_call_target_type'] = $value;
    }
    // boolean # Was the last HTTP call made successful?
    public function getLastHttpCallSuccess()
    {
        return @$this->attributes['last_http_call_success'];
    }

    public function setLastHttpCallSuccess($value)
    {
        return $this->attributes['last_http_call_success'] = $value;
    }
    // int64 # Last HTTP Call Response Code
    public function getLastHttpCallResponseCode()
    {
        return @$this->attributes['last_http_call_response_code'];
    }

    public function setLastHttpCallResponseCode($value)
    {
        return $this->attributes['last_http_call_response_code'] = $value;
    }
    // string # Last HTTP Call Response Body. Large responses are truncated.
    public function getLastHttpCallResponseBody()
    {
        return @$this->attributes['last_http_call_response_body'];
    }

    public function setLastHttpCallResponseBody($value)
    {
        return $this->attributes['last_http_call_response_body'] = $value;
    }
    // string # Last HTTP Call Error Message if applicable
    public function getLastHttpCallErrorMessage()
    {
        return @$this->attributes['last_http_call_error_message'];
    }

    public function setLastHttpCallErrorMessage($value)
    {
        return $this->attributes['last_http_call_error_message'] = $value;
    }
    // string # Time of Last HTTP Call
    public function getLastHttpCallTime()
    {
        return @$this->attributes['last_http_call_time'];
    }

    public function setLastHttpCallTime($value)
    {
        return $this->attributes['last_http_call_time'] = $value;
    }
    // int64 # Duration of the last HTTP Call in milliseconds
    public function getLastHttpCallDurationMs()
    {
        return @$this->attributes['last_http_call_duration_ms'];
    }

    public function setLastHttpCallDurationMs($value)
    {
        return $this->attributes['last_http_call_duration_ms'] = $value;
    }
    // string # Time of Most Recent Successful HTTP Call
    public function getMostRecentHttpCallSuccessTime()
    {
        return @$this->attributes['most_recent_http_call_success_time'];
    }

    public function setMostRecentHttpCallSuccessTime($value)
    {
        return $this->attributes['most_recent_http_call_success_time'] = $value;
    }
    // string # Connection Test Entry
    public function getConnectionTestEntry()
    {
        return @$this->attributes['connection_test_entry'];
    }

    public function setConnectionTestEntry($value)
    {
        return $this->attributes['connection_test_entry'] = $value;
    }
    // string # Applicable only for destination type: splunk. Authentication token provided by Splunk.
    public function getSplunkToken()
    {
        return @$this->attributes['splunk_token'];
    }

    public function setSplunkToken($value)
    {
        return $this->attributes['splunk_token'] = $value;
    }
    // string # Applicable only for destination type: azure. Client Credentials OAuth Client Secret.
    public function getAzureOauthClientCredentialsClientSecret()
    {
        return @$this->attributes['azure_oauth_client_credentials_client_secret'];
    }

    public function setAzureOauthClientCredentialsClientSecret($value)
    {
        return $this->attributes['azure_oauth_client_credentials_client_secret'] = $value;
    }
    // string # Applicable only for destination type: qradar. Basic auth password provided by QRadar.
    public function getQradarPassword()
    {
        return @$this->attributes['qradar_password'];
    }

    public function setQradarPassword($value)
    {
        return $this->attributes['qradar_password'] = $value;
    }
    // string # Applicable only for destination type: solar_winds. Authentication token provided by Solar Winds.
    public function getSolarWindsToken()
    {
        return @$this->attributes['solar_winds_token'];
    }

    public function setSolarWindsToken($value)
    {
        return $this->attributes['solar_winds_token'] = $value;
    }
    // string # Applicable only for destination type: new_relic. API key provided by New Relic.
    public function getNewRelicApiKey()
    {
        return @$this->attributes['new_relic_api_key'];
    }

    public function setNewRelicApiKey($value)
    {
        return $this->attributes['new_relic_api_key'] = $value;
    }
    // string # Applicable only for destination type: datadog. API key provided by Datadog.
    public function getDatadogApiKey()
    {
        return @$this->attributes['datadog_api_key'];
    }

    public function setDatadogApiKey($value)
    {
        return $this->attributes['datadog_api_key'] = $value;
    }

    // Parameters:
    //   name - string - Name for this Destination
    //   additional_headers - object - Additional HTTP Headers included in calls to the destination URL
    //   sending_active - boolean - Whether this SIEM HTTP Destination is currently being sent to or not
    //   generic_payload_type - string - Applicable only for destination type: generic. Indicates the type of HTTP body. Can be json_newline or json_array. json_newline is multiple log entries as JSON separated by newlines. json_array is a single JSON array containing multiple log entries as JSON.
    //   file_destination_path - string - Applicable only for destination type: file. Destination folder path on Files.com.
    //   file_format - string - Applicable only for destination type: file. Generated file format.
    //   file_interval_minutes - int64 - Applicable only for destination type: file. Interval, in minutes, between file deliveries. Valid values are 5, 10, 15, 20, 30, 60, 90, 180, 240, 360.
    //   splunk_token - string - Applicable only for destination type: splunk. Authentication token provided by Splunk.
    //   azure_dcr_immutable_id - string - Applicable only for destination types: azure, azure_legacy. Immutable ID of the Data Collection Rule.
    //   azure_stream_name - string - Applicable only for destination type: azure. Name of the stream in the DCR that represents the destination table.
    //   azure_oauth_client_credentials_tenant_id - string - Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Tenant ID.
    //   azure_oauth_client_credentials_client_id - string - Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Client ID.
    //   azure_oauth_client_credentials_client_secret - string - Applicable only for destination type: azure. Client Credentials OAuth Client Secret.
    //   qradar_username - string - Applicable only for destination type: qradar. Basic auth username provided by QRadar.
    //   qradar_password - string - Applicable only for destination type: qradar. Basic auth password provided by QRadar.
    //   solar_winds_token - string - Applicable only for destination type: solar_winds. Authentication token provided by Solar Winds.
    //   new_relic_api_key - string - Applicable only for destination type: new_relic. API key provided by New Relic.
    //   datadog_api_key - string - Applicable only for destination type: datadog. API key provided by Datadog.
    //   sftp_action_send_enabled - boolean - Whether or not sending is enabled for sftp_action logs.
    //   ftp_action_send_enabled - boolean - Whether or not sending is enabled for ftp_action logs.
    //   web_dav_action_send_enabled - boolean - Whether or not sending is enabled for web_dav_action logs.
    //   sync_send_enabled - boolean - Whether or not sending is enabled for sync logs.
    //   outbound_connection_send_enabled - boolean - Whether or not sending is enabled for outbound_connection logs.
    //   automation_send_enabled - boolean - Whether or not sending is enabled for automation logs.
    //   api_request_send_enabled - boolean - Whether or not sending is enabled for api_request logs.
    //   public_hosting_request_send_enabled - boolean - Whether or not sending is enabled for public_hosting_request logs.
    //   email_send_enabled - boolean - Whether or not sending is enabled for email logs.
    //   exavault_api_request_send_enabled - boolean - Whether or not sending is enabled for exavault_api_request logs.
    //   settings_change_send_enabled - boolean - Whether or not sending is enabled for settings_change logs.
    //   destination_type - string - Destination Type
    //   destination_url - string - Destination Url
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

        if (@$params['generic_payload_type'] && !is_string(@$params['generic_payload_type'])) {
            throw new \Files\Exception\InvalidParameterException('$generic_payload_type must be of type string; received ' . gettype(@$params['generic_payload_type']));
        }

        if (@$params['file_destination_path'] && !is_string(@$params['file_destination_path'])) {
            throw new \Files\Exception\InvalidParameterException('$file_destination_path must be of type string; received ' . gettype(@$params['file_destination_path']));
        }

        if (@$params['file_format'] && !is_string(@$params['file_format'])) {
            throw new \Files\Exception\InvalidParameterException('$file_format must be of type string; received ' . gettype(@$params['file_format']));
        }

        if (@$params['file_interval_minutes'] && !is_int(@$params['file_interval_minutes'])) {
            throw new \Files\Exception\InvalidParameterException('$file_interval_minutes must be of type int; received ' . gettype(@$params['file_interval_minutes']));
        }

        if (@$params['splunk_token'] && !is_string(@$params['splunk_token'])) {
            throw new \Files\Exception\InvalidParameterException('$splunk_token must be of type string; received ' . gettype(@$params['splunk_token']));
        }

        if (@$params['azure_dcr_immutable_id'] && !is_string(@$params['azure_dcr_immutable_id'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_dcr_immutable_id must be of type string; received ' . gettype(@$params['azure_dcr_immutable_id']));
        }

        if (@$params['azure_stream_name'] && !is_string(@$params['azure_stream_name'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_stream_name must be of type string; received ' . gettype(@$params['azure_stream_name']));
        }

        if (@$params['azure_oauth_client_credentials_tenant_id'] && !is_string(@$params['azure_oauth_client_credentials_tenant_id'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_oauth_client_credentials_tenant_id must be of type string; received ' . gettype(@$params['azure_oauth_client_credentials_tenant_id']));
        }

        if (@$params['azure_oauth_client_credentials_client_id'] && !is_string(@$params['azure_oauth_client_credentials_client_id'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_oauth_client_credentials_client_id must be of type string; received ' . gettype(@$params['azure_oauth_client_credentials_client_id']));
        }

        if (@$params['azure_oauth_client_credentials_client_secret'] && !is_string(@$params['azure_oauth_client_credentials_client_secret'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_oauth_client_credentials_client_secret must be of type string; received ' . gettype(@$params['azure_oauth_client_credentials_client_secret']));
        }

        if (@$params['qradar_username'] && !is_string(@$params['qradar_username'])) {
            throw new \Files\Exception\InvalidParameterException('$qradar_username must be of type string; received ' . gettype(@$params['qradar_username']));
        }

        if (@$params['qradar_password'] && !is_string(@$params['qradar_password'])) {
            throw new \Files\Exception\InvalidParameterException('$qradar_password must be of type string; received ' . gettype(@$params['qradar_password']));
        }

        if (@$params['solar_winds_token'] && !is_string(@$params['solar_winds_token'])) {
            throw new \Files\Exception\InvalidParameterException('$solar_winds_token must be of type string; received ' . gettype(@$params['solar_winds_token']));
        }

        if (@$params['new_relic_api_key'] && !is_string(@$params['new_relic_api_key'])) {
            throw new \Files\Exception\InvalidParameterException('$new_relic_api_key must be of type string; received ' . gettype(@$params['new_relic_api_key']));
        }

        if (@$params['datadog_api_key'] && !is_string(@$params['datadog_api_key'])) {
            throw new \Files\Exception\InvalidParameterException('$datadog_api_key must be of type string; received ' . gettype(@$params['datadog_api_key']));
        }

        if (@$params['destination_type'] && !is_string(@$params['destination_type'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_type must be of type string; received ' . gettype(@$params['destination_type']));
        }

        if (@$params['destination_url'] && !is_string(@$params['destination_url'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_url must be of type string; received ' . gettype(@$params['destination_url']));
        }

        $response = Api::sendRequest('/siem_http_destinations/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new SiemHttpDestination((array) (@$response->data ?: []), $this->options);
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

        $response = Api::sendRequest('/siem_http_destinations/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/siem_http_destinations', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new SiemHttpDestination((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Siem Http Destination ID.
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

        $response = Api::sendRequest('/siem_http_destinations/' . @$params['id'] . '', 'GET', $params, $options);

        return new SiemHttpDestination((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   name - string - Name for this Destination
    //   additional_headers - object - Additional HTTP Headers included in calls to the destination URL
    //   sending_active - boolean - Whether this SIEM HTTP Destination is currently being sent to or not
    //   generic_payload_type - string - Applicable only for destination type: generic. Indicates the type of HTTP body. Can be json_newline or json_array. json_newline is multiple log entries as JSON separated by newlines. json_array is a single JSON array containing multiple log entries as JSON.
    //   file_destination_path - string - Applicable only for destination type: file. Destination folder path on Files.com.
    //   file_format - string - Applicable only for destination type: file. Generated file format.
    //   file_interval_minutes - int64 - Applicable only for destination type: file. Interval, in minutes, between file deliveries. Valid values are 5, 10, 15, 20, 30, 60, 90, 180, 240, 360.
    //   splunk_token - string - Applicable only for destination type: splunk. Authentication token provided by Splunk.
    //   azure_dcr_immutable_id - string - Applicable only for destination types: azure, azure_legacy. Immutable ID of the Data Collection Rule.
    //   azure_stream_name - string - Applicable only for destination type: azure. Name of the stream in the DCR that represents the destination table.
    //   azure_oauth_client_credentials_tenant_id - string - Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Tenant ID.
    //   azure_oauth_client_credentials_client_id - string - Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Client ID.
    //   azure_oauth_client_credentials_client_secret - string - Applicable only for destination type: azure. Client Credentials OAuth Client Secret.
    //   qradar_username - string - Applicable only for destination type: qradar. Basic auth username provided by QRadar.
    //   qradar_password - string - Applicable only for destination type: qradar. Basic auth password provided by QRadar.
    //   solar_winds_token - string - Applicable only for destination type: solar_winds. Authentication token provided by Solar Winds.
    //   new_relic_api_key - string - Applicable only for destination type: new_relic. API key provided by New Relic.
    //   datadog_api_key - string - Applicable only for destination type: datadog. API key provided by Datadog.
    //   sftp_action_send_enabled - boolean - Whether or not sending is enabled for sftp_action logs.
    //   ftp_action_send_enabled - boolean - Whether or not sending is enabled for ftp_action logs.
    //   web_dav_action_send_enabled - boolean - Whether or not sending is enabled for web_dav_action logs.
    //   sync_send_enabled - boolean - Whether or not sending is enabled for sync logs.
    //   outbound_connection_send_enabled - boolean - Whether or not sending is enabled for outbound_connection logs.
    //   automation_send_enabled - boolean - Whether or not sending is enabled for automation logs.
    //   api_request_send_enabled - boolean - Whether or not sending is enabled for api_request logs.
    //   public_hosting_request_send_enabled - boolean - Whether or not sending is enabled for public_hosting_request logs.
    //   email_send_enabled - boolean - Whether or not sending is enabled for email logs.
    //   exavault_api_request_send_enabled - boolean - Whether or not sending is enabled for exavault_api_request logs.
    //   settings_change_send_enabled - boolean - Whether or not sending is enabled for settings_change logs.
    //   destination_type (required) - string - Destination Type
    //   destination_url - string - Destination Url
    public static function create($params = [], $options = [])
    {
        if (!@$params['destination_type']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: destination_type');
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['generic_payload_type'] && !is_string(@$params['generic_payload_type'])) {
            throw new \Files\Exception\InvalidParameterException('$generic_payload_type must be of type string; received ' . gettype(@$params['generic_payload_type']));
        }

        if (@$params['file_destination_path'] && !is_string(@$params['file_destination_path'])) {
            throw new \Files\Exception\InvalidParameterException('$file_destination_path must be of type string; received ' . gettype(@$params['file_destination_path']));
        }

        if (@$params['file_format'] && !is_string(@$params['file_format'])) {
            throw new \Files\Exception\InvalidParameterException('$file_format must be of type string; received ' . gettype(@$params['file_format']));
        }

        if (@$params['file_interval_minutes'] && !is_int(@$params['file_interval_minutes'])) {
            throw new \Files\Exception\InvalidParameterException('$file_interval_minutes must be of type int; received ' . gettype(@$params['file_interval_minutes']));
        }

        if (@$params['splunk_token'] && !is_string(@$params['splunk_token'])) {
            throw new \Files\Exception\InvalidParameterException('$splunk_token must be of type string; received ' . gettype(@$params['splunk_token']));
        }

        if (@$params['azure_dcr_immutable_id'] && !is_string(@$params['azure_dcr_immutable_id'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_dcr_immutable_id must be of type string; received ' . gettype(@$params['azure_dcr_immutable_id']));
        }

        if (@$params['azure_stream_name'] && !is_string(@$params['azure_stream_name'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_stream_name must be of type string; received ' . gettype(@$params['azure_stream_name']));
        }

        if (@$params['azure_oauth_client_credentials_tenant_id'] && !is_string(@$params['azure_oauth_client_credentials_tenant_id'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_oauth_client_credentials_tenant_id must be of type string; received ' . gettype(@$params['azure_oauth_client_credentials_tenant_id']));
        }

        if (@$params['azure_oauth_client_credentials_client_id'] && !is_string(@$params['azure_oauth_client_credentials_client_id'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_oauth_client_credentials_client_id must be of type string; received ' . gettype(@$params['azure_oauth_client_credentials_client_id']));
        }

        if (@$params['azure_oauth_client_credentials_client_secret'] && !is_string(@$params['azure_oauth_client_credentials_client_secret'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_oauth_client_credentials_client_secret must be of type string; received ' . gettype(@$params['azure_oauth_client_credentials_client_secret']));
        }

        if (@$params['qradar_username'] && !is_string(@$params['qradar_username'])) {
            throw new \Files\Exception\InvalidParameterException('$qradar_username must be of type string; received ' . gettype(@$params['qradar_username']));
        }

        if (@$params['qradar_password'] && !is_string(@$params['qradar_password'])) {
            throw new \Files\Exception\InvalidParameterException('$qradar_password must be of type string; received ' . gettype(@$params['qradar_password']));
        }

        if (@$params['solar_winds_token'] && !is_string(@$params['solar_winds_token'])) {
            throw new \Files\Exception\InvalidParameterException('$solar_winds_token must be of type string; received ' . gettype(@$params['solar_winds_token']));
        }

        if (@$params['new_relic_api_key'] && !is_string(@$params['new_relic_api_key'])) {
            throw new \Files\Exception\InvalidParameterException('$new_relic_api_key must be of type string; received ' . gettype(@$params['new_relic_api_key']));
        }

        if (@$params['datadog_api_key'] && !is_string(@$params['datadog_api_key'])) {
            throw new \Files\Exception\InvalidParameterException('$datadog_api_key must be of type string; received ' . gettype(@$params['datadog_api_key']));
        }

        if (@$params['destination_type'] && !is_string(@$params['destination_type'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_type must be of type string; received ' . gettype(@$params['destination_type']));
        }

        if (@$params['destination_url'] && !is_string(@$params['destination_url'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_url must be of type string; received ' . gettype(@$params['destination_url']));
        }

        $response = Api::sendRequest('/siem_http_destinations', 'POST', $params, $options);

        return new SiemHttpDestination((array) (@$response->data ?: []), $options);
    }

    // Parameters:
    //   siem_http_destination_id - int64 - SIEM HTTP Destination ID
    //   destination_type - string - Destination Type
    //   destination_url - string - Destination Url
    //   name - string - Name for this Destination
    //   additional_headers - object - Additional HTTP Headers included in calls to the destination URL
    //   sending_active - boolean - Whether this SIEM HTTP Destination is currently being sent to or not
    //   generic_payload_type - string - Applicable only for destination type: generic. Indicates the type of HTTP body. Can be json_newline or json_array. json_newline is multiple log entries as JSON separated by newlines. json_array is a single JSON array containing multiple log entries as JSON.
    //   file_destination_path - string - Applicable only for destination type: file. Destination folder path on Files.com.
    //   file_format - string - Applicable only for destination type: file. Generated file format.
    //   file_interval_minutes - int64 - Applicable only for destination type: file. Interval, in minutes, between file deliveries. Valid values are 5, 10, 15, 20, 30, 60, 90, 180, 240, 360.
    //   splunk_token - string - Applicable only for destination type: splunk. Authentication token provided by Splunk.
    //   azure_dcr_immutable_id - string - Applicable only for destination types: azure, azure_legacy. Immutable ID of the Data Collection Rule.
    //   azure_stream_name - string - Applicable only for destination type: azure. Name of the stream in the DCR that represents the destination table.
    //   azure_oauth_client_credentials_tenant_id - string - Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Tenant ID.
    //   azure_oauth_client_credentials_client_id - string - Applicable only for destination types: azure, azure_legacy. Client Credentials OAuth Client ID.
    //   azure_oauth_client_credentials_client_secret - string - Applicable only for destination type: azure. Client Credentials OAuth Client Secret.
    //   qradar_username - string - Applicable only for destination type: qradar. Basic auth username provided by QRadar.
    //   qradar_password - string - Applicable only for destination type: qradar. Basic auth password provided by QRadar.
    //   solar_winds_token - string - Applicable only for destination type: solar_winds. Authentication token provided by Solar Winds.
    //   new_relic_api_key - string - Applicable only for destination type: new_relic. API key provided by New Relic.
    //   datadog_api_key - string - Applicable only for destination type: datadog. API key provided by Datadog.
    //   sftp_action_send_enabled - boolean - Whether or not sending is enabled for sftp_action logs.
    //   ftp_action_send_enabled - boolean - Whether or not sending is enabled for ftp_action logs.
    //   web_dav_action_send_enabled - boolean - Whether or not sending is enabled for web_dav_action logs.
    //   sync_send_enabled - boolean - Whether or not sending is enabled for sync logs.
    //   outbound_connection_send_enabled - boolean - Whether or not sending is enabled for outbound_connection logs.
    //   automation_send_enabled - boolean - Whether or not sending is enabled for automation logs.
    //   api_request_send_enabled - boolean - Whether or not sending is enabled for api_request logs.
    //   public_hosting_request_send_enabled - boolean - Whether or not sending is enabled for public_hosting_request logs.
    //   email_send_enabled - boolean - Whether or not sending is enabled for email logs.
    //   exavault_api_request_send_enabled - boolean - Whether or not sending is enabled for exavault_api_request logs.
    //   settings_change_send_enabled - boolean - Whether or not sending is enabled for settings_change logs.
    public static function sendTestEntry($params = [], $options = [])
    {
        if (@$params['siem_http_destination_id'] && !is_int(@$params['siem_http_destination_id'])) {
            throw new \Files\Exception\InvalidParameterException('$siem_http_destination_id must be of type int; received ' . gettype(@$params['siem_http_destination_id']));
        }

        if (@$params['destination_type'] && !is_string(@$params['destination_type'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_type must be of type string; received ' . gettype(@$params['destination_type']));
        }

        if (@$params['destination_url'] && !is_string(@$params['destination_url'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_url must be of type string; received ' . gettype(@$params['destination_url']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['generic_payload_type'] && !is_string(@$params['generic_payload_type'])) {
            throw new \Files\Exception\InvalidParameterException('$generic_payload_type must be of type string; received ' . gettype(@$params['generic_payload_type']));
        }

        if (@$params['file_destination_path'] && !is_string(@$params['file_destination_path'])) {
            throw new \Files\Exception\InvalidParameterException('$file_destination_path must be of type string; received ' . gettype(@$params['file_destination_path']));
        }

        if (@$params['file_format'] && !is_string(@$params['file_format'])) {
            throw new \Files\Exception\InvalidParameterException('$file_format must be of type string; received ' . gettype(@$params['file_format']));
        }

        if (@$params['file_interval_minutes'] && !is_int(@$params['file_interval_minutes'])) {
            throw new \Files\Exception\InvalidParameterException('$file_interval_minutes must be of type int; received ' . gettype(@$params['file_interval_minutes']));
        }

        if (@$params['splunk_token'] && !is_string(@$params['splunk_token'])) {
            throw new \Files\Exception\InvalidParameterException('$splunk_token must be of type string; received ' . gettype(@$params['splunk_token']));
        }

        if (@$params['azure_dcr_immutable_id'] && !is_string(@$params['azure_dcr_immutable_id'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_dcr_immutable_id must be of type string; received ' . gettype(@$params['azure_dcr_immutable_id']));
        }

        if (@$params['azure_stream_name'] && !is_string(@$params['azure_stream_name'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_stream_name must be of type string; received ' . gettype(@$params['azure_stream_name']));
        }

        if (@$params['azure_oauth_client_credentials_tenant_id'] && !is_string(@$params['azure_oauth_client_credentials_tenant_id'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_oauth_client_credentials_tenant_id must be of type string; received ' . gettype(@$params['azure_oauth_client_credentials_tenant_id']));
        }

        if (@$params['azure_oauth_client_credentials_client_id'] && !is_string(@$params['azure_oauth_client_credentials_client_id'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_oauth_client_credentials_client_id must be of type string; received ' . gettype(@$params['azure_oauth_client_credentials_client_id']));
        }

        if (@$params['azure_oauth_client_credentials_client_secret'] && !is_string(@$params['azure_oauth_client_credentials_client_secret'])) {
            throw new \Files\Exception\InvalidParameterException('$azure_oauth_client_credentials_client_secret must be of type string; received ' . gettype(@$params['azure_oauth_client_credentials_client_secret']));
        }

        if (@$params['qradar_username'] && !is_string(@$params['qradar_username'])) {
            throw new \Files\Exception\InvalidParameterException('$qradar_username must be of type string; received ' . gettype(@$params['qradar_username']));
        }

        if (@$params['qradar_password'] && !is_string(@$params['qradar_password'])) {
            throw new \Files\Exception\InvalidParameterException('$qradar_password must be of type string; received ' . gettype(@$params['qradar_password']));
        }

        if (@$params['solar_winds_token'] && !is_string(@$params['solar_winds_token'])) {
            throw new \Files\Exception\InvalidParameterException('$solar_winds_token must be of type string; received ' . gettype(@$params['solar_winds_token']));
        }

        if (@$params['new_relic_api_key'] && !is_string(@$params['new_relic_api_key'])) {
            throw new \Files\Exception\InvalidParameterException('$new_relic_api_key must be of type string; received ' . gettype(@$params['new_relic_api_key']));
        }

        if (@$params['datadog_api_key'] && !is_string(@$params['datadog_api_key'])) {
            throw new \Files\Exception\InvalidParameterException('$datadog_api_key must be of type string; received ' . gettype(@$params['datadog_api_key']));
        }

        $response = Api::sendRequest('/siem_http_destinations/send_test_entry', 'POST', $params, $options);

        return;
    }
}
