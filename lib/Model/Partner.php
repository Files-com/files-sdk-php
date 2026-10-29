<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Partner
 *
 * @package Files
 */
class Partner
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
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
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
    // boolean # Allow Partner Admins to change Two-Factor Authentication requirements for Partner Users.
    public function getAllowBypassing2faPolicies()
    {
        return isset($this->attributes['allow_bypassing_2fa_policies']) ? $this->attributes['allow_bypassing_2fa_policies'] : null;
    }

    public function setAllowBypassing2faPolicies($value)
    {
        return $this->attributes['allow_bypassing_2fa_policies'] = $value;
    }
    // string # A list of allowed IPs for this Partner. Newline delimited. Partner User IP access is allowed when the IP matches the Partner, User, or Site allowed IP lists.
    public function getAllowedIps()
    {
        return isset($this->attributes['allowed_ips']) ? $this->attributes['allowed_ips'] : null;
    }

    public function setAllowedIps($value)
    {
        return $this->attributes['allowed_ips'] = $value;
    }
    // boolean # Allow Partner Admins to change or reset credentials for users belonging to this Partner.
    public function getAllowCredentialChanges()
    {
        return isset($this->attributes['allow_credential_changes']) ? $this->attributes['allow_credential_changes'] : null;
    }

    public function setAllowCredentialChanges($value)
    {
        return $this->attributes['allow_credential_changes'] = $value;
    }
    // boolean # Allow Partner Admins to provide GPG keys.
    public function getAllowProvidingGpgKeys()
    {
        return isset($this->attributes['allow_providing_gpg_keys']) ? $this->attributes['allow_providing_gpg_keys'] : null;
    }

    public function setAllowProvidingGpgKeys($value)
    {
        return $this->attributes['allow_providing_gpg_keys'] = $value;
    }
    // boolean # Allow Partner Admins to create users.
    public function getAllowUserCreation()
    {
        return isset($this->attributes['allow_user_creation']) ? $this->attributes['allow_user_creation'] : null;
    }

    public function setAllowUserCreation($value)
    {
        return $this->attributes['allow_user_creation'] = $value;
    }
    // boolean # When `true`, emails sent to Partner users are copied to the responsible User or Group.
    public function getCcEmailsToResponsibleParty()
    {
        return isset($this->attributes['cc_emails_to_responsible_party']) ? $this->attributes['cc_emails_to_responsible_party'] : null;
    }

    public function setCcEmailsToResponsibleParty($value)
    {
        return $this->attributes['cc_emails_to_responsible_party'] = $value;
    }
    // array(object) # Approved Connected Sites relationships for this Partner, in both directions. Empty when this Partner has no connections. Read-only.
    public function getConnections()
    {
        return isset($this->attributes['connections']) ? $this->attributes['connections'] : null;
    }

    public function setConnections($value)
    {
        return $this->attributes['connections'] = $value;
    }
    // int64 # The unique ID of the Partner.
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // int64 # AI Assistant Personality ID assigned to this Partner, if any. Users in the Partner inherit it unless a direct per-user assignment overrides it.
    public function getAiAssistantPersonalityId()
    {
        return isset($this->attributes['ai_assistant_personality_id']) ? $this->attributes['ai_assistant_personality_id'] : null;
    }

    public function setAiAssistantPersonalityId($value)
    {
        return $this->attributes['ai_assistant_personality_id'] = $value;
    }
    // int64 # ID of the Workspace associated with this Partner.
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    // string # The name of the Partner.
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // string # Notes about this Partner.
    public function getNotes()
    {
        return isset($this->attributes['notes']) ? $this->attributes['notes'] : null;
    }

    public function setNotes($value)
    {
        return $this->attributes['notes'] = $value;
    }
    // array(int64) # Array of User IDs that are Partner Admins for this Partner.
    public function getPartnerAdminIds()
    {
        return isset($this->attributes['partner_admin_ids']) ? $this->attributes['partner_admin_ids'] : null;
    }

    public function setPartnerAdminIds($value)
    {
        return $this->attributes['partner_admin_ids'] = $value;
    }
    // int64 # ID of the Partner Channel Template assigned to this Partner.
    public function getPartnerChannelTemplateId()
    {
        return isset($this->attributes['partner_channel_template_id']) ? $this->attributes['partner_channel_template_id'] : null;
    }

    public function setPartnerChannelTemplateId($value)
    {
        return $this->attributes['partner_channel_template_id'] = $value;
    }
    // string # This site's role for this Partner in Connected Sites relationships. `host` is a Partner this site configured. `guest` is a Partner created by approving another site's connection request; it has no root folder and cannot hold users, permissions, or Partner Channels, or host a connection. `host_and_guest` is a configured Partner that is also the guest side of a connection. Promote a `guest` Partner by setting this to `host_and_guest` together with a `root_folder`.
    public function getPartnershipRole()
    {
        return isset($this->attributes['partnership_role']) ? $this->attributes['partnership_role'] : null;
    }

    public function setPartnershipRole($value)
    {
        return $this->attributes['partnership_role'] = $value;
    }
    // int64 # ID of the Group responsible for this Partner.
    public function getResponsibleGroupId()
    {
        return isset($this->attributes['responsible_group_id']) ? $this->attributes['responsible_group_id'] : null;
    }

    public function setResponsibleGroupId($value)
    {
        return $this->attributes['responsible_group_id'] = $value;
    }
    // int64 # ID of the User responsible for this Partner.
    public function getResponsibleUserId()
    {
        return isset($this->attributes['responsible_user_id']) ? $this->attributes['responsible_user_id'] : null;
    }

    public function setResponsibleUserId($value)
    {
        return $this->attributes['responsible_user_id'] = $value;
    }
    // string # The root folder path for this Partner.
    public function getRootFolder()
    {
        return isset($this->attributes['root_folder']) ? $this->attributes['root_folder'] : null;
    }

    public function setRootFolder($value)
    {
        return $this->attributes['root_folder'] = $value;
    }
    // boolean # Show Partner users a simplified home page built from this Partner's Channels.
    public function getShowPartnerChannelHomePage()
    {
        return isset($this->attributes['show_partner_channel_home_page']) ? $this->attributes['show_partner_channel_home_page'] : null;
    }

    public function setShowPartnerChannelHomePage($value)
    {
        return $this->attributes['show_partner_channel_home_page'] = $value;
    }
    // string # Comma-separated list of Tags for this Partner. Tags are used for other features, such as UserLifecycleRules, which can target specific tags.  Tags must only contain lowercase letters, numbers, and hyphens.
    public function getTags()
    {
        return isset($this->attributes['tags']) ? $this->attributes['tags'] : null;
    }

    public function setTags($value)
    {
        return $this->attributes['tags'] = $value;
    }
    // array(int64) # Array of User IDs that belong to this Partner.
    public function getUserIds()
    {
        return isset($this->attributes['user_ids']) ? $this->attributes['user_ids'] : null;
    }

    public function setUserIds($value)
    {
        return $this->attributes['user_ids'] = $value;
    }

    // Parameters:
    //   ai_assistant_personality_id - int64 - AI Assistant Personality ID assigned to this Partner, if any. Users in the Partner inherit it unless a direct per-user assignment overrides it.
    //   allowed_ips - string - A list of allowed IPs for this Partner. Newline delimited. Partner User IP access is allowed when the IP matches the Partner, User, or Site allowed IP lists.
    //   allow_bypassing_2fa_policies - boolean - Allow Partner Admins to change Two-Factor Authentication requirements for Partner Users.
    //   allow_credential_changes - boolean - Allow Partner Admins to change or reset credentials for users belonging to this Partner.
    //   allow_providing_gpg_keys - boolean - Allow Partner Admins to provide GPG keys.
    //   allow_user_creation - boolean - Allow Partner Admins to create users.
    //   cc_emails_to_responsible_party - boolean - When `true`, emails sent to Partner users are copied to the responsible User or Group.
    //   notes - string - Notes about this Partner.
    //   partner_channel_template_id - int64 - ID of the Partner Channel Template assigned to this Partner.
    //   responsible_group_id - int64 - ID of the Group responsible for this Partner.
    //   responsible_user_id - int64 - ID of the User responsible for this Partner.
    //   show_partner_channel_home_page - boolean - Show Partner users a simplified home page built from this Partner's Channels.
    //   tags - string - Comma-separated list of Tags for this Partner. Tags are used for other features, such as UserLifecycleRules, which can target specific tags.  Tags must only contain lowercase letters, numbers, and hyphens.
    //   name - string - The name of the Partner.
    //   partnership_role - string - This site's role for this Partner in Connected Sites relationships. `host` is a Partner this site configured. `guest` is a Partner created by approving another site's connection request; it has no root folder and cannot hold users, permissions, or Partner Channels, or host a connection. `host_and_guest` is a configured Partner that is also the guest side of a connection. Promote a `guest` Partner by setting this to `host_and_guest` together with a `root_folder`.
    //   root_folder - string - The root folder path for this Partner.
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

        if (!empty($params['ai_assistant_personality_id']) && !is_int($params['ai_assistant_personality_id'])) {
            throw new \Files\Exception\InvalidParameterException('$ai_assistant_personality_id must be of type int; received ' . gettype(@$params['ai_assistant_personality_id']));
        }

        if (!empty($params['allowed_ips']) && !is_string($params['allowed_ips'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_ips must be of type string; received ' . gettype(@$params['allowed_ips']));
        }

        if (!empty($params['notes']) && !is_string($params['notes'])) {
            throw new \Files\Exception\InvalidParameterException('$notes must be of type string; received ' . gettype(@$params['notes']));
        }

        if (!empty($params['partner_channel_template_id']) && !is_int($params['partner_channel_template_id'])) {
            throw new \Files\Exception\InvalidParameterException('$partner_channel_template_id must be of type int; received ' . gettype(@$params['partner_channel_template_id']));
        }

        if (!empty($params['responsible_group_id']) && !is_int($params['responsible_group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$responsible_group_id must be of type int; received ' . gettype(@$params['responsible_group_id']));
        }

        if (!empty($params['responsible_user_id']) && !is_int($params['responsible_user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$responsible_user_id must be of type int; received ' . gettype(@$params['responsible_user_id']));
        }

        if (!empty($params['tags']) && !is_string($params['tags'])) {
            throw new \Files\Exception\InvalidParameterException('$tags must be of type string; received ' . gettype(@$params['tags']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['partnership_role']) && !is_string($params['partnership_role'])) {
            throw new \Files\Exception\InvalidParameterException('$partnership_role must be of type string; received ' . gettype(@$params['partnership_role']));
        }

        if (!empty($params['root_folder']) && !is_string($params['root_folder'])) {
            throw new \Files\Exception\InvalidParameterException('$root_folder must be of type string; received ' . gettype(@$params['root_folder']));
        }

        $response = Api::sendRequest('/partners/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new Partner((array) (@$response->data ?: []), $this->options);
    }

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

        $response = Api::sendRequest('/partners/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
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
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id` and `name`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `workspace_id`.
    public static function all($params = [], $options = [])
    {
        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/partners', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Partner((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Partner ID.
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

        $response = Api::sendRequest('/partners/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new Partner((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   ai_assistant_personality_id - int64 - AI Assistant Personality ID assigned to this Partner, if any. Users in the Partner inherit it unless a direct per-user assignment overrides it.
    //   allowed_ips - string - A list of allowed IPs for this Partner. Newline delimited. Partner User IP access is allowed when the IP matches the Partner, User, or Site allowed IP lists.
    //   allow_bypassing_2fa_policies - boolean - Allow Partner Admins to change Two-Factor Authentication requirements for Partner Users.
    //   allow_credential_changes - boolean - Allow Partner Admins to change or reset credentials for users belonging to this Partner.
    //   allow_providing_gpg_keys - boolean - Allow Partner Admins to provide GPG keys.
    //   allow_user_creation - boolean - Allow Partner Admins to create users.
    //   cc_emails_to_responsible_party - boolean - When `true`, emails sent to Partner users are copied to the responsible User or Group.
    //   notes - string - Notes about this Partner.
    //   partner_channel_template_id - int64 - ID of the Partner Channel Template assigned to this Partner.
    //   responsible_group_id - int64 - ID of the Group responsible for this Partner.
    //   responsible_user_id - int64 - ID of the User responsible for this Partner.
    //   show_partner_channel_home_page - boolean - Show Partner users a simplified home page built from this Partner's Channels.
    //   tags - string - Comma-separated list of Tags for this Partner. Tags are used for other features, such as UserLifecycleRules, which can target specific tags.  Tags must only contain lowercase letters, numbers, and hyphens.
    //   name (required) - string - The name of the Partner.
    //   root_folder (required) - string - The root folder path for this Partner.
    //   workspace_id - int64 - ID of the Workspace associated with this Partner.
    public static function create($params = [], $options = [])
    {
        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (empty($params['root_folder'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: root_folder');
        }

        if (!empty($params['ai_assistant_personality_id']) && !is_int($params['ai_assistant_personality_id'])) {
            throw new \Files\Exception\InvalidParameterException('$ai_assistant_personality_id must be of type int; received ' . gettype(@$params['ai_assistant_personality_id']));
        }

        if (!empty($params['allowed_ips']) && !is_string($params['allowed_ips'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_ips must be of type string; received ' . gettype(@$params['allowed_ips']));
        }

        if (!empty($params['notes']) && !is_string($params['notes'])) {
            throw new \Files\Exception\InvalidParameterException('$notes must be of type string; received ' . gettype(@$params['notes']));
        }

        if (!empty($params['partner_channel_template_id']) && !is_int($params['partner_channel_template_id'])) {
            throw new \Files\Exception\InvalidParameterException('$partner_channel_template_id must be of type int; received ' . gettype(@$params['partner_channel_template_id']));
        }

        if (!empty($params['responsible_group_id']) && !is_int($params['responsible_group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$responsible_group_id must be of type int; received ' . gettype(@$params['responsible_group_id']));
        }

        if (!empty($params['responsible_user_id']) && !is_int($params['responsible_user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$responsible_user_id must be of type int; received ' . gettype(@$params['responsible_user_id']));
        }

        if (!empty($params['tags']) && !is_string($params['tags'])) {
            throw new \Files\Exception\InvalidParameterException('$tags must be of type string; received ' . gettype(@$params['tags']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['root_folder']) && !is_string($params['root_folder'])) {
            throw new \Files\Exception\InvalidParameterException('$root_folder must be of type string; received ' . gettype(@$params['root_folder']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/partners', 'POST', $params, $options);

        return new Partner((array) (@$response->data ?: []), $options);
    }
}
