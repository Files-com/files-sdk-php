# Secret

## Example Secret Object

```
{
  "id": 1,
  "workspace_id": 1,
  "name": "Production API token",
  "description": "Used by production API integrations.",
  "secret_type": "token",
  "metadata": {
    "header_name": "Authorization"
  },
  "value_field_names": [
    "example"
  ],
  "created_at": "2000-01-01T01:00:00Z",
  "updated_at": "2000-01-01T01:00:00Z"
}
```

* `id` (int64): Secret ID.
* `workspace_id` (int64): Workspace ID. 0 means the default workspace.
* `name` (string): Secret name.
* `description` (string): Internal description for your reference.
* `secret_type` (string): Secret type.
* `metadata` (object): Non-secret metadata for the Secret type.
* `value_field_names` (array(string)): Names of configured secret value fields. Secret values are never returned.
* `created_at` (date-time): Secret create date/time.
* `updated_at` (date-time): Secret update date/time.

---

## List Secrets

```
\Files\Model\Secret::all();
```

`\Files\Model\Secret::listPage()` takes the same parameters and returns the results
one `\Files\Page` at a time, requesting each later page only when it is asked for.


### Parameters

* `cursor` (string): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
* `per_page` (int64): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
* `sort_by` (object): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `name` or `secret_type`.
* `filter` (object): If set, return records where the specified field is equal to the supplied value. Valid fields are `workspace_id`, `name` or `secret_type`. Valid field combinations are `[ workspace_id, name ]`, `[ workspace_id, secret_type ]`, `[ secret_type, name ]` or `[ workspace_id, secret_type, name ]`.
* `filter_prefix` (object): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `name`.

---

## Show Secret

```
\Files\Model\Secret::find($id);
```


### Parameters

* `id` (int64): Required - Secret ID.

---

## Create Secret

```
\Files\Model\Secret::create([
  'name' => "Production API token",
  'description' => "Used by production API integrations.",
  'secret_type' => "token",
  'metadata' => {"header_name":"Authorization"},
  'workspace_id' => 0,
]);
```


### Parameters

* `name` (string): Required - Secret name.
* `description` (string): Internal description for your reference.
* `secret_type` (string): Required - Secret type.
* `metadata` (object): Non-secret metadata for the Secret type.
* `workspace_id` (int64): Workspace ID. 0 means the default workspace.

---

## Update Secret

```
$secret = \Files\Model\Secret::find($id);

$secret->update([
  'name' => "Production API token",
  'description' => "Used by production API integrations.",
  'secret_type' => "token",
  'metadata' => {"header_name":"Authorization"},
]);
```

`update()` refreshes `$secret` with the saved attributes, and also returns them as a separate
`\Files\Model\Secret`.

### Parameters

* `id` (int64): Required - Secret ID.
* `name` (string): Secret name.
* `description` (string): Internal description for your reference.
* `secret_type` (string): Secret type.
* `metadata` (object): Non-secret metadata for the Secret type.

### Example Response

```json
{
  "id": 1,
  "workspace_id": 1,
  "name": "Production API token",
  "description": "Used by production API integrations.",
  "secret_type": "token",
  "metadata": {
    "header_name": "Authorization"
  },
  "value_field_names": [
    "example"
  ],
  "created_at": "2000-01-01T01:00:00Z",
  "updated_at": "2000-01-01T01:00:00Z"
}
```

---

## Delete Secret

```
$secret = \Files\Model\Secret::find($id);

$secret->delete();
```

### Parameters

* `id` (int64): Required - Secret ID.

