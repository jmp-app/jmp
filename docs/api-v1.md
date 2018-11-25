# API Spec Version 1

## JSON Objects

### User

```json
{
    "id": 1,
    "username": "jake",
    "lastname": "Smith",
    "firstname": "Jacob",
    "email": "jake@example.com"
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
    "id": 1,
    "title": "GA",
    "description": "General Assembly",
    "from": "2018-11-28T10:00:00Z",
    "to": "2018-11-29T18:00:00Z",
    "place": "Earth",
    "eventType": "3",
    "defaultRegistrationState": "2",
    "groups": [1, 4]
}
```

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

Returns: the [User](#User)

### Create User

```http
POST /v1/users
```

Parameters:

| Field     | Description                 | Required |
| --------- | --------------------------- | -------- |
| username  | The user's username         | ✔️        |
| lastname  | The user's last name        | ✔️        |
| firstname | The user's first name       | ✔️        |
| email     | The user's email            | ❌        |
| password  | The user's initial password | ✔️        |

Example request data:

```json
{
    "username": "jake",
    "lastname": "Smith",
    "firstname": "Jacob",
    "email": "jake@example.com",
    "password": "secure"
}
```

Returns: the [User](#User)

### List Users

```http
GET /v1/users
```

Parameters:

| Field | Description               | Required |
| ----- | ------------------------- | -------- |
| group | Get all users by group id | ❌        |

___TODO___: Example

Returns: List of queried users

### Get Current User

```http
GET /v1/user
```

Parameters: none

Returns: the [User](#User)

### Get User

```http
GET /v1/users/{id}
```

Parameters: none

Returns: the [User](#User)

### Update User 

```http
PUT /v1/users/{id}
```

Parameters:

| Field     | Description        | Required |
| --------- | ------------------ | -------- |
| username  | The new username   | ❌        |
| lastname  | The new last name  | ❌        |
| firstname | The new first name | ❌        |
| email     | The new email      | ❌        |
| password  | The new password   | ❌        |

Example request data:

```json
{
    "username": "jake2",
    "email": "jacob.smith@example.com",
    "password": "moresecure"
}
```

Returns: the [User](#User)

### Delete User 

```http
DELETE /v1/users/{id}
```

Parameters: none

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

Returns: the [Event](#Event)

### List Events

```http
GET /v1/events/
```

Parameters:

| Field     | Description                    | Required |
| --------- | ------------------------------ | -------- |
| group     | To get all events by the group | ❌        |
| eventType | To get all events by type      | ❌        |

___TODO___: Example

Returns: List of queried events

### Get Event

```http
GET /v1/events/{id}
```

Parameters: none

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

Returns: the [Event](#Event)

### Delete Event 

```http
DELETE /v1/events/{id}
```

Parameters: none

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

Returns: the [Event Type](#Event Type)

### List Event Types

```http
GET /v1/event-types/
```

Parameters: none

Returns: List of all event types

### Get Event Type

```http
GET /v1/event-types/{id}
```

Parameters: none

Returns: the [Event Type](#Event Type)

### Delete Event Type

```http
DELETE /v1/event-types/{id}
```

Parameters: none

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

Returns: the [Group](#Group)

### List Groups

```http
GET /v1/groups/
```

Parameters: none

Returns: List of all groups

### Get Group

```http
GET /v1/groups/{id}
```

Parameters: none

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

Returns: the [Group](#Group)

### Delete Group 

```http
DELETE /v1/groups/{id}
```

Parameters: none

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

Returns: the [Registration State](#Registration State)

### Get Registration State

```http
GET /v1/registration_state/{id}
```

Parameters: none

Returns: the [Registration State](#Registration State)

### Delete Registration State

```http
DELETE /v1/registration_state/{id}
```

Parameters: none

___TODO:___ Registration, Presence and User Meta; Errors