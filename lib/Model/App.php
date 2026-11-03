<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An App represents one of the various integrations provided by Files.com.
 *
 * @property string|null $app_type The type of the App
 * @property object|null $documentation_links Collection of named links to documentation
 * @property string|null $extended_description Long description for the in-App landing page
 * @property string|null $extended_description_for_marketing_site Long form description of the App
 * @property string|null $external_homepage_url Link to external homepage
 * @property bool|null $featured Is featured on the App listing?
 * @property string|null $folder_behavior_type Associated Folder Behavior type, if any
 * @property string|null $icon_url App icon
 * @property string|null $logo_thumbnail_url Logo thumbnail for the App
 * @property string|null $logo_url Full size logo for the App
 * @property string|null $marketing_intro Marketing introdution of the App
 * @property string|null $marketing_youtube_url Marketing video page
 * @property string|null $name Name of the App
 * @property string|null $package_manager_install_command Package manager install command
 * @property string|null $remote_server_type Associated Remote Server type, if any
 * @property string[]|null $screenshot_list_urls Screenshots of the App
 * @property string|null $sdk_installation_instructions_link Link to SDK installation instructions
 * @property string|null $short_description Short description of the App
 * @property string|null $sso_strategy_type Associated SSO Strategy type, if any
 * @property string|null $siem_type Associated SIEM type, if any
 * @property string|null $tutorial_youtube_url Tutorial video page
 * @package Files
 */
class App
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
     * The type of the App
     *
     * @return string|null
     */
    public function getAppType()
    {
        return isset($this->attributes['app_type']) ? $this->attributes['app_type'] : null;
    }
    /**
     * Collection of named links to documentation
     *
     * @return object|null
     */
    public function getDocumentationLinks()
    {
        return isset($this->attributes['documentation_links']) ? $this->attributes['documentation_links'] : null;
    }
    /**
     * Long description for the in-App landing page
     *
     * @return string|null
     */
    public function getExtendedDescription()
    {
        return isset($this->attributes['extended_description']) ? $this->attributes['extended_description'] : null;
    }
    /**
     * Long form description of the App
     *
     * @return string|null
     */
    public function getExtendedDescriptionForMarketingSite()
    {
        return isset($this->attributes['extended_description_for_marketing_site']) ? $this->attributes['extended_description_for_marketing_site'] : null;
    }
    /**
     * Link to external homepage
     *
     * @return string|null
     */
    public function getExternalHomepageUrl()
    {
        return isset($this->attributes['external_homepage_url']) ? $this->attributes['external_homepage_url'] : null;
    }
    /**
     * Is featured on the App listing?
     *
     * @return bool|null
     */
    public function getFeatured()
    {
        return isset($this->attributes['featured']) ? $this->attributes['featured'] : null;
    }
    /**
     * Associated Folder Behavior type, if any
     *
     * @return string|null
     */
    public function getFolderBehaviorType()
    {
        return isset($this->attributes['folder_behavior_type']) ? $this->attributes['folder_behavior_type'] : null;
    }
    /**
     * App icon
     *
     * @return string|null
     */
    public function getIconUrl()
    {
        return isset($this->attributes['icon_url']) ? $this->attributes['icon_url'] : null;
    }
    /**
     * Logo thumbnail for the App
     *
     * @return string|null
     */
    public function getLogoThumbnailUrl()
    {
        return isset($this->attributes['logo_thumbnail_url']) ? $this->attributes['logo_thumbnail_url'] : null;
    }
    /**
     * Full size logo for the App
     *
     * @return string|null
     */
    public function getLogoUrl()
    {
        return isset($this->attributes['logo_url']) ? $this->attributes['logo_url'] : null;
    }
    /**
     * Marketing introdution of the App
     *
     * @return string|null
     */
    public function getMarketingIntro()
    {
        return isset($this->attributes['marketing_intro']) ? $this->attributes['marketing_intro'] : null;
    }
    /**
     * Marketing video page
     *
     * @return string|null
     */
    public function getMarketingYoutubeUrl()
    {
        return isset($this->attributes['marketing_youtube_url']) ? $this->attributes['marketing_youtube_url'] : null;
    }
    /**
     * Name of the App
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }
    /**
     * Package manager install command
     *
     * @return string|null
     */
    public function getPackageManagerInstallCommand()
    {
        return isset($this->attributes['package_manager_install_command']) ? $this->attributes['package_manager_install_command'] : null;
    }
    /**
     * Associated Remote Server type, if any
     *
     * @return string|null
     */
    public function getRemoteServerType()
    {
        return isset($this->attributes['remote_server_type']) ? $this->attributes['remote_server_type'] : null;
    }
    /**
     * Screenshots of the App
     *
     * @return string[]|null
     */
    public function getScreenshotListUrls()
    {
        return isset($this->attributes['screenshot_list_urls']) ? $this->attributes['screenshot_list_urls'] : null;
    }
    /**
     * Link to SDK installation instructions
     *
     * @return string|null
     */
    public function getSdkInstallationInstructionsLink()
    {
        return isset($this->attributes['sdk_installation_instructions_link']) ? $this->attributes['sdk_installation_instructions_link'] : null;
    }
    /**
     * Short description of the App
     *
     * @return string|null
     */
    public function getShortDescription()
    {
        return isset($this->attributes['short_description']) ? $this->attributes['short_description'] : null;
    }
    /**
     * Associated SSO Strategy type, if any
     *
     * @return string|null
     */
    public function getSsoStrategyType()
    {
        return isset($this->attributes['sso_strategy_type']) ? $this->attributes['sso_strategy_type'] : null;
    }
    /**
     * Associated SIEM type, if any
     *
     * @return string|null
     */
    public function getSiemType()
    {
        return isset($this->attributes['siem_type']) ? $this->attributes['siem_type'] : null;
    }
    /**
     * Tutorial video page
     *
     * @return string|null
     */
    public function getTutorialYoutubeUrl()
    {
        return isset($this->attributes['tutorial_youtube_url']) ? $this->attributes['tutorial_youtube_url'] : null;
    }

    /**
     * List Apps
     *
     * GET /apps
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `name` and `app_type`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `name` and `app_type`. Valid field combinations are `[ name, app_type ]`.
     *     - `filter_prefix` (array|object, optional): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `name`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\App[]
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

        $response = Api::sendRequest('/apps', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new App(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/apps', $params, $options, App::class);
    }
}
