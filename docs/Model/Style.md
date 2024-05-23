# Style

## Example Style Object

```
{
  "id": 1,
  "path": "example",
  "logo": "https://mysite.files.com/...",
  "thumbnail": "example"
}
```

* `id` (int64): Style ID
* `path` (string): Folder path This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
* `logo` (Image): Logo
* `thumbnail` (Image): Logo thumbnail
* `file` (file): Logo for custom branding.

---

## Show Style

```
$style = new \Files\Model\Style();
$style->find($path);
```


### Parameters

* `path` (string): Required - Style path.

---

## Update Style

```
$style = \Files\Model\Style::find($path);

$style->update([
  'file' => "file",
]);
```

### Parameters

* `path` (string): Required - Style path.
* `file` (file): Required - Logo for custom branding.

### Example Response

```json
{
  "id": 1,
  "path": "example",
  "logo": "https://mysite.files.com/...",
  "thumbnail": "example"
}
```

---

## Delete Style

```
$style = \Files\Model\Style::find($path);

$style->delete();
```

### Parameters

* `path` (string): Required - Style path.

