# Workspace

## Example Workspace Object

```
{
  "id": 1,
  "name": "Project Alpha"
}
```

* `id` (int64): Workspace ID
* `name` (string): Workspace name

---

## List Workspaces

```
\Files\Model\Workspace::all();
```

`\Files\Model\Workspace::listPage()` takes the same parameters and returns the results
one `\Files\Page` at a time, requesting each later page only when it is asked for.


### Parameters

* `cursor` (string): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
* `per_page` (int64): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
* `sort_by` (object): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `name`.
* `filter` (object): If set, return records where the specified field is equal to the supplied value. Valid fields are `name`.
* `filter_prefix` (object): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `name`.

---

## Show Workspace

```
\Files\Model\Workspace::find($id);
```


### Parameters

* `id` (int64): Required - Workspace ID.

---

## Create Workspace

```
\Files\Model\Workspace::create([
  'name' => "Project Alpha",
]);
```


### Parameters

* `name` (string): Workspace name

---

## Update Workspace

```
$workspace = \Files\Model\Workspace::find($id);

$workspace->update([
  'name' => "Project Alpha",
]);
```

`update()` refreshes `$workspace` with the saved attributes, and also returns them as a separate
`\Files\Model\Workspace`.

### Parameters

* `id` (int64): Required - Workspace ID.
* `name` (string): Workspace name

### Example Response

```json
{
  "id": 1,
  "name": "Project Alpha"
}
```

---

## Delete Workspace

```
$workspace = \Files\Model\Workspace::find($id);

$workspace->delete();
```

### Parameters

* `id` (int64): Required - Workspace ID.

