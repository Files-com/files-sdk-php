<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class AutomationExecutionNode
 *
 * @package Files
 */
class AutomationExecutionNode
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
    // string # Node ID from the pinned Automation definition.
    public function getNodeId()
    {
        return @$this->attributes['node_id'];
    }
    // string # Node type.
    public function getNodeType()
    {
        return @$this->attributes['node_type'];
    }
    // string # Node status.
    public function getStatus()
    {
        return @$this->attributes['status'];
    }
    // string # Current node execution stage.
    public function getRunStage()
    {
        return @$this->attributes['run_stage'];
    }
    // boolean # Whether this node reused persisted output from an earlier run.
    public function getReused()
    {
        return @$this->attributes['reused'];
    }
    // int64 # Count of successful operations in this node.
    public function getSuccessfulOperations()
    {
        return @$this->attributes['successful_operations'];
    }
    // int64 # Count of failed operations in this node.
    public function getFailedOperations()
    {
        return @$this->attributes['failed_operations'];
    }
    // date-time # When this node started.
    public function getStartedAt()
    {
        return @$this->attributes['started_at'];
    }
    // date-time # When this node completed.
    public function getCompletedAt()
    {
        return @$this->attributes['completed_at'];
    }
    // int64 # Node runtime in milliseconds.
    public function getDurationMs()
    {
        return @$this->attributes['duration_ms'];
    }
    // array(object) # Ordered inbound edge references.
    public function getInputs()
    {
        return @$this->attributes['inputs'];
    }
    // object # Output counts, item kinds, and typed-error summaries by outlet.
    public function getOutputs()
    {
        return @$this->attributes['outputs'];
    }
    // object # Materialized input items currently available, grouped by inlet.
    public function getInputItems()
    {
        return @$this->attributes['input_items'];
    }
    // object # Materialized output items grouped by outlet.
    public function getOutputItems()
    {
        return @$this->attributes['output_items'];
    }
}
