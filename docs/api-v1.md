# API Spec Version 1
# Table of Content:
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
  * [User](#user-routes)
    * [Create User](#create-user)
    * [List Users](#list-users)
    * [Get Current User](#get-current-user)
    * [Get User](#get-user)
    * [Update User](#update-user)
    * [Delete User](#delete-user)
    * [Change Password](#change-password)
  * [Event](#event-routes)
    * [Create Event](#create-event)
    * [List Events](#list-events)
    * [Get Event](#get-event)
    * [Update Event](#update-event)
    * [Delete Event](#delete-event)
  * [Event Type](#event-type-routes)
    * [Create Event Type](#create-event-type)
    * [List Event Types](#list-event-types)
    * [Get Event Type](#get-event-type)
    * [Delete Event Type](#delete-event-type)
  * [Group](#group-routes)
    * [Create Group](#create-group)
    * [List Groups](#list-groups)
    * [Get Group](#get-group)
    * [Update Group](#update-group)
    * [Delete Group](#delete-group)
    * [Join Group](#join-group)
    * [Leave Group](#leave-group)
  * [Registration State](#registration-state-routes)
    * [Create Registration State](#create-registration-state)
    * [List Registration States](#list-registration-states)
    * [Get Registration State](#get-registration-state)
    * [Delete Registration State](#delete-registration-state)
  * [Registration](#registration-routes)
    * [Create Registration](#create-registration)
    * [Update Registration](#update-registration)
    * [Get Registration](#get-registration)
    * [Delete Registration](#delete-registration)
    * [Get Registrations](#get-registrations)
  * [Presence](#presence-routes)
    * [Create Presence](#create-presence)
    * [Get Presence](#get-presence)
    * [Update Presence](#update-presence)
    * [Delete Presence](#delete-presence)
    * [Create Presences](#create-presences)
    * [Get Presences](#get-presences)

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

### Extended Registration
This is an extended version of the registration object. It contains both information about the user and the registration.
```json
{
  "id": 162,
  "username": "walter",
  "lastname": "White",
  "firstname": "Walter",
  "email": "walter@white.me",
  "passwordChange": false,
  "isAdmin": false,
  "registration": {
    "reason": "Sick",
    "registrationState": {
      "id": 1,
      "name": "Deregistered",
      "reasonRequired": true
    }
  }
}
```

### Presence

```json
{
  "event": 29,
  "user": 160,
  "auditor": 160,
  "hasAttended": false
}
```
### Extended Presence
This is an extended version of the registration object. It contains both information about the user and the registration.
```json
{
  "id": 162,
  "username": "walter",
  "lastname": "White",
  "firstname": "Walter",
  "email": "walter@white.me",
  "passwordChange": false,
  "isAdmin": false,
  "presence": {
    "auditor": 160,
    "hasAttended": false
  }
}
```
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

| Field     | Description         | Required | Type |
| --------- | ------------------- | -------- | ---- |
| username  | The user's username | ✔️        | varchar(100) |
| password | The user's password | ✔️        | varchar(255) |

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

### User Routes

#### Create User

```http
POST /v1/users
```

Parameters:

| Field     | Description                                                  | Required | Type |
| --------- | ------------------------------------------------------------ | -------- | ---- |
| username  | The user's username                                          | ✔️        | varchar(100) |
| lastname  | The user's last name                                         | ❌        | varchar(50) |
| firstname | The user's first name                                        | ❌️        | varchar(50) |
| email     | The user's email                                             | ❌        | varchar(255) |
| password  | The user's initial password                                  | ✔️        | varchar(255) |
| passwordChange  | does the user have to change the password              | ✔️        | boolean(0 or 1) |
| isAdmin   | Whether the user is an administrator or not. Defaults to no admin | ❌        | boolean(0 or 1) |

Example request data:

```json
{
    "username": "jake",
    "lastname": "Smith",
    "firstname": "Jacob",
    "email": "jake@example.com",
    "password": "secure",
    "isAdmin": 1
}
```

Access rights: authentication required, user has to be an admin

Returns: the [User](#User)

#### List Users

```http
GET /v1/users
```

Parameters:

| Field | Description               | Required | Type |
| ----- | ------------------------- | -------- | ---- |
| group | Get all users by group id | ❌        | numeric |

Access rights: authentication required, user has to be an admin

Returns: List of queried [Users](#User)

#### Get Current User

```http
GET /v1/user
```

Parameters: none

Access rights: authentication required

Returns: the [User](#User)

#### Get User

```http
GET /v1/users/{id}
```

Parameters: none

Access rights: authentication required, user has to be an admin

Returns: the [User](#User)

#### Update User 

```http
PUT /v1/users/{id}
```

Parameters:

| Field     | Description         | Required | Type |
| --------- | ------------------- | -------- | ---- |
| username  | The new username    | ❌        | varchar(100) |
| lastname  | The new last name   | ❌        | varchar(50) |
| firstname | The new first name  | ❌        | varchar(50) |
| email     | The new email       | ❌        | varchar(255) |
| password  | The new password    | ❌        | varchar(255) |
| isAdmin   | The new admin state | ❌        | boolean(0 or 1) |
| passwordChange | does the user have to change the password | ❌        | boolean(0 or 1) |

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

#### Delete User 

```http
DELETE /v1/users/{id}
```

Parameters: none

Access rights: authentication required, user has to be an admin

Returns: Status 204 or errors

#### Change Password

```http
PUT /v1/user/change-password
```

Parameters:

| Field       | Description                 | Required | Type |
| ----------- | --------------------------- | -------- | ---- |
| password    | The user's current password | ✔        | varchar(255) |
| newPassword | The new password to set     | ✔        | varchar(255) |

Access rights: authentication required

Returns: Success or errors

### Event Routes

#### Create Event

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

#### List Events

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

#### Get Event

```http
GET /v1/events/{id:[0-9]+}
```

Parameters: none

Access rights: authentication required  
**Note:** An admin can access every event but a user can only access events of groups in which he has a membership.

Returns: the [Event](#Event)

#### Update Event 

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

#### Delete Event 

```http
DELETE /v1/events/{id}
```

Parameters: none

Access rights: authentication required, user has to be an admin

**Note:**  
When an Event is deleted, all associated registrations and presences are also deleted.  
So be careful and ask the user twice if he really wants to delete an Event.

### Event Type Routes

#### Create Event Type

```http
POST /v1/event-types
```

Parameters:

| Field | Description                        | Required | Type |
| ----- | ---------------------------------- | -------- | ---- |
| title | The title of the event type        | ✔️        | varchar(50) |
| color | The color used for this event type | ✔️        | hex-rgb-color |

Example request data:

```json
{
    "title": "Default",
    "color": "#ff0000"
}
```

Access rights: authentication required, user has to be an admin

Returns: the [Event Type](#event-type)

#### List Event Types

```http
GET /v1/event-types
```

Parameters: none

Access rights: authentication required

Returns: List of all event types

#### Get Event Type

```http
GET /v1/event-types/{id}
```

Parameters: none

Access rights: authentication required

Returns: the [Event Type](#event-type)

#### Update Event Type

```http
PUT /v1/event-types/{id}
```

Parameters:

| Field | Description                        | Required | Type |
| ----- | ---------------------------------- | -------- | ---- |
| title | The title of the event type        | ❌        | varchar(50) |
| color | The color used for this event type | ❌        | hex-rgb-color |

Example request data:

```json
{
    "title": "Default",
    "color": "#ff0000"
}
```

Access rights: authentication required, user has to be an admin

Returns: the [Event Type](#event-type)

#### Delete Event Type

```http
DELETE /v1/event-types/{id}
```

Parameters: none

Access rights: authentication required, user has to be an admin

**Note:** Event types can only be deleted when they currently aren't in use.

Returns: Status 204 or errors

### Group Routes

#### Create Group

```http
POST /v1/groups
```

Parameters:

| Field | Description           | Required | Type |
| ----- | --------------------- | -------- | ---- |
| name  | The name of the group | ✔️    | varchar(45) |

Example request data:

```json
{
    "name": "Members"
}
```

Access rights: authentication required, user has to be an admin

Returns: the [Group](#Group)

#### List Groups

```http
GET /v1/groups/
```

Parameters: none

Access rights: authentication required

Returns: List of all groups

#### Get Group

```http
GET /v1/groups/{id}
```

Parameters: none

Access rights: authentication required

Returns: the [Group](#Group)

#### Update Group 

```http
PUT /v1/groups/{id}
```

Parameters:

| Field | Description  | Required | Type |
| ----- | ------------ | -------- | ---- |
| name  | The new name | ❌        | varchar(45) |

Example request data:

```json
{
    "name": "Users"
}
```

Access rights: authentication required, user has to be an admin

Returns: the [Group](#Group)

#### Delete Group 

```http
DELETE /v1/groups/{id}
```

Parameters: none

Access rights: authentication required, user has to be an admin

Returns: 204 or errors

#### Join Group

```http
POST /v1/groups/{id}/join
```

Parameters:

| Field | Description                         | Required | Type |
| ----- | ----------------------------------- | -------- | ---- |
| users | One or more users to join the group | ✔        | array with id's (numeric) |

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
        "invalidUsers": [5,10]
    }
}
````

#### Leave Group

```http
DELETE /v1/groups/{id}/leave
```

Parameters:

| Field | Description                          | Required | Type |
| ----- | ------------------------------------ | -------- | ---- |
| users | One or more users to leave the group | ✔️       | array with id's (numeric) |

Example request data:

```json
{
    "users": [1]
}
```

**Note:** If any of the given user id's should be invalid, there is no notification about this.

Access rights: authentication required, user has to be an admin

Returns: the [Group](#Group)

### Registration State Routes

#### Create Registration State

```http
POST /v1/registration-state
```

Parameters:

| Field          | Description                       | Required | Type |
| -------------- | --------------------------------- | -------- | ---- |
| name           | The name                          | ✔       |varchar(255) |
| reasonRequired | If a reason / excuse is mandatory | ✔       |boolean(0 or 1)

Example request data:

```json
{
    "name": "Accepted",
    "reasonRequired": 1
}
```

Access rights: authentication required, user has to be an admin

Returns: the [Registration State](#Registration State)

#### Update Registration State

```http
PUT /v1/registration-state/{id}
```

Parameters:

| Field          | Description                       | Required | Type |
| -------------- | --------------------------------- | -------- | ---- |
| name           | The name                          | ❌       | varchar(255) |
| reasonRequired | If a reason / excuse is mandatory | ❌       | boolean(0 or 1)

Example request data:

```json
{
    "name": "Accepted",
    "reasonRequired": 0
}
```

Access rights: authentication required, user has to be an admin

Returns: the [Registration State](#Registration State)

#### List Registration States

```http
GET /v1/registration-state/
```

Parameters: none

Access rights: authentication required

Returns: List of all event types

#### Get Registration State

```http
GET /v1/registration-state/{id}
```

Parameters: none

Access rights: authentication required

Returns: the [Registration State](#registration-state)

#### Delete Registration State

```http
DELETE /v1/registration-state/{id}
```

Parameters: none

Access rights: authentication required, user has to be an admin

Returns: 204 or errors

### Registration Routes

#### Get Registration from Event

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

| Field     | Description                                                  | Required | Type |
| --------- | ------------------------------------------------------------ | -------- | ---- |
| eventId   | The event's id                                               | ✔️     | numeric |
| userId    | The user's id                                                | ✔        | numeric |
| reason    | The reason for the registration                              | ❌        | varchar(80) |
| registrationState | [Registration State](#registration-state)'s id       | ❌       | numeric |

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

**Note:**  
When no registration state is delivered, the default registration state of the event is used.
When the default registration state of the event requires a reason, but no reason is delivered, then it will create a default reason which equals the registration state name.

Access rights: authentication required

Returns: the [Registration](#registration)

#### Get Registration 

```http
GET /v1/registration/{{eventId}}/{{userId}}
```

Parameters:

| Field     | Description                                                  | Required | Type |
| --------- | ------------------------------------------------------------ | -------- | ---- |
| eventId   | The event's id                                               | ✔️      | numeric |
| userId    | The user's id                                                | ✔        | numeric |


Access rights: authentication required

Returns: the [Registration](#registration)

#### Update Registration 

```http
PUT /v1/registration/{eventId}/{userId}
```

Parameters:

| Field     | Description                                                  | Required | Type |
| --------- | ------------------------------------------------------------ | -------- | ---- |
| reason    | The reason for the registration                              | ❌        | varchar(80) |
| registrationState | [Registration State](#registration-state)'s id       | ✔        | numeric |

Example request data:

```json
{
    "reason": "Sick",
    "registrationState": 1
}
```

Access rights: authentication required

Returns: the [Registration](#registration)

#### Delete Registration 

```http
DELETE /v1/registration/{eventId}/{userId}
```

Parameters:

| Field     | Description                                                  | Required | Type |
| --------- | ------------------------------------------------------------ | -------- | ---- |
| eventId   | The event's id                                               | ✔️     |numeric|
| userId    | The user's id                                                | ✔        | numeric |


Access rights: authentication required

Returns: HTTP 204 status code when successful

#### Get Registrations 

```http
GET /v1/events/{eventId}/registrations
```

Parameters:

| Field     | Description                                                  | Required | Type |
| --------- | ------------------------------------------------------------ | -------- | ---- |
| eventId   | The event's id                                               | ✔️     |numeric|


Access rights: admin

Returns: [Event](#event) and list of [Extended Registrations](#extended-registration)

Example:

```json
{
  "event": {
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
        "name": "green"
      }
    ]
  },
  "registrations": [
    {
      "id": 162,
      "username": "walter",
      "lastname": "White",
      "firstname": "Walter",
      "email": "walter@white.me",
      "passwordChange": false,
      "isAdmin": false,
      "registration": {
        "reason": "Sick",
        "registrationState": {
          "id": 1,
          "name": "Deregistered",
          "reasonRequired": true
        }
      }
    }
  ]
}
```

### Presence Routes

#### Create Presence

```http request
POST /v1/presence
```

Parameters:

| Field     | Description                                                  | Required | Type |
| --------- | ------------------------------------------------------------ | -------- | ---- |
| event   | The event's id                                               | ✔️     | numeric |
| user    | The user's id                                                | ✔        | numeric |
| hasAttended    | True if the user as attended at the event               | ✔        | boolean(0 or 1) |

Access rights: admin

Returns: [Presence](#presence)

#### Get Presence 

```http
GET /v1/presence/{eventId}/{userId}/{auditorId}
```

Parameters:

| Field     | Description                                                  | Required | Type |
| --------- | ------------------------------------------------------------ | -------- | ---- |
| eventId   | The event's id                                               | ✔️      | numeric |
| userId    | The user's id                                                | ✔        | numeric |
| auditorId    | The auditor's id                                                | ✔        | numeric |


**Note:**  
For different auditors there can exists different presence entrys as may more than one is performing attendance checks.

Access rights: admin

Returns: [Presence](#presence)

#### Update Presence 

```http
PUT /v1/presence/{eventId}/{userId}/{auditorId}
```

Parameters:

| Field     | Description                                                  | Required | Type |
| --------- | ------------------------------------------------------------ | -------- | ---- |
| event   | The event's id                                               | ✔️     | numeric |
| user    | The user's id                                                | ✔        | numeric |
| auditorId    | The auditor's id                                                | ✔        | numeric |
| hasAttended    | True if the user as attended at the event               | ✔        | boolean(0 or 1) |


Example request data:

```json
{
    "hasAttended": 0
}
```

**Note:**  
For different auditors there can exists different presence entrys as may more than one is performing attendance checks.

Access rights: admin

Returns: [Presence](#presence)

#### Delete Presence 

```http
DELETE /v1/presence/{eventId}/{userId}/{auditorId}
```

Parameters:

| Field     | Description                                                  | Required | Type |
| --------- | ------------------------------------------------------------ | -------- | ---- |
| event   | The event's id                                               | ✔️     | numeric |
| auditorId    | The auditor's id                                                | ✔        | numeric |
| user    | The user's id                                                | ✔        | numeric |


Access rights: admin

Returns: HTTP 204 status code when successful

#### Create Presences

````http request
POST /v1/events/{eventId}/presences
````

Parameters:

| Field     | Description                                                  | Required | Type |
| --------- | ------------------------------------------------------------ | -------- | ---- |
| eventId   | The event's id                                               | ✔️      | numeric |
| presences    | An array where each object contains an userId and hasAttended | ✔        | array |

Example request data:

````json
[
  {
    "user": 12,
    "hasAttended": 1
  },
  {
    "user": 13,
    "hasAttended": 0
  }
]
````

**Note:**  
For different auditors there can exists different presence entrys as may more than one is performing attendance checks.

Access rights: admin

Returns: [Event](#event) and list of [Extended Presences](#extended-presence)


#### Get Presences 

```http
GET /v1/events/{eventId}/presences
```

Parameters:

| Field     | Description                                                  | Required | Type |
| --------- | ------------------------------------------------------------ | -------- | ---- |
| eventId   | The event's id                                               | ✔️     |numeric|


Access rights: admin

Returns: [Event](#event) and list of [Extended Presences](#extended-presence)

___TODO:___ Presence and User Meta
