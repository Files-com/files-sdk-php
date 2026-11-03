# IpAddress

## Example IpAddress Object

```
{
  "id": "Site",
  "associated_with": "Site",
  "group_id": 1,
  "ip_addresses": [
    "127.0.0.1"
  ]
}
```

* `id` (string): Unique label for list; used by Zapier and other integrations.
* `associated_with` (string): The object that this public IP address list is associated with.
* `group_id` (int64): Group ID
* `ip_addresses` (array(string)): A list of IP addresses.

---

## List IP Addresses associated with the current site

```
\Files\Model\IpAddress::all();
```

`\Files\Model\IpAddress::listPage()` takes the same parameters and returns the results
one `\Files\Page` at a time, requesting each later page only when it is asked for.


### Parameters

* `cursor` (string): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
* `per_page` (int64): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).

---

## List all possible public SmartFile IP addresses

```
\Files\Model\IpAddress::getSmartfileReserved();
```

`\Files\Model\IpAddress::getSmartfileReservedPage()` takes the same parameters and returns the results
one `\Files\Page` at a time, requesting each later page only when it is asked for.


### Parameters

* `cursor` (string): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
* `per_page` (int64): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).

---

## List all possible public ExaVault IP addresses

```
\Files\Model\IpAddress::getExavaultReserved();
```

`\Files\Model\IpAddress::getExavaultReservedPage()` takes the same parameters and returns the results
one `\Files\Page` at a time, requesting each later page only when it is asked for.


### Parameters

* `cursor` (string): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
* `per_page` (int64): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).

---

## List all possible public IP addresses

```
\Files\Model\IpAddress::getReserved();
```

`\Files\Model\IpAddress::getReservedPage()` takes the same parameters and returns the results
one `\Files\Page` at a time, requesting each later page only when it is asked for.


### Parameters

* `cursor` (string): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
* `per_page` (int64): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
