# API Spec Version 1
# Table of Contens:
- [API Spec Version 1](#api-spec-version-1)
- [JSON Objects](#json-objects)
  * [User](#user)
  * [Group](#group)
  * [Event](#event)
  * [Event Type](#event-type)
  * [Registration State](#registration-state)
- [Error Handling](#error-handling)
  * [HTTP Status Codes](#http-status-codes)
  * [JSON Error Objects](#json-error-objects)
- [Authentication](#authentication)
- [Endpoints](#endpoints)
  * [Login](#login)
  * [Create User](#create-user)
  * [List Users](#list-users)
  * [Get Current User](#get-current-user)
  * [Get User](#get-user)
  * [Update User](#update-user)
  * [Delete User](#delete-user)
  * [Create Event](#create-event)
  * [List Events](#list-events)
  * [Get Event](#get-event)
  * [Update Event](#update-event)
  * [Delete Event](#delete-event)
  * [Create Event Type](#create-event-type)
  * [List Event Types](#list-event-types)
  * [Get Event Type](#get-event-type)
  * [Delete Event Type](#delete-event-type)
  * [Create Group](#create-group)
  * [List Groups](#list-groups)
  * [Get Group](#get-group)
  * [Update Group](#update-group)
  * [Delete Group](#delete-group)
  * [Join Group](#join-group)
  * [Leave Group](#leave-group)
  * [Create Registration](#create-registration)
  * [Update Registration](#update-registration)
  * [Get Registration](#get-registration)
  * [Delete Registration](#delete-registration)
  * [Create Registration State](#create-registration-state)
  * [List Registration States](#list-registration-states)
  * [Get Registration State](#get-registration-state)
  * [Delete Registration State](#delete-registration-state)

<small><i><a href='http://ecotrust-canada.github.io/markdown-toc/'>Table of contents generated with markdown-toc</a></i></small>

## JSON Objects

### User

```json
{
  "id": 162,
  "username": "walter",
  "lastname": "White",
  "firstname": "Walter",
  "email": "walter@white.me",
  "passwordChange": false,
  "isAdmin": false
}
```
### Group

```json
{
  "id": 6,
  "name": "green",
  "users": [
    {
      "id": 161,
      "username": "allen",
      "lastname": "Burdon",
      "firstname": "Allen",
      "passwordChange": false,
      "isAdmin": false
    }
  ]
}
```

### Event

```json
{
  "id": 31,
  "title": "green event",
  "from": "2019-01-15T12:12",
  "to": "2019-01-15T13:13",
  "place": "GibmIT, Pratteln",
  "description": "1",
  "eventType": {
    "id": 1,
    "title": "foo",
    "color": "#FF0000"
  },
  "defaultRegistrationState": {
    "id": 2,
    "name": "subscribed",
    "reasonRequired": true
  },
  "groups": [
    {
      "id": 6,
      "name": "green",
      "users": [
        {
          "id": 161,
          "username": "allen",
          "lastname": "Burdon",
          "firstname": "Allen",
          "passwordChange": false,
          "isAdmin": false
        }
      ]
    }
  ]
}

```

**Note:** The [Event](#Event) includes an [Event Type](#event-type), a [Registration State](#registration-state) and a list of [Groups](#Group)


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

**Note** [Registration](#Registration) includes a [Registration State](#registration-state)

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

### Login

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
| passwordChange  | does the user have to change the password              | ✔️        |
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

Returns: List of queried [Users](#User)

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

### Change Password

```http
PUT /v1/user/change-password
```

Parameters:

| Field       | Description                 | Required |
| ----------- | --------------------------- | -------- |
| password    | The user's current password | ✔️        |
| newPassword | The new password to set     | ✔️        |

Access rights: authentication required

Returns: Success or errors

### Create Event

```http
POST /v1/events
```

Parameters:

| Field                    | Description                                                  | Required | Type |
| ------------------------ | ------------------------------------------------------------ | -------- | ---- |
| title                    | The title of the event                                       | ✔️        |  varchar(50) |
| description              | Description of the event                                     | ❌        | varchar(255) |
| from                     | The begin date of the event                                  | ✔️        |  Date in the ISO format: `Y-m-d\TH:i` |
| to                       | The end date of the event                                    | ✔️        |  Date in the ISO format: `Y-m-d\TH:i` |
| place                    | Where the event takes place                                  | ❌        | varchar(50) |
| eventType                | The id of the event type this event has                      | ✔️        |  numeric |
| defaultRegistrationState | The id of the registration state that is used as default for this event. | ✔️        |  numeric |
| groups                   | An array of group ids that are assigned to the event         | ✔️        |  Array of numbers |

Example request data:

```json
{
  "title": "GA",
  "description": "General Assembly",
  "from": "2018-11-28T10:00:00Z",
  "to": "2018-11-29T18:00:00Z",
  "eventType": 3,
  "defaultRegistrationState": 2,
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

| Field     | Description                          | Required | Type |
| --------- | ------------------------------------ | -------- | -------- |
| group     | To get all events by the group id    |          | numeric  |
| eventType | To get all events by type id         |          | numeric  |
| limit     | Limit the amount of events retrieved |          | numeric  |
| offset    | Skip the fist _x_ events             |          | numeric |
| all       | List events of all users/groups **(works only as ks only as admin)**| | bool |
| elapsed   | List also elapsed events             |          | bool |

Example request:

```http
GET /v1/events?limit=5&offset=10&eventType=1&all=1&elapsed=1
```
Access rights: authentication required  
**Note:** ```all``` is only considered if the user is an admin. Otherwise an 401 is returned.

Returns:  
List of queried [Events](#event) of all groups in which the user has a membership, sorted __ascending__ by their __start date__ (The near-time events are listed first).  
By default (without the elapsed parameter set), only current and upcoming events are selected.

### Get Event

```http
GET /v1/events/{id:[0-9]+}
```

Parameters: none

Access rights: authentication required  
**Note:** An admin can access every event but a user can only access events of groups in which he has a membership.

Returns: the [Event](#Event)

### Update Event 

```http
PUT /v1/events/{id}
```

Parameters:

| Field                    | Description                                                  | Required | Type |
| ------------------------ | ------------------------------------------------------------ | -------- | ---- |
| title                    | The title of the event                                       | ❌        |  varchar(50) |
| description              | Description of the event                                     | ❌        | varchar(255) |
| from                     | The begin date of the event                                  | ❌        |  Date in the ISO format: `Y-m-d\TH:i` |
| to                       | The end date of the event                                    | ❌        |  Date in the ISO format: `Y-m-d\TH:i` |
| place                    | Where the event takes place                                  | ❌        | varchar(50) |
| eventType                | The id of the event type this event has                      | ❌        |  numeric |
| defaultRegistrationState | The id of the registration state that is used as default for this event. | ❌        |  numeric |
| groups                   | An array of group ids that are assigned to the event         | ❌        |  Array of numbers |

Example request data:

```json
{
  "title": "GA",
  "description": "General Assembly",
  "from": "2018-11-28T10:00:00Z",
  "to": "2018-11-29T18:00:00Z",
  "groups": [1, 4]
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

Returns: the [Event Type](#event-type)

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

Returns: the [Event Type](#event-type)

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
| users | One or more users to join the group | ✔        |

Example request data:

```json
{
    "users": [1, 42]
}
```

Access rights: authentication required, user has to be an admin

Returns: A [group](#Group) and [errors](#json-error-objects) with all invalid user id's which could not be added.
````json
{
    "group": {
        "id": 5,
        "name": "Admin",
        "users": []
    },
    "errors": {
        "invalidUsers": "5,-1"
    }
}
````

### Leave Group

```http
DELETE /v1/groups/{id}/leave
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
| registrationState | [Registration State](#registration-state)'s id       | ❌       |

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

### Get Registration 

```http
GET /v1/registration/{{eventId}}/{{userId}}
```

Parameters:

| Field     | Description                                                  | Required |
| --------- | ------------------------------------------------------------ | -------- |
| eventId   | The event's id                                               | ✔️     |
| userId    | The user's id                                                | ✔        |


Access rights: authentication required

Returns: the [Registration](#registration)

### Update Registration 

```http
PUT /v1/registration/{eventId}/{userId}
```

Parameters:

| Field     | Description                                                  | Required |
| --------- | ------------------------------------------------------------ | -------- |
| reason    | The reason for the registration                              | ❌        |
| registrationState | [Registration State](#registration-state)'s id       | ✔ |

Example request data:

```json
{
    "reason": "Sick",
    "registrationState": 1
}
```

Access rights: authentication required

Returns: the [Registration](#registration)

### Delete Registration 

```http
DELETE /v1/registration/{eventId}/{userId}
```

Parameters:

| Field     | Description                                                  | Required |
| --------- | ------------------------------------------------------------ | -------- |
| eventId   | The event's id                                               | ✔️     |
| userId    | The user's id                                                | ✔        |


Access rights: authentication required

Returns: HTTP 204 status code when successful

___TODO:___ Registration, Presence and User Meta
