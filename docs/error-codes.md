## 001
### Description:
Login failed.
### Cause:
Username or password is incorrect.
### Parameters:
none
### Example:
```json
{
  "errors": [
    {
      "code": "001"
    }
  ]
}
```

## 002
### Description:
Failed to add users to a group.
### Cause:
Tried to add invalid user id's.
### Parameters:
| Name    | Type  |
|---------|-------|
| userIds | Array |
### Example:
```json
{
  "errors": [
    {
      "code": "001",
      "parameters": {
        "userIds": [1,2,3]
      }
    }
  ]
}
```
