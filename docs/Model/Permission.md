# Permission

## Example Permission Object

```
{
  "id": 1,
  "path": "example",
  "user_id": 1,
  "username": "user",
  "group_id": 1,
  "group_name": "example",
  "permission": "full",
  "recursive": true,
  "site_id": 1
}
```

* `id` (int64): Permission ID
* `path` (string): Path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
* `user_id` (int64): User ID
* `username` (string): Username (if applicable)
* `group_id` (int64): Group ID
* `group_name` (string): Group name (if applicable)
* `permission` (string): Permission type.  See the table referenced in the documentation for an explanation of each permission.
* `recursive` (boolean): Recursive: does this permission apply to subfolders?
* `site_id` (int64): Site ID

---

## List Permissions

```
$permission = new \Files\Model\Permission();
$permission->list(, [
  'path' => "example",
  'include_groups' => false,
  'group_id' => 1,
  'user_id' => 1,
]);
```


### Parameters

* `cursor` (string): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
* `per_page` (int64): Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
* `sort_by` (object): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `site_id`, `group_id`, `path` or `user_id`.
* `filter` (object): If set, return records where the specified field is equal to the supplied value. Valid fields are `path`, `group_id` or `user_id`. Valid field combinations are `[ group_id, path ]`, `[ user_id, path ]` or `[ user_id, group_id ]`.
* `filter_prefix` (object): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `path`.
* `path` (string): Permission path.  If provided, will scope all permissions(including upward) to this path.
* `include_groups` (boolean): If searching by user or group, also include user's permissions that are inherited from its groups?
* `group_id` (string): 
* `user_id` (string): 

---

## Create Permission

```
$permission = new \Files\Model\Permission();
$permission->create(, [
  'path' => "path",
  'group_id' => 1,
  'permission' => "full",
  'recursive' => false,
  'user_id' => 1,
  'username' => "user",
  'group_name' => "example",
  'site_id' => 1,
]);
```


### Parameters

* `path` (string): Required - Folder path
* `group_id` (int64): Group ID. Provide `group_name` or `group_id`
* `permission` (string): Permission type.  Can be `admin`, `full`, `readonly`, `writeonly`, `list`, or `history`
* `recursive` (boolean): Apply to subfolders recursively?
* `user_id` (int64): User ID.  Provide `username` or `user_id`
* `username` (string): User username.  Provide `username` or `user_id`
* `group_name` (string): Group name.  Provide `group_name` or `group_id`
* `site_id` (int64): Site ID. If not provided, will default to current site. Used when creating a permission for a child site.

---

## Delete Permission

```
$permission = current(\Files\Model\Permission::all());

$permission->delete();
```

### Parameters

* `id` (int64): Required - Permission ID.

