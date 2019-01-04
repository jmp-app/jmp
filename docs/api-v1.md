# API Spec Version 1

## JSON Objects

### User

```json
{
    "id": 1,
    "username": "jake",
    "lastname": "Smith",
    "firstname": "Jacob",
    "email": "jake@example.com",
    "isAdmin": true
}
```
### Group

```json
{
    "id": 1,
    "name": "Members",
    "users": [1, 3, 5]
}
```

### Event

```json
{
  "id": 29,
  "title": "GA",
  "from": "2019-01-15 12:12:12",
  "to": "2019-01-15 13:13:13",
  "place": "Earth",
  "description": "General Assembly",
  "eventType": {
    "id": 1,
    "title": "Default",
    "color": "#d6f936"
  },
  "defaultRegistrationState": {
    "id": "2",
    "name": "Accepted",
    "reasonRequired": false
  },
  "groups": [
    {
      "id": 5,
      "name": "Members"
    }
  ]
}

```

**Note:** The [Event](#Event) includes an [Event Type](#event_type), a [Registration State](#registration_state) and a list of [Groups](#Group)


### Event Type

```json
{
    "id": 1,
    "title": "Default",
    "color": "#ff0000"
}
```

### Registration State

```json
{
    "id": 1,
    "name": "Accepted",
    "requiresReason": false
}
```

### Registration

```json
{
    "eventId": 29,
    "userId": 160,
    "reason": "Sick",
    "registrationState": {
        "id": 1,
        "name": "Deregistered",
        "reasonRequired": true
    }
}
```

**Note** [Registration](#Registration) includes a [Registration State](#registration_state)

## Error Handling

### HTTP Status Codes

The following errors may occur:

| Status Code | Name               | Description                                           |
| ----------- | ------------------ | ----------------------------------------------------- |
| 400         | Bad request        | The request                                           |
| 401         | Unauthorized       | Request requires authentication but it isn't provided |
| 403         | Forbidden          | The user has no rights to access the requested URI    |
| 404         | Resource Not Found | The requested resource can't be found                 |

### JSON Error Objects

Further details to the errors are provided as JSON Objects:

```json
{
    "errors": {
        "authentication": "authentication required"
    }
}
```

## Authentication

To authenticate use the following header:

`Authorization: Bearer THESECRETJWTTOKEN`

The token is first received by [logging in](#Authentication). All routes except the login route require authorization. Additionally some routes can only be accessed by **administrators**. 

## Endpoints

### Authentication

``````http
POST /v1/login
``````

Parameters:

| Field    | Description         | Required |
| -------- | ------------------- | -------- |
| username | The user's username | ✔️        |
| password | The user's password | ✔️        |

Example request data:

```json
{
    "username": "jake",
    "password": "secure"
}
```

Returns: the user's token and the user data

```JSON
{
    "token": "thesecrettoken",
    "user": {
        "id": 1,
    	"username": "jake",
    	"lastname": "Smith",
    	"firstname": "Jacob",
    	"email": "jake@example.com",
        "isAdmin": true,
        "passwordChange": false
    }
}
```

Access rights: no authentication required

**Note:** token is a jwt token used for authorization. See more: https://jwt.io/

### Create User

```http
POST /v1/users
```

Parameters:

| Field     | Description                                                  | Required |
| --------- | ------------------------------------------------------------ | -------- |
| username  | The user's username                                          | ✔️        |
| lastname  | The user's last name                                         | ❌        |
| firstname | The user's first name                                        | ❌️        |
| email     | The user's email                                             | ❌        |
| password  | The user's initial password                                  | ✔️        |
| isAdmin   | Whether the user is an administrator or not. Defaults to no admin | ❌        |

Example request data:

```json
{
    "username": "jake",
    "lastname": "Smith",
    "firstname": "Jacob",
    "email": "jake@example.com",
    "password": "secure",
    "isAdmin": true
}
```

Access rights: authentication required, user has to be an admin

Returns: the [User](#User)

### List Users

```http
GET /v1/users
```

Parameters:

| Field | Description               | Required |
| ----- | ------------------------- | -------- |
| group | Get all users by group id | ❌        |

Access rights: authentication required, user has to be an admin

Returns: List of queried users

### Get Current User

```http
GET /v1/user
```

Parameters: none

Access rights: authentication required

Returns: the [User](#User)

### Get User

```http
GET /v1/users/{id}
```

Parameters: none

Access rights: authentication required, user has to be an admin

Returns: the [User](#User)

### Update User 

```http
PUT /v1/users/{id}
```

Parameters:

| Field     | Description         | Required |
| --------- | ------------------- | -------- |
| username  | The new username    | ❌        |
| lastname  | The new last name   | ❌        |
| firstname | The new first name  | ❌        |
| email     | The new email       | ❌        |
| password  | The new password    | ❌        |
| isAdmin   | The new admin state | ❌        |

Example request data:

```json
{
    "username": "jake2",
    "email": "jacob.smith@example.com",
    "password": "moresecure"
}
```

Access rights: authentication required, user has to be an admin

Returns: the [User](#User)

### Delete User 

```http
DELETE /v1/users/{id}
```

Parameters: none

Access rights: authentication required, user has to be an admin

### Create Event

```http
POST /v1/events
```

Parameters:

| Field                    | Description                                                  | Required |
| ------------------------ | ------------------------------------------------------------ | -------- |
| title                    | The title of the event                                       | ✔️        |
| description              | Description of the event                                     | ❌        |
| from                     | The begin date of the event                                  | ✔️        |
| to                       | The end date of the event                                    | ✔️        |
| place                    | Where the event takes place                                  | ❌        |
| eventType                | The id of the event type this event has                      | ✔️        |
| defaultRegistrationState | The id of the registration state that is used as default for this event. | ✔️        |
| groups                   | An array of group ids that are assigned to the event         | ✔️        |

Example request data:

```json
{
    "title": "GA",
    "description": "General Assembly",
    "from": "2018-11-28T10:00:00Z",
    "to": "2018-11-29T18:00:00Z",
    "eventType": "3",
    "defaultRegistrationState": "2",
    "groups": [1, 4]
}
```

Access rights: authentication required, user has to be an admin

Returns: the [Event](#Event)

### List Events

```http
GET /v1/events/
```

Parameters:

| Field     | Description                          | Required |
| --------- | ------------------------------------ | -------- |
| group     | To get all events by the group id    | ❌        |
| eventType | To get all events by type id         | ❌        |
| limit     | Limit the amount of events retrieved | ❌        |
| offset    | Skip the fist _x_ events             | ❌        |

Example request:

```http
GET /v1/events?limit=5&offset=10&eventType=1
```
Access rights: authentication required

Returns: List of queried events, sorted __descending__ by their __start date__.

### Get Event

```http
GET /v1/events/{id}
```

Parameters: none

Access rights: authentication required

Returns: the [Event](#Event)

### Update Event 

```http
PUT /v1/events/{id}
```

Parameters:

| Field                    | Description                                                  | Required |
| ------------------------ | ------------------------------------------------------------ | -------- |
| title                    | The title of the event                                       | ❌        |
| description              | Description of the event                                     | ❌        |
| from                     | The begin date of the event                                  | ❌        |
| to                       | The end date of the event                                    | ❌        |
| place                    | Where the event takes place                                  | ❌        |
| eventType                | The id of the event type this event has                      | ❌        |
| defaultRegistrationState | The id of the registration state that is used as default for this event. | ❌        |
| groups                   | An array of group ids that are assigned to the event         | ❌        |

Example request data:

```json
{
    "title": "",
    "description": ""
}
```

Access rights: authentication required, user has to be an admin

Returns: the [Event](#Event)

### Delete Event 

```http
DELETE /v1/events/{id}
```

Parameters: none

Access rights: authentication required, user has to be an admin

### Create Event Type

```http
POST /v1/event-types
```

Parameters:

| Field | Description                        | Required |
| ----- | ---------------------------------- | -------- |
| title | The title of the event type        | ✔️        |
| color | The color used for this event type | ✔️        |

Example request data:

```json
{
    "title": "Default",
    "color": "#ff0000"
}
```

Access rights: authentication required, user has to be an admin

Returns: the [Event Type](#event_type)

### List Event Types

```http
GET /v1/event-types/
```

Parameters: none

Access rights: authentication required

Returns: List of all event types

### Get Event Type

```http
GET /v1/event-types/{id}
```

Parameters: none

Access rights: authentication required

Returns: the [Event Type](#event_type)

### Delete Event Type

```http
DELETE /v1/event-types/{id}
```

Parameters: none

Access rights: authentication required, user has to be an admin

### Create Group

```http
POST /v1/groups
```

Parameters:

| Field | Description           | Required |
| ----- | --------------------- | -------- |
| name  | The name of the group | ✔️        |

Example request data:

```json
{
    "name": "Members"
}
```

Access rights: authentication required, user has to be an admin

Returns: the [Group](#Group)

### List Groups

```http
GET /v1/groups/
```

Parameters: none

Access rights: authentication required

Returns: List of all groups

### Get Group

```http
GET /v1/groups/{id}
```

Parameters: none

Access rights: authentication required

Returns: the [Group](#Group)

### Update Group 

```http
PUT /v1/groups/{id}
```

Parameters:

| Field | Description  | Required |
| ----- | ------------ | -------- |
| name  | The new name | ❌        |

Example request data:

```json
{
    "name": "Users"
}
```

Access rights: authentication required, user has to be an admin

Returns: the [Group](#Group)

### Delete Group 

```http
DELETE /v1/groups/{id}
```

Parameters: none

Access rights: authentication required, user has to be an admin

### Join Group

```http
POST /v1/groups/{id}/join
```

Parameters:

| Field | Description                         | Required |
| ----- | ----------------------------------- | -------- |
| users | One or more users to join the group | ✔️        |

Example request data:

```json
{
    "users": [1, 42]
}
```

Access rights: authentication required, user has to be an admin

Returns: the [Group](#Group)

### Leave Group

```http
DELETE /v1/groups/{id}/join
```

Parameters:

| Field | Description                          | Required |
| ----- | ------------------------------------ | -------- |
| users | One or more users to leave the group | ✔️        |

Example request data:

```json
{
    "users": [1]
}
```

Access rights: authentication required, user has to be an admin

Returns: the [Group](#Group)

### Create Registration State

```http
POST /v1/registration-state
```

Parameters:

| Field          | Description                       | Required |
| -------------- | --------------------------------- | -------- |
| name           | The name                          | ✔️        |
| reasonRequired | If a reason / excuse is mandatory | ✔️        |

Example request data:

```json
{
    "name": "Accepted",
    "reasonRequired": false
}
```

Access rights: authentication required, user has to be an admin

Returns: the [Registration State](#Registration State)

### List Registration States

```http
GET /v1/registration-state/
```

Parameters: none

Access rights: authentication required

Returns: List of all event types

### Get Registration State

```http
GET /v1/registration-state/{id}
```

Parameters: none

Access rights: authentication required

Returns: the [Registration State](#registration-state)

### Delete Registration State

```http
DELETE /v1/registration-state/{id}
```

Parameters: none

Access rights: authentication required, user has to be an admin

### Get Registration State from Event

```http
GET /v1/registration/{eventId}/{userId}
```

Parameters: none

Access rights: authentication required

Returns: the [Registration](#registration)

### Create Registration

```http
POST /v1/registration
```

Parameters:

| Field     | Description                                                  | Required |
| --------- | ------------------------------------------------------------ | -------- |
| eventId   | The event's id                                               | ✔️     |
| userId    | The user's id                                                | ✔        |
| reason    | The reason for the registration                              | ❌        |
| registrationState | [Registration State](#registration-state)'s id       | ✔ |

Example request data:

```json
{
    "eventId": 1,
    "userId": 24,
    "reason": "Ill",
    "registrationState": {
      "id": 1,
      "name": "Declined",
      "requiresReason": true
    }
}
```

Access rights: authentication required

Returns: the [Registration](#registration)

___TODO:___ Registration, Presence and User Meta
