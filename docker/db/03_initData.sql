INSERT INTO jmp.event_type (id, title, color)
VALUES (1, 'foo', '#FF0000');
INSERT INTO jmp.event_type (id, title, color)
VALUES (2, 'bar', '#00FF00');
INSERT INTO jmp.`group` (id, name)
VALUES (7, 'blue');
INSERT INTO jmp.`group` (id, name)
VALUES (6, 'green');
INSERT INTO jmp.`group` (id, name)
VALUES (5, 'red');
INSERT INTO jmp.registration_state (id, name, reason_required)
VALUES (1, 'unsubscribed', 1);
INSERT INTO jmp.registration_state (id, name, reason_required)
VALUES (2, 'subscribed', 1);
INSERT INTO jmp.registration_state (id, name, reason_required)
VALUES (3, 'perhaps', 1);
INSERT INTO jmp.registration_state (id, name, reason_required)
VALUES (4, 'no-reason', 0);
INSERT INTO jmp.user (id, username, lastname, firstname, email, password, password_change, is_admin)
VALUES (161, 'allen', 'Burdon', 'Allen', null, '$2y$10$T8pnjmUmDywvUIr9R7xfZOt6Qc0OgMa11DfVNNJ6m.KLMouUj47Sm', 0, 0);
INSERT INTO jmp.user (id, username, lastname, firstname, email, password, password_change, is_admin)
VALUES (162, 'walter', 'White', 'Walter', 'walter@white.me',
        '$2y$10$H13UpxodSO8umDJn0ROEg.jDUj4nco.ZmWHU1S7bVZSBny/avlNyq', 0, 0);
INSERT INTO jmp.user (id, username, lastname, firstname, email, password, password_change, is_admin)
VALUES (163, 'adam', 'Fawell', 'Adam', null, '$2y$10$T8pnjmUmDywvUIr9R7xfZOt6Qc0OgMa11DfVNNJ6m.KLMouUj47Sm', 0, 1);
INSERT INTO jmp.membership (group_id, user_id)
VALUES (5, 163);
INSERT INTO jmp.membership (group_id, user_id)
VALUES (6, 161);
INSERT INTO jmp.membership (group_id, user_id)
VALUES (6, 162);
INSERT INTO jmp.membership (group_id, user_id)
VALUES (7, 162);
INSERT INTO jmp.membership (group_id, user_id)
VALUES (7, 163);
INSERT INTO jmp.event (id, title, `from`, `to`, place, description, event_type_id, default_registration_state_id)
VALUES (29, 'red event', '2019-01-15 12:12:12', '2019-03-28 13:13:13', 'GibmIT, Pratteln', '2', 1, 2);
INSERT INTO jmp.event (id, title, `from`, `to`, place, description, event_type_id, default_registration_state_id)
VALUES (30, 'red event', '2019-03-27 13:13:13', '2019-03-28 13:13:13', 'GibmIT, Pratteln', '4', 2, 1);
INSERT INTO jmp.event (id, title, `from`, `to`, place, description, event_type_id, default_registration_state_id)
VALUES (31, 'green event', '2019-01-15 12:12:12', '2019-01-15 13:13:13', 'GibmIT, Pratteln', '1', 1, 2);
INSERT INTO jmp.event (id, title, `from`, `to`, place, description, event_type_id, default_registration_state_id)
VALUES (32, 'green event', '2019-04-28 11:13:13', '2019-04-28 13:13:13', 'GibmIT, Pratteln', '7', 1, 1);
INSERT INTO jmp.event (id, title, `from`, `to`, place, description, event_type_id, default_registration_state_id)
VALUES (33, 'blue event', '2019-04-27 08:13:13', '2019-04-27 13:13:13', 'GibmIT, Pratteln', '6', 2, 2);
INSERT INTO jmp.event (id, title, `from`, `to`, place, description, event_type_id, default_registration_state_id)
VALUES (34, 'blue/red event', '2019-03-31 10:13:13', '2019-03-31 13:13:13', 'GibmIT, Pratteln', '5', 1, 2);
INSERT INTO jmp.event (id, title, `from`, `to`, place, description, event_type_id, default_registration_state_id)
VALUES (35, 'blue/red/green event', '2019-03-04 08:13:13', '2019-03-05 13:13:13', 'GibmIT, Pratteln', '3', 1, 1);
INSERT INTO jmp.event_has_group (event_id, group_id)
VALUES (29, 5);
INSERT INTO jmp.event_has_group (event_id, group_id)
VALUES (30, 5);
INSERT INTO jmp.event_has_group (event_id, group_id)
VALUES (31, 6);
INSERT INTO jmp.event_has_group (event_id, group_id)
VALUES (32, 6);
INSERT INTO jmp.event_has_group (event_id, group_id)
VALUES (33, 7);
INSERT INTO jmp.event_has_group (event_id, group_id)
VALUES (34, 5);
INSERT INTO jmp.event_has_group (event_id, group_id)
VALUES (34, 6);
INSERT INTO jmp.event_has_group (event_id, group_id)
VALUES (35, 5);
INSERT INTO jmp.event_has_group (event_id, group_id)
VALUES (35, 6);
INSERT INTO jmp.event_has_group (event_id, group_id)
VALUES (35, 7);





