# EventChannel

## Example EventChannel Object

```
{
  "id": 1,
  "name": "example",
  "workspace_id": 1,
  "description": "example",
  "enabled": true,
  "default_channel": true,
  "created_at": "2000-01-01T01:00:00Z",
  "updated_at": "2000-01-01T01:00:00Z"
}
```

* `id` (int64): Event Channel ID
* `name` (string): Event Channel name.
* `workspace_id` (int64): Workspace ID. 0 means the default workspace.
* `description` (string): Event Channel description.
* `enabled` (boolean): Whether this Event Channel can dispatch events.
* `default_channel` (boolean): Whether this Event Channel is the default destination for newly published events.
* `created_at` (date-time): Event Channel create date/time.
* `updated_at` (date-time): Event Channel update date/time.

---

## List Event Channels

```
\Files\Model\EventChannel::all();
```

`\Files\Model\EventChannel::listPage()` takes the same parameters and returns the results
one `\Files\Page` at a time, requesting each later page only when it is asked for.


### Parameters

* `cursor` (string): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
* `per_page` (int64): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
* `sort_by` (object): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `name`, `enabled`, `default_channel` or `workspace_id`.
* `filter` (object): If set, return records where the specified field is equal to the supplied value. Valid fields are `enabled`, `default_channel` or `workspace_id`. Valid field combinations are `[ workspace_id, enabled ]` and `[ workspace_id, default_channel ]`.

---

## Show Event Channel

```
\Files\Model\EventChannel::find($id);
```


### Parameters

* `id` (int64): Required - Event Channel ID.

---

## Create Event Channel

```
\Files\Model\EventChannel::create([
  'name' => "example",
  'workspace_id' => 1,
  'description' => "example",
  'enabled' => true,
  'default_channel' => true,
]);
```


### Parameters

* `name` (string): Required - Event Channel name.
* `workspace_id` (int64): Workspace ID. 0 means the default workspace.
* `description` (string): Event Channel description.
* `enabled` (boolean): Whether this Event Channel can dispatch events.
* `default_channel` (boolean): Whether this Event Channel is the default destination for newly published events.

---

## Update Event Channel

```
$event_channel = \Files\Model\EventChannel::find($id);

$event_channel->update([
  'name' => "example",
  'workspace_id' => 1,
  'description' => "example",
  'enabled' => true,
  'default_channel' => true,
]);
```

`update()` refreshes `$event_channel` with the saved attributes, and also returns them as a separate
`\Files\Model\EventChannel`.

### Parameters

* `id` (int64): Required - Event Channel ID.
* `name` (string): Event Channel name.
* `workspace_id` (int64): Workspace ID. 0 means the default workspace.
* `description` (string): Event Channel description.
* `enabled` (boolean): Whether this Event Channel can dispatch events.
* `default_channel` (boolean): Whether this Event Channel is the default destination for newly published events.

### Example Response

```json
{
  "id": 1,
  "name": "example",
  "workspace_id": 1,
  "description": "example",
  "enabled": true,
  "default_channel": true,
  "created_at": "2000-01-01T01:00:00Z",
  "updated_at": "2000-01-01T01:00:00Z"
}
```

---

## Delete Event Channel

```
$event_channel = \Files\Model\EventChannel::find($id);

$event_channel->delete();
```

### Parameters

* `id` (int64): Required - Event Channel ID.

