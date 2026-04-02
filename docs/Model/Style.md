# Style

## Example Style Object

```
{
  "id": 1,
  "path": "example",
  "logo": "https://mysite.files.com/...",
  "logo_click_href": "https://www.example.com",
  "thumbnail": {
    "name": "My logo",
    "uri": "https://mysite.files.com/.../my_image.png"
  }
}
```

* `id` (int64): Style ID
* `path` (string): Folder path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
* `logo` (Image): Logo
* `logo_click_href` (string): URL to open when a public visitor clicks the logo
* `thumbnail` (Image): Logo thumbnail
* `file` (file): Logo for custom branding. Required when creating a new style.

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
  'logo_click_href' => "https://www.example.com",
]);
```

### Parameters

* `path` (string): Required - Style path.
* `file` (file): Logo for custom branding. Required when creating a new style.
* `logo_click_href` (string): URL to open when a public visitor clicks the logo.

### Example Response

```json
{
  "id": 1,
  "path": "example",
  "logo": "https://mysite.files.com/...",
  "logo_click_href": "https://www.example.com",
  "thumbnail": {
    "name": "My logo",
    "uri": "https://mysite.files.com/.../my_image.png"
  }
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

