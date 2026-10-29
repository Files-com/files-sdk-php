<?php

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
    // string # Node ID from the pinned Automation definition.
    public function getNodeId()
    {
        return isset($this->attributes['node_id']) ? $this->attributes['node_id'] : null;
    }
    // string # Node type.
    public function getNodeType()
    {
        return isset($this->attributes['node_type']) ? $this->attributes['node_type'] : null;
    }
    // string # Node status.
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    // string # Current node execution stage.
    public function getRunStage()
    {
        return isset($this->attributes['run_stage']) ? $this->attributes['run_stage'] : null;
    }
    // boolean # Whether this node reused persisted output from an earlier run.
    public function getReused()
    {
        return isset($this->attributes['reused']) ? $this->attributes['reused'] : null;
    }
    // int64 # Count of successful operations in this node.
    public function getSuccessfulOperations()
    {
        return isset($this->attributes['successful_operations']) ? $this->attributes['successful_operations'] : null;
    }
    // int64 # Count of failed operations in this node.
    public function getFailedOperations()
    {
        return isset($this->attributes['failed_operations']) ? $this->attributes['failed_operations'] : null;
    }
    // date-time # When this node started.
    public function getStartedAt()
    {
        return isset($this->attributes['started_at']) ? $this->attributes['started_at'] : null;
    }
    // date-time # When this node completed.
    public function getCompletedAt()
    {
        return isset($this->attributes['completed_at']) ? $this->attributes['completed_at'] : null;
    }
    // int64 # Node runtime in milliseconds.
    public function getDurationMs()
    {
        return isset($this->attributes['duration_ms']) ? $this->attributes['duration_ms'] : null;
    }
    // array(object) # Ordered inbound edge references.
    public function getInputs()
    {
        return isset($this->attributes['inputs']) ? $this->attributes['inputs'] : null;
    }
    // object # Output counts, item kinds, and typed-error summaries by outlet.
    public function getOutputs()
    {
        return isset($this->attributes['outputs']) ? $this->attributes['outputs'] : null;
    }
    // object # Materialized input items currently available, grouped by inlet.
    public function getInputItems()
    {
        return isset($this->attributes['input_items']) ? $this->attributes['input_items'] : null;
    }
    // object # Materialized output items grouped by outlet.
    public function getOutputItems()
    {
        return isset($this->attributes['output_items']) ? $this->attributes['output_items'] : null;
    }
}
