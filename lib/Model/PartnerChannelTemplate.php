<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A PartnerChannelTemplate defines reusable Partner Channel configuration that can be applied to Partners.
 *
 * @property int|null $id The unique ID of the Partner Channel Template.
 * @property int|null $workspace_id ID of the Workspace associated with this Partner Channel Template.
 * @property string|null $direction Channel directions. `two_way` enables both directions, `to_partner` enables outgoing downloads, and `from_partner` enables incoming uploads.
 * @property bool|null $use_channel_root Use the Channel folder directly for a one-way exchange. Defaults to false. Cannot be changed after creation. Folder name overrides must be blank when enabled, and the Template must remain one-way.
 * @property string|null $name The name of the Partner Channel Template.
 * @property string|null $path Channel path relative to the Partner root folder. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property string|null $to_partner_folder_name Optional Channel-level to-Partner folder name override.
 * @property string|null $from_partner_folder_name Optional Channel-level from-Partner folder name override.
 * @property string|null $from_partner_route_path_pattern Optional route path pattern for files uploaded by the Partner. Supports {{partner_name}}.
 * @property string|null $to_partner_route_path_pattern Optional route path pattern for files delivered to the Partner. Supports {{partner_name}}.
 * @property string[]|null $to_partner_managed_folder_paths Managed folder paths inside the to-Partner folder.
 * @property string[]|null $from_partner_managed_folder_paths Managed folder paths inside the from-Partner folder.
 * @property string|null $effective_to_partner_folder_name Resolved to-Partner subfolder name. Null when the direction is disabled or uses the Channel folder directly.
 * @property string|null $effective_from_partner_folder_name Resolved from-Partner subfolder name. Null when the direction is disabled or uses the Channel folder directly.
 * @package Files
 */
class PartnerChannelTemplate
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
     * The unique ID of the Partner Channel Template.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * The unique ID of the Partner Channel Template.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * ID of the Workspace associated with this Partner Channel Template.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * ID of the Workspace associated with this Partner Channel Template.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * Channel directions. `two_way` enables both directions, `to_partner` enables outgoing downloads, and `from_partner` enables incoming uploads.
     *
     * @return string|null
     */
    public function getDirection()
    {
        return isset($this->attributes['direction']) ? $this->attributes['direction'] : null;
    }

    /**
     * Channel directions. `two_way` enables both directions, `to_partner` enables outgoing downloads, and `from_partner` enables incoming uploads.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDirection($value)
    {
        return $this->attributes['direction'] = $value;
    }
    /**
     * Use the Channel folder directly for a one-way exchange. Defaults to false. Cannot be changed after creation. Folder name overrides must be blank when enabled, and the Template must remain one-way.
     *
     * @return bool|null
     */
    public function getUseChannelRoot()
    {
        return isset($this->attributes['use_channel_root']) ? $this->attributes['use_channel_root'] : null;
    }

    /**
     * Use the Channel folder directly for a one-way exchange. Defaults to false. Cannot be changed after creation. Folder name overrides must be blank when enabled, and the Template must remain one-way.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setUseChannelRoot($value)
    {
        return $this->attributes['use_channel_root'] = $value;
    }
    /**
     * The name of the Partner Channel Template.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * The name of the Partner Channel Template.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Channel path relative to the Partner root folder. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }

    /**
     * Channel path relative to the Partner root folder. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    /**
     * Optional Channel-level to-Partner folder name override.
     *
     * @return string|null
     */
    public function getToPartnerFolderName()
    {
        return isset($this->attributes['to_partner_folder_name']) ? $this->attributes['to_partner_folder_name'] : null;
    }

    /**
     * Optional Channel-level to-Partner folder name override.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setToPartnerFolderName($value)
    {
        return $this->attributes['to_partner_folder_name'] = $value;
    }
    /**
     * Optional Channel-level from-Partner folder name override.
     *
     * @return string|null
     */
    public function getFromPartnerFolderName()
    {
        return isset($this->attributes['from_partner_folder_name']) ? $this->attributes['from_partner_folder_name'] : null;
    }

    /**
     * Optional Channel-level from-Partner folder name override.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFromPartnerFolderName($value)
    {
        return $this->attributes['from_partner_folder_name'] = $value;
    }
    /**
     * Optional route path pattern for files uploaded by the Partner. Supports {{partner_name}}.
     *
     * @return string|null
     */
    public function getFromPartnerRoutePathPattern()
    {
        return isset($this->attributes['from_partner_route_path_pattern']) ? $this->attributes['from_partner_route_path_pattern'] : null;
    }

    /**
     * Optional route path pattern for files uploaded by the Partner. Supports {{partner_name}}.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFromPartnerRoutePathPattern($value)
    {
        return $this->attributes['from_partner_route_path_pattern'] = $value;
    }
    /**
     * Optional route path pattern for files delivered to the Partner. Supports {{partner_name}}.
     *
     * @return string|null
     */
    public function getToPartnerRoutePathPattern()
    {
        return isset($this->attributes['to_partner_route_path_pattern']) ? $this->attributes['to_partner_route_path_pattern'] : null;
    }

    /**
     * Optional route path pattern for files delivered to the Partner. Supports {{partner_name}}.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setToPartnerRoutePathPattern($value)
    {
        return $this->attributes['to_partner_route_path_pattern'] = $value;
    }
    /**
     * Managed folder paths inside the to-Partner folder.
     *
     * @return string[]|null
     */
    public function getToPartnerManagedFolderPaths()
    {
        return isset($this->attributes['to_partner_managed_folder_paths']) ? $this->attributes['to_partner_managed_folder_paths'] : null;
    }

    /**
     * Managed folder paths inside the to-Partner folder.
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setToPartnerManagedFolderPaths($value)
    {
        return $this->attributes['to_partner_managed_folder_paths'] = $value;
    }
    /**
     * Managed folder paths inside the from-Partner folder.
     *
     * @return string[]|null
     */
    public function getFromPartnerManagedFolderPaths()
    {
        return isset($this->attributes['from_partner_managed_folder_paths']) ? $this->attributes['from_partner_managed_folder_paths'] : null;
    }

    /**
     * Managed folder paths inside the from-Partner folder.
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setFromPartnerManagedFolderPaths($value)
    {
        return $this->attributes['from_partner_managed_folder_paths'] = $value;
    }
    /**
     * Resolved to-Partner subfolder name. Null when the direction is disabled or uses the Channel folder directly.
     *
     * @return string|null
     */
    public function getEffectiveToPartnerFolderName()
    {
        return isset($this->attributes['effective_to_partner_folder_name']) ? $this->attributes['effective_to_partner_folder_name'] : null;
    }

    /**
     * Resolved to-Partner subfolder name. Null when the direction is disabled or uses the Channel folder directly.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setEffectiveToPartnerFolderName($value)
    {
        return $this->attributes['effective_to_partner_folder_name'] = $value;
    }
    /**
     * Resolved from-Partner subfolder name. Null when the direction is disabled or uses the Channel folder directly.
     *
     * @return string|null
     */
    public function getEffectiveFromPartnerFolderName()
    {
        return isset($this->attributes['effective_from_partner_folder_name']) ? $this->attributes['effective_from_partner_folder_name'] : null;
    }

    /**
     * Resolved from-Partner subfolder name. Null when the direction is disabled or uses the Channel folder directly.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setEffectiveFromPartnerFolderName($value)
    {
        return $this->attributes['effective_from_partner_folder_name'] = $value;
    }

    // Saves the changes, then refreshes this object with the saved attributes, which it also returns as a separate
    // PartnerChannelTemplate with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update Partner Channel Template
     *
     * PATCH /partner_channel_templates/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Partner Channel Template ID.
     *     - `direction` (string, optional): Channel directions. `two_way` enables both directions, `to_partner` enables outgoing downloads, and `from_partner` enables incoming uploads. Allowed values: two_way, to_partner, from_partner.
     *     - `use_channel_root` (bool, optional): Use the Channel folder directly for a one-way exchange. Defaults to false. Cannot be changed after creation. Folder name overrides must be blank when enabled, and the Template must remain one-way.
     *     - `from_partner_folder_name` (string, optional): Optional Channel-level from-Partner folder name override.
     *     - `from_partner_managed_folder_paths` (string[], optional): Managed folder paths inside the from-Partner folder.
     *     - `from_partner_route_path_pattern` (string, optional): Optional route path pattern for files uploaded by the Partner. Supports {{partner_name}}.
     *     - `to_partner_folder_name` (string, optional): Optional Channel-level to-Partner folder name override.
     *     - `to_partner_managed_folder_paths` (string[], optional): Managed folder paths inside the to-Partner folder.
     *     - `to_partner_route_path_pattern` (string, optional): Optional route path pattern for files delivered to the Partner. Supports {{partner_name}}.
     *     - `name` (string, optional): The name of the Partner Channel Template.
     *     - `path` (string, optional): Channel path relative to the Partner root folder.
     * @return \Files\Model\PartnerChannelTemplate
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

        if (isset($params['direction']) && !is_string($params['direction'])) {
            throw new \Files\Exception\InvalidParameterException('$direction must be of type string; received ' . gettype(@$params['direction']));
        }

        if (isset($params['use_channel_root']) && !is_scalar($params['use_channel_root'])) {
            throw new \Files\Exception\InvalidParameterException('$use_channel_root must be of type boolean; received ' . gettype(@$params['use_channel_root']));
        }

        if (isset($params['from_partner_folder_name']) && !is_string($params['from_partner_folder_name'])) {
            throw new \Files\Exception\InvalidParameterException('$from_partner_folder_name must be of type string; received ' . gettype(@$params['from_partner_folder_name']));
        }

        if (isset($params['from_partner_managed_folder_paths']) && !is_array($params['from_partner_managed_folder_paths'])) {
            throw new \Files\Exception\InvalidParameterException('$from_partner_managed_folder_paths must be of type array; received ' . gettype(@$params['from_partner_managed_folder_paths']));
        }

        if (isset($params['from_partner_route_path_pattern']) && !is_string($params['from_partner_route_path_pattern'])) {
            throw new \Files\Exception\InvalidParameterException('$from_partner_route_path_pattern must be of type string; received ' . gettype(@$params['from_partner_route_path_pattern']));
        }

        if (isset($params['to_partner_folder_name']) && !is_string($params['to_partner_folder_name'])) {
            throw new \Files\Exception\InvalidParameterException('$to_partner_folder_name must be of type string; received ' . gettype(@$params['to_partner_folder_name']));
        }

        if (isset($params['to_partner_managed_folder_paths']) && !is_array($params['to_partner_managed_folder_paths'])) {
            throw new \Files\Exception\InvalidParameterException('$to_partner_managed_folder_paths must be of type array; received ' . gettype(@$params['to_partner_managed_folder_paths']));
        }

        if (isset($params['to_partner_route_path_pattern']) && !is_string($params['to_partner_route_path_pattern'])) {
            throw new \Files\Exception\InvalidParameterException('$to_partner_route_path_pattern must be of type string; received ' . gettype(@$params['to_partner_route_path_pattern']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        $response = Api::sendRequest('/partner_channel_templates/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        $updated = new PartnerChannelTemplate(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete Partner Channel Template
     *
     * DELETE /partner_channel_templates/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Partner Channel Template ID.
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

        $response = Api::sendRequest('/partner_channel_templates/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Partner Channel Template
     *
     * DELETE /partner_channel_templates/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Partner Channel Template ID.
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
     * List Partner Channel Templates
     *
     * GET /partner_channel_templates
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id` and `name`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `workspace_id`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\PartnerChannelTemplate[]
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

        $response = Api::sendRequest('/partner_channel_templates', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new PartnerChannelTemplate(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/partner_channel_templates', $params, $options, PartnerChannelTemplate::class);
    }

    /**
     * Show Partner Channel Template
     *
     * GET /partner_channel_templates/{id}
     *
     * @param int $id Partner Channel Template ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\PartnerChannelTemplate
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

        $response = Api::sendRequest('/partner_channel_templates/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new PartnerChannelTemplate(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Partner Channel Template
     *
     * GET /partner_channel_templates/{id}
     *
     * Alias of find().
     *
     * @param int $id Partner Channel Template ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\PartnerChannelTemplate
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Partner Channel Template
     *
     * POST /partner_channel_templates
     *
     * @param array $params Request parameters.
     *     - `direction` (string, optional): Channel directions. `two_way` enables both directions, `to_partner` enables outgoing downloads, and `from_partner` enables incoming uploads. Allowed values: two_way, to_partner, from_partner.
     *     - `use_channel_root` (bool, optional): Use the Channel folder directly for a one-way exchange. Defaults to false. Cannot be changed after creation. Folder name overrides must be blank when enabled, and the Template must remain one-way.
     *     - `from_partner_folder_name` (string, optional): Optional Channel-level from-Partner folder name override.
     *     - `from_partner_managed_folder_paths` (string[], optional): Managed folder paths inside the from-Partner folder.
     *     - `from_partner_route_path_pattern` (string, optional): Optional route path pattern for files uploaded by the Partner. Supports {{partner_name}}.
     *     - `to_partner_folder_name` (string, optional): Optional Channel-level to-Partner folder name override.
     *     - `to_partner_managed_folder_paths` (string[], optional): Managed folder paths inside the to-Partner folder.
     *     - `to_partner_route_path_pattern` (string, optional): Optional route path pattern for files delivered to the Partner. Supports {{partner_name}}.
     *     - `name` (string, required): The name of the Partner Channel Template.
     *     - `path` (string, required): Channel path relative to the Partner root folder.
     *     - `workspace_id` (int, optional): ID of the Workspace associated with this Partner Channel Template.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\PartnerChannelTemplate
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (empty($params['path'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (isset($params['direction']) && !is_string($params['direction'])) {
            throw new \Files\Exception\InvalidParameterException('$direction must be of type string; received ' . gettype(@$params['direction']));
        }

        if (isset($params['use_channel_root']) && !is_scalar($params['use_channel_root'])) {
            throw new \Files\Exception\InvalidParameterException('$use_channel_root must be of type boolean; received ' . gettype(@$params['use_channel_root']));
        }

        if (isset($params['from_partner_folder_name']) && !is_string($params['from_partner_folder_name'])) {
            throw new \Files\Exception\InvalidParameterException('$from_partner_folder_name must be of type string; received ' . gettype(@$params['from_partner_folder_name']));
        }

        if (isset($params['from_partner_managed_folder_paths']) && !is_array($params['from_partner_managed_folder_paths'])) {
            throw new \Files\Exception\InvalidParameterException('$from_partner_managed_folder_paths must be of type array; received ' . gettype(@$params['from_partner_managed_folder_paths']));
        }

        if (isset($params['from_partner_route_path_pattern']) && !is_string($params['from_partner_route_path_pattern'])) {
            throw new \Files\Exception\InvalidParameterException('$from_partner_route_path_pattern must be of type string; received ' . gettype(@$params['from_partner_route_path_pattern']));
        }

        if (isset($params['to_partner_folder_name']) && !is_string($params['to_partner_folder_name'])) {
            throw new \Files\Exception\InvalidParameterException('$to_partner_folder_name must be of type string; received ' . gettype(@$params['to_partner_folder_name']));
        }

        if (isset($params['to_partner_managed_folder_paths']) && !is_array($params['to_partner_managed_folder_paths'])) {
            throw new \Files\Exception\InvalidParameterException('$to_partner_managed_folder_paths must be of type array; received ' . gettype(@$params['to_partner_managed_folder_paths']));
        }

        if (isset($params['to_partner_route_path_pattern']) && !is_string($params['to_partner_route_path_pattern'])) {
            throw new \Files\Exception\InvalidParameterException('$to_partner_route_path_pattern must be of type string; received ' . gettype(@$params['to_partner_route_path_pattern']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/partner_channel_templates', 'POST', $params, $options);

        return new PartnerChannelTemplate(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
