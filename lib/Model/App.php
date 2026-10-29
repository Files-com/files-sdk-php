<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class App
 *
 * @package Files
 */
class App
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
    // string # The type of the App
    public function getAppType()
    {
        return isset($this->attributes['app_type']) ? $this->attributes['app_type'] : null;
    }
    // object # Collection of named links to documentation
    public function getDocumentationLinks()
    {
        return isset($this->attributes['documentation_links']) ? $this->attributes['documentation_links'] : null;
    }
    // string # Long description for the in-App landing page
    public function getExtendedDescription()
    {
        return isset($this->attributes['extended_description']) ? $this->attributes['extended_description'] : null;
    }
    // string # Long form description of the App
    public function getExtendedDescriptionForMarketingSite()
    {
        return isset($this->attributes['extended_description_for_marketing_site']) ? $this->attributes['extended_description_for_marketing_site'] : null;
    }
    // string # Link to external homepage
    public function getExternalHomepageUrl()
    {
        return isset($this->attributes['external_homepage_url']) ? $this->attributes['external_homepage_url'] : null;
    }
    // boolean # Is featured on the App listing?
    public function getFeatured()
    {
        return isset($this->attributes['featured']) ? $this->attributes['featured'] : null;
    }
    // string # Associated Folder Behavior type, if any
    public function getFolderBehaviorType()
    {
        return isset($this->attributes['folder_behavior_type']) ? $this->attributes['folder_behavior_type'] : null;
    }
    // string # App icon
    public function getIconUrl()
    {
        return isset($this->attributes['icon_url']) ? $this->attributes['icon_url'] : null;
    }
    // string # Logo thumbnail for the App
    public function getLogoThumbnailUrl()
    {
        return isset($this->attributes['logo_thumbnail_url']) ? $this->attributes['logo_thumbnail_url'] : null;
    }
    // string # Full size logo for the App
    public function getLogoUrl()
    {
        return isset($this->attributes['logo_url']) ? $this->attributes['logo_url'] : null;
    }
    // string # Marketing introdution of the App
    public function getMarketingIntro()
    {
        return isset($this->attributes['marketing_intro']) ? $this->attributes['marketing_intro'] : null;
    }
    // string # Marketing video page
    public function getMarketingYoutubeUrl()
    {
        return isset($this->attributes['marketing_youtube_url']) ? $this->attributes['marketing_youtube_url'] : null;
    }
    // string # Name of the App
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }
    // string # Package manager install command
    public function getPackageManagerInstallCommand()
    {
        return isset($this->attributes['package_manager_install_command']) ? $this->attributes['package_manager_install_command'] : null;
    }
    // string # Associated Remote Server type, if any
    public function getRemoteServerType()
    {
        return isset($this->attributes['remote_server_type']) ? $this->attributes['remote_server_type'] : null;
    }
    // array(string) # Screenshots of the App
    public function getScreenshotListUrls()
    {
        return isset($this->attributes['screenshot_list_urls']) ? $this->attributes['screenshot_list_urls'] : null;
    }
    // string # Link to SDK installation instructions
    public function getSdkInstallationInstructionsLink()
    {
        return isset($this->attributes['sdk_installation_instructions_link']) ? $this->attributes['sdk_installation_instructions_link'] : null;
    }
    // string # Short description of the App
    public function getShortDescription()
    {
        return isset($this->attributes['short_description']) ? $this->attributes['short_description'] : null;
    }
    // string # Associated SSO Strategy type, if any
    public function getSsoStrategyType()
    {
        return isset($this->attributes['sso_strategy_type']) ? $this->attributes['sso_strategy_type'] : null;
    }
    // string # Associated SIEM type, if any
    public function getSiemType()
    {
        return isset($this->attributes['siem_type']) ? $this->attributes['siem_type'] : null;
    }
    // string # Tutorial video page
    public function getTutorialYoutubeUrl()
    {
        return isset($this->attributes['tutorial_youtube_url']) ? $this->attributes['tutorial_youtube_url'] : null;
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `name` and `app_type`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `name` and `app_type`. Valid field combinations are `[ name, app_type ]`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `name`.
    public static function all($params = [], $options = [])
    {
        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/apps', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new App((array) $obj, $options);
        }

        return $return_array;
    }
}
