<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Partner is a first-class entity that cleanly represents an external organization, enables delegated administration, and enforces strict boundaries.
 *
 * @property bool|null $allow_bypassing_2fa_policies Allow Partner Admins to change Two-Factor Authentication requirements for Partner Users.
 * @property string|null $allowed_ips A list of allowed IPs for this Partner. Newline delimited. Partner User IP access is allowed when the IP matches the Partner, User, or Site allowed IP lists.
 * @property bool|null $allow_credential_changes Allow Partner Admins to change or reset credentials for users belonging to this Partner.
 * @property bool|null $allow_providing_gpg_keys Allow Partner Admins to provide GPG keys.
 * @property bool|null $allow_user_creation Allow Partner Admins to create users.
 * @property bool|null $cc_emails_to_responsible_party When `true`, emails sent to Partner users are copied to the responsible User or Group.
 * @property object[]|null $connections Approved Connected Sites relationships for this Partner, in both directions. Empty when this Partner has no connections. Read-only.
 * @property int|null $id The unique ID of the Partner.
 * @property int|null $ai_assistant_personality_id AI Assistant Personality ID assigned to this Partner, if any. Users in the Partner inherit it unless a direct per-user assignment overrides it.
 * @property int|null $workspace_id ID of the Workspace associated with this Partner.
 * @property string|null $name The name of the Partner.
 * @property string|null $notes Notes about this Partner.
 * @property int[]|null $partner_admin_ids Array of User IDs that are Partner Admins for this Partner.
 * @property int|null $partner_channel_template_id ID of the Partner Channel Template assigned to this Partner.
 * @property string|null $partnership_role This site's role for this Partner in Connected Sites relationships. `host` is a Partner this site configured. `guest` is a Partner created by approving another site's connection request; it has no root folder and cannot hold users, permissions, or Partner Channels, or host a connection. `host_and_guest` is a configured Partner that is also the guest side of a connection. Promote a `guest` Partner by setting this to `host_and_guest` together with a `root_folder`.
 * @property int|null $responsible_group_id ID of the Group responsible for this Partner.
 * @property int|null $responsible_user_id ID of the User responsible for this Partner.
 * @property string|null $root_folder The root folder path for this Partner.
 * @property bool|null $show_partner_channel_home_page Show Partner users a simplified home page built from this Partner's Channels.
 * @property string|null $tags Comma-separated list of Tags for this Partner. Tags are used for other features, such as UserLifecycleRules, which can target specific tags. Tags must only contain lowercase letters, numbers, and hyphens.
 * @property int[]|null $user_ids Array of User IDs that belong to this Partner.
 * @package Files
 */
class Partner
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
     * Allow Partner Admins to change Two-Factor Authentication requirements for Partner Users.
     *
     * @return bool|null
     */
    public function getAllowBypassing2faPolicies()
    {
        return isset($this->attributes['allow_bypassing_2fa_policies']) ? $this->attributes['allow_bypassing_2fa_policies'] : null;
    }

    /**
     * Allow Partner Admins to change Two-Factor Authentication requirements for Partner Users.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setAllowBypassing2faPolicies($value)
    {
        return $this->attributes['allow_bypassing_2fa_policies'] = $value;
    }
    /**
     * A list of allowed IPs for this Partner. Newline delimited. Partner User IP access is allowed when the IP matches the Partner, User, or Site allowed IP lists.
     *
     * @return string|null
     */
    public function getAllowedIps()
    {
        return isset($this->attributes['allowed_ips']) ? $this->attributes['allowed_ips'] : null;
    }

    /**
     * A list of allowed IPs for this Partner. Newline delimited. Partner User IP access is allowed when the IP matches the Partner, User, or Site allowed IP lists.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAllowedIps($value)
    {
        return $this->attributes['allowed_ips'] = $value;
    }
    /**
     * Allow Partner Admins to change or reset credentials for users belonging to this Partner.
     *
     * @return bool|null
     */
    public function getAllowCredentialChanges()
    {
        return isset($this->attributes['allow_credential_changes']) ? $this->attributes['allow_credential_changes'] : null;
    }

    /**
     * Allow Partner Admins to change or reset credentials for users belonging to this Partner.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setAllowCredentialChanges($value)
    {
        return $this->attributes['allow_credential_changes'] = $value;
    }
    /**
     * Allow Partner Admins to provide GPG keys.
     *
     * @return bool|null
     */
    public function getAllowProvidingGpgKeys()
    {
        return isset($this->attributes['allow_providing_gpg_keys']) ? $this->attributes['allow_providing_gpg_keys'] : null;
    }

    /**
     * Allow Partner Admins to provide GPG keys.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setAllowProvidingGpgKeys($value)
    {
        return $this->attributes['allow_providing_gpg_keys'] = $value;
    }
    /**
     * Allow Partner Admins to create users.
     *
     * @return bool|null
     */
    public function getAllowUserCreation()
    {
        return isset($this->attributes['allow_user_creation']) ? $this->attributes['allow_user_creation'] : null;
    }

    /**
     * Allow Partner Admins to create users.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setAllowUserCreation($value)
    {
        return $this->attributes['allow_user_creation'] = $value;
    }
    /**
     * When `true`, emails sent to Partner users are copied to the responsible User or Group.
     *
     * @return bool|null
     */
    public function getCcEmailsToResponsibleParty()
    {
        return isset($this->attributes['cc_emails_to_responsible_party']) ? $this->attributes['cc_emails_to_responsible_party'] : null;
    }

    /**
     * When `true`, emails sent to Partner users are copied to the responsible User or Group.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setCcEmailsToResponsibleParty($value)
    {
        return $this->attributes['cc_emails_to_responsible_party'] = $value;
    }
    /**
     * Approved Connected Sites relationships for this Partner, in both directions. Empty when this Partner has no connections. Read-only.
     *
     * @return object[]|null
     */
    public function getConnections()
    {
        return isset($this->attributes['connections']) ? $this->attributes['connections'] : null;
    }

    /**
     * Approved Connected Sites relationships for this Partner, in both directions. Empty when this Partner has no connections. Read-only.
     *
     * @param object[]|null $value
     * @return object[]|null The assigned value.
     */
    public function setConnections($value)
    {
        return $this->attributes['connections'] = $value;
    }
    /**
     * The unique ID of the Partner.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * The unique ID of the Partner.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * AI Assistant Personality ID assigned to this Partner, if any. Users in the Partner inherit it unless a direct per-user assignment overrides it.
     *
     * @return int|null
     */
    public function getAiAssistantPersonalityId()
    {
        return isset($this->attributes['ai_assistant_personality_id']) ? $this->attributes['ai_assistant_personality_id'] : null;
    }

    /**
     * AI Assistant Personality ID assigned to this Partner, if any. Users in the Partner inherit it unless a direct per-user assignment overrides it.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setAiAssistantPersonalityId($value)
    {
        return $this->attributes['ai_assistant_personality_id'] = $value;
    }
    /**
     * ID of the Workspace associated with this Partner.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * ID of the Workspace associated with this Partner.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * The name of the Partner.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * The name of the Partner.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Notes about this Partner.
     *
     * @return string|null
     */
    public function getNotes()
    {
        return isset($this->attributes['notes']) ? $this->attributes['notes'] : null;
    }

    /**
     * Notes about this Partner.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setNotes($value)
    {
        return $this->attributes['notes'] = $value;
    }
    /**
     * Array of User IDs that are Partner Admins for this Partner.
     *
     * @return int[]|null
     */
    public function getPartnerAdminIds()
    {
        return isset($this->attributes['partner_admin_ids']) ? $this->attributes['partner_admin_ids'] : null;
    }

    /**
     * Array of User IDs that are Partner Admins for this Partner.
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setPartnerAdminIds($value)
    {
        return $this->attributes['partner_admin_ids'] = $value;
    }
    /**
     * ID of the Partner Channel Template assigned to this Partner.
     *
     * @return int|null
     */
    public function getPartnerChannelTemplateId()
    {
        return isset($this->attributes['partner_channel_template_id']) ? $this->attributes['partner_channel_template_id'] : null;
    }

    /**
     * ID of the Partner Channel Template assigned to this Partner.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setPartnerChannelTemplateId($value)
    {
        return $this->attributes['partner_channel_template_id'] = $value;
    }
    /**
     * This site's role for this Partner in Connected Sites relationships. `host` is a Partner this site configured. `guest` is a Partner created by approving another site's connection request; it has no root folder and cannot hold users, permissions, or Partner Channels, or host a connection. `host_and_guest` is a configured Partner that is also the guest side of a connection. Promote a `guest` Partner by setting this to `host_and_guest` together with a `root_folder`.
     *
     * @return string|null
     */
    public function getPartnershipRole()
    {
        return isset($this->attributes['partnership_role']) ? $this->attributes['partnership_role'] : null;
    }

    /**
     * This site's role for this Partner in Connected Sites relationships. `host` is a Partner this site configured. `guest` is a Partner created by approving another site's connection request; it has no root folder and cannot hold users, permissions, or Partner Channels, or host a connection. `host_and_guest` is a configured Partner that is also the guest side of a connection. Promote a `guest` Partner by setting this to `host_and_guest` together with a `root_folder`.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPartnershipRole($value)
    {
        return $this->attributes['partnership_role'] = $value;
    }
    /**
     * ID of the Group responsible for this Partner.
     *
     * @return int|null
     */
    public function getResponsibleGroupId()
    {
        return isset($this->attributes['responsible_group_id']) ? $this->attributes['responsible_group_id'] : null;
    }

    /**
     * ID of the Group responsible for this Partner.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setResponsibleGroupId($value)
    {
        return $this->attributes['responsible_group_id'] = $value;
    }
    /**
     * ID of the User responsible for this Partner.
     *
     * @return int|null
     */
    public function getResponsibleUserId()
    {
        return isset($this->attributes['responsible_user_id']) ? $this->attributes['responsible_user_id'] : null;
    }

    /**
     * ID of the User responsible for this Partner.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setResponsibleUserId($value)
    {
        return $this->attributes['responsible_user_id'] = $value;
    }
    /**
     * The root folder path for this Partner.
     *
     * @return string|null
     */
    public function getRootFolder()
    {
        return isset($this->attributes['root_folder']) ? $this->attributes['root_folder'] : null;
    }

    /**
     * The root folder path for this Partner.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setRootFolder($value)
    {
        return $this->attributes['root_folder'] = $value;
    }
    /**
     * Show Partner users a simplified home page built from this Partner's Channels.
     *
     * @return bool|null
     */
    public function getShowPartnerChannelHomePage()
    {
        return isset($this->attributes['show_partner_channel_home_page']) ? $this->attributes['show_partner_channel_home_page'] : null;
    }

    /**
     * Show Partner users a simplified home page built from this Partner's Channels.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setShowPartnerChannelHomePage($value)
    {
        return $this->attributes['show_partner_channel_home_page'] = $value;
    }
    /**
     * Comma-separated list of Tags for this Partner. Tags are used for other features, such as UserLifecycleRules, which can target specific tags.  Tags must only contain lowercase letters, numbers, and hyphens.
     *
     * @return string|null
     */
    public function getTags()
    {
        return isset($this->attributes['tags']) ? $this->attributes['tags'] : null;
    }

    /**
     * Comma-separated list of Tags for this Partner. Tags are used for other features, such as UserLifecycleRules, which can target specific tags.  Tags must only contain lowercase letters, numbers, and hyphens.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setTags($value)
    {
        return $this->attributes['tags'] = $value;
    }
    /**
     * Array of User IDs that belong to this Partner.
     *
     * @return int[]|null
     */
    public function getUserIds()
    {
        return isset($this->attributes['user_ids']) ? $this->attributes['user_ids'] : null;
    }

    /**
     * Array of User IDs that belong to this Partner.
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setUserIds($value)
    {
        return $this->attributes['user_ids'] = $value;
    }

    // Saves the changes, then refreshes this object with the saved attributes, which it also returns as a separate
    // Partner with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update Partner
     *
     * PATCH /partners/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Partner ID.
     *     - `ai_assistant_personality_id` (int, optional): AI Assistant Personality ID assigned to this Partner, if any. Users in the Partner inherit it unless a direct per-user assignment overrides it.
     *     - `allowed_ips` (string, optional): A list of allowed IPs for this Partner. Newline delimited. Partner User IP access is allowed when the IP matches the Partner, User, or Site allowed IP lists.
     *     - `allow_bypassing_2fa_policies` (bool, optional): Allow Partner Admins to change Two-Factor Authentication requirements for Partner Users.
     *     - `allow_credential_changes` (bool, optional): Allow Partner Admins to change or reset credentials for users belonging to this Partner.
     *     - `allow_providing_gpg_keys` (bool, optional): Allow Partner Admins to provide GPG keys.
     *     - `allow_user_creation` (bool, optional): Allow Partner Admins to create users.
     *     - `cc_emails_to_responsible_party` (bool, optional): When `true`, emails sent to Partner users are copied to the responsible User or Group.
     *     - `notes` (string, optional): Notes about this Partner.
     *     - `partner_channel_template_id` (int, optional): ID of the Partner Channel Template assigned to this Partner.
     *     - `responsible_group_id` (int, optional): ID of the Group responsible for this Partner.
     *     - `responsible_user_id` (int, optional): ID of the User responsible for this Partner.
     *     - `show_partner_channel_home_page` (bool, optional): Show Partner users a simplified home page built from this Partner's Channels.
     *     - `tags` (string, optional): Comma-separated list of Tags for this Partner. Tags are used for other features, such as UserLifecycleRules, which can target specific tags.  Tags must only contain lowercase letters, numbers, and hyphens.
     *     - `name` (string, optional): The name of the Partner.
     *     - `partnership_role` (string, optional): This site's role for this Partner in Connected Sites relationships. `host` is a Partner this site configured. `guest` is a Partner created by approving another site's connection request; it has no root folder and cannot hold users, permissions, or Partner Channels, or host a connection. `host_and_guest` is a configured Partner that is also the guest side of a connection. Promote a `guest` Partner by setting this to `host_and_guest` together with a `root_folder`. Allowed values: host, guest, host_and_guest.
     *     - `root_folder` (string, optional): The root folder path for this Partner.
     * @return \Files\Model\Partner
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

        if (isset($params['ai_assistant_personality_id']) && !is_int($params['ai_assistant_personality_id'])) {
            throw new \Files\Exception\InvalidParameterException('$ai_assistant_personality_id must be of type int; received ' . gettype(@$params['ai_assistant_personality_id']));
        }

        if (isset($params['allowed_ips']) && !is_string($params['allowed_ips'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_ips must be of type string; received ' . gettype(@$params['allowed_ips']));
        }

        if (isset($params['allow_bypassing_2fa_policies']) && !is_scalar($params['allow_bypassing_2fa_policies'])) {
            throw new \Files\Exception\InvalidParameterException('$allow_bypassing_2fa_policies must be of type boolean; received ' . gettype(@$params['allow_bypassing_2fa_policies']));
        }

        if (isset($params['allow_credential_changes']) && !is_scalar($params['allow_credential_changes'])) {
            throw new \Files\Exception\InvalidParameterException('$allow_credential_changes must be of type boolean; received ' . gettype(@$params['allow_credential_changes']));
        }

        if (isset($params['allow_providing_gpg_keys']) && !is_scalar($params['allow_providing_gpg_keys'])) {
            throw new \Files\Exception\InvalidParameterException('$allow_providing_gpg_keys must be of type boolean; received ' . gettype(@$params['allow_providing_gpg_keys']));
        }

        if (isset($params['allow_user_creation']) && !is_scalar($params['allow_user_creation'])) {
            throw new \Files\Exception\InvalidParameterException('$allow_user_creation must be of type boolean; received ' . gettype(@$params['allow_user_creation']));
        }

        if (isset($params['cc_emails_to_responsible_party']) && !is_scalar($params['cc_emails_to_responsible_party'])) {
            throw new \Files\Exception\InvalidParameterException('$cc_emails_to_responsible_party must be of type boolean; received ' . gettype(@$params['cc_emails_to_responsible_party']));
        }

        if (isset($params['notes']) && !is_string($params['notes'])) {
            throw new \Files\Exception\InvalidParameterException('$notes must be of type string; received ' . gettype(@$params['notes']));
        }

        if (isset($params['partner_channel_template_id']) && !is_int($params['partner_channel_template_id'])) {
            throw new \Files\Exception\InvalidParameterException('$partner_channel_template_id must be of type int; received ' . gettype(@$params['partner_channel_template_id']));
        }

        if (isset($params['responsible_group_id']) && !is_int($params['responsible_group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$responsible_group_id must be of type int; received ' . gettype(@$params['responsible_group_id']));
        }

        if (isset($params['responsible_user_id']) && !is_int($params['responsible_user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$responsible_user_id must be of type int; received ' . gettype(@$params['responsible_user_id']));
        }

        if (isset($params['show_partner_channel_home_page']) && !is_scalar($params['show_partner_channel_home_page'])) {
            throw new \Files\Exception\InvalidParameterException('$show_partner_channel_home_page must be of type boolean; received ' . gettype(@$params['show_partner_channel_home_page']));
        }

        if (isset($params['tags']) && !is_string($params['tags'])) {
            throw new \Files\Exception\InvalidParameterException('$tags must be of type string; received ' . gettype(@$params['tags']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['partnership_role']) && !is_string($params['partnership_role'])) {
            throw new \Files\Exception\InvalidParameterException('$partnership_role must be of type string; received ' . gettype(@$params['partnership_role']));
        }

        if (isset($params['root_folder']) && !is_string($params['root_folder'])) {
            throw new \Files\Exception\InvalidParameterException('$root_folder must be of type string; received ' . gettype(@$params['root_folder']));
        }

        $response = Api::sendRequest('/partners/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        $updated = new Partner(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete Partner
     *
     * DELETE /partners/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Partner ID.
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

        $response = Api::sendRequest('/partners/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Partner
     *
     * DELETE /partners/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Partner ID.
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
     * List Partners
     *
     * GET /partners
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id` and `name`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `workspace_id`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Partner[]
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

        $response = Api::sendRequest('/partners', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new Partner(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/partners', $params, $options, Partner::class);
    }

    /**
     * Show Partner
     *
     * GET /partners/{id}
     *
     * @param int $id Partner ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Partner
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

        $response = Api::sendRequest('/partners/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new Partner(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Partner
     *
     * GET /partners/{id}
     *
     * Alias of find().
     *
     * @param int $id Partner ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Partner
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Partner
     *
     * POST /partners
     *
     * @param array $params Request parameters.
     *     - `ai_assistant_personality_id` (int, optional): AI Assistant Personality ID assigned to this Partner, if any. Users in the Partner inherit it unless a direct per-user assignment overrides it.
     *     - `allowed_ips` (string, optional): A list of allowed IPs for this Partner. Newline delimited. Partner User IP access is allowed when the IP matches the Partner, User, or Site allowed IP lists.
     *     - `allow_bypassing_2fa_policies` (bool, optional): Allow Partner Admins to change Two-Factor Authentication requirements for Partner Users.
     *     - `allow_credential_changes` (bool, optional): Allow Partner Admins to change or reset credentials for users belonging to this Partner.
     *     - `allow_providing_gpg_keys` (bool, optional): Allow Partner Admins to provide GPG keys.
     *     - `allow_user_creation` (bool, optional): Allow Partner Admins to create users.
     *     - `cc_emails_to_responsible_party` (bool, optional): When `true`, emails sent to Partner users are copied to the responsible User or Group.
     *     - `notes` (string, optional): Notes about this Partner.
     *     - `partner_channel_template_id` (int, optional): ID of the Partner Channel Template assigned to this Partner.
     *     - `responsible_group_id` (int, optional): ID of the Group responsible for this Partner.
     *     - `responsible_user_id` (int, optional): ID of the User responsible for this Partner.
     *     - `show_partner_channel_home_page` (bool, optional): Show Partner users a simplified home page built from this Partner's Channels.
     *     - `tags` (string, optional): Comma-separated list of Tags for this Partner. Tags are used for other features, such as UserLifecycleRules, which can target specific tags.  Tags must only contain lowercase letters, numbers, and hyphens.
     *     - `name` (string, required): The name of the Partner.
     *     - `root_folder` (string, required): The root folder path for this Partner.
     *     - `workspace_id` (int, optional): ID of the Workspace associated with this Partner.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Partner
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (empty($params['root_folder'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: root_folder');
        }

        if (isset($params['ai_assistant_personality_id']) && !is_int($params['ai_assistant_personality_id'])) {
            throw new \Files\Exception\InvalidParameterException('$ai_assistant_personality_id must be of type int; received ' . gettype(@$params['ai_assistant_personality_id']));
        }

        if (isset($params['allowed_ips']) && !is_string($params['allowed_ips'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_ips must be of type string; received ' . gettype(@$params['allowed_ips']));
        }

        if (isset($params['allow_bypassing_2fa_policies']) && !is_scalar($params['allow_bypassing_2fa_policies'])) {
            throw new \Files\Exception\InvalidParameterException('$allow_bypassing_2fa_policies must be of type boolean; received ' . gettype(@$params['allow_bypassing_2fa_policies']));
        }

        if (isset($params['allow_credential_changes']) && !is_scalar($params['allow_credential_changes'])) {
            throw new \Files\Exception\InvalidParameterException('$allow_credential_changes must be of type boolean; received ' . gettype(@$params['allow_credential_changes']));
        }

        if (isset($params['allow_providing_gpg_keys']) && !is_scalar($params['allow_providing_gpg_keys'])) {
            throw new \Files\Exception\InvalidParameterException('$allow_providing_gpg_keys must be of type boolean; received ' . gettype(@$params['allow_providing_gpg_keys']));
        }

        if (isset($params['allow_user_creation']) && !is_scalar($params['allow_user_creation'])) {
            throw new \Files\Exception\InvalidParameterException('$allow_user_creation must be of type boolean; received ' . gettype(@$params['allow_user_creation']));
        }

        if (isset($params['cc_emails_to_responsible_party']) && !is_scalar($params['cc_emails_to_responsible_party'])) {
            throw new \Files\Exception\InvalidParameterException('$cc_emails_to_responsible_party must be of type boolean; received ' . gettype(@$params['cc_emails_to_responsible_party']));
        }

        if (isset($params['notes']) && !is_string($params['notes'])) {
            throw new \Files\Exception\InvalidParameterException('$notes must be of type string; received ' . gettype(@$params['notes']));
        }

        if (isset($params['partner_channel_template_id']) && !is_int($params['partner_channel_template_id'])) {
            throw new \Files\Exception\InvalidParameterException('$partner_channel_template_id must be of type int; received ' . gettype(@$params['partner_channel_template_id']));
        }

        if (isset($params['responsible_group_id']) && !is_int($params['responsible_group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$responsible_group_id must be of type int; received ' . gettype(@$params['responsible_group_id']));
        }

        if (isset($params['responsible_user_id']) && !is_int($params['responsible_user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$responsible_user_id must be of type int; received ' . gettype(@$params['responsible_user_id']));
        }

        if (isset($params['show_partner_channel_home_page']) && !is_scalar($params['show_partner_channel_home_page'])) {
            throw new \Files\Exception\InvalidParameterException('$show_partner_channel_home_page must be of type boolean; received ' . gettype(@$params['show_partner_channel_home_page']));
        }

        if (isset($params['tags']) && !is_string($params['tags'])) {
            throw new \Files\Exception\InvalidParameterException('$tags must be of type string; received ' . gettype(@$params['tags']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['root_folder']) && !is_string($params['root_folder'])) {
            throw new \Files\Exception\InvalidParameterException('$root_folder must be of type string; received ' . gettype(@$params['root_folder']));
        }

        if (isset($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/partners', 'POST', $params, $options);

        return new Partner(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
