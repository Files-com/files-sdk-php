# BundleNotification

## Example BundleNotification Object

```
{
  "bundle_id": 1,
  "id": 1,
  "notify_on_registration": true,
  "notify_on_upload": true,
  "notify_user_id": 1
}
```

* `bundle_id` (int64): Bundle ID to notify on
* `id` (int64): Bundle Notification ID
* `notify_on_registration` (boolean): Triggers bundle notification when a registration action occurs for it.
* `notify_on_upload` (boolean): Triggers bundle notification when a upload action occurs for it.
* `notify_user_id` (int64): The id of the user to notify.
* `user_id` (int64): User ID.  Provide a value of `0` to operate the current session's user.

---

## List Bundle Notifications

```
$bundle_notification = new \Files\Model\BundleNotification();
$bundle_notification->list(, [
  'user_id' => 1,
]);
```


### Parameters

* `user_id` (int64): User ID.  Provide a value of `0` to operate the current session's user.
* `cursor` (string): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
* `per_page` (int64): Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
* `sort_by` (object): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `bundle_id`.
* `filter` (object): If set, return records where the specified field is equal to the supplied value. Valid fields are `bundle_id`.

---

## Show Bundle Notification

```
$bundle_notification = new \Files\Model\BundleNotification();
$bundle_notification->find($id);
```


### Parameters

* `id` (int64): Required - Bundle Notification ID.

---

## Create Bundle Notification

```
$bundle_notification = new \Files\Model\BundleNotification();
$bundle_notification->create(, [
  'user_id' => 1,
  'bundle_id' => 1,
  'notify_user_id' => 1,
  'notify_on_registration' => true,
  'notify_on_upload' => true,
]);
```


### Parameters

* `user_id` (int64): User ID.  Provide a value of `0` to operate the current session's user.
* `bundle_id` (int64): Required - Bundle ID to notify on
* `notify_user_id` (int64): The id of the user to notify.
* `notify_on_registration` (boolean): Triggers bundle notification when a registration action occurs for it.
* `notify_on_upload` (boolean): Triggers bundle notification when a upload action occurs for it.

---

## Update Bundle Notification

```
$bundle_notification = \Files\Model\BundleNotification::find($id);

$bundle_notification->update([
  'notify_on_registration' => true,
  'notify_on_upload' => true,
]);
```

### Parameters

* `id` (int64): Required - Bundle Notification ID.
* `notify_on_registration` (boolean): Triggers bundle notification when a registration action occurs for it.
* `notify_on_upload` (boolean): Triggers bundle notification when a upload action occurs for it.

### Example Response

```json
{
  "bundle_id": 1,
  "id": 1,
  "notify_on_registration": true,
  "notify_on_upload": true,
  "notify_user_id": 1
}
```

---

## Delete Bundle Notification

```
$bundle_notification = \Files\Model\BundleNotification::find($id);

$bundle_notification->delete();
```

### Parameters

* `id` (int64): Required - Bundle Notification ID.

